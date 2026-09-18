<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageHelper
{
    /**
     * Set elevated memory and execution limits for image processing operations
     */
    private static function prepareEnvironment()
    {
        @ini_set('memory_limit', '512M');
        @set_time_limit(300);
    }

    public static function handleUploadedImage($file, $path, $delete = null)
    {
        self::prepareEnvironment();

        if ($file) {
            if ($delete) {
                try {
                    Storage::delete($path . '/' . basename($delete));
                    Storage::delete($delete);
                } catch (\Throwable $e) {}
            }

            $ext = $file->getClientOriginalExtension() ?: 'png';
            $name = Str::random(6) . '_' . uniqid() . '.' . $ext;

            try {
                Storage::putFileAs($path, $file, $name);
            } catch (\Throwable $e) {
                try {
                    $targetDir = storage_path('app/public/' . $path);
                    if (!file_exists($targetDir)) {
                        @mkdir($targetDir, 0777, true);
                    }
                    @move_uploaded_file($file->getPathname(), $targetDir . '/' . $name);
                } catch (\Throwable $ex) {}
            }

            return $name;
        }
    }

    public static function uploadSummernoteImage($file, $path)
    {
        self::prepareEnvironment();

        if (!file_exists($path)) {
            @mkdir($path, 0777, true);
        }

        if ($file) {
            $ext = $file->getClientOriginalExtension() ?: 'png';
            $name = 'OM_' . time() . Str::random(8) . '.' . $ext;
            Storage::putFileAs($path, $file, $name);

            return $name;
        }
    }

    public static function ItemhandleUploadedImage($file, $path, $delete = null)
    {
        self::prepareEnvironment();

        if ($file) {
            if ($delete) {
                Storage::delete($path . '/' . $delete);
            }

            $ext = $file->getClientOriginalExtension() ?: 'png';
            $photoName = 'OM_' . time() . Str::random(8) . '.' . $ext;
            $thumbnailName = 'OM_thumb_' . time() . Str::random(8) . '.' . $ext;

            // Save main photo
            Storage::putFileAs($path, $file, $photoName);

            // Attempt resizing for thumbnail with fallback
            try {
                if (class_exists(\Image::class)) {
                    $image = \Image::make($file)->resize(230, 230, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    $thumbnailPath = $path . '/' . $thumbnailName;
                    Storage::put($thumbnailPath, (string) $image->encode());
                } else {
                    Storage::putFileAs($path, $file, $thumbnailName);
                }
            } catch (\Throwable $e) {
                Storage::putFileAs($path, $file, $thumbnailName);
            }

            return [$photoName, $thumbnailName];
        }
    }

    public static function handleUpdatedUploadedImage($file, $path, $data, $delete_path, $field)
    {
        self::prepareEnvironment();

        $ext = $file->getClientOriginalExtension() ?: 'png';
        $name = 'OM_' . time() . Str::random(8) . '.' . $ext;

        Storage::putFileAs($path, $file, $name);

        if (!empty($data[$field])) {
            Storage::delete($delete_path . '/' . $data[$field]);
        }

        return $name;
    }

    public static function ItemhandleUpdatedUploadedImage($file, $path, $data, $delete_path, $field)
    {
        self::prepareEnvironment();

        $ext = $file->getClientOriginalExtension() ?: 'png';
        $photoName = 'OM_' . time() . Str::random(8) . '.' . $ext;
        $thumbnailName = 'OM_thumb_' . time() . Str::random(8) . '.' . $ext;

        // Attempt resizing for thumbnail with fallback
        try {
            if (class_exists(\Image::class)) {
                $image = \Image::make($file)->resize(230, 230, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $thumbnailPath = $path . '/' . $thumbnailName;
                Storage::put($thumbnailPath, (string) $image->encode());
            } else {
                Storage::putFileAs($path, $file, $thumbnailName);
            }
        } catch (\Throwable $e) {
            Storage::putFileAs($path, $file, $thumbnailName);
        }

        // Save main photo
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
