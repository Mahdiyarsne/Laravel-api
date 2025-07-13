<?php


namespace App\Trait;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

trait FileUploadTrait
{

    //اپلود فایل 
    function uploadImage(Request $request, $inputName, $path = '/uploads')
    {

        if ($request->hasFile($inputName)) {

            $image = $request->{$inputName};
            $ext = $image->getClientOriginalExtension();
            $imageName = 'media_' . uniqid() . '.' . $ext;
            $image->move(public_path($path), $imageName);

            return $path . '/' . $imageName;
        }
    }

    //حدف فایل
    function removeImage(string $path)
    {
        if (File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }
}
