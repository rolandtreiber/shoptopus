<?php

namespace Database\Factories;

use App\Helpers\GeneralHelper;
use App\Traits\TranslatableFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductTagFactory extends Factory
{
    use TranslatableFactory;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $translations = $this->getTranslated($this->faker, ['name', 'description'], ['word', 'medium']);

        $hasBadge = $this->faker->boolean();

        $badge = GeneralHelper::getPhotoFromSamples('tags', 'tag');

        $result = [
            'name' => $translations['name'],
            'description' => null,
            'badge' => null,
            'display_badge' => false,
            'enabled' => true,
        ];

        if (env('APP_ENV') !== 'testing') {
            $result['description'] = $translations['description'];
            $result['display_badge'] = $hasBadge;
            $result['badge'] = $hasBadge ? [
                'url' => $badge['url'],
                'file_name' => $badge['file_name'],
            ] : null;
        }

        return $result;
    }
}
