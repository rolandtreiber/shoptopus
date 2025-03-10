<?php

namespace App\Services\Local\Report;

use App\Enums\ChartRange;
use App\Enums\Interval;
use App\Enums\Palette;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ReportService implements ReportServiceInterface
{
    protected array $labels = [];

    protected array $datasets = [];

    protected array $dataset = [];

    protected $items;

    protected $response;

    private $randomColors = false;

    private $palette;

    private $count = 0;

    private $start;

    private $end;

    private $interval;

    private array $dates = [];

    private $shadow = false;

    /**
     * ReportHelper constructor.
     * Sets the palette
     * @param Carbon|null $start
     * @param Carbon|null $end
     * @param integer|null $interval
     * @param array|null $palette
     */
    public function setup(Carbon $start = null, Carbon $end = null, $interval = null, array $palette = null): ReportService
    {
        $this->datasets = [];
        if ($start && $end && $interval !== null) {
            $end->setHours(23)->setMinutes(59)->setSeconds(59);
            $this->start = $start;
            $this->end = $end;
            $this->interval = $interval;
            $this->setLabelsAndDates();
        }
        $this->setPalette($palette);

        return $this;
    }

    public function setLabels(array $labels): ReportService
    {
        $this->labels = $labels;

        return $this;
    }

    public function getPalette(): array
    {
        return $this->palette;
    }

    /**
     * @param array $palette
     */
    public function setPalette($palette = null): ReportService
    {
        if ($palette) {
            $this->palette = $palette;
        } else {
            $this->palette = [
                'rgba(49, 129, 237, 1)',
                'rgba(19, 93, 190, 1)',
                'rgba(6, 70, 153, 1)',
                'rgba(71, 148, 235, 1)',
                'rgba(27, 88, 158, 1)',
                'rgba(59, 129, 209, 1)',
                'rgba(90, 155, 230, 1)',
                'rgba(44, 96, 156, 1)',
                'rgba(41, 133, 240, 1)',
                Palette::Red,
                Palette::Purple,
                Palette::DarkBlue,
                Palette::Aquamarine,
                Palette::Turquoise,
                Palette::DarkGreen,
                Palette::Yellow,
                Palette::Orange,
                Palette::LightGray,
                Palette::DarkGray,
            ];
        }

        return $this;
    }

    /**
     * @return array
     * Creates the response array that corresponds to the expected format of the chart vue component.
     */
    public function getChartjsResponse(): array
    {
        return [
            'labels' => $this->labels,
            'datasets' => $this->datasets,
        ];
    }

    public function getApexChartsPieResponse(): array
    {
        $series = [];
        if ($this->datasets) {
            $series = $this->datasets[0]['data'];
        }

        return [
            'series' => $series,
            'chartOptions' => [
                'labels' => $this->labels,
            ],
        ];
    }

    public function getApexChartsResponse(): array
    {
        $series = [];
        foreach ($this->datasets as $dataset) {
            $series[] = [
                'name' => $dataset['label'],
                'data' => $dataset['data'],
            ];
        }

        return [
            'series' => $series,
            'categories' => $this->labels,
        ];
    }

    public function getApexBarChartsResponse(): array
    {
        $series = [];
        $palette = $this->palette;
        $i = 0;
        foreach ($this->datasets as $dataset) {
            foreach ($dataset['data'] as $data) {
                $series[] = [
                    'name' => 'Total',
                    'data' => $data,
                    'color' => $palette[$i],
                ];
                $i++;
                if ($i = count($palette)) {
                    $i = 0;
                }
            }
        }

        return [
            'series' => $series,
            'categories' => $this->labels,
        ];
    }

    public function getApexCompositePieResponse(): array
    {
        $dataset = [];
        if ($this->datasets) {
            $dataset = $this->datasets[0];
        }

        $series = [];
        foreach ($dataset['data'] as $index => $data) {
            $series[] = [
                'data' => $data,
                'name' => $dataset['labels'][$index],
                'color' => $dataset['backgroundColor'][$index],
            ];
        }

        return $series;
    }

    public function getDatasets(): array
    {
        return $this->datasets;
    }

    public function randomizeColors($r): ReportService
    {
        $this->randomColors = $r;

        return $this;
    }

    public function setShadow($s): ReportService
    {
        $this->shadow = $s;

        return $this;
    }

    /**
     * @return void
     * Extracts the labels from the selected timespan.
     */
    private function setLabelsAndDates(): void
    {
        $labels = [];
        $dates = [];
        $startDate = $this->start->copy();
        $endDate = $this->end->copy();
        $rollingDate = $startDate->copy();
        switch ($this->interval) {
            case Interval::Day:
                do {
                    $labels[] = $rollingDate->format('m-d');
                    $date = [
                        'start' => Carbon::parse($rollingDate->format('Y-m-d').' 00:00:00'),
                        'end' => Carbon::parse($rollingDate->format('Y-m-d').' 23:59:59'),
                    ];
                    $dates[] = $date;
                    $rollingDate->addDay();
                } while ($rollingDate <= $endDate);
                break;
            case Interval::Week:
                $startDate = $startDate->startOfWeek();
                $endDate = $endDate->endOfWeek();
                $rollingDate = $startDate->copy();
                do {
                    $labels[] = $rollingDate->format('Y').' week '.$rollingDate->weekOfYear;
                    $date = [
                        'start' => Carbon::parse($rollingDate->copy()->startOfWeek()->format('Y-m-d').' 00:00:00'),
                        'end' => Carbon::parse($rollingDate->copy()->endOfWeek()->format('Y-m-d').' 23:59:59'),
                    ];
                    $dates[] = $date;
                    $rollingDate->addWeek();
                } while ($rollingDate <= $endDate);
                break;
            case Interval::Month:
                do {
                    $labels[] = $rollingDate->format('Y-m');
                    $date = [
                        'start' => Carbon::parse($rollingDate->format('Y-m').'-1 00:00:00'),
                        'end' => Carbon::parse($rollingDate->format('Y-m').'-'.$rollingDate->copy()->lastOfMonth()->format('d').' 23:59:59'),
                    ];
                    $dates[] = $date;
                    $rollingDate->addMonth();
                } while ($rollingDate <= $endDate);
                break;
            case Interval::Year:
                do {
                    $labels[] = $rollingDate->format('Y');
                    $date = [
                        'start' => Carbon::parse($rollingDate->format('Y').'-1-1 00:00:00'),
                        'end' => Carbon::parse($rollingDate->format('Y').'-12-31 23:59:59'),
                    ];
                    $dates[] = $date;
                    $rollingDate->addYear();
                } while ($rollingDate <= $endDate);
                break;
        }
        $this->labels = $labels;
        $this->dates = $dates;
    }

    /**
     * @return array
     * Returns the current labels
     */
    public function getLabels(): array
    {
        return $this->labels;
    }

    /**
     * @return $this
     * Clears out the labels
     */
    public function clearLabels(): ReportService
    {
        $this->labels = [];

        return $this;
    }

    /**
     * @return array
     * Returns the current labels
     */
    public function getDates(): array
    {
        return $this->dates;
    }

    /**
     * @param  Collection  $items
     * Sets the items to be used for constructing a dataset.
     */
    public function setItems(Collection $items): ReportService
    {
        $this->items = $items;

        return $this;
    }

    /**
     * Returns the current set of items
     */
    public function getCurrentItems()
    {
        return $this->items;
    }

    /**
     * Returns the current set of items
     */
    public function getCurrentDataSet(): array
    {
        return $this->dataset;
    }

    /**
     * @return $this
     * Creates a report dataset based on an attribute.
     */
    public function makeReportDatasetByAttribute($attr, bool $cascade = false): ReportService
    {
        $dataset = [
            'label' => '',
            'borderColor' => Palette::Transparent,
        ];
        $bgColors = [];
        $rollingValue = 0;
        $data = [];
        $color = $this->shadow === true ? 'rgba(0,0,0,0.3)' : $this->palette[0];
        foreach ($this->dates as $date) {
            $value = $this->items->where('created_at', '>=', $date['start'])->where('created_at', '<=', $date['end'])->sum($attr);
            $bgColors[] = $this->randomColors === 'true' && ! $this->shadow ? 'rgba('.rand(0, 255).', '.rand(0, 255).', '.rand(0, 255).', 0.6)' : $color;
            $data[] = $rollingValue + $value;
            if ($cascade) {
                $rollingValue += $value;
            }
        }
        $dataset['data'] = $data;
        $dataset['backgroundColor'] = $bgColors;
        $this->dataset = $dataset;

        return $this;
    }

    public function addSingleLabel($label): ReportService
    {
        $this->labels[] = $label;

        return $this;
    }

    public function makeSingleValueDatasetItem($label): ReportService
    {
        $dataset = [
            'label' => $label,
            'backgroundColor' => [],
            'data' => [],
        ];
        $this->dataset = $dataset;

        return $this;
    }

    public function addDataToSingleDataset($value): ReportService
    {
        $color = $this->shadow === true ? 'rgba(0,0,0,0.3)' : $this->palette[0];
        $this->dataset['backgroundColor'][] = $this->randomColors === 'true' && ! $this->shadow ? 'rgba('.rand(0, 255).', '.rand(0, 255).', '.rand(0, 255).', 0.6)' : $color;
        $this->dataset['data'][] = $value;
        $this->count++;

        return $this;
    }

    /**
     * @return $this
     * Creates a report dataset based on the number of items.
     */
    public function makeReportDatasetByNumberOfItems(bool $cascade = false): ReportService
    {
        $dataset = [
            'label' => '',
            'borderColor' => Palette::Transparent,
        ];
        $bgColors = [];

        $rollingValue = 0;
        $data = [];
        $color = $this->shadow === true ? 'rgba(0,0,0,0.3)' : $this->palette[0];

        $start = Carbon::parse($this->dates[0]['start']);
        $end = Carbon::parse($this->dates[count($this->dates) - 1]['end']);
        $lengthInDays = $end->diffInDays($start);
        foreach ($this->dates as $date) {
            $shadowStart = Carbon::parse($date['start'])->subDays($lengthInDays + 1)->format('Y-m-d H:i:s');
            $shadowEnd = Carbon::parse($date['end'])->subDays($lengthInDays + 1)->format('Y-m-d H:i:s');

            if (! $this->shadow) {
                $value = $this->items->where('created_at', '>=', $date['start'])->where('created_at', '<=', $date['end'])->count();
            } else {
                $value = $this->items->where('created_at', '>=', $shadowStart)->where('created_at', '<=', $shadowEnd)->count();
            }
            $data[] = $rollingValue + $value;
            $bgColors[] = $this->randomColors === 'true' && ! $this->shadow ? 'rgba('.rand(0, 255).', '.rand(0, 255).', '.rand(0, 255).', 0.6)' : $color;
            if ($cascade) {
                $rollingValue += $value;
            }
        }
        $dataset['data'] = $data;
        $dataset['backgroundColor'] = $bgColors;
        $this->dataset = $dataset;

        return $this;
    }

    /**
     * Appends the dataset to the $datasets array, then empties the array.
     */
    public function addDataset(array $dataset = null): ReportService
    {
        if ($dataset) {
            $this->datasets[] = $dataset;
        } else {
            $this->datasets[] = $this->dataset;
        }
        $this->count++;
        $this->dataset = [];

        return $this;
    }

    /**
     * @return $this
     * Sets the label for the current dataset.
     */
    public function addLabel($label): ReportService
    {
        $this->dataset['label'] = $label;

        return $this;
    }

    public function getControlsFromType(int $type = null): array
    {
        switch ($type) {
            case ChartRange::LastWeek:
                return [
                    'start' => Carbon::now()->subWeek()->startOfDay(),
                    'end' => Carbon::now()->endOfDay(),
                    'interval' => Interval::Day,
                ];
            case ChartRange::LastYear:
                return [
                    'start' => Carbon::now()->subYear()->startOfDay(),
                    'end' => Carbon::now()->endOfDay(),
                    'interval' => Interval::Month,
                ];
            default:
                return [
                    'start' => Carbon::now()->subMonth()->startOfDay(),
                    'end' => Carbon::now()->endOfDay(),
                    'interval' => Interval::Day,
                ];
        }
    }
}
