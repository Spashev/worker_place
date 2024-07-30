<?php

namespace App\Components\Orders\Contracts;

interface ImagePathsInterface
{
    public function getFileFullPath(): string;
    public function getFileThumbPath(): string;
    public function setFileFullPath(string $url): void;
    public function setFileThumbPath(string $url): void;

    public function getBlobPath(): string;
    public function setBlobPath(string $url): void;
    public function getBlobLink(): string;
    public function setBlobLink(string $url): void;
}