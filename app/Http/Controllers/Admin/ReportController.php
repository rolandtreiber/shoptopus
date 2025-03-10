<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReportOverviewRequest;
use App\Http\Requests\Admin\ReportProductRequest;
use App\Http\Requests\Admin\ReportRequest;
use App\Http\Requests\Admin\ReportSalesRequest;
use App\Http\Resources\Admin\ReportOverviewResource;
use App\Http\Resources\Admin\ReportSalesResource;
use App\Models\Product;
use App\Repositories\Admin\Report\ReportRepositoryInterface;

class ReportController extends Controller
{
    protected ReportRepositoryInterface $reportRepository;

    public function __construct(ReportRepositoryInterface $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }

    public function getOverview(ReportOverviewRequest $request): ReportOverviewResource
    {
        return new ReportOverviewResource($this->reportRepository->getOverview($request->toArray()));
    }

    public function getSales(ReportSalesRequest $request): ReportSalesResource
    {
        return new ReportSalesResource($this->reportRepository->getSales($request->toArray()));
    }

    public function getChart(ReportRequest $request)
    {
        return $this->reportRepository->getChartData($request->toArray())->getApexBarChartsResponse();
    }

    public function getProductRatings(Product $product)
    {
        return $this->reportRepository->getProductRatings($product);
    }
}
