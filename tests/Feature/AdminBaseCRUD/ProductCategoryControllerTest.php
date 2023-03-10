<?php

namespace Tests\Feature\AdminBaseCRUD;

use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\AdminControllerTestCase;

/**
 * @group admin-base-crud
 * @group product_categories
 *
 * @see \App\Http\Controllers\Admin\ProductCategoryController
 */
class ProductCategoryControllerTest extends AdminControllerTestCase
{
    use RefreshDatabase;
    // Happy

    /**
     * @test
     */
    public function test_product_categories_can_be_listed()
    {
        $rootCategories = ProductCategory::factory()->count(2)->create();
        $childCategory = ProductCategory::factory()->state([
            'parent_id' => $rootCategories[0]->id,
        ])->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.api.index.product-categories', [
            'page' => 1,
            'paginate' => 20,
            'filters' => [],
        ]));
        $response->assertJsonFragment([
            'id' => $rootCategories[0]->id,
        ]);
        $response->assertJsonFragment([
            'id' => $rootCategories[1]->id,
        ]);
        $response->assertJsonFragment([
            'id' => $childCategory->id,
        ]);
    }

    /**
     * @test
     */
    public function test_product_category_can_be_created()
    {
        Storage::fake('uploads');
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->post(route('admin.api.create.product-category'), [
            'name' => json_encode([
                'en' => 'Test Category',
                'de' => 'Test Kategorie',
            ]),
            'description' => json_encode([
                'en' => 'Test Category Description',
                'de' => 'Test Kategorie Beschreibung',
            ]),
            'header_image' => UploadedFile::fake()->image('product_category_header_image1.jpg'),
            'menu_image' => UploadedFile::fake()->image('product_category_menu_image1.jpg'),
        ]);
        $productCategoryId = $response->json()['data']['id'];
        $productCategory = ProductCategory::find($productCategoryId);
        Storage::disk('uploads')->assertExists($productCategory->header_image->file_name);
        Storage::disk('uploads')->assertExists($productCategory->menu_image->file_name);
        $this->assertEquals('Test Category', $productCategory->setLocale('en')->name);
        $this->assertEquals('Test Kategorie', $productCategory->setLocale('de')->name);
        $this->assertEquals('Test Category Description', $productCategory->setLocale('en')->description);
        $this->assertEquals('Test Kategorie Beschreibung', $productCategory->setLocale('de')->description);
    }

    /**
     * @test
     */
    public function test_product_category_can_be_updated()
    {
        Storage::fake('uploads');
        $productCategory = ProductCategory::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->patch(route('admin.api.update.product-category', [
            'category' => $productCategory->id,
        ]), [
            'name' => json_encode([
                'en' => 'Updated Test Category',
                'de' => 'Aktualisiert Test Kategorie',
            ]),
            'description' => json_encode([
                'en' => 'Updated Test Category Description',
                'de' => 'Aktualisiert Test Kategorie Beschreibung',
            ]),
            'header_image' => UploadedFile::fake()->image('product_category_header_image1.jpg'),
            'menu_image' => UploadedFile::fake()->image('product_category_menu_image1.jpg'),
        ]);

        $productCategory = ProductCategory::find($productCategory->id);
        Storage::disk('uploads')->assertExists($productCategory->header_image->file_name);
        Storage::disk('uploads')->assertExists($productCategory->menu_image->file_name);
        $this->assertEquals('Updated Test Category', $productCategory->setLocale('en')->name);
        $this->assertEquals('Aktualisiert Test Kategorie', $productCategory->setLocale('de')->name);
        $this->assertEquals('Updated Test Category Description', $productCategory->setLocale('en')->description);
        $this->assertEquals('Aktualisiert Test Kategorie Beschreibung', $productCategory->setLocale('de')->description);
    }

    /**
     * @test
     */
    public function test_product_category_can_be_shown()
    {
        $productCategory = ProductCategory::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.api.show.product-category', [
            'category' => $productCategory->id,
        ]))->json();
        $productCategoryId = $response['data']['id'];
        $name = $response['data']['name'];
        $description = $response['data']['description'];
        $this->assertEquals($productCategoryId, $productCategory->id);
        $this->assertEquals(json_encode($name), json_encode($productCategory->getTranslations('name')));
        $this->assertEquals(json_encode($description), json_encode($productCategory->getTranslations('description')));
    }

    /**
     * @test
     */
    public function test_product_category_can_be_deleted()
    {
        $productCategory = ProductCategory::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $this->delete(route('admin.api.delete.product-category', $productCategory));

        $this->assertSoftDeleted($productCategory);
    }

    // Unhappy

    /**
     * @test
     */
    public function test_product_category_create_validation()
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->post(route('admin.api.create.product-category'), [
            'description' => json_encode([
                'en' => 'Test Category Description',
                'de' => 'Test Kategorie Beschreibung',
            ]),
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_product_category_delete_permission()
    {
        $this->actingAs(User::where('email', 'storeassistant@m.com')->first());
        $productCategory = ProductCategory::factory()->create();
        $response = $this->delete(route('admin.api.delete.product-category', $productCategory));
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_all_subcategories_disabled_when_parent_category_disabled()
    {
        $topLevelProductCategory = ProductCategory::factory()->state([
            'enabled' => true,
        ])->create();
        $firstLevelProductCategory = ProductCategory::factory()->state([
            'parent_id' => $topLevelProductCategory->id,
            'enabled' => true,
        ])->create();
        $secondLevelProductCategory = ProductCategory::factory()->state([
            'parent_id' => $firstLevelProductCategory->id,
            'enabled' => true,
        ])->create();
        $thirdLevelProductCategory = ProductCategory::factory()->state([
            'parent_id' => $secondLevelProductCategory->id,
            'enabled' => true,
        ])->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->patch(route('admin.api.update.product-category', [
            'category' => $topLevelProductCategory->id,
        ]), [
            'enabled' => false,
        ]);
        $this->assertEquals($thirdLevelProductCategory->refresh()->enabled, false);
        $this->assertEquals($secondLevelProductCategory->refresh()->enabled, false);
        $this->assertEquals($firstLevelProductCategory->refresh()->enabled, false);
    }

    /**
     * @test
     */
    public function test_all_parent_categories_enabled_when_subcategory_enabled()
    {
        $topLevelProductCategory = ProductCategory::factory()->state([
            'enabled' => false,
        ])->create();
        $firstLevelProductCategory = ProductCategory::factory()->state([
            'parent_id' => $topLevelProductCategory->id,
            'enabled' => false,
        ])->create();
        $secondLevelProductCategory = ProductCategory::factory()->state([
            'parent_id' => $firstLevelProductCategory->id,
            'enabled' => false,
        ])->create();
        $thirdLevelProductCategory = ProductCategory::factory()->state([
            'parent_id' => $secondLevelProductCategory->id,
            'enabled' => false,
        ])->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->patch(route('admin.api.update.product-category', [
            'category' => $thirdLevelProductCategory->id,
        ]), [
            'enabled' => true,
        ]);
        $this->assertEquals($secondLevelProductCategory->refresh()->enabled, true);
        $this->assertEquals($firstLevelProductCategory->refresh()->enabled, true);
        $this->assertEquals($topLevelProductCategory->refresh()->enabled, true);
    }
}
