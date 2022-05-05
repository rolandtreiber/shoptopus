<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

trait APIControllerTrait
{
    /**
     * Return a get response formatted with next and last pagination if requested
     *
     * @param array $page_formatting
     * @param array $query_response
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    protected function getResponse(array $page_formatting, array $query_response, Request $request) : array
    {
        $filters = $this->getAndValidateFilters($request);
        $filter_query_param_string = '';

        foreach ($filters as $key => $filter_value) {
            if (is_array($filter_value)) {
                if (array_key_exists('value', $filter_value)) {
                    $filter_value = $filter_value['value'];
                }
            }

            $filter_query_param_string .= "filter[$key]=$filter_value&";
        }

        //if we sent in pagination and returned the correct data structure from the service layer the below code will run
        if (isset($page_formatting["offset"]) && isset($page_formatting["limit"]) && isset($query_response["data"])) {
            $current_offset = ($page_formatting["offset"] / $page_formatting["limit"]) + 1;
            $next_offset = $current_offset + 1;
            $previous_offset = $current_offset - 1;
            $last_offset = round($query_response["count"] / $page_formatting["limit"], 0, PHP_ROUND_HALF_UP);

            $path_string = $this->siteURL() . $request->path() . "/?" . $filter_query_param_string . "page[offset]=";
            $pagination_query_string = "&page[limit]=" . $page_formatting["limit"];

            $response = [
                "message" => "OK",
                "data" => $query_response["data"],
                "page" => $current_offset,
                "per_page" => (int) $page_formatting["limit"],
                "next" => $path_string . $next_offset . $pagination_query_string,
                "previous" => $path_string . $previous_offset . $pagination_query_string,
                "last" => $path_string . $last_offset . $pagination_query_string,
                "records" => count($query_response["data"]),
                "total_records" => $query_response["count"]
            ];

            if ($next_offset > $last_offset) {
                $response["next"] = null;
                unset($response["last"]);
            }

            if ($previous_offset < 1) {
                unset($response["previous"]);
            }

            return $response;
        }

        //if we didn't send in pagination, we either asked for everything, or for just a single record /{id} etc. work out which below and format accordingly
        if (isset($query_response["data"])) {
            $data = $query_response["data"];
            $records = count($query_response["data"]);
        } else {
            if (!empty($query_response)) {
                $query_response = (is_object($query_response[array_key_first($query_response)])) ? $query_response : [$query_response];
            }
            $data = $query_response;
            $records = count($query_response);
        }

        return [
            "message" => "OK",
            "data" => $data,
            "page" => null,
            "per_page" => null,
            "next" => null,
            "previous" => null,
            "last" => null,
            "records" => $records,
            "total_records" => $records
        ];
    }

    /**
     * Return a post response
     *
     * @param array $data
     * @return array
     */
    protected function postResponse(array $data) : array
    {
        return ["message" => "OK, CREATED", "data" => $this->getFormattedData($data)];
    }

    /**
     * Return a put response
     *
     * @param array $data
     * @return array
     */
    protected function putResponse(array $data) : array
    {
        return ["message" => "OK, UPDATED", "data" => $this->getFormattedData($data)];
    }

    /**
     * Return a delete response
     *
     * @return array
     */
    protected function deleteResponse() : array
    {
        return ["message" => "OK, DELETED"];
    }

    /**
     * Format and return an error response
     *
     * @param mixed $e
     * @param string $user_message
     * @param int|null $error_code
     * @param int $status_code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse(mixed $e, string $user_message, int $error_code = null, int $status_code = 500) : \Illuminate\Http\JsonResponse
    {
        if ($e instanceof \Illuminate\Validation\ValidationException) {
            return response()->json([
                "error_code" => 422,
                'user_message' => 'The given data was invalid.',
                'errors' => $e->validator->getMessageBag()
            ], 422);
        }

        $error_code = $error_code ?? $e->getCode();

        if (!$error_code) { //if the error code is still 0, some generic error, //@todo log this as critical
            $error_code = Config::get('api_error_codes.controllers.response.generic');
            $user_message = __("error_messages." . Config::get('api_error_codes.controllers.response.generic'));
        }

        return response()->json([
            "error_code" => $error_code,
            "developer_message" => str_starts_with($e->getMessage(), "SQLSTATE")
                ? "SQL ERROR - Please see Kibana logstash index for details."
                : $e->getMessage(),
            "user_message" => $user_message,
            "more_info" => $this->siteURL() . "error_codes/" . $error_code
        ], $status_code);
    }

    /**
     * Get and validate the filters from the query string
     *
     * @param Request $request
     * @return array
     */
    protected function getAndValidateFilters(Request $request) : array
    {
        $relation_queries = [];
        $filters = $request->query('filter') ?? [];

        $options = $request->query('options') ?? [];
        $categories = $request->query('product_categories') ?? [];
        $tags = $request->query('product_tags') ?? [];

        if (!empty($options)) {
            $relation_queries += ['product_attribute_options' => $options];
        }

        if (!empty($categories)) {
            $relation_queries += ['product_categories' => $categories];
        }

        if (!empty($tags)) {
            $relation_queries += ['product_tags' => $tags];
        }

        return $relation_queries + $filters;
    }

    /**
     * Get and validate the page attributes from the query string
     *
     * @param Request $request
     * @return array
     */
    protected function getPageFormatting(Request $request) : array
    {
        $page_formatting = [];
        if ($request->query("page")) {
            $page_formatting = $request->query("page");

            if (isset($page_formatting["offset"]) && isset($page_formatting["limit"])) {
                $page_formatting["offset"] = ($page_formatting["offset"] - 1) * $page_formatting["limit"];
            }
        }
        return $page_formatting;
    }

    /**
     * Ensure $data is always an array of objects
     *
     * @param array $data
     * @return array
     */
    protected function getFormattedData(array $data) : array
    {
        return isset($data[0]) && (is_object($data[0]) || is_array($data[0]))
            ? array_merge([], $data)
            : array_merge([], [$data]);
    }

    /**
     * Get the filters and page formatting
     *
     * @param Request $request
     * @param int|null $per_page
     * @return array
     */
    protected function getFiltersAndPageFormatting(Request $request, int $per_page = null) : array
    {
        $filters = $this->getAndValidateFilters($request);
        $page_formatting = $this->getPageFormatting($request);

        $page_formatting['sort'] = $page_formatting['sort'] ?? 'created_at';
        $page_formatting['offset'] = $page_formatting['offset'] ?? 0;
        $page_formatting['limit'] = $page_formatting['limit'] ?? ($per_page ?? 12);

        return [ $filters, $page_formatting ];
    }

    /**
     * Get the current site URL
     *
     * @return string
     */
    protected function siteURL() : string
    {
        if(app()->environment('testing')) {
            return "http://localhost:8000/";
        }

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'] . '/';

        return $protocol . $domainName;
    }
}
