<?php

namespace App\Repositories\Admin\Report;

interface ReportRepositoryInterface {

    public function getOverview();
    public function getChartData(array $data);

}
