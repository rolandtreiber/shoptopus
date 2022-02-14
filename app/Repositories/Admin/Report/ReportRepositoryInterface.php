<?php

namespace App\Repositories\Admin\Report;

interface ReportRepositoryInterface {

    public function getOverview(array $data);
    public function getChartData(array $data);
    public function getSales(array $data);

}
