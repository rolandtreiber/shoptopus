<?php

namespace App\Helpers;

use App\Enums\DiscountType;
use App\Enums\FileType;
use App\Enums\RandomStringMode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GeneralHelper {

    public static function fromCamelCase($input) {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        $result = implode(' ', $ret);
        return strtoupper($result);
    }

    public static function addSpacesBeforeCapitalLetters($input)
    {
        return preg_replace('/(?<!\ )[A-Z]/', ' $0', $input);
    }

    /**
     * @param int $length
     * @param int $mode
     * @return string
     */
    public static function generateRandomString(int $length = 10, int $mode = 0): string
    {
        switch ($mode) {
            case RandomStringMode::UppercaseLowercaseAndNumbers:
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case RandomStringMode::UppercaseAndNumbers:
                $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case RandomStringMode::LowecaseAndNumbers:
                $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
                break;
            case RandomStringMode::UppercaseAndLowecase:
                $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case RandomStringMode::NumbersOnly:
                $characters = '0123456789';
                break;
            default:
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * @param $url
     * @return string
     */
    public static function imageToDataUrl($url): string
    {
        $imageData = base64_encode(file_get_contents($url));
        return 'data: '.mime_content_type($url).';base64,'.$imageData;
    }

    public static function getCleanHtml($text)
    {
        return str_replace("&nbsp;", '', preg_replace( "/\r|\n/", "", $text));
    }

    /**
     * @param null $timestamp
     */
    public static function getFormattedTimestamp($timestamp = null): string
    {
        $now = Carbon::now();
        $timestampRaw = $timestamp ? Carbon::parse($timestamp) : Carbon::now();
        if ($now->year == $timestampRaw->year && $now->month == $timestampRaw->month && $now->day == $timestampRaw->day) {
            $timestampFormatted = $timestampRaw->format('H:i');
        } else if ($now->year == $timestampRaw->year) {
            $timestampFormatted = $timestampRaw->format('d-m H:i');
        } else {
            $timestampFormatted = $timestampRaw->format('d-m-Y H:i');
        }
        return $timestampFormatted;
    }

    public static function getDiscountValue($type, $amount) {
        $value = str_replace('.00', '', $amount);
        switch ($type) {
            case DiscountType::Percentage:
                return $value.'%';
            case DiscountType::Amount:
                if (config('app.default_currency.side') === 'left') {
                    return config('app.default_currency.symbol') . $value;
                } else {
                    return $value.config('app.default_currency.symbol');
                }
        }
        return $amount;
    }

    public static function getDiscountedValue($discountType, $discountAmount, $basePrice) : mixed
    {
        $discounted = 0;

        switch ($discountType) {
            case DiscountType::Amount:
                $discounted = $basePrice - $discountAmount;
                break;
            case DiscountType::Percentage:
                $discounted = round($basePrice - (($basePrice / 100) * $discountAmount), 2);
                break;
            default:
        }

        return $discounted > 0 ? $discounted : 0;
    }

    /**
     * @param $amount
     * @return string
     */
    public static function displayPrice($amount): string
    {
        if (config('app.default_currency.side') === 'left') {
            return config('app.default_currency.symbol') . $amount;
        } else {
            return $amount . config('app.default_currency.symbol');
        }
    }

    public static function getRandomPhotoFromSamples()
    {
        $dir = storage_path('app/test-data-images');
        $files = glob($dir . '/*.*');
        $file = $files[array_rand($files)];

        $contents = file_get_contents($file);

        $extension = 'jpg';
        $fileName = Str::random(40) . '.' . $extension;

        Storage::disk('local')->delete('public/uploads/' . $fileName);
        Storage::disk('uploads')->put($fileName, $contents);
        $url = config('app.url') . '/uploads/' . $fileName;

        return [
            'type' => FileType::Image,
            'url' => $url,
            'file_name' => $fileName
        ];
    }
}
