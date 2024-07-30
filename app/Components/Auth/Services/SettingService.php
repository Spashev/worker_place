<?php

namespace App\Components\Auth\Services;

use App\Components\Auth\Repository\SettingsMutator;
use App\Components\Auth\Repository\SettingsQuery;
use Exception;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\DatabaseManager;
use Bloomex\Common\Blca\Models\BlcaSetting;
use App\Components\Auth\Contracts\SettingsInterface;

class SettingService
{
    public function __construct(
        private readonly SettingsMutator $settingsMutator,
        private readonly SettingsQuery   $settingsQuery,
        private readonly Log             $log,
        private readonly DatabaseManager $db,
    ) {
    }
    /**
     * @throws \Throwable
     */
    public function saveSetting(SettingsInterface $request): void
    {
        /** @var User $creator */
        $updaterId = auth()->id();
        try {
            $this->db->beginTransaction();
            $this->settingsMutator->saveSetting($request, $updaterId);
            $this->db->commit();
        } catch (\Throwable $t) {
            $this->db->rollBack();
            $this->log::error($t->getMessage());
            throw new Exception('Internal error check logs');
        }
    }

    public function listSettings(): Collection
    {
        return $this->settingsQuery->getSettings()->get();
    }

    public function removeSetting(BlcaSetting $setting)
    {
        $this->settingsMutator->markAsInactive($setting->id);
    }
}