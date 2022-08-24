<?php

namespace Database\Factories;

use App\Helpers\GeneralHelper;
use App\Models\ProductCategory;
use App\Traits\TranslatableFactory;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductCategoryFactory extends Factory
{
    use TranslatableFactory;
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     * @throws Exception
     */
    public function definition(): array
    {
        $translations = $this->getTranslated($this->faker, ['name', 'description'], ['word', 'medium']);

        $result = [
            'name' => $translations['name'],
            'description' => $translations['description'],
            'parent_id' => null,
            'enabled' => true,
            'menu_image' => null,
            'header_image' => null,
            ];

        if (env('APP_ENV') !== 'testing') {
            $imageId = random_int(1, 10);
            $menuImage = GeneralHelper::getPhotoFromSamples('categories/menu', 'menu', $imageId);
            $headerImage = GeneralHelper::getPhotoFromSamples('categories/banner', 'banner', $imageId);
            $result['menu_image'] = [
                'url' => $menuImage['url'],
                'file_name' => $menuImage['file_name']
            ];
            $result['header_image'] = [
                'url' => $headerImage['url'],
                'file_name' => $headerImage['file_name']
            ];
        }

        return $result;

    }
}
