<?php

namespace App\Components\Orders\Services\ImageService;

use Exception;
use App\Jobs\StoreImage;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Filesystem\Factory;
use Bloomex\Common\Blca\Models\BlcaOrderHistory;
use Bloomex\Common\Blca\Models\BlcaOrderHistoryImage;
use App\Components\Orders\Contracts\ImageRequestInterface;
use App\Components\Orders\Repository\HistoryImageMutator;
use Illuminate\Contracts\Config\Repository as ConfigContract;

class ImageService
{
    public function __construct(
        private readonly Log                 $log,
        private readonly PathGenerator       $builder,
        private readonly HistoryImageMutator $historyImageMutator,
        private readonly Factory             $storage,
        private readonly ConfigContract      $conf,
    ) {
    }

    /**
     * @throws Exception
     */
    public function addHistoryImage(BlcaOrderHistory $history, ImageRequestInterface $dataProvider): BlcaOrderHistoryImage
    {
        try {
            $historyId = $history->order_status_history_id;
            $img = $dataProvider->getImage();
            $extension = $dataProvider->getExtension();

            $imagePaths = $this->builder->pathBuilder($history->order_id, $historyId, $extension);
            $this->storage->disk($this->conf->get('image.disk'))->put($imagePaths->getBlobPath(), file_get_contents($img));
            $historyImage = $this->historyImageMutator->createHistoryImage($historyId, $imagePaths);

            StoreImage::dispatch($imagePaths);

        } catch (\Throwable $t) {
            $this->log::error($t->getMessage());
            throw new Exception('Internal error check logs');
        }
        return $historyImage;
    }

    public function deleteHistoryImage(BlcaOrderHistoryImage $historyImage): bool
    {
        $imageId = $historyImage->id;
        try {
            return $this->historyImageMutator->removeHistoryImage($imageId);
        } catch (\Throwable $t) {
            $this->log::error($t->getMessage());
            throw new Exception('Internal error check logs');
        }
    }
}