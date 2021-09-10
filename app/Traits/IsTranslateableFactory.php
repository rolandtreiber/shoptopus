<?php

namespace App\Traits;

trait IsTranslateableFactory {

    /**
     * @param $faker
     * @param array $fields
     * @param String $type
     * @return array
     */
    public function getTranslated($faker, array $fields, array $types): array
    {
        $supportedLocales = config('app.locales_supported');
        $result = [];
        $typeCount = 0;
        foreach ($fields as $field) {
            $value = [];
            foreach ($supportedLocales as $localeKey => $localeValue) {
                switch ($types[$typeCount]) {
                    case 'short':
                        $value[$localeKey] = $faker->words(5, true);
                        break;
                    case 'medium':
                        $value[$localeKey] = $faker->words(10, true);
                        break;
                    case 'long':
                        $value[$localeKey] = $faker->words(20, true);
                        break;
                }
            }
            $result[$field] = $value;
            $typeCount++;
        }
        return $result;
    }

}
