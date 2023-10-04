<?php

namespace Tests\Feature\AdminBaseCRUD;

use App\Enums\AccessTokenType;
use App\Enums\FileType;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Requests\Admin\ProductStoreRequest;
use App\Http\Requests\Admin\ProductUpdateRequest;
use App\Models\AccessToken;
use App\Models\FileContent;
use App\Models\PaidFileContent;
use App\Models\Product;
use App\Models\ProductTag;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use JMac\Testing\Traits\AdditionalAssertions;
use Tests\AdminControllerTestCase;
use Tests\CreatesApplication;

/**
 * @group admin-base-crud
 * @group products
 *
 * @see \App\Http\Controllers\Admin\ProductController
 */
class ProductControllerTest extends AdminControllerTestCase
{
    use RefreshDatabase;
    use AdditionalAssertions, WithFaker, CreatesApplication;

    /**
     * @test
     */
    public function test_can_list_products(): void
    {
        $product = Product::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.api.index.products', ['page' => 1, 'paginate' => 20, 'filters' => []]));
        $response
            ->assertJson(fn (AssertableJson $json) => $json->where('data.0.id', $product->id)
                ->where('data.0.name', $product->getTranslations('name'))
                ->where('data.0.price', $product->price)
                ->where('data.0.final_price', $product->final_price)
                ->has('data', 1)
                ->etc()
            );
    }

    /**
     * @test
     */
    public function test_can_retrieve_an_individual_product(): void
    {
        $product = Product::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.api.show.product', ['product' => $product->id]));
        $response
            ->assertJson(fn (AssertableJson $json) => $json->where('data.id', $product->id)
                ->where('data.name', $product->getTranslations('name'))
                ->where('data.price', $product->price)
                ->where('data.final_price', $product->final_price)
                ->etc()
            );
    }

    /**
     * @test
     */
    public function test_product_has_the_right_cover_photo(): void
    {
        $product = Product::factory()->create();
        $image = FileContent::factory()->state([
            'type' => FileType::Image,
            'fileable_type' => Product::class,
            'fileable_id' => $product->id
        ])->create();
        $product->save();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.api.show.product', ['product' => $product->id]));
        $response
            ->assertJson(fn (AssertableJson $json) => $json->where('data.cover_photo.file_name', $image->file_name)
                ->where('data.cover_photo.url', $image->url)
                ->etc()
            );
    }

    /**
     * @test
     */
    public function test_product_cover_photo_cleared_if_file_deleted(): void
    {
        $product = Product::factory()->create();
        $image = FileContent::factory()->state([
            'type' => FileType::Image,
            'fileable_type' => Product::class,
            'fileable_id' => $product->id
        ])->create();
        $product->save();
        $image->delete();
        $product->refresh();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.api.show.product', ['product' => $product->id]));
        $response
            ->assertJson(fn (AssertableJson $json) => $json->where('data.cover_photo', null)
                ->etc()
            );
    }

