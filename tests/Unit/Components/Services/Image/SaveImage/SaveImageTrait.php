<?php

namespace Tests\Unit\Components\Services\Image\SaveImage;

use App\Http\Requests\Order\ImageRequest;
use Mockery as moc;
use Illuminate\Http\UploadedFile;

trait SaveImageTrait
{
    public function makeRequest()
    {
        $upload_file = UploadedFile::fake()->image('myImage.jpg')->size(500);
        $request = moc::mock(ImageRequest::class);
        $request->shouldReceive('getImage')->once()->andReturn($upload_file);
        $request->shouldReceive('getExtension')->once()->andReturn('jpg');

        return $request;
    }
}