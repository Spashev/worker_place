<?php

namespace App\Helpers;


use Bloomex\Common\Core\Enums\TimeZones;
use Illuminate\Support\Carbon;

class TimeHelper
{
    public function getTimestampToronto(): string|int|float
    {
        return Carbon::now(TimeZones::Toronto->value)->timestamp;
    }

    public function getDateTimeToronto(): string|int|float
    {
        return Carbon::now(TimeZones::Toronto->value);
    }

    public function isBeforeNoonNow(TimeZones $timeZone): bool
    {
        $time = Carbon::now($timeZone->value);
        $hour = $time->format('H');

        if ($hour <= 12) {
            return true;
        }

        return false;
    }

    public function getTorontoTimestamp($date, $time): int
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date . $time, TimeZones::Toronto->value)->timestamp;
    }

    public function getDateTimeOfToronto(): string|int|float
    {
        return Carbon::now( TimeZones::Toronto->value);
    }
}