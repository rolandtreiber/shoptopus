<?php

namespace App\Traits;

use App\Enums\FileType;
use App\Models\FileContent;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

trait ProcessRequest
{
    public function saveFiles($request, $modelClass, $modelId, $deleteCurrent): Collection
    {
        if ($deleteCurrent) {
            $attachments = FileContent::where('fileable_type', $modelClass)->where('fileable_id', $modelId)->where('type', FileType::Image)->get();
            foreach ($attachments as $attachment) {
                $attachment->delete();
            }
        }

        $files = new Collection();

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
                    $files->add($attachment);
                }
            }
        }

        return $files;
    }

    public function deleteCurrentFile($name): void
    {
        if (env('APP_ENV') === 'development' || env('APP_ENV') === 'local' || config('app.env') === 'testing') {
            Storage::disk('uploads')->delete($name);
        } else {
            Storage::disk('digitalocean')->delete($name);
        }
    }

    public function deleteCurrentPaidFile($name): void
    {
        if (env('APP_ENV') === 'development' || env('APP_ENV') === 'local' || config('app.env') === 'testing') {
            Storage::disk('paid')->delete($name);
        } else {
            Storage::disk('digitalocean')->delete($name);
        }
    }

    public function saveFileAndGetUrl($file, int $sizeX = 1024, int $sizeY = 768): ?array
    {
        // Images
        $imageFormats = ['jpg', 'jpeg', 'gif', 'png', 'webp'];
        $spreadsheetFormats = ['xls', 'xlsx', 'csv'];
        $textFormats = ['txt', 'doc', 'docx'];
        $audioFormats = ['mp3', 'wma', 'wav', 'ogg'];
        $videoFormats = ['avi', 'mpg', 'mpeg'];
        $fileType = FileType::Other;
        if (in_array($file->extension(), $imageFormats, true)) {
            $img = Image::make($file->path());
            $img->orientate();
            $fileName = Str::random(40).'.jpg';
            $data = $img->resize($sizeX, $sizeY, function ($const) {
                $const->aspectRatio();
            })->encode('jpg', 80);
            if (config('app.env') === 'development' || config('app.env') === 'local' || config('app.env') === 'testing') {
                Storage::disk('uploads')->put($fileName, $data);
                $url = config('app.url').'/uploads/'.$fileName;
            } else {
                Storage::disk('digitalocean')->put($fileName, $data, ['visibility' => 'public']);
                $url = config('filesystems.disks.digitalocean.endpoint').'/'.config('filesystems.disks.digitalocean.bucket').'/'.$fileName;
            }
        } else {
            $fileName = Str::random(40).'.'.strtolower($file->extension());
            /** @var File $data */
            $data = $file;
            if (config('app.env') === 'development' || config('app.env') === 'local' || config('app.env') === 'testing') {
                Storage::disk('uploads')->put($fileName, $data->getContent());
                $url = config('app.url').'/uploads/'.$fileName;
            } else {
                Storage::disk('digitalocean')->put($fileName, $data->getContent(), ['visibility' => 'public']);
                $url = config('filesystems.disks.digitalocean.endpoint').'/'.config('filesystems.disks.digitalocean.bucket').'/'.$fileName;
            }
        }

        // Image
        if (in_array(strtolower($file->extension()), $imageFormats, true)) {
            $fileType = FileType::Image;
        }

        // PDF
        if (strtolower($file->extension()) === 'pdf') {
            $fileType = FileType::Pdf;
        }

        // Spreadsheet
        if (in_array(strtolower($file->extension()), $spreadsheetFormats, true)) {
            $fileType = FileType::Spreadsheet;
        }

        // Text
        if (in_array(strtolower($file->extension()), $textFormats, true)) {
            $fileType = FileType::TextDocument;
        }

        // Audio
        if (in_array(strtolower($file->extension()), $audioFormats, true)) {
            $fileType = FileType::Audio;
        }

        // Video
        if (in_array(strtolower($file->extension()), $videoFormats, true)) {
            $fileType = FileType::Video;
        }

        return [
            'type' => $fileType,
            'url' => $url,
            'file_name' => $fileName,
        ];
    }

    public function savePaidFileAndGetUrl(UploadedFile $file): ?array
    {
        $fileName = Str::random(40).'.'.strtolower($file->extension());
        /** @var File $data */
        $data = $file;
        if (config('app.env') === 'development' || config('app.env') === 'local' || config('app.env') === 'testing') {
            Storage::disk('paid')->put($fileName, $data->getContent());
            $url = config('app.url').'/api/download-paid-file/'.$fileName.'?token=';
        } else {
            // TODO: optimize this for production
            Storage::disk('digitalocean')->put($fileName, $data->getContent(), ['visibility' => 'public']);
            $url = config('filesystems.disks.digitalocean.endpoint').'/'.config('filesystems.disks.digitalocean.bucket').'/'.$fileName;
        }

        return [
            'type' => FileType::DownloadOnly,
            'url' => $url,
            'file_name' => $fileName,
            'size' => $file->getSize()
        ];
    }

    /**
     * @param $request
     * @param $dateFields
     * @param $jsonFields
     * @return mixed
     */
    public function getProcessed($request, $dateFields = [], $jsonFields = []): mixed
    {
        $data = !is_array($request) ? $request->toArray() : $request;
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
            if (! is_array($field)) {
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
