<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait FilterTrait
{
    /**
     * The supported names of the filterable relationships
     */
    private array $filterableRelationships = [
        'product_attribute_options',
        'product_categories',
        'product_tags',
    ];

    /**
     * Override the table names for the filterable relationships
     */
    private array $relationshipTableNameOverrides = [
        'product_attribute_options' => 'product_product_attribute',
    ];

    /**
     * Get filters
     *
     * @param  array  $filters
     * @param $tableName
     * @return object
     */
    public function getFilters($tableName, array $filters = []): object
    {
        $filter_string = '';
        $query_parameters = [];
//        $searchableColumns = $this->getSearchableColumnsInTable($tableName);

        if ($filters) {
            $shouldGroupBy = false;
            $table_prefix = rtrim($tableName, '.').'.';
            $local_name = Str::singular($tableName);
            $local_key = $table_prefix.'id';
            $count = 0;
            $joints_count = 0;

            foreach ($this->filterableRelationships as $relation) {
                if (array_key_exists($relation, $filters)) {
                    $shouldGroupBy = true;
                    $singular_relation_name = Str::singular($relation);
                    $table = $this->getRelationshipTableName($local_name, $singular_relation_name);

                    $filter_string .= $this->getJoinStatement($table, $local_name, $local_key);
                }
            }

            foreach ($this->filterableRelationships as $relation) {
                if (array_key_exists($relation, $filters)) {
                    $singular_relation_name = Str::singular($relation);
                    $table = $this->getRelationshipTableName($local_name, $singular_relation_name);

                    $filter_string .= $this->getRelationshipWhereClause($table, $singular_relation_name, $filters[$relation], $joints_count);

                    foreach (explode(',', $filters[$relation]) as $query_param) {
                        array_push($query_parameters, $query_param);
                    }

                    unset($filters[$relation]);

                    $joints_count++;
                    $count++;
                }
            }

            foreach ($filters as $filter_column => $filter_value) {
                // $isSearchQuery = $filter_column === 'search';
                $isCustomQuery = is_array($filter_value);

                if ($isCustomQuery) {
                    $filter_value = $filter_value['value'];
                }

                if (is_array($filter_value)) {
                    $filter_value = implode(',', $filter_value);
                } elseif (strtolower($filter_value) === 'true') {
                    $filter_value = 1;
                } elseif (strtolower($filter_value) === 'false') {
                    $filter_value = 0;
                } elseif (strtolower($filter_value) === 'null' || $filter_value === null) {
                    $filter_value = 'NULL';
                } elseif (strtolower($filter_value) === '!null') {
                    $filter_value = 'NOT NULL';
                }

                $filter_column = $table_prefix.$filter_column;
                $first_sql_clause = $count === 0 ? 'WHERE' : 'AND';

                if ($filter_value === 'NULL' || $filter_value === 'NOT NULL') {
                    $filter_string .= " $first_sql_clause $filter_column IS $filter_value";
                } else {
                    $dynamic_filters = explode(',', $filter_value);
                    $dynamic_placeholders = trim(str_repeat('?,', count($dynamic_filters)), ',');

                    if ($isCustomQuery) {
                        $filter_operator = $filter_value['operator'];

                        if (strtolower($filter_operator) === 'exclude') {
                            $filter_string = $filter_string." $first_sql_clause $filter_column NOT IN ($dynamic_placeholders)";
                        } else {
                            $filter_string = $filter_string." $first_sql_clause $filter_column $filter_operator $dynamic_placeholders";
                        }
                    } else {
                        $filter_string .= " $first_sql_clause $filter_column IN ($dynamic_placeholders)";
//                            if ($isSearchQuery) {
//                                $searchableColumns = array_map(function($column) use ($table_prefix) {
//                                    return $table_prefix . $column;
//                                }, $searchableColumns);
//
//                                $columns = implode(',', $searchableColumns);
//
//                                $filter_string .= " $first_sql_clause CONCAT(' ', $columns) LIKE CONCAT( '%','?','%')";
//                            } else {
//                                $filter_string .= " $first_sql_clause $filter_column IN ($dynamic_placeholders)";
//                            }
                    }

                    $query_parameters = array_merge($query_parameters, $dynamic_filters);
                }

                $count++;
            }

            if ($shouldGroupBy) {
                $filter_string .= " GROUP BY {$local_key}";
            }
        }

        return (object) [
            'filter_string' => $filter_string,
            'query_parameters' => $query_parameters,
        ];
    }

    /**
     * Guess the table name for the relationship query
     *
     * @param  string  $local_name
     * @param  string  $foreign_name
     * @return string
     */
    private function getRelationshipTableName(string $local_name, string $foreign_name): string
    {
        if (isset($this->relationshipTableNameOverrides[Str::plural($foreign_name)])) {
            return $this->relationshipTableNameOverrides[Str::plural($foreign_name)];
        }

        return strcmp($local_name, $foreign_name) < 0
            ? $local_name.'_'.$foreign_name
            : $foreign_name.'_'.$local_name;
    }

    /**
     * get the string representation of the ORDER BY sql expression
     *
     * @param  array  $page_formatting
     * @return string
     */
    protected function getOrderByString(array $page_formatting = []): string
    {
        $sortBy = array_key_exists('sort', $page_formatting) ? $page_formatting['sort'] : 'created_at';
        $sortDirection = array_key_exists('order', $page_formatting) ? $page_formatting['order'] : 'DESC';

        return 'ORDER BY '.$sortBy.' '.$sortDirection;
    }

    /**
     * get the string representation of the ORDER BY sql expression
     *
     * @param $filter_vars
     * @param $page_formatting
     * @return array
     */
    protected function getQueryParams($filter_vars, $page_formatting): array
    {
        $page_formatting_params = [
            array_key_exists('offset', $page_formatting) ? $page_formatting['offset'] : 0,
            array_key_exists('limit', $page_formatting) ? $page_formatting['limit'] : 50,
        ];

        return empty($filter_vars->query_parameters)
            ? $page_formatting_params
            : array_merge($filter_vars->query_parameters, $page_formatting_params);
    }

    /**
     * Get the joint statement for the relationship query
     *
     * @param $table
     * @param $local_name
     * @param $local_key
     * @return string
     */
    private function getJoinStatement($table, $local_name, $local_key): string
    {
        $parent_key = $local_name.'_id';

        return " JOIN {$table} ON $table.$parent_key = $local_key";
    }

    /**
     * Get the where clause for the relationship
     *
     * @param  string  $table
     * @param  string  $singular_relation_name
     * @param  string  $filter_values
     * @param  int  $count
     * @return string
     */
    private function getRelationshipWhereClause(string $table, string $singular_relation_name, string $filter_values, int $count): string
    {
        $foreign_key = $singular_relation_name.'_id';
        $dynamic_filters = explode(',', $filter_values);
        $dynamic_placeholders = trim(str_repeat('?,', count($dynamic_filters)), ',');
        $relation_sql_clause = $count === 0 ? 'WHERE' : 'AND';

        return " $relation_sql_clause $table.$foreign_key IN ($dynamic_placeholders)";
    }

//    /**
//     * Get searchable columns for the specified table
//     *
//     * @param string $tableName
//     * @return string[]|null
//     */
//    private function getSearchableColumnsInTable(string $tableName) : ?array
//    {
//        if ($tableName === '') {
//            return [
//                ''
//            ];
//        }
//
//        return null;
//    }
}
