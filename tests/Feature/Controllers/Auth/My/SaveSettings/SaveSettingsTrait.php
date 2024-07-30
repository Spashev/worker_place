<?php

namespace Tests\Feature\Controllers\Auth\My\SaveSettings;

trait SaveSettingsTrait
{
    public function createCredentials($theme = 'dark', $lang = 'en'): array
    {
        return [
            'theme' => $theme,
            'lang' => $lang,
        ];
    }
}