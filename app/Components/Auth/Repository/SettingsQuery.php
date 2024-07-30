<?php

namespace App\Components\Auth\Repository;

use Bloomex\Common\Blca\Models\BlcaSetting;
use Illuminate\Database\Eloquent\Builder;

class SettingsQuery
{
    /**
     * @param BlcaSetting $settings
     */
    public function __construct(
        protected readonly BlcaSetting $settings
    ) {
    }

    public function getSettings(): Builder
    {
        return $this->settings->newModelQuery()
            ->where('is_active', true);
    }
}