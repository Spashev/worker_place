<?php

namespace App\Components\Orders\Filters;

use App\Helpers\TimeHelper;
use Bloomex\Common\Blca\Models\BlcaOrder;
use Bloomex\Common\Blca\Models\Filters\AbstractFilter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrdersFilter extends AbstractFilter
{
    //SEARCH
    public const SEARCH = 'search';
    public const STATUS = 'status';
    public const ORDER_ID = 'id';
    public const DELIVERY = 'delivered_at';
    public const WAREHOUSE = 'warehouse';
    public const RANGE_DELIVERY = 'range.delivered_at';
    public const RANGE_CREATED = 'range.created_at';
    public const RANGE_UPDATED = 'range.updated_at';
    public const CURRENCY = 'order_currency';
    public const OCCASION = 'occasion';

    //SORT
    public const SORT_ID = 'sort.id';
    public const SORT_OCCASION = 'sort.occasion';
    public const SORT_STATUS = 'sort.status';
    public const SORT_DELIVERY = 'sort.delivered_at';
    public const SORT_CREATED = 'sort.created_at';
    public const SORT_UPDATED = 'sort.updated_at';
    public const SORT_WAREHOUSE = 'sort.warehouse';
    public const SORT_TOTAL = 'sort.total';

    protected function getCallbacks(): array
    {
        return [
            //search
            self::SEARCH => [$this, 'search'],
            self::ORDER_ID => [$this, 'orderId'],
            self::DELIVERY => [$this, 'deliveredAt'],
            self::WAREHOUSE => [$this, 'warehouse'],
            self::RANGE_DELIVERY => [$this, 'rangeDeliveredAt'],
            self::RANGE_CREATED => [$this, 'rangeCreatedAt'],
            self::RANGE_UPDATED => [$this, 'rangeUpdatedAt'],
            self::STATUS => [$this, 'status'],
            self::CURRENCY => [$this, 'orderCurrency'],
            self::OCCASION => [$this, 'occasion'],


            //sorts
            self::SORT_ID => [$this, 'sortId'],
            self::SORT_OCCASION => [$this, 'sortOccasion'],
            self::SORT_STATUS => [$this, 'sortStatus'],
            self::SORT_DELIVERY => [$this, 'sortDelivery'],
            self::SORT_CREATED => [$this, 'sortCreated'],
            self::SORT_UPDATED => [$this, 'sortUpdated'],
            self::SORT_WAREHOUSE => [$this, 'sortWarehouse'],
            self::SORT_TOTAL => [$this, 'sortTotal'],
        ];
    }

    public function orderCurrency(Builder $builder, $value): void
    {
        $builder->where('order_currency', 'like', "%$value%");
    }

    public function orderId(Builder $builder, $value): void
    {
        $ids = explode(",", $value);
        $builder->whereIn('order_id', $ids);
    }

    public function deliveredAt(Builder $builder, $value): void
    {
        $dates = explode(",", $value);
        $builder->whereIn('ddate', $dates);
    }

    public function warehouse(Builder $builder, $value): void
    {
        $warehouses = explode(",", $value);
        $builder->whereIn('warehouse', $warehouses);
    }

    public function rangeDeliveredAt(Builder $builder, $value): void
    {
        $builder->whereBetween('ddate', [$value['start'], $value['finish']]);
    }

    public function rangeCreatedAt(Builder $builder, $value): void
    {
        $timeHelper = new TimeHelper();
        $start = $timeHelper->getTorontoTimestamp($value['start'], ' 00:00:00');
        $finish =$timeHelper->getTorontoTimestamp($value['finish'], ' 23:59:59');
        $builder->whereBetween('cdate', [$start, $finish]);
    }

    public function rangeUpdatedAt(Builder $builder, $value): void
    {
        $timeHelper = new TimeHelper();
        $start = $timeHelper->getTorontoTimestamp($value['start'], ' 00:00:00');
        $finish =$timeHelper->getTorontoTimestamp($value['finish'], ' 23:59:59');
        $builder->whereBetween('mdate', [$start, $finish]);
    }

    public function occasion(Builder $builder, $value): void
    {
        $occasions = explode(",", $value);
        $builder->whereIn('customer_occasion', $occasions);
    }

    public function status(Builder $builder, $value): void
    {
        $statuses = explode(",", $value);
        $builder->whereIn('order_status', $statuses);
    }

    public function search(Builder $builder, $value): void
    {
        $builder->where('order_id', 'like', "%$value%")
            ->orWhere('order_currency', 'like', "%$value%");
    }

    // sorts ----------------------------------------------------------

    public function sortId(Builder $builder, $value): void
    {
        $builder->orderBy('order_id', $value);
    }

    public function sortTotal(Builder $builder, $value): void
    {
        $builder->orderBy('order_total', $value);
    }

    public function sortDelivery(Builder $builder, $value): void
    {
        $builder->orderByRaw("ISNULL(ddate), ddate $value");
    }

    public function sortCreated(Builder $builder, $value): void
    {
        $builder->orderByRaw("ISNULL(cdate), cdate $value");
    }

    public function sortUpdated(Builder $builder, $value): void
    {
        $builder->orderByRaw("ISNULL(mdate), mdate $value");
    }

    public function sortOccasion(Builder $builder, $value): void
    {
        $sortColumn = 'order_occasion_name';
        $builder->aggregateJoin(
            $sortColumn,
            'jos_vm_order_occasion',
            'customer_occasion',
            'order_occasion_code'
        )->orderBy($sortColumn, $value);

        //alternative
        //$builder->withAggregate('occasion', 'order_occasion_name')
        //->orderBy('occasion_order_occasion_name', $value);
    }

    public function sortWarehouse(Builder $builder, $value): void
    {
        $sortColumn = 'warehouse_name';
        $builder->aggregateJoin(
            $sortColumn,
            'jos_vm_warehouse',
            'warehouse',
            'warehouse_code'
        )->orderBy($sortColumn, $value);
    }

    public function sortStatus(Builder $builder, $value): void
    {
        $sortColumn = 'order_status_name';
        $builder->aggregateJoin(
            $sortColumn,
            'jos_vm_order_status',
            'order_status',
            'order_status_code'
        )->orderBy($sortColumn, $value);
    }
}