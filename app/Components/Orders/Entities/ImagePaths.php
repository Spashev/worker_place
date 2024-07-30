<?php

namespace App\Components\Orders\Entities;

use App\Components\Orders\Contracts\ImagePathsInterface;

/**
 * @property String $filePath
 * @property String $fileThumbPath
 * @property String $fileBlobPath
 * @property String $fileBlobLink
 */
class ImagePaths implements ImagePathsInterface
{
    public function getFileFullPath(): string
    {
        return $this->filePath;
    }

    public function setFileFullPath(string $url): void
    {
        $this->filePath = $url;
    }

    public function getFileThumbPath(): string
    {
        return $this->fileThumbPath;
    }

    public function setFileThumbPath(string $url): void
    {
        $this->fileThumbPath = $url;
    }

    public function getBlobLink(): string
    {
        return $this->fileBlobLink;
    }

    public function setBlobLink(string $url): void
    {
        $this->fileBlobLink = $url;
    }

    public function getBlobPath(): string
    {
        return $this->fileBlobPath;
    }

    public function setBlobPath(string $url): void
    {
        $this->fileBlobPath = $url;
    }
}