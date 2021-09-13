<?php

namespace App\Traits;

use App\Enums\AttachmentTypes;
use App\Models\FileContent;
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
    public function saveFiles($request, $modelClass, $modelId, $deleteCurrent, int $type = AttachmentTypes::General): void
    {
        if ($deleteCurrent) {
            $attachments = FileContent::where('fileable_type', $modelClass)->where('fileable_id', $modelId)->get();
            foreach ($attachments as $attachment) {
                $attachment->delete();
            }
        }

        if ($request->hasFile('attachments')) {
            foreach ($request->attachments as $attachment) {
                $url = $this->saveFileAndGetUrl($attachment);
                if ($url) {
                    $attachment = new FileContent();
                    $attachment->fileable_type = $modelClass;
                    $attachment->fileable_id = $modelId;
                    $attachment->url = $url;
                    $attachment->type = $type;
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
        if (env('APP_ENV') === 'local') {
            Storage::disk('uploads')->delete($name);
        } else {
            Storage::disk('digitalocean')->delete($name);
        }
    }

    /**
     * @param $file
     * @param int $sizeX
     * @param int $sizeY
     * @return string
     */
    public function saveFileAndGetUrl($file, int $sizeX = 6000, int $sizeY = 4000): string
    {
        $url = '';
        $imageFormats = ['jpg', 'jpeg', 'gif', 'png'];
        if (in_array($file->extension(), $imageFormats, true)) {
            $img = Image::make($file->path());
            $img->orientate();
            $filename = Str::random(40).'.jpg';
            $image = $img->resize($sizeX, $sizeY, function ($const) {
                $const->aspectRatio();
            })->encode('jpg',80);
            if (config('app.env') === 'local') {
                Storage::disk('uploads')->put($filename, $image);
                $url = config('app.url').'/uploads/'.$filename;
            } else {
                Storage::disk('digitalocean')->put($filename, $image, ['visibility' => 'public']);
                $url = config('filesystems.disks.digitalocean.endpoint').'/'.config('filesystems.disks.digitalocean.bucket').'/'.$filename;
            }
        }
        return $url;
    }

    public function getProcessed($request, $dateFields = [])
    {
        $data = $request->toArray();
        foreach ($data as $key => $field) {
            if ($key === 'query') {
                $query = json_decode($field, true);
                $data['query'] = $query;
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
