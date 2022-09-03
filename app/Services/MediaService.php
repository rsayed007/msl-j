<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class MediaService
{
    public function uploadImage(UploadedFile $file)
    {
        $original_ext = $file->getClientOriginalExtension();
        $dataImage = md5(Str::random(10)) . '.' . $original_ext;
        $destinationPath =  productFilePath();
        $file->move($destinationPath, $dataImage);

        return  $destinationPath . '' . $dataImage;
    }
}
