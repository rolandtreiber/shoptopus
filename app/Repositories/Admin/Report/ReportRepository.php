<?php

namespace App\Repositories\Admin\Report;

use App\Enums\Intervals;
use App\Enums\OrderStatuses;
use App\Models\Order;
use App\Models\User;
use App\Services\Local\Report\ReportService;
use App\Services\Local\Report\ReportServiceInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportRepository implements ReportRepositoryInterface
{

    protected ReportServiceInterface $reportService;

    public function __construct(ReportServiceInterface $reportService)
    {
        $this->reportService = $reportService;
    }

    public function getSignupsOverTime(): array
    {
        $start = Carbon::now()->subMonth();
        $end = Carbon::now();

        $service = $this->getChartData([
            'date_from' => $start,
            'date_to' => $end,
            'interval' => Intervals::Day,
            'models' => [
                [
                    'label' => 'Signups',
                    'model' => User::class,
                ]
            ],
            'cascade' => false,
            'randomize_colors' => false
        ]);

        return $service->getApexChartsResponse();
    }

    public function getRevenueOverTime(): array
    {
        $start = Carbon::now()->subMonth();
        $end = Carbon::now();

        $lengthInDays = $end->diffInDays($start);
        $shadowStart = $start->copy()->subDays($lengthInDays + 1);
        $shadowEnd = $end->copy()->subDays($lengthInDays + 1);

        $service = $this->getChartData([
            'date_from' => $start,
            'date_to' => $end,
            'interval' => Intervals::Day,
            'models' => [
                [
                    'label' => 'Completed Orders',
                    'model' => Order::class,
                    'attribute' => 'total_price',
                    'conditions' => [
                        ['where', 'status', '=', OrderStatuses::Completed]
                    ]
                ],
                [
                    'label' => 'Paid Orders (Paid, Processing, In Transit)',
                    'model' => Order::class,
                    'attribute' => 'total_price',
                    'conditions' => [
                        ['whereIn', 'status', [
                            OrderStatuses::Paid,
                            OrderStatuses::Processing,
                            OrderStatuses::InTransit,
                        ]]
                    ]
                ]
            ],
            'cascade' => true,
            'randomize_colors' => false
        ]);

        return $service->getApexChartsResponse();
    }

    /**
     * @return array
     */
    public function getProductByStatusChartData(): array
    {
        $reportService = $this->reportService->setup();
        $products = DB::table('products')->select(['products.status', DB::raw('count(*) as total')])->groupBy('products.status')->get();
        $color = 0;
        $count = [];
        $bgColor = [];
        $labels = [];
        $statuses = [
            0 => 'Provisional',
            1 => 'Active',
            2 => 'Discontinued'
        ];
        $palette = $reportService->getPalette();
        foreach ($products as $product) {
            $labels[] = $statuses[$product['status']];
            $count[] = $product['total'];
            $bgColor[] = $palette[$color];
            $color++;
            if ($color == 9) {
                $color = 0;
            }
        }

        $reportService->addDataset([
            'label' => "Products",
            'borderColor' => "transparent",
            'backgroundColor' => $bgColor,
            'data' => $count,
            'labels' => $labels
        ])->setLabels($labels);
        return $reportService->getApexCompositePieResponse();
    }

    /**
     * @return array
     */
    public function getOrdersByStatusChartData(): array
    {
        $reportService = $this->reportService->setup();
        $orders = DB::table('orders')->select(['orders.status', DB::raw('count(*) as total')])->groupBy('orders.status')->get();
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
            if ($color == 9) {
                $color = 0;
            }
        }

        $reportService->addDataset([
            'label' => "Orders",
            'borderColor' => "transparent",
            'backgroundColor' => $bgColor,
            'data' => $count,
            'labels' => $labels
        ]);
        return $reportService->getApexCompositePieResponse();
    }

    /**
     * @return array
     */
    public function getOverview(): array
    {
        $ordersByStatusChartData = $this->getOrdersByStatusChartData();
        $productsByStatusChartData = $this->getProductByStatusChartData();
        $signupsOverTime = $this->getSignupsOverTime();
        $revenueOverTime = $this->getRevenueOverTime();

        return [
            'products_by_status_pie_chart_data' => $productsByStatusChartData,
            'orders_by_status_pie_chart_data' => $ordersByStatusChartData,
            'user_signups_over_time' => $signupsOverTime,
            'revenue_over_time' => $revenueOverTime
        ];
    }

    /**
     * @param array $data
     * @return ReportService
     */
    public function getChartData(array $data): ReportService
    {
        $start = $data['date_from'];
        $end = $data['date_to'];
        $interval = $data['interval'];
        $cascade = $data['cascade'];
        $reportService = $this->reportService->setup($start, $end, $interval);
        $reportService->randomizeColors($data['randomize_colors']);
        $reportService->setShadow(false);

        foreach ($data['models'] as $m) {
            $model = new $m['model'];
            $query = $model->where('created_at', '>=', $start)->where('created_at', '<=', $end);

            if (array_key_exists('conditions', $m)) {
                foreach ($m['conditions'] as $condition) {
                    switch ($condition[0]) {
                        case 'whereIn':
                            $query = $query->whereIn($condition[1], $condition[2]);
                            break;
                        default:
                            $query = $query->where($condition[1], $condition[2], $condition[3]);
                    }
                }
            }

            $data = $query->get();

            $reportService->setItems($data);
            if (!isset($m['attribute'])) {
                $reportService->makeReportDatasetByNumberOfItems($cascade)->addLabel($m['label'])->addDataset();
            } else {
                $reportService->makeReportDatasetByAttribute($m['attribute'], $cascade)->addLabel($m['label'])->addDataset();
            }
            $reportService->setShadow(true);
        }
        return $reportService;

    }
}
