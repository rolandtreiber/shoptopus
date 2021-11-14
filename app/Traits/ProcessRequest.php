<?php

namespace App\Traits;

use App\Enums\AttachmentTypes;
use App\Enums\FileTypes;
use App\Models\FileContent;
use Illuminate\Http\File;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

trait ProcessRequest
{

    /**
     * @param $request
     * @param $modelClass
     * @param $modelId
     * @param $deleteCurrent
     * @param int $type
     */
    public function saveFiles($request, $modelClass, $modelId, $deleteCurrent): void
    {
        if ($deleteCurrent) {
            $attachments = FileContent::where('fileable_type', $modelClass)->where('fileable_id', $modelId)->where('type', FileTypes::Image)->get();
            foreach ($attachments as $attachment) {
                $attachment->delete();
            }
        }

        if ($request->hasFile('attachments')) {
            foreach ($request->attachments as $attachment) {
                $file = $this->saveFileAndGetUrl($attachment);
                if ($file) {
                    $attachment = new FileContent();
                    $attachment->fileable_type = $modelClass;
                    $attachment->fileable_id = $modelId;
                    $attachment->url = $file['url'];
                    $attachment->file_name = $file['file_name'];
                    $attachment->type = $file['type'];
                    $attachment->save();
                }
            }
        }
    }

    /**
     * @param $name
     */
    public function deleteCurrentFile($name): void
    {
        if (env('APP_ENV') === 'local' || config('app.env') === 'testing') {
            Storage::disk('uploads')->delete($name);
        } else {
            Storage::disk('digitalocean')->delete($name);
        }
    }

    /**
     * @param $file
     * @param int $sizeX
     * @param int $sizeY
     * @return array|null
     */
    public function saveFileAndGetUrl($file, int $sizeX = 1024, int $sizeY = 768): ?array
    {
        // Images
        $imageFormats = ['jpg', 'jpeg', 'gif', 'png'];
        $spreadsheetFormats = ['xls', 'xlsx', 'csv'];
        $textFormats = ['txt', 'doc', 'docx'];
        $audioFormats = ['mp3', 'wma', 'wav', 'ogg'];
        $videoFormats = ['avi', 'mpg', 'mpeg'];
        $fileType = FileTypes::Other;
        if (in_array($file->extension(), $imageFormats, true)) {
            $img = Image::make($file->path());
            $img->orientate();
            $fileName = Str::random(40) . '.jpg';
            $data = $img->resize($sizeX, $sizeY, function ($const) {
                $const->aspectRatio();
            })->encode('jpg', 80);
            if (config('app.env') === 'local' || config('app.env') === 'testing') {
                Storage::disk('uploads')->put($fileName, $data);
                $url = config('app.url') . '/uploads/' . $fileName;
            } else {
                Storage::disk('digitalocean')->put($fileName, $data, ['visibility' => 'public']);
                $url = config('filesystems.disks.digitalocean.endpoint') . '/' . config('filesystems.disks.digitalocean.bucket') . '/' . $fileName;
            }
        } else {
            $fileName = Str::random(40) . '.' . strtolower($file->extension());
            /** @var File $data */
            $data = $file;
            if (config('app.env') === 'local' || config('app.env') === 'testing') {
                Storage::disk('uploads')->put($fileName, $data->getContent());
                $url = config('app.url') . '/uploads/' . $fileName;
            } else {
                Storage::disk('digitalocean')->put($fileName, $data->getContent(), ['visibility' => 'public']);
                $url = config('filesystems.disks.digitalocean.endpoint') . '/' . config('filesystems.disks.digitalocean.bucket') . '/' . $fileName;
            }
        }

        // PDF
        if (strtolower($file->extension()) === 'pdf') {
            $fileType = FileTypes::Pdf;
        }

        // Spreadsheet
        if (in_array(strtolower($file->extension()), $spreadsheetFormats, true)) {
            $fileType = FileTypes::Spreadsheet;
        }

        // Text
        if (in_array(strtolower($file->extension()), $textFormats, true)) {
            $fileType = FileTypes::TextDocument;
        }

        // Audio
        if (in_array(strtolower($file->extension()), $audioFormats, true)) {
            $fileType = FileTypes::Audio;
        }

        // Video
        if (in_array(strtolower($file->extension()), $videoFormats, true)) {
            $fileType = FileTypes::Video;
        }

        return [
            'type' => $fileType,
            'url' => $url,
            'file_name' => $fileName
        ];
    }

    public function getProcessed($request, $dateFields = [], $jsonFields = [])
    {
        $data = $request->toArray();
        foreach ($data as $key => $field) {
            if (in_array($key, $jsonFields)) {
                $query = json_decode($field, true);
                $data[$key] = $query;
            }
            if ($key === 'location') {
                $location = json_decode($field, true);
                $data['lat'] = $location['lat'];
                $data['lng'] = $location['lng'];
            }
            if (in_array($key, $dateFields)) {
                $data[$key] = Carbon::parse(str_replace('"', '', $field));
            }
            if ($field === 'false') {
                $data[$key] = 0;
            }
            if ($field === 'true') {
                $data[$key] = 1;
            }
            if ($field === 'null') {
                unset($data[$key]);
            }
            if (!is_array($field)) {
                if (json_decode($field)) {
                    $decoded = json_decode($field, true);
                    if (is_array($decoded)) {
                        if (array_key_exists('value', $decoded)) {
                            $data[$key] = $decoded['value'];
                        }
                    }
                }
            }
        }
        return $data;
    }

}
