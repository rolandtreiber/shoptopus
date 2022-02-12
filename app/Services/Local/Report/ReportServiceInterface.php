<?php

namespace App\Services\Local\Report;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

interface ReportServiceInterface {

    public function setup(Carbon $start, Carbon $end, $interval): ReportService;
    public function setPalette($palette = null): ReportService;
    public function getPalette(): array;
    public function randomizeColors($r): ReportService;
    public function setShadow($s): ReportService;
    public function getLabels(): array;
    public function clearLabels(): ReportService;
    public function getDates(): array;
    public function getCurrentDataSet(): array;
    public function getResponse(): array;
    public function setItems(Collection $items): ReportService;
    public function getCurrentItems();
    public function makeReportDatasetByAttribute($attr, bool $cascade = false): ReportService;
    public function addSingleLabel($label): ReportService;
    public function setLabels(array $labels): ReportService;
    public function makeSingleValueDatasetItem($label): ReportService;
    public function addDataToSingleDataset($value): ReportService;
    public function addDataset(array $dataset = null): ReportService;
    public function addLabel($label): ReportService;
    public function makeReportDatasetByNumberOfItems(bool $cascade = false): ReportService;

}
