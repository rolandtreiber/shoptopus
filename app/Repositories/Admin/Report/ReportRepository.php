<?php

namespace App\Repositories\Admin\Report;

use App\Enums\OrderStatus;
use App\Enums\ProductStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Rating;
use App\Models\User;
use App\Services\Local\Report\ReportService;
use App\Services\Local\Report\ReportServiceInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use JetBrains\PhpStorm\NoReturn;

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
                ],
            ],
            'cascade' => false,
            'randomize_colors' => false,
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
                        ['where', 'status', '=', OrderStatus::Completed],
                    ],
                ],
                [
                    'label' => 'Paid Orders (Paid, Processing, In Transit)',
                    'model' => Order::class,
                    'attribute' => 'total_price',
                    'conditions' => [
                        ['whereIn', 'status', [
                            OrderStatus::Paid,
                            OrderStatus::Processing,
                            OrderStatus::InTransit,
                        ]],
                    ],
                ],
            ],
            'cascade' => true,
            'randomize_colors' => false,
        ]);

        return $service->getApexChartsResponse();
    }

    /**
     * @param string|null $categoryId
     */
    public function getProductBreakdown(array $controls, string $categoryId = null): array
    {
        $topLevelCategories = ProductCategory::where('enabled', 1)->whereNull('parent_id')->select('id', 'name')->get();

        if (! $categoryId && $topLevelCategories !== null) {
            $categoryId = $topLevelCategories->first()->id;
        }

        $reportService = $this->reportService->setup();
        $products = DB::table('order_product')
            ->leftJoin('products as product', 'order_product.product_id', 'product.id')
            ->leftJoin('product_product_category as ppc', function ($join) use ($categoryId) {
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
            'label' => 'Products',
            'borderColor' => 'transparent',
            'backgroundColor' => $bgColor,
            'data' => $count,
            'labels' => $labels,
        ])->setLabels($labels);

        $data = $reportService->getApexBarChartsResponse();

        return [
            'categories' => $topLevelCategories,
            'data' => $data,
        ];
    }

    public function getProductByStatusChartData(): array
    {
        $reportService = $this->reportService->setup();
        $products = DB::table('products')->select(['products.status', DB::raw('count(*) as total')])->groupBy('products.status')->get();
        $color = 0;
        $count = [];
        $bgColor = [];
        $labels = [];
        $statuses = [
            1 => 'Provisional',
            2 => 'Active',
            3 => 'Discontinued',
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
            'label' => 'Products',
            'borderColor' => 'transparent',
            'backgroundColor' => $bgColor,
            'data' => $count,
            'labels' => $labels,
        ])->setLabels($labels);

        return $reportService->getApexCompositePieResponse();
    }

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
            1 => 'Awaiting Payment',
            2 => 'Paid',
            3 => 'Processing',
            4 => 'In Transit',
            5 => 'Completed',
            6 => 'On Hold',
            7 => 'Cancelled'
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
            'label' => 'Orders',
            'borderColor' => 'transparent',
            'backgroundColor' => $bgColor,
            'data' => $count,
            'labels' => $labels,
        ]);

        return $reportService->getApexCompositePieResponse();
    }

    public function getTotalOverviewValues(): array
    {
        // @phpstan-ignore-next-line
        $unsold = DB::table('products')
            ->leftJoin('product_variants as pv', function ($join) {
                $join->on('products.id', 'pv.product_id')
                    ->where('pv.enabled', 1);
            })->select([
                DB::raw('CASE WHEN SUM(pv.price) IS NOT NULL THEN (SUM(pv.price) * SUM(pv.stock)) ELSE (SUM(products.price) * SUM(products.stock)) END AS value'),
            ])->where('products.status', ProductStatus::Active)->groupBy('products.id')->get()->sum('value');

        $ordersTotal = DB::table('orders')->whereIn('status', [
            OrderStatus::Completed,
            OrderStatus::Paid,
            OrderStatus::InTransit, ])
            ->select([
                DB::raw('SUM(orders.total_price) as revenue'),
                DB::raw('SUM(orders.delivery_cost) as delivery'),
                DB::raw('SUM(orders.total_discount) as discount'),
            ])->first();

        return [
            'unrealized_revenue' => $unsold,
            'total_revenue' => $ordersTotal['revenue'],
            'total_delivery' => $ordersTotal['delivery'],
            'total_discount' => $ordersTotal['discount'],
        ];
    }

    private function getSalesStatsRow($query): string
    {
        return $query->sum('total_price').' ('.$query->count().')';
    }

    /**
     * @return string[]
     */
    public function getSalesStats(array $controls): array
    {
        $start = $controls['start'];
        $end = $controls['end'];

        $pendingOrdersQuery = Order::view('paid')->where('created_at', '>=', $start)->where('created_at', '<=', $end);
        $completedOrdersQuery = Order::view('completed')->where('created_at', '>=', $start)->where('created_at', '<=', $end);
        $processingOrdersQuery = Order::view('processing')->where('created_at', '>=', $start)->where('created_at', '<=', $end);
        $inTransitOrdersQuery = Order::view('in_transit')->where('created_at', '>=', $start)->where('created_at', '<=', $end);
        $cancelledOrdersQuery = Order::view('cancelled')->where('created_at', '>=', $start)->where('created_at', '<=', $end);

        return [
            'pending_orders' => $this->getSalesStatsRow($pendingOrdersQuery),
            'completed_orders' => $this->getSalesStatsRow($completedOrdersQuery),
            'processing_orders' => $this->getSalesStatsRow($processingOrdersQuery),
            'in_transit_orders' => $this->getSalesStatsRow($inTransitOrdersQuery),
            'cancelled_orders' => $this->getSalesStatsRow($cancelledOrdersQuery),
        ];
    }

    public function getOverallSatisfactionByRatable($ratableType, $ratableId): array
    {
        $reportService = $this->reportService->setup();
        $ratings = Rating::where([
            'ratable_type' => $ratableType,
            'ratable_id' => $ratableId
            ])->groupBy('rating')->select(DB::raw("COUNT(ratings.rating) as count, rating"))->get()->toArray();

        $color = 0;
        $count = [];
        $bgColor = [];
        $labels = [];
        $stars = [
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5,
        ];
        $palette = $reportService->getPalette();

        foreach ($stars as $star) {
            $labels[] = $star;
            $total = 0;
            foreach ($ratings as $r) {
                if ($r['rating'] === $star) {
                    $total = $r['count'];
                }
            }
            $count[] = $total;
            $bgColor[] = $palette[$color];
            $color++;
            if ($color == 9) {
                $color = 0;
            }
        }

        $reportService->addDataset([
            'label' => 'Ratings',
            'borderColor' => 'transparent',
            'backgroundColor' => $bgColor,
            'data' => $count,
            'labels' => $labels,
        ])->setLabels($labels);

        return $reportService->getApexCompositePieResponse();
    }

    public function getOverviewStats(): array
    {
        $orders = Order::count();

        $products = Product::count();
        $payments = Payment::count();
        $customers = User::customers()->count();

        return [
            'orders' => $orders,
            'products' => $products,
            'payments' => $payments,
            'customers' => $customers,
        ];
    }

    public function getOverview(array $data): array
    {
        $ordersByStatusChartData = $this->getOrdersByStatusChartData($this->reportService->getControlsFromType((int) $data['orders_overview_chart_range']));
        $productsByStatusChartData = $this->getProductByStatusChartData();
        $signupsOverTime = $this->getSignupsOverTime($this->reportService->getControlsFromType((int) $data['signups_chart_range']));

        return [
            'stats' => $this->getOverviewStats(),
            'products_by_status_pie_chart_data' => $productsByStatusChartData,
            'orders_by_status_pie_chart_data' => $ordersByStatusChartData,
            'user_signups_over_time' => $signupsOverTime,
            'pending_orders' => Order::view('paid')->count(),
            'new_signups' => User::whereDate('created_at', '>=', Carbon::now()->endOfDay()->subDays(3))->count(),
            'low_stock' => Product::where('stock', '<=', 10)->count(),
            'todays_orders' => Order::whereDate('created_at', '>=', Carbon::today()->startOfDay())->count(),
        ];
    }

    public function getChartData(array $data): ReportService
    {
        $start = Carbon::parse($data['date_from']);
        $end = Carbon::parse($data['date_to']);
        $interval = $data['interval'];
        if (array_key_exists('cascade', $data)) {
            $cascade = $data['cascade'];
        } else {
            $cascade = false;
        }
        $reportService = $this->reportService->setup($start, $end, $interval);
        $reportService->randomizeColors($data['randomize_colors']);
        $reportService->setShadow(false);

        foreach ($data['models'] as $m) {
            $model = new $m['model'];
            $query = $model->where('created_at', '>=', $start)->where('created_at', '<=', $end);
            if (array_key_exists('with', $m)) {
                foreach ($m['with'] as $w) {
                    $query = $query->with($w);
                }
            }

            if (array_key_exists('conditions', $m)) {
                foreach ($m['conditions'] as $condition) {
                    switch ($condition[0]) {
                        case 'whereIn':
                            $query = $query->whereIn($condition[1], $condition[2]);
                            break;
                        case 'whereHas':
                            $query = $query->whereHas($condition[1], $condition[2]);
                            break;
                        default:
                            $query = $query->where($condition[1], $condition[2], $condition[3]);
                    }
                }
            }

            $data = $query->get();
            $reportService->setItems($data);
            if (! isset($m['attribute'])) {
                $reportService->makeReportDatasetByNumberOfItems($cascade)->addLabel($m['label'])->addDataset();
            } else {
                $reportService->makeReportDatasetByAttribute($m['attribute'], $cascade)->addLabel($m['label'])->addDataset();
            }
            $reportService->setShadow(true);
        }

        return $reportService;
    }

    /**
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
        $stats = $this->getSalesStats($this->reportService->getControlsFromType((int) $data['revenue_over_time_range']));

        return [
            'stats' => $stats,
            'revenue_over_time' => $revenueOverTime,
            'products_breakdown' => $productsBreakdown,
            'totals' => $totalOverviewValues,
        ];
    }

    /**
     * @param Product $product
     * @return array
     */
    public function getProductRatings(Product $product): array
    {
        $reportService = $this->reportService->setup();
        $ratings = DB::table('ratings')->where([
            'ratable_type' => Product::class,
            'ratable_id' => $product->id
        ])
            ->select(['ratings.rating', DB::raw('count(*) as total')])
            ->groupBy('ratings.rating')->get();
        $color = 0;
        $count = [];
        $bgColor = [];
        $labels = [];
        $stars = [
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5,
        ];
        $palette = $reportService->getPalette();
        foreach ($stars as $star) {
            $labels[] = $star;
            $total = 0;
            foreach ($ratings as $r) {
                if ($r['rating'] === $star) {
                    $total+=$r['total'];
                }
            }
            $count[] = $total;
            $bgColor[] = $palette[$color];
            $color++;
            if ($color == 9) {
                $color = 0;
            }
        }

        $reportService->addDataset([
            'label' => 'Ratings',
            'borderColor' => 'transparent',
            'backgroundColor' => $bgColor,
            'data' => $count,
            'labels' => $labels,
        ])->setLabels($labels);

        return $reportService->getApexCompositePieResponse();
    }

    public function getProductSalesTimeline(Product $product, array $controls): array
    {
        $start = Carbon::parse($controls['start']);
        $end = Carbon::parse($controls['end']);
        $interval = $controls['interval'];
        $reportService = $this->reportService->setup($start, $end, $interval);
        $reportService->randomizeColors(false);
        $reportService->setShadow(false);

        $completedOrders = Order::where('status', OrderStatus::Completed)->with('products')->whereHas('products', function (Builder $query) use($product) {
            $query->where('products.id', '=', $product->id);
        })->get();
        foreach ($completedOrders as $order) {
            // @phpstan-ignore-next-line
            $order->product_count = $order->products[0]->pivot->amount;
        }
        $reportService->setShadow(true);
        $reportService->setItems($completedOrders);
        $reportService->makeReportDatasetByAttribute('product_count')->addLabel('Completed Orders')->addDataset();

        $otherStatusOrders = Order::whereIn('status', [
            OrderStatus::Paid,
            OrderStatus::Processing,
            OrderStatus::InTransit,
        ])->with('products')->whereHas('products', function (Builder $query) use($product) {
            $query->where('products.id', '=', $product->id);
        })->get();
        foreach ($otherStatusOrders as $order) {
            // @phpstan-ignore-next-line
            $order->product_count = $order->products[0]->pivot->amount;
        }
        $reportService->setShadow(true);
        $reportService->setItems($otherStatusOrders);
        $reportService->makeReportDatasetByAttribute('product_count')->addLabel('Paid Orders (Paid, Processing, In Transit)')->addDataset();

        return $reportService->getApexChartsResponse();
    }
}
