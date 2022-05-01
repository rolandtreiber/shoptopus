<?php

namespace App\Traits;

trait FilterTrait
{
    /**
     * getFilters
     * @param array $filters
     * @param null $tableName
     * @return object
     */
    public function getFilters(array $filters = [], $tableName = null) : object
    {
        $filter_string = "";
        $query_parameters = [];
        $searchableColumns = !is_null($tableName) ? $this->getSearchableColumnsInTable($tableName) : null;

        if ($filters) {
            $count = 0;

            foreach ($filters as $filter_column => $filter_value) {
                $isSearchQuery = $filter_column === 'search';
                $isCustomQuery = is_array($filter_value) && $filter_column !== 'relation';
                $isRelationalQuery = is_array($filter_value) && $filter_column === 'relation';
                $tablePrefix = is_null($tableName) ? null : rtrim($tableName, '.') . '.';

                if ($isRelationalQuery) {
                    $type = $filters['relation']['type'];
                    $table = $filters['relation']['table'];
                    $local_pivot_key = $filters['relation']['local_pivot_key'];
                    $foreign_pivot_key = $filters['relation']['foreign_pivot_key'];
                    $foreign_pivot_value = $filters['relation']['foreign_pivot_value'];

                    if ($type === 'belongsToMany') {
                        $filter_string .= " JOIN {$table} ON $table.$local_pivot_key = $tablePrefix" . "id";
                        $filter_string .= " WHERE $table.$foreign_pivot_key = ?";
                    }

                    array_unshift($query_parameters, $foreign_pivot_value);
                } else {
                    if ($isCustomQuery) {
                        $filter_value = $filter_value['value'];
                    }

                    if (is_array($filter_value)) {
                        $filter_value = implode(",", $filter_value);
                    } else if (strtolower($filter_value) === 'true') {
                        $filter_value = 1;
                    } else if (strtolower($filter_value) === 'false') {
                        $filter_value = 0;
                    } else if (strtolower($filter_value) === 'null' || $filter_value === null) {
                        $filter_value = 'NULL';
                    } else if (strtolower($filter_value) === '!null') {
                        $filter_value = 'NOT NULL';
                    }

                    $filter_column = $tablePrefix ? $tablePrefix . $filter_column : $filter_column;
                    $first_sql_clause = $count === 0 ? "WHERE" : "AND";

                    if ($filter_value === 'NULL' || $filter_value === 'NOT NULL') {
                        $filter_string .= " $first_sql_clause $filter_column IS $filter_value";
                    } else {
                        $dynamic_filters = explode(",", $filter_value);
                        $dynamic_placeholders = trim(str_repeat('?,', count($dynamic_filters)), ',');

                        if ($isCustomQuery) {
                            $filter_operator = $filter_value['operator'];

                            if (strtolower($filter_operator) === 'exclude') {
                                $filter_string = $filter_string . " $first_sql_clause $filter_column NOT IN ($dynamic_placeholders)";
                            } else {
                                $filter_string = $filter_string . " $first_sql_clause $filter_column $filter_operator $dynamic_placeholders";
                            }
                        } else {
                            if ($isSearchQuery) {
                                $searchableColumns = array_map(function($column) use ($tablePrefix) {
                                    return $tablePrefix . $column;
                                }, $searchableColumns);

                                $columns = implode(',', $searchableColumns);

                                $filter_string .= " $first_sql_clause CONCAT(' ', $columns) LIKE CONCAT( '%','?','%')";
                            } else {
                                $filter_string .= " $first_sql_clause $filter_column IN ($dynamic_placeholders)";
                            }
                        }

                        $query_parameters = array_merge($query_parameters, $dynamic_filters);
                    }
                }

                $count++;
            }
        }

        return (object) [
            "filter_string" => $filter_string,
            "query_parameters" => $query_parameters
        ];
    }

    /**
     * get the string representation of the ORDER BY sql expression
     * @param array $page_formatting
     * @return string
     */
    protected function getOrderByString(array $page_formatting = []) : string
    {
        $sortBy = array_key_exists('sort', $page_formatting) ? $page_formatting["sort"] : 'created_at';
        $sortDirection = array_key_exists('order', $page_formatting) ? $page_formatting["order"] : 'DESC';

        return "ORDER BY " . $sortBy . " " . $sortDirection;
    }

    /**
     * get the string representation of the ORDER BY sql expression
     * @param $filter_vars
     * @param $page_formatting
     * @return array
     */
    protected function getQueryParams($filter_vars, $page_formatting) : array
    {

        $page_formatting_params = [
            array_key_exists('offset', $page_formatting) ? $page_formatting["offset"] : 0,
            array_key_exists('limit', $page_formatting) ? $page_formatting["limit"] : 50
        ];

        return empty($filter_vars->query_parameters)
            ? $page_formatting_params
            : array_merge($filter_vars->query_parameters, $page_formatting_params);
    }

    /**
     * get searchable columns for the specified table
     * @param string $tableName
     * @return string[]|null
     */
    protected function getSearchableColumnsInTable(string $tableName) : ?array
    {
        if ($tableName === '') {
            return [
                ''
            ];
        }

        return null;
    }
}
