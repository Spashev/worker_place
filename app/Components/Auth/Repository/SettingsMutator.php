<?php

namespace App\Components\Auth\Repository;

use App\Components\Auth\Contracts\SettingsInterface;
use Bloomex\Common\Blca\Models\BlcaSetting;

class SettingsMutator
{
    /**
     * @param BlcaSetting $settings
     */
    public function __construct(
        protected readonly BlcaSetting $settings
    ) {
    }

    public function saveSetting(SettingsInterface $request, int $updaterId): BlcaSetting
    {
        return $this->settings->newModelQuery()->updateOrCreate(
            ['id' => $request->getId()],
            [
                'key' => $request->getKey(),
                'value' => $request->getValue(),
                'type' => $request->getType(),
                'updated_by' => $updaterId
            ]);
    }

    public function markAsInactive(int $id): bool
    {
        return $this->settings->newModelQuery()
            ->where('id', $id)
            ->update(['is_active' => false]);
    }
}