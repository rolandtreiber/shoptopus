<?php

namespace App\Traits;

trait TranslatableFactory
{
    public function getTranslated($faker, array $fields, array $types): array
    {
        $supportedLocales = config('app.locales_supported');
        $result = [];
        $typeCount = 0;
        foreach ($fields as $field) {
            $value = [];
            foreach ($supportedLocales as $localeKey => $localeValue) {
                $value[$localeKey] = match ($types[$typeCount]) {
                    'word' => $faker->word(true),
                    'short' => $faker->words(5, true),
                    'medium' => $faker->words(10, true),
                    'long' => $faker->words(20, true),
                };
            }
            $result[$field] = $value;
            $typeCount++;
        }

        return $result;
    }
}
