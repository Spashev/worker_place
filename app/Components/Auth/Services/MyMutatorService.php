<?php

namespace App\Components\Auth\Services;

use App\Components\Auth\Contracts\MySettingsInterface;
use App\Components\Auth\Repository\MyMutator;
use App\Components\Auth\Repository\MySettingsMutator;
use App\Models\User;
use Bloomex\Common\Blca\Models\BlcaUserSettings;
use Exception;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\Log;

class MyMutatorService
{
    public function __construct(
        private readonly PasswordCreator   $passwordCreator,
        private readonly MyMutator         $myMutator,
        private readonly MySettingsMutator $settingsMutator,
        private readonly Log               $log,
        private readonly DatabaseManager   $db,
    ) {
    }

    /**
     * @throws Exception
     */
    public function createCode(): string
    {
        /** @var User $user */
        $user = auth()->user();
        try {
            if ($user->getGuardRoleNames()->contains('Packer') && $user->getGuardRoleNames()->count() === 1) {
                $code = $this->passwordCreator->createAccessCode(4);
            } else {
                $code = $this->passwordCreator->createAccessCode(6);
            }
            $this->myMutator->updateAccessCode($user, $code);
        } catch (\Throwable $t) {
            $this->log::error($t->getMessage());
            throw new Exception('Internal error check logs');
        }

        return $code;
    }

    /**
     * @throws \Throwable
     */
    public function saveSettings(MySettingsInterface $request): BlcaUserSettings
    {
        /** @var User $creator */
        $creator = auth()->user();
        try {
            $this->db->beginTransaction();
            $settings = $this->settingsMutator->saveSettings($creator, $request);
            $this->db->commit();
        } catch (\Throwable $t) {
            $this->db->rollBack();
            $this->log::error($t->getMessage());
            throw new Exception('Internal error check logs');
        }

        return $settings;
    }
}