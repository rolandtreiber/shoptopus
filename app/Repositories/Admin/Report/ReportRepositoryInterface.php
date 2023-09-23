<?php

namespace App\Repositories\Admin\Report;

use App\Models\Product;

interface ReportRepositoryInterface
{
    public function getOverview(array $data);

    public function getChartData(array $data);

    public function getSales(array $data);

    public function getProductRatings(Product $product);

    public function getOverallSatisfactionByRatable($ratableType, $ratableId): array;

    public function getProductSalesTimeline(Product $product, array $controls): array;

}
