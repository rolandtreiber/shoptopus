<?php

namespace App\Helpers;

use App\Enums\DiscountTypes;
use App\Enums\RandomStringModes;
use Carbon\Carbon;

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
            case RandomStringModes::UppercaseLowercaseAndNumbers:
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case RandomStringModes::UppercaseAndNumbers:
                $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case RandomStringModes::LowecaseAndNumbers:
                $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
                break;
            case RandomStringModes::UppercaseAndLowecase:
                $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case RandomStringModes::NumbersOnly:
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
            case DiscountTypes::Percentage:
                return $value.'%';
            case DiscountTypes::Amount:
                if (config('app.default_currency.side') === 'left') {
                    return config('app.default_currency.symbol') . $value;
                } else {
                    return $value.config('app.default_currency.symbol');
                }
        }
        return $amount;
    }

    public static function getDiscountedValue($type, $amount, $basis) {
        $discounted = 0;
        switch ($type) {
            case DiscountTypes::Amount:
                $discounted = $basis - $amount;
                break;
            case DiscountTypes::Percentage:
                $discounted = round($basis - (($basis / 100) * $amount), 2);
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
}
