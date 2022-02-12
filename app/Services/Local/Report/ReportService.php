<?php

namespace App\Services\Local\Report;

use App\Enums\Intervals;
use App\Enums\Palette;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ReportService implements ReportServiceInterface {

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
     */
    public function setup(Carbon $start = null, Carbon $end = null, $interval = null, $palette = null): ReportService
    {
        $this->datasets = [];
        if ($start && $end && $interval) {
            $end->setHours(23)->setMinutes(59)->setSeconds(59);
            $this->start = $start;
            $this->end = $end;
            $this->interval = $interval;
            $this->setLabelsAndDates();
        }
        $this->setPalette($palette);
        return $this;
    }

    /**
     * @param array $labels
     * @return ReportService
     */
    public function setLabels(array $labels): ReportService
    {
        $this->labels = $labels;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPalette(): array
    {
        return $this->palette;
    }

    /**
     * @param mixed $palette
     */
    public function setPalette($palette = null): ReportService
    {
        if ($palette) {
            $this->palette = $palette;
        } else {
            $this->palette = [
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
    public function getResponse(): array
    {
        return [
            'labels' => $this->labels,
            'datasets' => $this->datasets
        ];
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
            case Intervals::Day:
                do {
                    $labels[] = $rollingDate->format('m-d');
                    $date = [
                        'start' => Carbon::parse($rollingDate->format('Y-m-d')." 00:00:00"),
                        'end' => Carbon::parse($rollingDate->format('Y-m-d')." 23:59:59"),
                    ];
                    $dates[] = $date;
                    $rollingDate->addDay();
                } while ($rollingDate <= $endDate);
                break;
            case Intervals::Week:
                $startDate = $startDate->startOfWeek();
                $endDate = $endDate->endOfWeek();
                $rollingDate = $startDate->copy();
                do {
                    $labels[] = $rollingDate->format('Y').' week '.$rollingDate->weekOfYear;
                    $date = [
                        'start' => Carbon::parse($rollingDate->copy()->startOfWeek()->format('Y-m-d')." 00:00:00"),
                        'end' => Carbon::parse($rollingDate->copy()->endOfWeek()->format('Y-m-d')." 23:59:59"),
                    ];
                    $dates[] = $date;
                    $rollingDate->addWeek();
                } while ($rollingDate <= $endDate);
                break;
            case Intervals::Month:
                do {
                    $labels[] = $rollingDate->format('Y-m');
                    $date = [
                        'start' => Carbon::parse($rollingDate->format('Y-m')."-1 00:00:00"),
                        'end' => Carbon::parse($rollingDate->format('Y-m').'-'.$rollingDate->copy()->lastOfMonth()->format('d')." 23:59:59"),
                    ];
                    $dates[] = $date;
                    $rollingDate->addMonth();
                } while ($rollingDate <= $endDate);
                break;
            case Intervals::Year:
                do {
                    $labels[] = $rollingDate->format('Y');
                    $date = [
                        'start' => Carbon::parse($rollingDate->format('Y')."-1-1 00:00:00"),
                        'end' => Carbon::parse($rollingDate->format('Y')."-12-31 23:59:59"),
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
     * @param Collection $items
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
     * @param $attr
     * @param bool $cascade
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
        $color = $this->shadow === true ? "rgba(0,0,0,0.3)" : $this->palette[0];
        foreach ($this->dates as $date) {
            $value = $this->items->where('created_at', '>=', $date['start'])->where('created_at', '<=', $date['end'])->sum($attr);
            $bgColors[] = $this->randomColors === 'true' && !$this->shadow ? 'rgba('.rand(0, 255).', '.rand(0, 255).', '.rand(0, 255).', 0.6)' : $color;
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

    /**
     * @param $label
     * @return $this
     */
    public function addSingleLabel($label): ReportService
    {
        $this->labels[] = $label;
        return $this;
    }

    /**
     * @param $label
     * @return $this
     */
    public function makeSingleValueDatasetItem($label): ReportService
    {
        $dataset = [
            'label' => $label,
            'backgroundColor' => [],
            'data' => []
        ];
        $this->dataset = $dataset;
        return $this;
    }

    /**
     * @param $value
     * @return $this
     */

    public function addDataToSingleDataset($value): ReportService
    {
        $color = $this->shadow === true ? "rgba(0,0,0,0.3)" : $this->palette[0];
        $this->dataset['backgroundColor'][] = $this->randomColors === 'true' && !$this->shadow ? 'rgba('.rand(0, 255).', '.rand(0, 255).', '.rand(0, 255).', 0.6)' : $color;
        $this->dataset['data'][] = $value;
        $this->count++;
        return $this;
    }

    /**
     * @param bool $cascade
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
        $color = $this->shadow === true ? "rgba(0,0,0,0.3)" : $this->palette[0];

        $start = Carbon::parse($this->dates[0]['start']);
        $end = Carbon::parse($this->dates[sizeof($this->dates)-1]['end']);
        $lengthInDays = $end->diffInDays($start);
        foreach ($this->dates as $date) {
            $shadowStart = Carbon::parse($date['start'])->subDays($lengthInDays+1)->format('Y-m-d H:i:s');
            $shadowEnd = Carbon::parse($date['end'])->subDays($lengthInDays+1)->format('Y-m-d H:i:s');

            if (!$this->shadow) {
                $value = $this->items->where('created_at', '>=', $date['start'])->where('created_at', '<=', $date['end'])->count();
            } else {
                $value = $this->items->where('created_at', '>=', $shadowStart)->where('created_at', '<=', $shadowEnd)->count();
            }
            $data[] = $rollingValue + $value;
            $bgColors[] = $this->randomColors === 'true' && !$this->shadow ? 'rgba('.rand(0, 255).', '.rand(0, 255).', '.rand(0, 255).', 0.6)' : $color;
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
     * @return $this
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
     * @param $label
     * @return $this
     * Sets the label for the current dataset.
     */
    public function addLabel($label): ReportService
    {
        $this->dataset['label'] = $label;
        return $this;
    }

}