    /**
     * @test
     */
    public function test_products_can_be_filtered_by_tags(): void
    {
        $products = Product::factory()->count(2)->create();
        $tag = ProductTag::factory()->create();
        $products[0]->product_tags()->attach($tag);
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.api.index.products', [
            'tags' => [$tag->id],
            'page' => 1,
            'paginate' => 20,
            'filters' => [],
        ]));
        $response
            ->assertJson(fn (AssertableJson $json) => $json->where('data.0.id', $products[0]->id)
                ->has('data', 1)
                ->etc()
            );
    }

    /**
     * @test
     */
    public function test_can_create_product(): void
    {
        Storage::fake('uploads');
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->post(route('admin.api.create.product'), [
            'name' => json_encode([
                'en' => 'Test Product',
                'de' => 'Test Produkt',
            ]),
            'price' => 12.50,
            'short_description' => json_encode([
                'en' => 'Short Description',
                'de' => 'Kurz Bezeichnung',
            ]),
            'description' => json_encode([
                'en' => 'Longer Description',
                'de' => 'Langer Bezeichnung',
            ]),
            'attachments' => [
                UploadedFile::fake()->image('product_image1.jpg'),
                UploadedFile::fake()->image('product_image2.jpg'),
            ],
        ]);
        $productId = $response->json()['data']['id'];
        $product = Product::find($productId);
        Storage::disk('uploads')->assertExists($product->fileContents[0]->file_name);
        Storage::disk('uploads')->assertExists($product->fileContents[1]->file_name);
        $response->assertCreated();
        $jsonResponse = $response->json();
        $productId = $jsonResponse['data']['id'];
        $product = Product::find($productId);
        $this->assertNotNull($product);
        $this->assertEquals('Test Product', $product->setLocale('en')->name);
        $this->assertEquals('Test Produkt', $product->setLocale('de')->name);
        $this->assertEquals('Short Description', $product->setLocale('en')->short_description);
        $this->assertEquals('Kurz Bezeichnung', $product->setLocale('de')->short_description);
        $this->assertEquals('Longer Description', $product->setLocale('en')->description);
        $this->assertEquals('Langer Bezeichnung', $product->setLocale('de')->description);
        $this->assertEquals(12.50, $product->price);
    }

    /**
     * @test
     */
    public function test_update_product_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            ProductController::class,
            'update',
            ProductUpdateRequest::class
        );
    }

    public function test_can_update_product(): void
    {
        Storage::fake('uploads');

        $product = Product::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->post(route('admin.api.create.product', [
            'product' => $product->id,
        ]), [
            'name' => json_encode([
                'en' => 'Updated Test Product',
                'de' => 'Aktualisiert Test Produkt',
            ]),
            'price' => 12.33,
            'short_description' => json_encode([
                'en' => 'Updated Short Description',
                'de' => 'Aktualisiert Kurz Bezeichnung',
            ]),
            'description' => json_encode([
                'en' => 'Updated Longer Description',
                'de' => 'Aktualisiert Langer Bezeichnung',
            ]),
            'attachments' => [
                UploadedFile::fake()->image('product_image1.jpg'),
                UploadedFile::fake()->image('product_image2.jpg'),
            ],
        ]);
        $response->assertCreated();
        $jsonResponse = $response->json();
        $productId = $jsonResponse['data']['id'];
        $product = Product::find($productId);
        Storage::disk('uploads')->assertExists($product->fileContents[0]->file_name);
        Storage::disk('uploads')->assertExists($product->fileContents[1]->file_name);
        $this->assertEquals('Updated Test Product', $product->setLocale('en')->name);
        $this->assertEquals('Aktualisiert Test Produkt', $product->setLocale('de')->name);
        $this->assertEquals('Updated Short Description', $product->setLocale('en')->short_description);
        $this->assertEquals('Aktualisiert Kurz Bezeichnung', $product->setLocale('de')->short_description);
        $this->assertEquals('Updated Longer Description', $product->setLocale('en')->description);
        $this->assertEquals('Aktualisiert Langer Bezeichnung', $product->setLocale('de')->description);
        $this->assertEquals(12.33, $product->price);
    }

    /**
     * @test
     */
    public function test_store_product_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            ProductController::class,
            'create',
            ProductStoreRequest::class
        );
    }

    /**
     * @test
     */
    public function test_destroy_product_deletes(): void
    {
        $product = Product::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $this->delete(route('admin.api.delete.product', $product));

        $this->assertSoftDeleted($product);
    }

    /**
     * @test
     * @group paid-file-contents
     */
    public function test_can_save_paid_file_content_for_product(): void
    {
        Storage::fake('uploads');

        $product = Product::factory()->state(['virtual' => true])->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->post(route('admin.api.save.paid-file', [
            'product' => $product->id,
        ]), [
            'title' => json_encode([
                'en' => 'Test file ENG',
                'de' => 'Test file GER',
            ]),
            'description' => json_encode([
                'en' => 'Test file description ENG',
                'de' => 'Test file description GER',
            ]),
            'file' => UploadedFile::fake()->image('product_image2.jpg')
        ]);
        $response->assertCreated();
        $jsonResponse = $response->json();
        Storage::disk('paid')->assertExists($jsonResponse['data']['file_name']);
        $this->assertDatabaseHas('paid_file_contents', [
            'fileable_type' => Product::class,
            'fileable_id' => $product->id
        ]);
    }

    /**
     * @test
     * @group paid-file-contents
     */
    public function test_can_update_paid_file_content_for_product(): void
    {
        Storage::fake('uploads');

        /** @var Product $product */
        $product = Product::factory()->state(['virtual' => true])->create();
        /** @var PaidFileContent $paidFileContent */
        $paidFileContent = PaidFileContent::factory()->state([
            'fileable_type' => Product::class,
            'fileable_id' => $product->id
        ])->create();
        Storage::disk('paid')->assertExists($paidFileContent->file_name);

        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->patch(route('admin.api.update.paid-file', [
            'product' => $product->id,
            'paidFileContent' => $paidFileContent->id
        ]), [
            'title' => json_encode([
                'en' => 'UPDATED Test file ENG title',
                'de' => 'AKTUALISIERT Test file GER title',
            ]),
            'description' => json_encode([
                'en' => 'UPDATED Test file description ENG description',
                'de' => 'AKTUALISIERT Test file description GER description',
            ]),
            'file' => UploadedFile::fake()->image('product_image2.jpg')
        ]);
        $response->assertOk();
        Storage::disk('paid')->assertMissing($paidFileContent->file_name);
        $jsonResponse = $response->json();
        Storage::disk('paid')->assertExists($jsonResponse['data']['file_name']);
        $this->assertDatabaseHas('paid_file_contents', [
            'fileable_type' => Product::class,
            'fileable_id' => $product->id
        ]);
    }

    /**
     * @test
     * @group paid-file-contents
     */
    public function test_can_delete_paid_file_contents_from_product(): void
    {
        Storage::fake('uploads');

        /** @var Product $product */
        $product = Product::factory()->state(['virtual' => true])->create();
        /** @var PaidFileContent $paidFileContent */
        $paidFileContent = PaidFileContent::factory()->state([
            'fileable_type' => Product::class,
            'fileable_id' => $product->id
        ])->create();
        Storage::disk('paid')->assertExists($paidFileContent->file_name);

        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->delete(route('admin.api.delete.paid-file', [
            'product' => $product->id,
            'paidFileContent' => $paidFileContent->id
        ]));
        $response->assertOk();
        Storage::disk('paid')->assertMissing($paidFileContent->file_name);
        $this->assertDatabaseMissing('paid_file_contents', [
            'fileable_type' => Product::class,
            'fileable_id' => $product->id
        ]);
    }


    /**
     * @test
     * @group paid-file-contents
     */
    public function test_can_retrieve_paid_file_contents_for_product(): void
    {
        Storage::fake('uploads');

        /** @var Product $product */
        $product = Product::factory()->state(['virtual' => true])->create();
        /** @var PaidFileContent $paidFileContent */
        $paidFileContents = PaidFileContent::factory()->state([
            'fileable_type' => Product::class,
            'fileable_id' => $product->id
        ])->count(3)->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.api.list.paid-files', [
            'product' => $product->id
        ]));
        $response
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data.0.id', $paidFileContents[0]->id)
                ->where('data.0.title.en', $paidFileContents[0]->title)
                ->where('data.0.description.en', $paidFileContents[0]->description)
                ->where('data.1.id', $paidFileContents[1]->id)
                ->where('data.1.title.en', $paidFileContents[1]->title)
                ->where('data.1.description.en', $paidFileContents[1]->description)
                ->where('data.2.id', $paidFileContents[2]->id)
                ->where('data.2.title.en', $paidFileContents[2]->title)
                ->where('data.2.description.en', $paidFileContents[2]->description)
                ->count('data', 3)
                ->etc());
    }

    /**
     * @test
     * @group paid-file-contents
     */
    public function test_can_download_paid_file_contents_as_admin_and_manager(): void
    {
        Storage::fake('uploads');

        /** @var Product $product */
        $product = Product::factory()->state(['virtual' => true])->create();
        /** @var PaidFileContent $paidFileContent */
        $paidFileContent = PaidFileContent::factory()->state([
            'fileable_type' => Product::class,
            'fileable_id' => $product->id
        ])->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.download.paid-file', [
            'product' => $product->id,
            'paidFileContent' => $paidFileContent->id
        ]));
        $response->assertDownload();
    }

    /**
     * @test
     * @group paid-file-contents
     */
    public function test_can_download_paid_file_contents_as_user_with_valid_access_token(): void
    {
        Storage::fake('uploads');

        /** @var Product $product */
        $product = Product::factory()->state(['virtual' => true])->create();
        /** @var PaidFileContent $paidFileContent */
        $paidFileContent = PaidFileContent::factory()->state([
            'fileable_type' => Product::class,
            'fileable_id' => $product->id
        ])->create();
        $accessToken = new AccessToken();
        $accessToken->accessable_id = $paidFileContent->id;
        $accessToken->accessable_type = PaidFileContent::class;
        $accessToken->type = AccessTokenType::PaidFileAccess;
        $accessToken->expiry = Carbon::now()->addYear();
        $accessToken->save();
        $response = $this->get(route('public.api.download-paid-file', [
            'paidFileContent' => $paidFileContent->id,
            'token' => $accessToken->token
        ]));
        $response->assertDownload();
    }

    /**
     * @test
     * @group paid-file-contents
     */
    public function test_download_paid_file_contents_as_user_fails_if_token_expired(): void
    {
        Storage::fake('uploads');

        /** @var Product $product */
        $product = Product::factory()->state(['virtual' => true])->create();
        /** @var PaidFileContent $paidFileContent */
        $paidFileContent = PaidFileContent::factory()->state([
            'fileable_type' => Product::class,
            'fileable_id' => $product->id
        ])->create();
        $accessToken = new AccessToken();
        $accessToken->accessable_id = $paidFileContent->id;
        $accessToken->accessable_type = PaidFileContent::class;
        $accessToken->type = AccessTokenType::PaidFileAccess;
        $accessToken->expiry = Carbon::now()->subMinute();
        $accessToken->save();
        $response = $this->get(route('public.api.download-paid-file', [
            'paidFileContent' => $paidFileContent->id,
            'token' => $accessToken->token
        ]));
        $response->assertJson([
            "status" => "error",
            "message" => "Token expired"
        ]);
    }

    /**
     * @test
     * @group paid-file-contents
     */
    public function test_download_paid_file_contents_as_user_fails_if_token_invalid(): void
    {
        Storage::fake('uploads');

        /** @var Product $product */
        $product = Product::factory()->state(['virtual' => true])->create();
        /** @var PaidFileContent $paidFileContent */
        $paidFileContent = PaidFileContent::factory()->state([
            'fileable_type' => Product::class,
            'fileable_id' => $product->id
        ])->create();
        $response = $this->get(route('public.api.download-paid-file', [
            'paidFileContent' => $paidFileContent->id,
            'token' => 'INVALID'
        ]));
        $response->assertJson([
            "status" => "error",
            "message" => "Something went wrong"
        ]);
    }


    /**
     * @test
     * @group paid-file-contents
     */
    public function test_cannot_download_paid_file_contents_as_non_super_user(): void
    {
        Storage::fake('uploads');

        /** @var Product $product */
        $product = Product::factory()->state(['virtual' => true])->create();
        /** @var PaidFileContent $paidFileContent */
        $paidFileContent = PaidFileContent::factory()->state([
            'fileable_type' => Product::class,
            'fileable_id' => $product->id
        ])->create();
        $this->actingAs(User::where('email', 'customer@m.com')->first());
        $response = $this->get(route('admin.download.paid-file', [
            'product' => $product->id,
            'paidFileContent' => $paidFileContent->id
        ]));
        $response->assertForbidden();
    }

    /**
     * @test
     * @group paid-file-contents
     */
    public function test_downloading_paid_file_content_as_super_user_requires_authentication(): void
    {
        Storage::fake('uploads');

        /** @var Product $product */
        $product = Product::factory()->state(['virtual' => true])->create();
        /** @var PaidFileContent $paidFileContent */
        $paidFileContent = PaidFileContent::factory()->state([
            'fileable_type' => Product::class,
            'fileable_id' => $product->id
        ])->create();
        $response = $this->get(route('admin.download.paid-file', [
            'product' => $product->id,
            'paidFileContent' => $paidFileContent->id
        ]));
        $response->assertStatus(500);
    }

    /**
     * @test
     * @group paid-file-contents
     */
    public function test_saving_paid_file_content_requires_authentication(): void
    {
        Storage::fake('uploads');

        $product = Product::factory()->state(['virtual' => true])->create();
        $response = $this->post(route('admin.api.save.paid-file', [
            'product' => $product->id,
        ]), [
            'title' => json_encode([
                'en' => 'Test file ENG',
                'de' => 'Test file GER',
            ]),
            'description' => json_encode([
                'en' => 'Test file description ENG',
                'de' => 'Test file description GER',
            ]),
            'file' => UploadedFile::fake()->image('product_image2.jpg')
        ]);
        $response->assertStatus(500);
    }

    /**
     * @test
     * @group paid-file-contents
     */
    public function test_updating_paid_file_content_requires_authentication(): void
    {
        Storage::fake('uploads');

        /** @var Product $product */
        $product = Product::factory()->state(['virtual' => true])->create();
        /** @var PaidFileContent $paidFileContent */
        $paidFileContent = PaidFileContent::factory()->state([
            'fileable_type' => Product::class,
            'fileable_id' => $product->id
        ])->create();
        Storage::disk('paid')->assertExists($paidFileContent->file_name);

        $response = $this->patch(route('admin.api.update.paid-file', [
            'product' => $product->id,
            'paidFileContent' => $paidFileContent->id
        ]), [
            'title' => json_encode([
                'en' => 'UPDATED Test file ENG title',
                'de' => 'AKTUALISIERT Test file GER title',
            ]),
            'description' => json_encode([
                'en' => 'UPDATED Test file description ENG description',
                'de' => 'AKTUALISIERT Test file description GER description',
            ]),
            'file' => UploadedFile::fake()->image('product_image2.jpg')
        ]);
        $response->assertStatus(500);
    }

    /**
     * @test
     * @group paid-file-contents
     */
    public function test_retrieving_paid_file_contents_requires_authentication(): void
    {
        Storage::fake('uploads');

        /** @var Product $product */
        $product = Product::factory()->state(['virtual' => true])->create();
        /** @var PaidFileContent $paidFileContent */
        $paidFileContents = PaidFileContent::factory()->state([
            'fileable_type' => Product::class,
            'fileable_id' => $product->id
        ])->count(3)->create();
        $response = $this->get(route('admin.api.list.paid-files', [
            'product' => $product->id
        ]));
        $response->assertStatus(500);
    }


    /**
     * @test
     * @group paid-file-contents
     */
    public function test_deleting_paid_file_contents_requires_authentication(): void
    {
        Storage::fake('uploads');

        /** @var Product $product */
        $product = Product::factory()->state(['virtual' => true])->create();
        /** @var PaidFileContent $paidFileContent */
        $paidFileContent = PaidFileContent::factory()->state([
            'fileable_type' => Product::class,
            'fileable_id' => $product->id
        ])->create();
        Storage::disk('paid')->assertExists($paidFileContent->file_name);
        $response = $this->delete(route('admin.api.delete.paid-file', [
            'product' => $product->id,
            'paidFileContent' => $paidFileContent->id
        ]));
        $response->assertStatus(500);
    }

}
