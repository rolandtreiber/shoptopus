<?php

namespace Tests\Feature\AdminBaseCRUD;

use App\Models\DeliveryType;
use App\Models\DiscountRule;
use App\Models\Note;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeOption;
use App\Models\ProductCategory;
use App\Models\ProductTag;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\VoucherCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\AdminControllerTestCase;

/**
 * @group admin-base-crud
 * @group notes
 */
class NoteControllerTest extends AdminControllerTestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function test_note_can_be_added_to_user(): void
    {
        $author = User::where('email', 'superadmin@m.com')->first();
        $this->actingAs($author);
        $user = User::factory()->create();
        $response = $this->post(route('admin.api.create.note', [
            'note' => 'TEST NOTE',
            'noteable_type' => 'user',
            'noteable_id' => $user->id,
        ]));
        $response->assertCreated();
        $response->assertJsonFragment([
            'note' => 'TEST NOTE',
            'user' => [
                'id' => $author->id,
                'name' => $author->name
            ],
        ]);
    }

    /**
     * @test
     */
    public function test_note_can_be_added_to_order(): void
    {
        $author = User::where('email', 'superadmin@m.com')->first();
        $this->actingAs($author);
        $order = Order::factory()->create();
        $response = $this->post(route('admin.api.create.note', [
            'note' => 'TEST NOTE',
            'noteable_type' => 'order',
            'noteable_id' => $order->id,
        ]));
        $response->assertCreated();
        $response->assertJsonFragment([
            'note' => 'TEST NOTE',
            'user' => [
                'id' => $author->id,
                'name' => $author->name
            ],
        ]);
    }

    /**
     * @test
     */
    public function test_note_can_be_added_to_payment(): void
    {
        $author = User::where('email', 'superadmin@m.com')->first();
        $this->actingAs($author);
        $payment = Payment::factory()->create();
        $response = $this->post(route('admin.api.create.note', [
            'note' => 'TEST NOTE',
            'noteable_type' => 'payment',
            'noteable_id' => $payment->id,
        ]));
        $response->assertCreated();
        $response->assertJsonFragment([
            'note' => 'TEST NOTE',
            'user' => [
                'id' => $author->id,
                'name' => $author->name
            ],
        ]);
    }

    /**
     * @test
     */
    public function test_note_can_be_added_to_voucher_code(): void
    {
        $author = User::where('email', 'superadmin@m.com')->first();
        $this->actingAs($author);
        $voucherCode = VoucherCode::factory()->create();
        $response = $this->post(route('admin.api.create.note', [
            'note' => 'TEST NOTE',
            'noteable_type' => 'voucher_code',
            'noteable_id' => $voucherCode->id,
        ]));
        $response->assertCreated();
        $response->assertJsonFragment([
            'note' => 'TEST NOTE',
            'user' => [
                'id' => $author->id,
                'name' => $author->name
            ],
        ]);
    }

    /**
     * @test
     */
    public function test_note_can_be_added_to_discount_rule(): void
    {
        $author = User::where('email', 'superadmin@m.com')->first();
        $this->actingAs($author);
        $discountRule = DiscountRule::factory()->create();
        $response = $this->post(route('admin.api.create.note', [
            'note' => 'TEST NOTE',
            'noteable_type' => 'discount_rule',
            'noteable_id' => $discountRule->id,
        ]));
        $response->assertCreated();
        $response->assertJsonFragment([
            'note' => 'TEST NOTE',
            'user' => [
                'id' => $author->id,
                'name' => $author->name
            ],
        ]);
    }

    /**
     * @test
     */
    public function test_note_can_be_added_to_delivery_type(): void
    {
        $author = User::where('email', 'superadmin@m.com')->first();
        $this->actingAs($author);
        $deliveryType = DeliveryType::factory()->create();
        $response = $this->post(route('admin.api.create.note', [
            'note' => 'TEST NOTE',
            'noteable_type' => 'delivery_type',
            'noteable_id' => $deliveryType->id,
        ]));
        $response->assertCreated();
        $response->assertJsonFragment([
            'note' => 'TEST NOTE',
            'user' => [
                'id' => $author->id,
                'name' => $author->name
            ],
        ]);
    }

    /**
     * @test
     */
    public function test_note_can_be_added_to_product(): void
    {
        $author = User::where('email', 'superadmin@m.com')->first();
        $this->actingAs($author);
        $product = Product::factory()->create();
        $response = $this->post(route('admin.api.create.note', [
            'note' => 'TEST NOTE',
            'noteable_type' => 'product',
            'noteable_id' => $product->id,
        ]));
        $response->assertCreated();
        $response->assertJsonFragment([
            'note' => 'TEST NOTE',
            'user' => [
                'id' => $author->id,
                'name' => $author->name
            ],
        ]);
    }

    /**
     * @test
     */
    public function test_note_can_be_added_to_product_variant(): void
    {
        $author = User::where('email', 'superadmin@m.com')->first();
        $this->actingAs($author);
        $productVariant = ProductVariant::factory()->create();
        $response = $this->post(route('admin.api.create.note', [
            'note' => 'TEST NOTE',
            'noteable_type' => 'product_variant',
            'noteable_id' => $productVariant->id,
        ]));
        $response->assertCreated();
        $response->assertJsonFragment([
            'note' => 'TEST NOTE',
            'user' => [
                'id' => $author->id,
                'name' => $author->name
            ],
        ]);
    }

    /**
     * @test
     */
    public function test_note_can_be_added_to_product_category(): void
    {
        $author = User::where('email', 'superadmin@m.com')->first();
        $this->actingAs($author);
        $productCategory = ProductCategory::factory()->create();
        $response = $this->post(route('admin.api.create.note', [
            'note' => 'TEST NOTE',
            'noteable_type' => 'product_category',
            'noteable_id' => $productCategory->id,
        ]));
        $response->assertCreated();
        $response->assertJsonFragment([
            'note' => 'TEST NOTE',
            'user' => [
                'id' => $author->id,
                'name' => $author->name
            ],
        ]);
    }

    /**
     * @test
     */
    public function test_note_can_be_added_to_product_tag(): void
    {
        $author = User::where('email', 'superadmin@m.com')->first();
        $this->actingAs($author);
        $productTag = ProductTag::factory()->create();
        $response = $this->post(route('admin.api.create.note', [
            'note' => 'TEST NOTE',
            'noteable_type' => 'user',
            'noteable_id' => $productTag->id,
        ]));
        $response->assertCreated();
        $response->assertJsonFragment([
            'note' => 'TEST NOTE',
            'user' => [
                'id' => $author->id,
                'name' => $author->name
            ],
        ]);
    }

    /**
     * @test
     */
    public function test_note_can_be_added_to_product_attribute(): void
    {
        $author = User::where('email', 'superadmin@m.com')->first();
        $this->actingAs($author);
        $productAttribute = ProductAttribute::factory()->create();
        $response = $this->post(route('admin.api.create.note', [
            'note' => 'TEST NOTE',
            'noteable_type' => 'product_attribute',
            'noteable_id' => $productAttribute->id,
        ]));
        $response->assertCreated();
        $response->assertJsonFragment([
            'note' => 'TEST NOTE',
            'user' => [
                'id' => $author->id,
                'name' => $author->name
            ],
        ]);
    }

    /**
     * @test
     */
    public function test_note_can_be_added_to_product_attribute_option(): void
    {
        $author = User::where('email', 'superadmin@m.com')->first();
        $this->actingAs($author);
        $productAttributeOption = ProductAttributeOption::factory()->create();
        $response = $this->post(route('admin.api.create.note', [
            'note' => 'TEST NOTE',
            'noteable_type' => 'product_attribute_option',
            'noteable_id' => $productAttributeOption->id,
        ]));
        $response->assertCreated();
        $response->assertJsonFragment([
            'note' => 'TEST NOTE',
            'user' => [
                'id' => $author->id,
                'name' => $author->name
            ],
        ]);
    }

    /**
     * @test
     */
    public function test_notes_can_be_retrieved_for_customer(): void
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $user = User::factory()->create();
        $note1 = Note::factory()->state([
            'noteable_type' => User::class,
            'noteable_id' => $user->id,
        ])->create();
        $note2 = Note::factory()->state([
            'noteable_type' => User::class,
            'noteable_id' => $user->id,
        ])->create();
        $response = $this->get(route('admin.api.show.customer', [
            'customer' => $user->id,
        ]));

        $response
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data.notes.0.id', $note1->id)
                ->where('data.notes.0.note', $note1->note)
                ->where('data.notes.1.id', $note2->id)
                ->where('data.notes.1.note', $note2->note)
                ->etc());
    }

    /**
     * @test
     */
    public function test_notes_can_be_retrieved_for_order(): void
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $deliveryType = DeliveryType::factory()->create();
        $order = Order::factory()->state(['delivery_type_id' => $deliveryType->id])->create();
        $note1 = Note::factory()->state([
            'noteable_type' => Order::class,
            'noteable_id' => $order->id,
        ])->create();
        $note2 = Note::factory()->state([
            'noteable_type' => Order::class,
            'noteable_id' => $order->id,
        ])->create();
        $response = $this->get(route('admin.api.show.order', [
            'order' => $order->id,
        ]));

        $response
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data.notes.0.id', $note1->id)
                ->where('data.notes.0.note', $note1->note)
                ->where('data.notes.1.id', $note2->id)
                ->where('data.notes.1.note', $note2->note)
                ->etc());
    }

    /**
     * @test
     */
    public function test_notes_can_be_retrieved_for_payment(): void
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $payment = Payment::factory()->create();
        $note1 = Note::factory()->state([
            'noteable_type' => Payment::class,
            'noteable_id' => $payment->id,
        ])->create();
        $note2 = Note::factory()->state([
            'noteable_type' => Payment::class,
            'noteable_id' => $payment->id,
        ])->create();
        $response = $this->get(route('admin.api.show.payment', [
            'payment' => $payment->id,
        ]));

        $response
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data.notes.0.id', $note1->id)
                ->where('data.notes.0.note', $note1->note)
                ->where('data.notes.1.id', $note2->id)
                ->where('data.notes.1.note', $note2->note)
                ->etc());
    }

    /**
     * @test
     */
    public function test_notes_can_be_retrieved_for_voucher_code(): void
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $voucherCode = VoucherCode::factory()->create();
        $note1 = Note::factory()->state([
            'noteable_type' => VoucherCode::class,
            'noteable_id' => $voucherCode->id,
        ])->create();
        $note2 = Note::factory()->state([
            'noteable_type' => VoucherCode::class,
            'noteable_id' => $voucherCode->id,
        ])->create();
        $response = $this->get(route('admin.api.show.voucher-code', [
            'voucherCode' => $voucherCode->id,
        ]));

        $response
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data.notes.0.id', $note1->id)
                ->where('data.notes.0.note', $note1->note)
                ->where('data.notes.1.id', $note2->id)
                ->where('data.notes.1.note', $note2->note)
                ->etc());
    }

    /**
     * @test
     */
    public function test_notes_can_be_retrieved_for_discount_rule(): void
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $discountRule = DiscountRule::factory()->create();
        $note1 = Note::factory()->state([
            'noteable_type' => DiscountRule::class,
            'noteable_id' => $discountRule->id,
        ])->create();
        $note2 = Note::factory()->state([
            'noteable_type' => DiscountRule::class,
            'noteable_id' => $discountRule->id,
        ])->create();
        $response = $this->get(route('admin.api.show.discount-rule', [
            'discountRule' => $discountRule->id,
        ]));

        $response
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data.notes.0.id', $note1->id)
                ->where('data.notes.0.note', $note1->note)
                ->where('data.notes.1.id', $note2->id)
                ->where('data.notes.1.note', $note2->note)
                ->etc());
    }

    /**
     * @test
     */
    public function test_notes_can_be_retrieved_for_delivery_type(): void
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $deliveryType = DeliveryType::factory()->create();
        $note1 = Note::factory()->state([
            'noteable_type' => DeliveryType::class,
            'noteable_id' => $deliveryType->id,
        ])->create();
        $note2 = Note::factory()->state([
            'noteable_type' => DeliveryType::class,
            'noteable_id' => $deliveryType->id,
        ])->create();
        $response = $this->get(route('admin.api.show.delivery-type', [
            'deliveryType' => $deliveryType->id,
        ]));

        $response
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data.notes.0.id', $note1->id)
                ->where('data.notes.0.note', $note1->note)
                ->where('data.notes.1.id', $note2->id)
                ->where('data.notes.1.note', $note2->note)
                ->etc());
    }

    /**
     * @test
     */
    public function test_notes_can_be_retrieved_for_product(): void
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $product = Product::factory()->create();
        $note1 = Note::factory()->state([
            'noteable_type' => Product::class,
            'noteable_id' => $product->id,
        ])->create();
        $note2 = Note::factory()->state([
            'noteable_type' => Product::class,
            'noteable_id' => $product->id,
        ])->create();
        $response = $this->get(route('admin.api.show.product', [
            'product' => $product->id,
        ]));

        $response
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data.notes.0.id', $note1->id)
                ->where('data.notes.0.note', $note1->note)
                ->where('data.notes.1.id', $note2->id)
                ->where('data.notes.1.note', $note2->note)
                ->etc());
    }

    /**
     * @test
     */
    public function test_notes_can_be_retrieved_for_product_variant(): void
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $productVariant = ProductVariant::factory()->create();
        $note1 = Note::factory()->state([
            'noteable_type' => ProductVariant::class,
            'noteable_id' => $productVariant->id,
        ])->create();
        $note2 = Note::factory()->state([
            'noteable_type' => ProductVariant::class,
            'noteable_id' => $productVariant->id,
        ])->create();
        $response = $this->get(route('admin.api.show.product-variant', [
            'product' => $productVariant->product_id,
            'variant' => $productVariant->id
        ]));

        $response
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data.notes.0.id', $note1->id)
                ->where('data.notes.0.note', $note1->note)
                ->where('data.notes.1.id', $note2->id)
                ->where('data.notes.1.note', $note2->note)
                ->etc());
    }

    /**
     * @test
     */
    public function test_notes_can_be_retrieved_for_product_category(): void
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $productCategory = ProductCategory::factory()->create();
        $note1 = Note::factory()->state([
            'noteable_type' => ProductCategory::class,
            'noteable_id' => $productCategory->id,
        ])->create();
        $note2 = Note::factory()->state([
            'noteable_type' => ProductCategory::class,
            'noteable_id' => $productCategory->id,
        ])->create();
        $response = $this->get(route('admin.api.show.product-category', [
            'category' => $productCategory->id,
        ]));

        $response
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data.notes.0.id', $note1->id)
                ->where('data.notes.0.note', $note1->note)
                ->where('data.notes.1.id', $note2->id)
                ->where('data.notes.1.note', $note2->note)
                ->etc());
    }

    /**
     * @test
     */
    public function test_notes_can_be_retrieved_for_product_tag(): void
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $productTag = ProductTag::factory()->create();
        $note1 = Note::factory()->state([
            'noteable_type' => ProductTag::class,
            'noteable_id' => $productTag->id,
        ])->create();
        $note2 = Note::factory()->state([
            'noteable_type' => ProductTag::class,
            'noteable_id' => $productTag->id,
        ])->create();
        $response = $this->get(route('admin.api.show.product-tag', [
            'tag' => $productTag->id,
        ]));

        $response
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data.notes.0.id', $note1->id)
                ->where('data.notes.0.note', $note1->note)
                ->where('data.notes.1.id', $note2->id)
                ->where('data.notes.1.note', $note2->note)
                ->etc());
    }

    /**
     * @test
     */
    public function test_notes_can_be_retrieved_for_product_attribute(): void
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $productAttribute = ProductAttribute::factory()->create();
        $note1 = Note::factory()->state([
            'noteable_type' => ProductAttribute::class,
            'noteable_id' => $productAttribute->id,
        ])->create();
        $note2 = Note::factory()->state([
            'noteable_type' => ProductAttribute::class,
            'noteable_id' => $productAttribute->id,
        ])->create();
        $response = $this->get(route('admin.api.show.product-attribute', [
            'attribute' => $productAttribute->id,
        ]));

        $response
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data.notes.0.id', $note1->id)
                ->where('data.notes.0.note', $note1->note)
                ->where('data.notes.1.id', $note2->id)
                ->where('data.notes.1.note', $note2->note)
                ->etc());
    }

    /**
     * @test
     */
    public function test_notes_can_be_retrieved_for_product_attribute_option(): void
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $productAttributeOption = ProductAttributeOption::factory()->create();
        $note1 = Note::factory()->state([
            'noteable_type' => ProductAttributeOption::class,
            'noteable_id' => $productAttributeOption->id,
        ])->create();
        $note2 = Note::factory()->state([
            'noteable_type' => ProductAttributeOption::class,
            'noteable_id' => $productAttributeOption->id,
        ])->create();
        $response = $this->get(route('admin.api.show.product-attribute-option', [
            'attribute' => $productAttributeOption->product_attribute_id,
            'option' => $productAttributeOption->id
        ]));

        $response
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('data.notes.0.id', $note1->id)
                ->where('data.notes.0.note', $note1->note)
                ->where('data.notes.1.id', $note2->id)
                ->where('data.notes.1.note', $note2->note)
                ->etc());
    }

    /**
     * @test
     */
    public function test_note_can_be_updated_by_author(): void
    {
        $author = User::where('email', 'storeassistant@m.com')->first();
        $this->actingAs($author);
        $voucherCode = VoucherCode::factory()->create();
        $note = Note::factory()->state([
            'note' => 'TEST NOTE',
            'noteable_type' => VoucherCode::class,
            'noteable_id' => $voucherCode->id,
            'user_id' => $author->id

        ])->create();
        $response = $this->patch(route('admin.api.update.note', [
            'note' => $note->id,
        ]), [
            'note' => "UPDATED NOTE"
        ]);

        $response->assertOk();
        $response->assertJsonFragment([
            'note' => 'UPDATED NOTE',
            'user' => [
                'id' => $author->id,
                'name' => $author->name
            ],
        ]);
    }

    /**
     * @test
     */
    public function test_note_can_be_updated_by_super_user(): void
    {
        $author = User::where('email', 'superadmin@m.com')->first();
        $this->actingAs($author);
        $voucherCode = VoucherCode::factory()->create();
        $note = Note::factory()->state([
            'note' => 'TEST NOTE',
            'noteable_type' => VoucherCode::class,
            'noteable_id' => $voucherCode->id,
            'user_id' => $author->id

        ])->create();
        $response = $this->patch(route('admin.api.update.note', [
            'note' => $note->id,
        ]), [
            'note' => "UPDATED NOTE"
        ]);

        $response->assertOk();
        $response->assertJsonFragment([
            'note' => 'UPDATED NOTE',
            'user' => [
                'id' => $author->id,
                'name' => $author->name
            ],
        ]);
    }

    /**
     * @test
     */
    public function test_note_cannot_be_updated_by_non_author_non_super_user(): void
    {
        $author = User::where('email', 'superadmin@m.com')->first();
        $user = User::where('email', 'storeassistant@m.com')->first();
        $this->actingAs($user);
        $voucherCode = VoucherCode::factory()->create();
        $note = Note::factory()->state([
            'note' => 'TEST NOTE',
            'noteable_type' => VoucherCode::class,
            'noteable_id' => $voucherCode->id,
            'user_id' => $author->id

        ])->create();
        $response = $this->patch(route('admin.api.update.note', [
            'note' => $note->id,
        ]), [
            'note' => "UPDATED NOTE"
        ]);
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_note_can_be_deleted_by_author(): void
    {
        $author = User::where('email', 'storeassistant@m.com')->first();
        $this->actingAs($author);
        $voucherCode = VoucherCode::factory()->create();
        $note = Note::factory()->state([
            'note' => 'TEST NOTE',
            'noteable_type' => VoucherCode::class,
            'noteable_id' => $voucherCode->id,
            'user_id' => $author->id

        ])->create();
        $response = $this->delete(route('admin.api.delete.note', [
            'note' => $note->id,
        ]));

        $response->assertOk();
        $this->assertDatabaseMissing('notes', [
            'id' => $note->id
        ]);
    }

    /**
     * @test
     */
    public function test_note_can_be_deleted_by_super_user(): void
    {
        $author = User::where('email', 'storeassistant@m.com')->first();
        $user = User::where('email', 'superadmin@m.com')->first();
        $this->actingAs($user);
        $voucherCode = VoucherCode::factory()->create();
        $note = Note::factory()->state([
            'note' => 'TEST NOTE',
            'noteable_type' => VoucherCode::class,
            'noteable_id' => $voucherCode->id,
            'user_id' => $author->id

        ])->create();
        $response = $this->delete(route('admin.api.delete.note', [
            'note' => $note->id,
        ]));

        $response->assertOk();
        $this->assertDatabaseMissing('notes', [
            'id' => $note->id
        ]);
    }

    /**
     * @test
     */
    public function test_note_cannot_be_deleted_by_non_author_non_super_user(): void
    {
        $author = User::where('email', 'superadmin@m.com')->first();
        $user = User::where('email', 'storeassistant@m.com')->first();
        $this->actingAs($user);
        $voucherCode = VoucherCode::factory()->create();
        $note = Note::factory()->state([
            'note' => 'TEST NOTE',
            'noteable_type' => VoucherCode::class,
            'noteable_id' => $voucherCode->id,
            'user_id' => $author->id

        ])->create();
        $response = $this->delete(route('admin.api.delete.note', [
            'note' => $note->id,
        ]));

        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_note_create_validation(): void
    {
        $author = User::where('email', 'superadmin@m.com')->first();
        $this->actingAs($author);
        $payment = Payment::factory()->create();
        $response = $this->post(route('admin.api.create.note', [
            'note' => 'TEST NOTE',
            'noteable_id' => $payment->id,
        ]));
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_note_update_validation(): void
    {
        $author = User::where('email', 'storeassistant@m.com')->first();
        $this->actingAs($author);
        $voucherCode = VoucherCode::factory()->create();
        $note = Note::factory()->state([
            'note' => 'TEST NOTE',
            'noteable_type' => VoucherCode::class,
            'noteable_id' => $voucherCode->id,
            'user_id' => $author->id

        ])->create();
        $response = $this->patch(route('admin.api.update.note', [
            'note' => $note->id,
        ]));
        $response->assertStatus(422);
    }

}
