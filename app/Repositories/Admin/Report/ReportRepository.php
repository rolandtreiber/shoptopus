<?php

namespace App\Repositories\Admin\Report;

use App\Enums\Intervals;
use App\Models\Order;
use App\Services\Local\Report\ReportServiceInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportRepository implements ReportRepositoryInterface {

    protected ReportServiceInterface $reportService;

    public function __construct(ReportServiceInterface $reportService)
    {
        $this->reportService = $reportService;
    }

    public function getOrdersByStatusChartData(): array
    {
        $reportService = $this->reportService->setup();
        $orders = DB::table('orders')->select(['orders.status',  DB::raw('count(*) as total')])->groupBy('orders.status')->get();
        $color = 0;
        $count = [];
        $bgColor = [];
        $labels = [];
        $statuses = [
          1 => 'Paid',
          2 => 'Processing',
          3 => 'In Transit',
          4 => 'Completed',
          5 => 'On Hold',
          6 => 'Cancelled'
        ];
        $palette = $reportService->getPalette();
        foreach ($orders as $order) {
            $labels[] = $statuses[$order['status']];
            $count[] = $order['total'];
            $bgColor[] = $palette[$color];
            $color++;
            if ($color == 9) { $color = 0; }
        }

        $reportService->addDataset([
            'label' => "Orders",
            'borderColor' => "transparent",
            'backgroundColor' => $bgColor,
            'data' => $count
        ])->setLabels($labels);
        return $reportService->getResponse();
    }

    public function getOverview()
    {
        $ordersByStatusChartData = $this->getOrdersByStatusChartData();
        dd($ordersByStatusChartData);
        $start = Carbon::now()->subMonths(3);
        $end = Carbon::now();
        $data1 = Order::where('created_at', '>=', $start)->where('created_at', '<=', $end)->get();
        $reportService = $this->reportService->setup($start, $end, Intervals::Week);
        $reportService->setItems($data1);
        $reportService->makeReportDatasetByAttribute('status')->addLabel('Status')->addDataset();
//        $reportService->setItems($data2);
//        $reportService->makeReportDatasetByNumberOfItems()->addLabel('Users')->addDataset();
//        $labels = $reportService->getLabels();
//        $dates = $reportService->getDates();
//        $items = $reportService->getCurrentItems();
//        $dataset = $reportService->getCurrentDataSet();
        return $reportService->getResponse();
    }

    public function getChart(array $data)
    {
        $start = Carbon::parse($data['date_from']);
        $end = Carbon::parse($data['date_to']);
        $lengthInDays = $end->diffInDays($start);
        $shadowStart = $start->copy()->subDays($lengthInDays+1);
        $shadowEnd = $end->copy()->subDays($lengthInDays+1);
        $interval = $data['interval'];
        $type = $data['type'];
        $reportService = $this->reportService->setup($start, $end, $interval);
        $reportService->randomizeColors($data['randomize_colors']);

        $reportService->setShadow(false);

        if ($type !== 'aggregate') {
            // Timeline or cascade
            foreach (json_decode($data['models'], true) as $m) {
                $model = new $m['model'];
                $shadowData = $model->where('created_at', '>=', $shadowStart)->where('created_at', '<=', $shadowEnd)->get();
                $data = $model->where('created_at', '>=', $start)->where('created_at', '<=', $end)->get();
                $reportService->setItems($data);
                if (!isset($m['attribute'])) {
                    $reportService->makeReportDatasetByNumberOfItems(true)->addLabel($m['label'])->addDataset();
                } else {
                    $reportService->makeReportDatasetByAttribute($m['attribute'], true)->addLabel($m['label'])->addDataset();
                }
                $reportService->setShadow(true);

                $reportService->setItems($shadowData);
                    $cascade = $type == 'cascade';
                    if (!isset($m['attribute'])) {
                        $reportService->makeReportDatasetByNumberOfItems($cascade)->addLabel($m['label'].' (previous period)')->addDataset();
                    } else {
                        $reportService->makeReportDatasetByAttribute($m['attribute'], $cascade)->addLabel($m['label'].' (previous period)')->addDataset();
                    }

            }
        } else if ($type === 'timeline') {
            $reportService->clearLabels();
            $reportService->makeSingleValueDatasetItem('');
            foreach ($data['models'] as $m) {
                $model = new $m['model'];
                $model = $model->where('created_at', '>=', $start)->where('created_at', '<=', $end);
                $shadowData = $model->where('created_at', '>=', $shadowStart)->where('created_at', '<=', $shadowEnd)->get();

                foreach ($m['conditions'] as $condition) {
                    switch ($condition['type']) {
                        case 'where':
                            $model = $model->where($condition['field'], $condition['operator'], $condition['value']);
                            $shadowData = $shadowData->where($condition['field'], $condition['operator'], $condition['value']);
                            break;
                        case 'whereIn':
                            $model = $model->whereIn($condition['field'], $condition['value']);
                            $shadowData = $shadowData->where($condition['field'], $condition['operator'], $condition['value']);
                            break;
                    }
                }
                if (!isset($m['attribute'])) {
                    $data = $model->count();
                    $shadow = $shadowData->count();
                } else {
                    $data = $model->sum($m['attribute']);
                    $shadow = $shadowData->sum($m['attribute']);
                }
                $reportService
                    ->setShadow(false)
                    ->addSingleLabel($m['label'])
                    ->addDataToSingleDataset($data)
                    ->addDataset()
                    ->setShadow(true)
                    ->addDataToSingleDataset($shadow)
                    ->addDataset();
            }
        }
        return $reportService->getResponse();

    }
}
