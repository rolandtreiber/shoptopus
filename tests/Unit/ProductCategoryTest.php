<?php

namespace Tests\Unit;

use App\Models\DiscountRule;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProductCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected $product_category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->product_category = ProductCategory::factory()->create();
    }

    /** @test */
    public function it_has_a_name_field(): void
    {
        $this->assertNotNull($this->product_category->name);
    }

    /** @test */
    public function it_has_a_slug_generated_from_its_name_field(): void
    {
        $this->assertEquals(Str::slug($this->product_category->name), $this->product_category->slug);
    }

    /** @test */
    public function it_returns_a_translated_name(): void
    {
        $this->product_category
            ->setTranslation('name', 'en', 'english translation')
            ->setTranslation('name', 'de', 'german translation')
            ->save();

        $this->assertEquals('english translation', $this->product_category->name);

        app()->setLocale('de');

        $this->assertEquals('german translation', $this->product_category->name);
    }

    /** @test */
    public function it_has_a_description_field(): void
    {
        $this->assertNotNull($this->product_category->description);
    }

    /** @test */
    public function it_returns_a_translated_description(): void
    {
        $this->product_category
            ->setTranslation('description', 'en', 'english translation')
            ->setTranslation('description', 'de', 'german translation')
            ->save();

        $this->assertEquals('english translation', $this->product_category->description);

        app()->setLocale('de');

        $this->assertEquals('german translation', $this->product_category->description);
    }

    /** @test */
    public function it_has_a_menu_image_field(): void
    {
        $this->assertNull($this->product_category->menu_image);
    }

    /** @test */
    public function it_has_a_header_image_field(): void
    {
        $this->assertNull($this->product_category->header_image);
    }

    /** @test */
    public function it_has_an_enabled_field(): void
    {
        $this->assertTrue($this->product_category->enabled);
    }

    /** @test */
    public function it_may_belong_to_a_parent(): void
    {
        $this->assertNull($this->product_category->parent);

        $this->product_category->update([
            'parent_id' => ProductCategory::factory()->create()->id,
        ]);

        $this->product_category->refresh();

        $this->assertInstanceOf(ProductCategory::class, $this->product_category->fresh()->parent);
    }

    /** @test */
    public function it_has_children(): void
    {
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Collection::class, $this->product_category->children
        );

        $this->product_category->addChildCategory(ProductCategory::factory()->make());

        $this->assertInstanceOf(ProductCategory::class, $this->product_category->fresh()->children->first());
    }

//    /** @test */
//    public function it_can_return_all_the_categories_with_its_subcategories()
//    {
//        $sub_cat1 = $this->category->addSubcategory(Category::factory()->make());
//        $sub_cat2 = $sub_cat1->addSubcategory(Category::factory()->make());
//
//        $categories = Category::tree();
//
//        $this->assertEquals($categories->first()->subcategories->first()->id, $sub_cat1->id);
//        $this->assertEquals($categories->first()->subcategories->first()->subcategories->first()->id, $sub_cat2->id);
//    }
//
//    /** @test */
//    public function it_can_find_and_return_a_parent_category_and_its_subcategories()
//    {
//        $sub_cat1 = $this->category->addSubcategory(Category::factory()->create());
//        $sub_cat2 = $sub_cat1->addSubcategory(Category::factory()->create());
//        $sub_cat3 = $sub_cat2->addSubcategory(Category::factory()->create());
//        $sub_cat4 = $sub_cat3->addSubcategory(Category::factory()->create());
//
//        $categories = Category::tree($sub_cat1->id);
//
//        $this->assertEquals($categories->first()->subcategories->first()->id, $sub_cat2->id);
//        $this->assertEquals($categories->first()->subcategories->first()->subcategories->first()->id, $sub_cat3->id);
//        $this->assertEquals(
//            $categories->first()->subcategories->first()->subcategories->first()->subcategories->first()->id,
//            $sub_cat4->id
//        );
//    }

    /** @test */
    public function it_may_have_many_valid_discount_rules(): void
    {
        $this->assertCount(0, $this->product_category->discount_rules);

        $discount_rule = DiscountRule::factory()->create([
            'valid_from' => now()->toDateTimeString(),
            'valid_until' => now()->addDays(5)->toDateTimeString(),
        ]);

        $this->product_category->discount_rules()->attach($discount_rule->id);

        $this->assertCount(1, $this->product_category->fresh()->discount_rules);

        $this->assertInstanceOf(DiscountRule::class, $this->product_category->discount_rules()->first());

        $discount_rule->update([
            'valid_until' => now()->subDays(5)->toDateTimeString(),
        ]);

        $this->assertCount(0, $this->product_category->fresh()->discount_rules);
    }

    /** @test */
    public function it_may_have_many_products(): void
    {
        $this->assertCount(0, $this->product_category->products);

        $product = Product::factory()->create();

        $this->product_category->products()->attach($product->id);

        $this->assertCount(1, $this->product_category->fresh()->products);

        $this->assertInstanceOf(Product::class, $this->product_category->products()->first());
    }
}
