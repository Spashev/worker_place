<?php

namespace App\Components\Auth\Contracts;

interface SettingsInterface
{
    public function getId(): ?int;
    public function getKey(): string;
    public function getValue(): string;
    public function getType(): string;
}