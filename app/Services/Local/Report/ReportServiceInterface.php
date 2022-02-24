<?php

namespace App\Services\Local\Report;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

interface ReportServiceInterface {

    // Setup
    public function setup(Carbon $start, Carbon $end, $interval): ReportService;

    // Styles
    public function setPalette($palette = null): ReportService;
    public function getPalette(): array;
    public function randomizeColors($r): ReportService;
    public function setShadow($s): ReportService;

    // Labels
    public function getLabels(): array;
    public function setLabels(array $labels): ReportService;
    public function addSingleLabel($label): ReportService;
    public function clearLabels(): ReportService;
    public function addLabel($label): ReportService;

    // Data
    public function getCurrentDataSet(): array;
    public function getCurrentItems();
    public function setItems(Collection $items): ReportService;
    public function addDataToSingleDataset($value): ReportService;
    public function addDataset(array $dataset = null): ReportService;
    public function getDates(): array;

    // Data processing
    public function makeReportDatasetByAttribute($attr, bool $cascade = false): ReportService;
    public function makeSingleValueDatasetItem($label): ReportService;
    public function makeReportDatasetByNumberOfItems(bool $cascade = false): ReportService;

    // Miscellaneous
    public function getControlsFromType(int $type): array;

    // Responses
    public function getChartjsResponse(): array;
    public function getApexChartsPieResponse(): array;
    public function getApexBarChartsResponse(): array;

}
