<?php

namespace App\Components\Orders\Services\ImageService;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Components\Orders\Entities\ImagePaths;
use Illuminate\Contracts\Config\Repository as ConfigContract;

class PathGenerator
{
    private const THUMB = 'thumb';
    private const ORIGINAL = 'original';
    private const BLOB = 'blob';

    public function __construct(
        private readonly Str            $str,
        private readonly ConfigContract $conf,
        private readonly Storage        $storage,
    ) {
    }

    public function pathBuilder(int $orderId, int $historyId, string $origExt): ImagePaths
    {
        $name = $this->str->orderedUuid();
        $paths = new ImagePaths();

        $extension = $this->conf->get('image.file_extension');

        $paths->setBlobPath($this->getCreatePath($orderId, $historyId, self::BLOB, $origExt, $name));
        $paths->setFileFullPath($this->getCreatePath($orderId, $historyId, self::ORIGINAL, $extension, $name));
        $paths->setFileThumbPath($this->getCreatePath($orderId, $historyId, self::THUMB, $extension, $name));

        $paths->setBlobLink($this->storage::url($paths->getBlobPath()));

        return $paths;
    }

    private function getCreatePath(int $orderId, int $historyId, string $sizeSeparator, string $extension, string $name): string
    {
        $mainFolder = $this->conf->get('image.folder');
        $separator = $this->conf->get('image.separator');
        $dot = '.';

        $partsOfPath = [];
        array_push(
            $partsOfPath,
            $mainFolder,
            $orderId,
            $historyId,
            implode($dot, [
                implode('-', [$sizeSeparator, $name]), $extension
            ]),
        );

        return implode($separator, $partsOfPath);
    }
}