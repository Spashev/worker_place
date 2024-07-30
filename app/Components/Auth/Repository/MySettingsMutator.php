<?php

namespace App\Components\Auth\Repository;

use App\Components\Auth\Contracts\MySettingsInterface;
use App\Models\User;
use Bloomex\Common\Blca\Models\BlcaUserSettings;

class MySettingsMutator
{
    public function saveSettings(User $creator, MySettingsInterface $request): BlcaUserSettings
    {
        $settings = $creator->settings;

        if (!$settings) {
            $settings = new BlcaUserSettings();
            $settings->user_id = $creator->id;
        }

        $settings->lang = $request->getLang();
        $settings->theme = $request->getTheme();
        $settings->save();

        return $settings;
    }
}