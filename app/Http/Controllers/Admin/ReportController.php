<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Intervals;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReportRequest;
use App\Models\Payment;
use App\Models\User;
use App\Services\Local\Report\ReportServiceInterface;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    protected ReportServiceInterface $reportService;

    public function __construct(ReportServiceInterface $reportService)
    {
        $this->reportService = $reportService;
    }

    public function getChart(ReportRequest $request)
    {
        $start = Carbon::parse($request->date_from);
        $end = Carbon::parse($request->date_to);
        $lengthInDays = $end->diffInDays($start);
        $shadowStart = $start->copy()->subDays($lengthInDays+1);
        $shadowEnd = $end->copy()->subDays($lengthInDays+1);
        $interval = $request->interval;
        $type = $request->type;
        $reportService = $this->reportService->setup($start, $end, $interval);
        $reportService->randomizeColors($request->randomize_colors);

        $reportService->setShadow(false);

        if ($type !== 'aggregate') {
            // Timeline or cascade
            foreach (json_decode($request->models, true) as $m) {
                $model = new $m['model'];
                $shadowData = $model->where('created_at', '>=', $shadowStart)->where('created_at', '<=', $shadowEnd)->get();
                $data = $model->where('created_at', '>=', $start)->where('created_at', '<=', $end)->get();
                $reportService->setItems($data);
                $cascade = $type == 'cascade';
                if (!isset($m['attribute'])) {
                    $reportService->makeReportDatasetByNumberOfItems($cascade)->addLabel($m['label'])->addDataset();
                } else {
                    $reportService->makeReportDatasetByAttribute($m['attribute'], $cascade)->addLabel($m['label'])->addDataset();
                }
                $reportService->setShadow(true);

                $reportService->setItems($shadowData);
                if ($type !== 'aggregate') {
                    $cascade = $type == 'cascade';
                    if (!isset($m['attribute'])) {
                        $reportService->makeReportDatasetByNumberOfItems($cascade)->addLabel($m['label'].' (previous period)')->addDataset();
                    } else {
                        $reportService->makeReportDatasetByAttribute($m['attribute'], $cascade)->addLabel($m['label'].' (previous period)')->addDataset();
                    }
                }

            }
        } else if ($type === 'timeline') {
            $reportService->clearLabels();
            $reportService->makeSingleValueDatasetItem('');
            foreach ($request->models as $m) {
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

    public function getTestData()
    {
        $start = Carbon::parse('2020-02-11');
        $end = Carbon::parse('2020-05-26');
        $data1 = Payment::where('created_at', '>=', $start)->where('created_at', '<=', $end)->get();
        $data2 = User::where('created_at', '>=', $start)->where('created_at', '<=', $end)->get();
        $reportService = $this->reportService->setup($start, $end, Intervals::Week);
        $reportService->setItems($data1);
        $reportService->makeReportDatasetByAttribute('amount')->addLabel('Payments')->addDataset();
        $reportService->setItems($data2);
        $reportService->makeReportDatasetByNumberOfItems()->addLabel('Users')->addDataset();
        $labels = $reportService->getLabels();
        $dates = $reportService->getDates();
        $items = $reportService->getCurrentItems();
        $dataset = $reportService->getCurrentDataSet();
        return $reportService->getResponse();
    }

}
