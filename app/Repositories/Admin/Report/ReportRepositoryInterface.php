<?php

namespace App\Repositories\Admin\Report;

interface ReportRepositoryInterface {

    public function getOverview();
    public function getChart(array $data);

}
