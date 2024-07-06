<?php

namespace App\Repositories\Local\Order;

use App\Models\Order;
use App\Repositories\Local\ModelRepository;
use App\Services\Local\Error\ErrorServiceInterface;
use App\Services\Local\Order\OrderService;
use Illuminate\Support\Facades\DB;

class OrderRepository extends ModelRepository implements OrderRepositoryInterface
{
    public function __construct(ErrorServiceInterface $errorService, Order $model)
    {
        parent::__construct($errorService, $model);
    }

    /**
     * Get the product attributes for the given products
     *
     *
     * @throws \Exception
     */
    public function getInvoice(string $orderId): array
    {
        try {
            return DB::select("
                SELECT
                    invoices.id,
                    invoices.user_id,
                    invoices.order_id,
                    invoices.address,
                    invoices.payment,
                    invoices.products,
                    invoices.voucher_code,
                    invoices.delivery_type
                FROM invoices AS invoices
                WHERE invoices.order_id = \"" . $orderId . "\"
                AND invoices.deleted_at IS NULL
            ");
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Get the required related models for the given parent
     *
     *
     * @throws \Exception
     */
    public function getTheResultWithRelationships($result, array $excludeRelationships = []): array
    {
        try {
            foreach ($result as &$model) {
                if (!in_array('invoice', $excludeRelationships)) {
                    $invoiceRawData = $this->getInvoice($model['id']);
                    if (is_array($invoiceRawData) and count($invoiceRawData) > 0) {
                        $invoice = $invoiceRawData[0];
                        $invoice['address'] = json_decode($invoice['address']);
                        $invoice['payment'] = json_decode($invoice['payment']);
                        $invoice['products'] = json_decode($invoice['products']);
                        $invoice['voucher_code'] = json_decode($invoice['voucher_code']);
                        $invoice['delivery_type'] = json_decode($invoice['delivery_type']);
                    } else {
                        $invoice = null;
                    }
                    $model['invoice'] = $invoice;
                }
            }

            return $result;
        } catch (\Exception|\Error $e) {
            $this->errorService->logException($e);
            throw $e;
        }
    }

    /**
     * Get the columns for selection
     */
    public function getSelectableColumns(bool $withTableNamePrefix = true): array
    {
        $columns = [
            "{$this->model_table}.id",
            "{$this->model_table}.user_id",
            "{$this->model_table}.delivery_type_id",
            "{$this->model_table}.voucher_code_id",
            "{$this->model_table}.address_id",
            "{$this->model_table}.original_price",
            "{$this->model_table}.subtotal",
            "{$this->model_table}.total_price",
            "{$this->model_table}.total_discount",
            "{$this->model_table}.delivery_cost",
            "{$this->model_table}.status",
            "{$this->model_table}.currency_code",
            "{$this->model_table}.deleted_at",
        ];

        return $withTableNamePrefix
            ? $columns
            : array_map(function ($column_name) {
                return str_replace($this->model_table . '.', '', $column_name);
            }, $columns);
    }
}
