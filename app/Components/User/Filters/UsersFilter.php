<?php

namespace App\Components\User\Filters;

use Bloomex\Common\Blca\Models\Filters\AbstractFilter;
use Illuminate\Database\Eloquent\Builder;

class UsersFilter extends AbstractFilter
{
    //SEARCH
    public const SEARCH = 'search';
    public const USER_ID = 'id';
    public const CREATED = 'created_at';
    public const LAST_VISIT = 'last_visit_at';

    //SORT
    public const SORT_ID = 'sort.id';
    public const SORT_CREATED = 'sort.created_at';
    public const SORT_LAST_VISIT = 'sort.last_visit_at';

    protected function getCallbacks(): array
    {
        return [
            //search
            self::SEARCH => [$this, 'search'],
            self::USER_ID => [$this, 'userId'],
            self::CREATED => [$this, 'created'],
            self::LAST_VISIT => [$this, 'lastVisit'],

            //sorts
            self::SORT_ID => [$this, 'sortId'],
            self::SORT_CREATED => [$this, 'sortCreated'],
            self::SORT_LAST_VISIT => [$this, 'sortLastVisit'],
        ];
    }

    public function userId(Builder $builder, $value): void
    {
        $ids = explode(",", $value);
        $builder->whereIn('id', $ids);
    }

    public function createdAt(Builder $builder, $value): void
    {
        $dates = explode(",", $value);
        $builder->whereIn('', $dates);
    }

    public function rangeCreatedAt(Builder $builder, $value): void
    {
        $builder->whereBetween('', [$value['start'], $value['finish']]);
    }

    public function search(Builder $builder, $value): void
    {
    }

    // sorts ----------------------------------------------------------
    public function sortId(Builder $builder, $value): void
    {
        $builder->orderBy('id', $value);
    }

    public function sortCreated(Builder $builder, $value): void
    {
        $builder->orderByRaw("ISNULL(),  $value");
    }
}