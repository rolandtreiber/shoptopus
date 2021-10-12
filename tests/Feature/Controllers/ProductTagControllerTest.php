<?php

namespace Tests\Feature\Controllers;

use App\Models\Product;
use App\Models\ProductTag;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

/**
 * @group product_tags
 * @see \App\Http\Controllers\Admin\ProductTagController
 */
class ProductTagControllerTest extends TestCase
{
    /**
     * @test
     */
    public function test_product_tags_can_be_listed()
    {
        $tags = ProductTag::factory()->count(2)->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.api.index.product-tags', [
            'page' => 1,
            'paginate' => 20,
            'filters' => []
        ]));
        $response->assertJsonFragment([
            'id' => $tags[0]->id
        ]);
        $response->assertJsonFragment([
            'id' => $tags[1]->id
        ]);
    }

    /**
     * @test
     */
    public function test_product_tag_can_be_shown()
    {
        $tag = ProductTag::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.api.show.product-tag', [
            'tag' => $tag->id,
            'page' => 1,
            'paginate' => 20,
            'filters' => []
        ]));
        $response->assertJsonFragment([
            'id' => $tag->id
        ]);
    }

    /**
     * @test
     */
    public function test_product_tag_can_be_created()
    {
        Storage::fake('uploads');
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->post(route('admin.api.create.product-tag'), [
            'name' => json_encode([
                'en' => 'Plant Based',
                'de' => 'Auf Pflanzlicher Basis'
            ]),
            'description' => json_encode([
                'en' => 'Does not contain meat or dairy products',
                'de' => 'Enthält kein Fleisch oder Milchprodukte'
            ]),
            'badge' => UploadedFile::fake()->image('product_tag.jpg'),
            'display_badge' => 1
        ]);
        $response->assertCreated();
        $tagId = $response->json()['data']['id'];
        $tag = ProductTag::find($tagId);
        Storage::disk('uploads')->assertExists($tag->badge->file_name);
        $this->assertEquals('Plant Based', $tag->setLocale('en')->name);
        $this->assertEquals('Auf Pflanzlicher Basis', $tag->setLocale('de')->name);
        $this->assertEquals('Does not contain meat or dairy products', $tag->setLocale('en')->description);
        $this->assertEquals('Enthält kein Fleisch oder Milchprodukte', $tag->setLocale('de')->description);
    }

    /**
     * @test
     */
    public function test_product_tag_can_be_updated()
    {
        Storage::fake('uploads');
        $tag = ProductTag::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->patch(route('admin.api.update.product-tag', [
            'tag' => $tag
        ]), [
            'name' => json_encode([
                'en' => 'Plant Based',
                'de' => 'Auf Pflanzlicher Basis'
            ]),
            'description' => json_encode([
                'en' => 'Does not contain meat or dairy products',
                'de' => 'Enthält kein Fleisch oder Milchprodukte'
            ]),
            'badge' => UploadedFile::fake()->image('product_tag.jpg'),
            'display_badge' => 1
        ]);
        $response->assertOk();
        $tagId = $response->json()['data']['id'];
        $this->assertEquals($tagId, $tag->id);
        $tag = ProductTag::find($tagId);
        Storage::disk('uploads')->assertExists($tag->badge->file_name);
        $this->assertEquals('Plant Based', $tag->setLocale('en')->name);
        $this->assertEquals('Auf Pflanzlicher Basis', $tag->setLocale('de')->name);
        $this->assertEquals('Does not contain meat or dairy products', $tag->setLocale('en')->description);
        $this->assertEquals('Enthält kein Fleisch oder Milchprodukte', $tag->setLocale('de')->description);
    }

    /**
     * @test
     */
    public function test_product_tag_can_be_deleted()
    {
        $tag = ProductTag::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $this->delete(route('admin.api.delete.product-tag', $tag));

        $this->assertSoftDeleted($tag);
    }

    /**
     * @test
     */
    public function test_product_tag_creation_validation()
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->post(route('admin.api.create.product-tag'), [
            'description' => json_encode([
                'en' => 'Does not contain meat or dairy products',
                'de' => 'Enthält kein Fleisch oder Milchprodukte'
            ]),
            'display_badge' => 1
        ]);
        $response->assertStatus(302);
    }

    /**
     * @test
     */
    public function test_product_tag_creation_requires_appropriate_permission()
    {
        $this->actingAs(User::where('email', 'customer@m.com')->first());
        $response = $this->post(route('admin.api.create.product-tag'), [
            'description' => json_encode([
                'en' => 'Does not contain meat or dairy products',
                'de' => 'Enthält kein Fleisch oder Milchprodukte'
            ]),
            'display_badge' => 1
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_product_tag_updating_requires_appropriate_permission()
    {
        $tag = ProductTag::factory()->create();
        $this->actingAs(User::where('email', 'customer@m.com')->first());
        $response = $this->patch(route('admin.api.update.product-tag', [
            'tag' => $tag->id
        ]), [
            'description' => json_encode([
                'en' => 'Does not contain meat or dairy products',
                'de' => 'Enthält kein Fleisch oder Milchprodukte'
            ]),
            'display_badge' => 1
        ]);
        $response->assertForbidden();
    }
}
