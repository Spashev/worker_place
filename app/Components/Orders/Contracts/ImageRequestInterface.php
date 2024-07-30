<?php

namespace App\Components\Orders\Contracts;

use Illuminate\Http\UploadedFile;

interface ImageRequestInterface
{
    public function getImage(): UploadedFile;
    public function getExtension(): string;
}