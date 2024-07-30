<?php

namespace App\Components\Auth\Contracts;

interface MySettingsInterface
{
    public function getTheme(): string;
    public function getLang(): string;
}