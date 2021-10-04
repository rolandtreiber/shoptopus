<?php

namespace Database\Factories;

use App\Models\Banner;
use App\Traits\IsTranslateableFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

class BannerFactory extends Factory
{
    use IsTranslateableFactory;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Banner::class;

    /**
     * Define the model's default state.
     *
     * @return array
     * @throws \Exception
     */
    public function definition(): array
    {
        $translated = $this->getTranslated($this->faker, ['title', 'description', 'button_text'], ['short', 'medium', 'word']);

        $showButton = $this->faker->boolean;
        $buttonText = $translated['button_text'];
        $buttonUrl = 'https://google.com';

        return [
            'title' => $translated['title'],
            'description' => $translated['description'],
            'background_image' => [
                'url' => $this->faker->imageUrl(1200, 450),
                'file_name' => ''
            ],
            'show_button' => $showButton,
            'button_text' => $showButton ? $buttonText : null,
            'button_url' => $showButton ? $buttonUrl : null,
            'enabled' => $this->faker->boolean,
            'total_clicks' => random_int(10, 200)
        ];
    }
}
