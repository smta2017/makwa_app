<?php

namespace App\Repositories\helpers;

use Storage;

class GeneralHelperRepository
{
    public function s3uploadImageBase64($disk, $fullImgCode, $savePath)
    {
        $base64_str = substr($fullImgCode, strpos($fullImgCode, ",") + 1);
        $image = base64_decode($base64_str);
        return  Storage::disk($disk)->put($savePath, $image);
    }
}
