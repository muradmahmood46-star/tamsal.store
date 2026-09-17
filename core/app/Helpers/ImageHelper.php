<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageHelper
{
    public static function handleUploadedImage($file, $path, $delete = null)
    {
        if ($file) {

            if ($delete) {
                Storage::delete($path . '/' . $delete);
            }

            $name = Str::random(4) . $file->getClientOriginalName();
            Storage::putFileAs($path, $file, $name);

            return $name;
        }
    }


    public static function uploadSummernoteImage($file, $path)
    {

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        if ($file) {

            $name = 'OM_' . time() .  Str::random(8) . '.' . $file->getClientOriginalExtension();
            Storage::putFileAs($path, $file, $name);

            return $name;
        }
    }



    public static function ItemhandleUploadedImage($file, $path, $delete = null)
    {
        if ($file) {

            if ($delete) {
                Storage::delete($path . '/' . $delete);
            }

            $ext = $file->getClientOriginalExtension() ?: 'png';
            $photoName = 'OM_' . time() .  Str::random(8) . '.' . $ext;
            $thumbnailName = 'OM_' . time() .  Str::random(8) . '.' . $ext;

            Storage::putFileAs($path, $file, $photoName);

            try {
                $image = \Image::make($file)->resize(230, 230);
                $thumbnailPath = $path . '/' . $thumbnailName;
                Storage::put($thumbnailPath, (string) $image->encode());
            } catch (\Throwable $e) {
                Storage::putFileAs($path, $file, $thumbnailName);
            }

            return [$photoName, $thumbnailName];
        }
    }

    public static function handleUpdatedUploadedImage($file, $path, $data, $delete_path, $field)
    {
        $ext = $file->getClientOriginalExtension() ?: 'png';
        $name = 'OM_' . time() .  Str::random(8) . '.' . $ext;

        Storage::putFileAs($path, $file, $name);

        if ($data[$field] != null) {
            Storage::delete($delete_path . '/' . $data[$field]);
        }

        return $name;
    }


    public static function ItemhandleUpdatedUploadedImage($file, $path, $data, $delete_path, $field)
    {
        $ext = $file->getClientOriginalExtension() ?: 'png';
        $photoName = 'OM_' . time() .  Str::random(8) . '.' . $ext;
        $thumbnailName = 'OM_' . time() . Str::random(8) . '.' . $ext;

        try {
            $image = \Image::make($file)->resize(230, 230);
            $thumbnailPath = $path . '/' . $thumbnailName;
            Storage::put($thumbnailPath, (string) $image->encode());
        } catch (\Throwable $e) {
            Storage::putFileAs($path, $file, $thumbnailName);
        }

        $photoPath = $path . '/' . $photoName;
        Storage::putFileAs($path, $file, $photoName);

        if (!empty($data['thumbnail'])) {
            Storage::delete($delete_path . '/' . $data['thumbnail']);
        }

        if (!empty($data[$field])) {
            Storage::delete($delete_path . '/' . $data[$field]);
        }

        return [$photoName, $thumbnailName];
    }


    public static function handleDeletedImage($data, $field, $delete_path)
    {
        if (!empty($data[$field])) {
            Storage::delete($delete_path . '/' . $data[$field]);
        }
    }
}
