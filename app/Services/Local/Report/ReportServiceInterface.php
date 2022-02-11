<?php

namespace App\Services\Local\Report;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

interface ReportServiceInterface {

    public function setup(Carbon $start, Carbon $end, $interval);
    public function getResponse(): array;
    public function setItems(Collection $items): ReportService;
    public function getCurrentItems();
    public function makeReportDatasetByAttribute($attr, bool $cascade = false): ReportService;
    public function addSingleLabel($label): ReportService;
    public function makeSingleValueDatasetItem($label): ReportService;
    public function addDataToSingleDataset($value): ReportService;
    public function addDataset(): ReportService;
    public function addLabel($label): ReportService;

}
