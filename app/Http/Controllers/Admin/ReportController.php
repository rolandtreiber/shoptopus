<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReportRequest;
use App\Http\Resources\Admin\ReportOverviewResource;
use App\Repositories\Admin\Report\ReportRepositoryInterface;

class ReportController extends Controller
{
    protected ReportRepositoryInterface $reportRepository;

    public function __construct(ReportRepositoryInterface $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }

    /**
     * @return ReportOverviewResource
     */
    public function getOverview()//: ReportOverviewResource
    {
        return $this->reportRepository->getOverview();
        return new ReportOverviewResource($this->reportRepository->getOverview());
    }

    public function getChart(ReportRequest $request)
    {
        return $this->reportRepository->getChart($request->toArray());
    }

}
