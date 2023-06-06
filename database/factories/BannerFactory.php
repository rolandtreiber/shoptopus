<?php

namespace Database\Factories;

use App\Traits\TranslatableFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

class BannerFactory extends Factory
{
    use TranslatableFactory;

    /**
     * Define the model's default state.
     *
     *
     * @throws \Exception
     */
    public function definition(): array
    {
        $translated = $this->getTranslated($this->faker, ['title', 'description', 'button_text'], ['short', 'medium', 'word']);

        $showButton = $this->faker->boolean();
        $buttonText = $translated['button_text'];
        $buttonUrl = 'https://google.com';

        return [
            'title' => $translated['title'],
            'description' => $translated['description'],
            'background_image' => [
                'url' => 'https://picsum.photos/1200/450',
                'file_name' => '',
            ],
            'show_button' => $showButton,
            'button_text' => $showButton ? $buttonText : null,
            'button_url' => $showButton ? $buttonUrl : null,
            'enabled' => $this->faker->boolean(),
            'total_clicks' => random_int(10, 200),
        ];
    }
}
