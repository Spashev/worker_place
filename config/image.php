<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Image Driver
    |--------------------------------------------------------------------------
    |
    | Intervention Image supports “GD Library” and “Imagick” to process images
    | internally. Depending on your PHP setup, you can choose one of them.
    |
    | Included options:
    |   - \Intervention\Image\Drivers\Gd\Driver::class
    |   - \Intervention\Image\Drivers\Imagick\Driver::class
    |
    */

    'driver' => \Intervention\Image\Drivers\Imagick\Driver::class,
    'disk' => env('FILESYSTEM_DISK', 'local'),
    'folder' => 'packager',
    'separator' => '/',
    'file_extension' => 'png',
    'original_height' => 1080,  //1920 × 1080
    'thumb_height' => 176, //176 x 99
];
