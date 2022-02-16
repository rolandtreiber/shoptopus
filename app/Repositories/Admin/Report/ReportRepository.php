<?php

namespace App\Repositories\Admin\Report;

use App\Enums\OrderStatuses;
use App\Enums\ProductStatuses;
use App\Http\Resources\Admin\ProductCategorySelectResource;
use App\Models\Order;
use App\Models\ProductCategory;
use App\Models\User;
use App\Services\Local\Report\ReportService;
use App\Services\Local\Report\ReportServiceInterface;
use Illuminate\Support\Facades\DB;

class ReportRepository implements ReportRepositoryInterface
{

    protected ReportServiceInterface $reportService;

    public function __construct(ReportServiceInterface $reportService)
    {
        $this->reportService = $reportService;
    }

    public function getSignupsOverTime(array $controls): array
    {
        $start = $controls['start'];
        $end = $controls['end'];

        $service = $this->getChartData([
            'date_from' => $start,
            'date_to' => $end,
            'interval' => $controls['interval'],
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

    public function getRevenueOverTime(array $controls): array
    {
        $service = $this->getChartData([
            'date_from' => $controls['start'],
            'date_to' => $controls['end'],
            'interval' => $controls['interval'],
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
    public function getProductBreakdown(array $controls, $categoryId = null): array
    {
        $topLevelCategories = ProductCategory::where('enabled', 1)->whereNull('parent_id')->select('id', 'name')->get();

        if (!$categoryId && $topLevelCategories !== null) {
            $categoryId = $topLevelCategories->first()->id;
        }

        $reportService = $this->reportService->setup();
        $products = DB::table('order_product')
            ->leftJoin('products as product', 'order_product.product_id', 'product.id')
            ->leftJoin('product_product_category as ppc', function($join) use ($categoryId) {
                $join->on('ppc.product_id', 'product.id');
                $join->where('ppc.product_category_id', $categoryId);
            })
            ->leftJoin('product_categories', 'ppc.product_category_id', 'product_categories.id')
            ->leftJoin('orders as order', 'order_product.order_id', 'order.id')
            ->whereNotNull('ppc.product_category_id')
            ->where('order.created_at', '>=', $controls['start'])
            ->where('order.created_at', '<=', $controls['end'])
            ->select(['ppc.product_category_id', 'product_categories.name as category_name', 'product.name', DB::raw('count(*) as total')])
            ->groupBy('product.name')
            ->get();

        $color = 0;
        $count = [];
        $bgColor = [];
        $labels = [];
        $palette = $reportService->getPalette();
        foreach ($products as $product) {
            $labels[] = $product['name'];
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

        $data = $reportService->getApexBarChartsResponse();

        return [
            'categories' => $topLevelCategories,
            'data' => $data
        ];
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
    public function getOrdersByStatusChartData(array $controls): array
    {
        $reportService = $this->reportService->setup();
        $query = DB::table('orders')
            ->select(['orders.status', DB::raw('count(*) as total')])
            ->where('orders.created_at', '>=', $controls['start'])
            ->where('orders.created_at', '<=', $controls['end'])
            ->groupBy('orders.status');

        $orders = $query->get();
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
    public function getTotalOverviewValues(): array
    {
        $unsold = DB::table('products')
            ->leftJoin('product_variants as pv', function($join) {
                $join->on('products.id', 'pv.product_id')
                    ->where('pv.enabled', 1);
            })->select([
                DB::raw('CASE WHEN SUM(pv.price) IS NOT NULL THEN (SUM(pv.price) * SUM(pv.stock)) ELSE (SUM(products.price) * SUM(products.stock)) END AS value'),
            ])->where('products.status', ProductStatuses::Active)->groupBy('products.id')->pluck('value')->sum();

        $ordersTotal = DB::table('orders')->whereIn('status', [
            OrderStatuses::Completed,
            OrderStatuses::Paid,
            OrderStatuses::InTransit])
            ->select([
                DB::raw('SUM(orders.total_price) as revenue'),
                DB::raw('SUM(orders.delivery) as delivery'),
                DB::raw('SUM(orders.total_discount) as discount'),
            ])->first();

        return [
            'unrealized_revenue' => $unsold,
            'total_revenue' => $ordersTotal['revenue'],
            'total_delivery' => $ordersTotal['delivery'],
            'total_discount' => $ordersTotal['discount'],
        ];
    }

    /**
     * @param array $data
     * @return array
     */
    public function getOverview(array $data): array
    {
        $ordersByStatusChartData = $this->getOrdersByStatusChartData($this->reportService->getControlsFromType((int) $data['orders_overview_chart_range']));
        $productsByStatusChartData = $this->getProductByStatusChartData();
        $signupsOverTime = $this->getSignupsOverTime($this->reportService->getControlsFromType((int) $data['signups_chart_range']));

        return [
            'products_by_status_pie_chart_data' => $productsByStatusChartData,
            'orders_by_status_pie_chart_data' => $ordersByStatusChartData,
            'user_signups_over_time' => $signupsOverTime,
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

    /**
     * @param array $data
     * @return array[]
     */
    public function getSales(array $data): array
    {
        $revenueOverTime = $this->getRevenueOverTime($this->reportService->getControlsFromType((int) $data['revenue_over_time_range']));
        $productsBreakdown = $this->getProductBreakdown(
            $this->reportService->getControlsFromType((int) $data['products_breakdown_time_range']),
            array_key_exists('category_id', $data) ? $data['category_id'] : null
        );
        $totalOverviewValues = $this->getTotalOverviewValues();

        return [
            'revenue_over_time' => $revenueOverTime,
            'products_breakdown' => $productsBreakdown,
            'totals' => $totalOverviewValues
        ];
    }
}
