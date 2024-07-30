<?php

namespace App\Components\Orders\Repository;

use App\Components\Orders\Contracts\ImagePathsInterface;
use Bloomex\Common\Blca\Models\BlcaOrderHistoryImage;

class HistoryImageMutator
{
    /**
     * @param BlcaOrderHistoryImage $orderHistoryImage
     */
    public function __construct(
        protected readonly BlcaOrderHistoryImage $orderHistoryImage
    ) {
    }

    public function createHistoryImage(int $historyId, ImagePathsInterface $imagePaths): BlcaOrderHistoryImage
    {
        return $this->orderHistoryImage->newModelQuery()
            ->create([
                'history_id' => $historyId,
                'image_link' => $imagePaths->getFileFullPath(),
                'thumb_link' => $imagePaths->getFileThumbPath(),
            ]);
    }

    public function removeHistoryImage(int $imageId): bool
    {
        return $this->orderHistoryImage->newModelQuery()
            ->where('id', $imageId)
            ->delete();
    }
}