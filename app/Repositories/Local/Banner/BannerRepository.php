<?php

namespace App\Repositories\Local\Banner;

use App\Models\Banner;
use App\Repositories\Local\ModelRepository;
use App\Services\Local\Error\ErrorServiceInterface;

class BannerRepository extends ModelRepository implements BannerRepositoryInterface
{
    public function __construct(ErrorServiceInterface $errorService, Banner $model)
    {
        parent::__construct($errorService, $model);
    }

    /**
     * Get the columns for selection
     */
    public function getSelectableColumns(bool $withTableNamePrefix = true): array
    {
        $columns = [
            "{$this->model_table}.id",
            "{$this->model_table}.title",
            "{$this->model_table}.description",
            "{$this->model_table}.background_image",
            "{$this->model_table}.show_button",
            "{$this->model_table}.button_text",
            "{$this->model_table}.button_url",
            "{$this->model_table}.slug"
        ];

        return $withTableNamePrefix
            ? $columns
            : array_map(function ($column_name) {
                return str_replace($this->model_table.'.', '', $column_name);
            }, $columns);
    }


}
