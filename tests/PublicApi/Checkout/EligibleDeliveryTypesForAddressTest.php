<?php

namespace PublicApi\Checkout;

use App\Models\Address;
use App\Models\Cart;
use App\Models\DeliveryRule;
use App\Models\DeliveryType;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * @group eligible-delivery-types-for-address
 */
class EligibleDeliveryTypesForAddressTest extends TestCase
{
    use RefreshDatabase;

    private Address $bigBen;
    private Address $rafflesHotel;
    private Address $tenDowningStreet;
    private DeliveryRule $ruleMinDistanceFromBigBen;
    private DeliveryRule $ruleMaxDistanceFromBigBen;
    private DeliveryRule $ruleMinWeight;
    private DeliveryRule $ruleMaxWeight;
    private DeliveryRule $rulePostCodeRaffleHotelAndDowningStreet;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->user = $user;

        $bigBen = new Address();
        $bigBen->town = "London";
        $bigBen->address_line_1 = "Big Ben";
        $bigBen->post_code = "SW1A 0AA";
        $bigBen->lat = 51.5007325;
        $bigBen->lon = -0.1272003;

        $rafflesHotel = new Address();
        $rafflesHotel->town = "London";
        $rafflesHotel->address_line_1 = "Raffles Hotel";
        $rafflesHotel->post_code = "SW1A 2BX";
        $rafflesHotel->lat = 51.504379;
        $rafflesHotel->lon = -0.1287421;

        $tenDowningStreet = new Address();
        $tenDowningStreet->town = "London";
        $tenDowningStreet->address_line_1 = "10 Downing Street";
        $tenDowningStreet->post_code = "SW1A 2AB";
        $tenDowningStreet->lat = 51.5032198;
        $tenDowningStreet->lon = -0.1335334;

        $ruleMinDistanceFromBigBen = new DeliveryRule();
        $ruleMinDistanceFromBigBen->lat = $bigBen->lat;
        $ruleMinDistanceFromBigBen->lon = $bigBen->lon;
        $ruleMinDistanceFromBigBen->min_distance = 50;

        $ruleMaxDistanceFromBigBen = new DeliveryRule();
        $ruleMaxDistanceFromBigBen->lat = $bigBen->lat;
        $ruleMaxDistanceFromBigBen->lon = $bigBen->lon;
        $ruleMaxDistanceFromBigBen->max_distance = 600;

        $ruleMinWeight = new DeliveryRule();
        $ruleMinWeight->min_weight = 1000;

        $ruleMaxWeight = new DeliveryRule();
        $ruleMaxWeight->max_weight = 5000;

        $rulePostCodeRaffleHotelAndDowningStreet = new DeliveryRule();
        $rulePostCodeRaffleHotelAndDowningStreet->postcodes = [$tenDowningStreet->post_code, $rafflesHotel->post_code];

        $this->bigBen = $bigBen;
        $this->rafflesHotel = $rafflesHotel;
        $this->tenDowningStreet = $tenDowningStreet;
        $this->ruleMinDistanceFromBigBen = $ruleMinDistanceFromBigBen;
        $this->ruleMaxDistanceFromBigBen = $ruleMaxDistanceFromBigBen;
        $this->ruleMinWeight = $ruleMinWeight;
        $this->ruleMaxWeight = $ruleMaxWeight;
        $this->rulePostCodeRaffleHotelAndDowningStreet = $rulePostCodeRaffleHotelAndDowningStreet;
    }

    /**
     * @test
     */
    public function finds_delivery_type_by_min_distance_rule(): void
    {
        $this->rafflesHotel->user_id = $this->user->id;
        $this->rafflesHotel->save();

        $deliveryType = DeliveryType::factory()->create();
        $this->ruleMinDistanceFromBigBen->delivery_type_id = $deliveryType->id;
        $this->ruleMinDistanceFromBigBen->save();

        $product = Product::factory()->state(['weight' => 1500])->create();
        $cart = new Cart();
        $cart->save();
        $cart->products()->attach($product->id, ['quantity' => 2]);

        $res = $this->signIn($this->user)
            ->sendRequest([
                'address_id' => $this->rafflesHotel->id,
                'cart_id' => $cart->id
            ]);

        $this->assertEquals($deliveryType->id, $res->json('data.0.id'));
    }

    /**
     * @test
     */
    public function finds_delivery_type_by_max_distance_rule(): void
    {
        $this->rafflesHotel->user_id = $this->user->id;
        $this->rafflesHotel->save();

        $deliveryType = DeliveryType::factory()->create();
        $this->ruleMaxDistanceFromBigBen->delivery_type_id = $deliveryType->id;
        $this->ruleMaxDistanceFromBigBen->save();

        $product = Product::factory()->state(['weight' => 1500])->create();
        $cart = new Cart();
        $cart->save();
        $cart->products()->attach($product->id, ['quantity' => 2]);

        $res = $this->signIn($this->user)
            ->sendRequest([
                'address_id' => $this->rafflesHotel->id,
                'cart_id' => $cart->id
            ]);

        $this->assertEquals($deliveryType->id, $res->json('data.0.id'));
    }

    /**
     * @test
     */
    public function finds_delivery_type_by_min_and_max_distance_rules(): void
    {
        $this->rafflesHotel->user_id = $this->user->id;
        $this->rafflesHotel->save();

        $deliveryType = DeliveryType::factory()->create();
        $this->ruleMaxDistanceFromBigBen->delivery_type_id = $deliveryType->id;
        $this->ruleMaxDistanceFromBigBen->save();
        $this->ruleMaxDistanceFromBigBen->delivery_type_id = $deliveryType->id;
        $this->ruleMaxDistanceFromBigBen->save();


        $product = Product::factory()->state(['weight' => 1500])->create();
        $cart = new Cart();
        $cart->save();
        $cart->products()->attach($product->id, ['quantity' => 2]);

        $res = $this->signIn($this->user)
            ->sendRequest([
                'address_id' => $this->rafflesHotel->id,
                'cart_id' => $cart->id
            ]);

        $this->assertEquals($deliveryType->id, $res->json('data.0.id'));

    }

    /**
     * @test
     */
    public function excludes_delivery_type_by_min_distance_rule(): void
    {
        $this->rafflesHotel->user_id = $this->user->id;
        $this->rafflesHotel->save();

        $deliveryType = DeliveryType::factory()->create();
        $this->ruleMinDistanceFromBigBen->min_distance = 600;
        $this->ruleMinDistanceFromBigBen->delivery_type_id = $deliveryType->id;
        $this->ruleMinDistanceFromBigBen->save();

        $product = Product::factory()->state(['weight' => 1500])->create();
        $cart = new Cart();
        $cart->save();
        $cart->products()->attach($product->id, ['quantity' => 2]);

        $res = $this->signIn($this->user)
            ->sendRequest([
                'address_id' => $this->rafflesHotel->id,
                'cart_id' => $cart->id
            ]);

        $this->assertCount(0, $res->json('data'));
    }

    /**
     * @test
     */
    public function excludes_delivery_type_by_max_distance_rule(): void
    {
        $this->rafflesHotel->user_id = $this->user->id;
        $this->rafflesHotel->save();

        $deliveryType = DeliveryType::factory()->create();
        $this->ruleMaxDistanceFromBigBen->max_distance = 200;
        $this->ruleMaxDistanceFromBigBen->delivery_type_id = $deliveryType->id;
        $this->ruleMaxDistanceFromBigBen->save();

        $product = Product::factory()->state(['weight' => 1500])->create();
        $cart = new Cart();
        $cart->save();
        $cart->products()->attach($product->id, ['quantity' => 2]);

        $res = $this->signIn($this->user)
            ->sendRequest([
                'address_id' => $this->rafflesHotel->id,
                'cart_id' => $cart->id
            ]);

        $this->assertCount(0, $res->json('data'));
    }

    /**
     * @test
     */
    public function returns_multiple_delivery_types_matching_by_min_and_max_distance_rules(): void
    {
        $this->rafflesHotel->user_id = $this->user->id;
        $this->rafflesHotel->save();

        $deliveryTypes = DeliveryType::factory()->count(2)->create();
        $this->ruleMaxDistanceFromBigBen->delivery_type_id = $deliveryTypes[0]->id;
        $this->ruleMaxDistanceFromBigBen->save();

        $this->ruleMinDistanceFromBigBen->delivery_type_id = $deliveryTypes[1]->id;
        $this->ruleMinDistanceFromBigBen->save();

        $product = Product::factory()->state(['weight' => 1500])->create();
        $cart = new Cart();
        $cart->save();
        $cart->products()->attach($product->id, ['quantity' => 2]);

        $res = $this->signIn($this->user)
            ->sendRequest([
                'address_id' => $this->rafflesHotel->id,
                'cart_id' => $cart->id
            ]);

        $this->assertCount(2, $res->json('data'));
        $this->assertEquals($deliveryTypes[0]->id, $res->json('data.0.id'));
        $this->assertEquals($deliveryTypes[1]->id, $res->json('data.1.id'));

    }

    /**
     * @test
     */
    public function finds_delivery_type_by_min_weight_rule(): void
    {
        $this->rafflesHotel->user_id = $this->user->id;
        $this->rafflesHotel->save();

        $deliveryType = DeliveryType::factory()->create();
        $this->ruleMinWeight->delivery_type_id = $deliveryType->id;
        $this->ruleMinWeight->save();

        $product = Product::factory()->state(['weight' => 1500])->create();
        $cart = new Cart();
        $cart->save();
        $cart->products()->attach($product->id, ['quantity' => 2]);

        $res = $this->signIn($this->user)
            ->sendRequest([
                'address_id' => $this->rafflesHotel->id,
                'cart_id' => $cart->id
            ]);

        $this->assertCount(1, $res->json('data'));
        $this->assertEquals($deliveryType->id, $res->json('data.0.id'));

    }

    /**
     * @test
     */
    public function finds_delivery_type_by_max_weight_rule(): void
    {
        $this->rafflesHotel->user_id = $this->user->id;
        $this->rafflesHotel->save();

        $deliveryType = DeliveryType::factory()->create();
        $this->ruleMaxWeight->delivery_type_id = $deliveryType->id;
        $this->ruleMaxWeight->save();

        $product = Product::factory()->state(['weight' => 1500])->create();
        $cart = new Cart();
        $cart->save();
        $cart->products()->attach($product->id, ['quantity' => 2]);

        $res = $this->signIn($this->user)
            ->sendRequest([
                'address_id' => $this->rafflesHotel->id,
                'cart_id' => $cart->id
            ]);

        $this->assertCount(1, $res->json('data'));
        $this->assertEquals($deliveryType->id, $res->json('data.0.id'));

    }

    /**
     * @test
     */
    public function excludes_delivery_type_by_min_weight_rule(): void
    {
        $this->rafflesHotel->user_id = $this->user->id;
        $this->rafflesHotel->save();

        $deliveryType = DeliveryType::factory()->create();
        $this->ruleMinWeight->min_weight = 4000;
        $this->ruleMinWeight->delivery_type_id = $deliveryType->id;
        $this->ruleMinWeight->save();

        $product = Product::factory()->state(['weight' => 1500])->create();
        $cart = new Cart();
        $cart->save();
        $cart->products()->attach($product->id, ['quantity' => 2]);

        $res = $this->signIn($this->user)
            ->sendRequest([
                'address_id' => $this->rafflesHotel->id,
                'cart_id' => $cart->id
            ]);

        $this->assertCount(0, $res->json('data'));
    }

    /**
     * @test
     */
    public function excludes_delivery_type_by_max_weight_rule(): void
    {
        $this->rafflesHotel->user_id = $this->user->id;
        $this->rafflesHotel->save();

        $deliveryType = DeliveryType::factory()->create();
        $this->ruleMaxWeight->max_weight = 2000;
        $this->ruleMaxWeight->delivery_type_id = $deliveryType->id;
        $this->ruleMaxWeight->save();

        $product = Product::factory()->state(['weight' => 1500])->create();
        $cart = new Cart();
        $cart->save();
        $cart->products()->attach($product->id, ['quantity' => 2]);

        $res = $this->signIn($this->user)
            ->sendRequest([
                'address_id' => $this->rafflesHotel->id,
                'cart_id' => $cart->id
            ]);

        $this->assertCount(0, $res->json('data'));
    }

    /**
     * @test
     */
    public function returns_multiple_delivery_types_matching_by_min_and_max_weight_rules(): void
    {
        $this->rafflesHotel->user_id = $this->user->id;
        $this->rafflesHotel->save();

        $deliveryTypes = DeliveryType::factory()->count(2)->create();
        $this->ruleMinWeight->delivery_type_id = $deliveryTypes[0]->id;
        $this->ruleMinWeight->save();

        $this->ruleMaxWeight->delivery_type_id = $deliveryTypes[1]->id;
        $this->ruleMaxWeight->save();

        $product = Product::factory()->state(['weight' => 1500])->create();
        $cart = new Cart();
        $cart->save();
        $cart->products()->attach($product->id, ['quantity' => 2]);

        $res = $this->signIn($this->user)
            ->sendRequest([
                'address_id' => $this->rafflesHotel->id,
                'cart_id' => $cart->id
            ]);

        $this->assertCount(2, $res->json('data'));
        $this->assertEquals($deliveryTypes[0]->id, $res->json('data.0.id'));
        $this->assertEquals($deliveryTypes[1]->id, $res->json('data.1.id'));

    }

    /**
     * @test
     */
    public function returns_multiple_delivery_types_matching_by_min_and_max_weight_and_min_max_distance_rules(): void
    {
        $this->rafflesHotel->user_id = $this->user->id;
        $this->rafflesHotel->save();

        $deliveryTypes = DeliveryType::factory()->count(4)->create();
        $this->ruleMinWeight->delivery_type_id = $deliveryTypes[0]->id;
        $this->ruleMinWeight->save();

        $this->ruleMaxWeight->delivery_type_id = $deliveryTypes[1]->id;
        $this->ruleMaxWeight->save();

        $this->ruleMaxDistanceFromBigBen->delivery_type_id = $deliveryTypes[2]->id;
        $this->ruleMaxDistanceFromBigBen->save();

        $this->ruleMinDistanceFromBigBen->delivery_type_id = $deliveryTypes[3]->id;
        $this->ruleMinDistanceFromBigBen->save();


        $product = Product::factory()->state(['weight' => 1500])->create();
        $cart = new Cart();
        $cart->save();
        $cart->products()->attach($product->id, ['quantity' => 2]);

        $res = $this->signIn($this->user)
            ->sendRequest([
                'address_id' => $this->rafflesHotel->id,
                'cart_id' => $cart->id
            ]);

        $this->assertCount(4, $res->json('data'));
        $this->assertEquals($deliveryTypes[0]->id, $res->json('data.0.id'));
        $this->assertEquals($deliveryTypes[1]->id, $res->json('data.1.id'));
        $this->assertEquals($deliveryTypes[2]->id, $res->json('data.2.id'));
        $this->assertEquals($deliveryTypes[3]->id, $res->json('data.3.id'));

    }

    /**
     * @test
     */
    public function returns_delivery_type_constrained_by_min_max_distance_and_weight_rules(): void
    {
        $this->rafflesHotel->user_id = $this->user->id;
        $this->rafflesHotel->save();

        $deliveryType = DeliveryType::factory()->create();
        $this->ruleMinWeight->delivery_type_id = $deliveryType->id;
        $this->ruleMinWeight->save();

        $this->ruleMaxWeight->delivery_type_id = $deliveryType->id;
        $this->ruleMaxWeight->save();

        $this->ruleMaxDistanceFromBigBen->delivery_type_id = $deliveryType->id;
        $this->ruleMaxDistanceFromBigBen->save();

        $this->ruleMinDistanceFromBigBen->delivery_type_id = $deliveryType->id;
        $this->ruleMinDistanceFromBigBen->save();


        $product = Product::factory()->state(['weight' => 1500])->create();
        $cart = new Cart();
        $cart->save();
        $cart->products()->attach($product->id, ['quantity' => 2]);

        $res = $this->signIn($this->user)
            ->sendRequest([
                'address_id' => $this->rafflesHotel->id,
                'cart_id' => $cart->id
            ]);

        $this->assertCount(1, $res->json('data'));
        $this->assertEquals($deliveryType->id, $res->json('data.0.id'));

    }

    /**
     * @test
     */
    public function finds_delivery_type_by_postcode_rule(): void
    {
        $this->tenDowningStreet->user_id = $this->user->id;
        $this->tenDowningStreet->save();

        $deliveryType = DeliveryType::factory()->create();
        $this->rulePostCodeRaffleHotelAndDowningStreet->delivery_type_id = $deliveryType->id;
        $this->rulePostCodeRaffleHotelAndDowningStreet->save();

        $product = Product::factory()->state(['weight' => 1500])->create();
        $cart = new Cart();
        $cart->save();
        $cart->products()->attach($product->id, ['quantity' => 2]);

        $res = $this->signIn($this->user)
            ->sendRequest([
                'address_id' => $this->tenDowningStreet->id,
                'cart_id' => $cart->id
            ]);

        $this->assertCount(1, $res->json('data'));
        $this->assertEquals($deliveryType->id, $res->json('data.0.id'));

    }

    /**
     * @test
     */
    public function finds_delivery_type_by_postcode_rule_regardless_the_formatting(): void
    {
        $this->tenDowningStreet->user_id = $this->user->id;
        $this->tenDowningStreet->post_code = " Sw1A  2ab ";
        $this->tenDowningStreet->save();

        $deliveryType = DeliveryType::factory()->create();
        $this->rulePostCodeRaffleHotelAndDowningStreet->delivery_type_id = $deliveryType->id;
        $this->rulePostCodeRaffleHotelAndDowningStreet->save();

        $product = Product::factory()->state(['weight' => 1500])->create();
        $cart = new Cart();
        $cart->save();
        $cart->products()->attach($product->id, ['quantity' => 2]);

        $res = $this->signIn($this->user)
            ->sendRequest([
                'address_id' => $this->tenDowningStreet->id,
                'cart_id' => $cart->id
            ]);

        $this->assertCount(1, $res->json('data'));
        $this->assertEquals($deliveryType->id, $res->json('data.0.id'));

    }

    /**
     * @test
     */
    public function excludes_delivery_type_by_postcode_rule(): void
    {
        $this->tenDowningStreet->user_id = $this->user->id;
        $this->tenDowningStreet->post_code = " Sw1A  2ac ";
        $this->tenDowningStreet->save();

        $deliveryType = DeliveryType::factory()->create();
        $this->rulePostCodeRaffleHotelAndDowningStreet->delivery_type_id = $deliveryType->id;
        $this->rulePostCodeRaffleHotelAndDowningStreet->save();

        $product = Product::factory()->state(['weight' => 1500])->create();
        $cart = new Cart();
        $cart->save();
        $cart->products()->attach($product->id, ['quantity' => 2]);

        $res = $this->signIn($this->user)
            ->sendRequest([
                'address_id' => $this->tenDowningStreet->id,
                'cart_id' => $cart->id
            ]);

        $this->assertCount(0, $res->json('data'));
    }

    /**
     * @test
     */
    public function the_endpoint_works_with_address_array(): void
    {
        $this->tenDowningStreet->user_id = $this->user->id;
        $this->tenDowningStreet->post_code = " Sw1A  2ab ";
        $this->tenDowningStreet->save();

        $deliveryType = DeliveryType::factory()->create();
        $this->rulePostCodeRaffleHotelAndDowningStreet->delivery_type_id = $deliveryType->id;
        $this->rulePostCodeRaffleHotelAndDowningStreet->save();


        $product = Product::factory()->state(['weight' => 1500])->create();
        $cart = new Cart();
        $cart->save();
        $cart->products()->attach($product->id, ['quantity' => 2]);

        $res = $this->signIn($this->user)
            ->sendRequest([
                'address' => $this->tenDowningStreet->toArray(),
                'address_id' => "", // It works with null in real requests, but seems to be failing in tests as it is likely to be removed from the request if null.
                'cart_id' => $cart->id
            ]);

        $this->assertCount(1, $res->json('data'));
    }

    /**
     * @test
     * @group work
     */
    public function returns_the_correct_set_of_available_delivery_types_upon_complex_set_of_criteria(): void
    {
        $this->tenDowningStreet->post_code = " Sw1A  2ab ";

        $deliveryTypes = DeliveryType::factory()->count(3)->create();

        // Delivery Type 1 - conforms all requirements - returned
        $this->rulePostCodeRaffleHotelAndDowningStreet->delivery_type_id = $deliveryTypes[0]->id;
        $this->rulePostCodeRaffleHotelAndDowningStreet->save();

        $this->ruleMinWeight->delivery_type_id = $deliveryTypes[0]->id;
        $this->ruleMinWeight->save();

        $this->ruleMaxWeight->delivery_type_id = $deliveryTypes[0]->id;
        $this->ruleMaxWeight->save();

        $this->ruleMaxDistanceFromBigBen->delivery_type_id = $deliveryTypes[0]->id;
        $this->ruleMaxDistanceFromBigBen->save();

        $this->ruleMinDistanceFromBigBen->delivery_type_id = $deliveryTypes[0]->id;
        $this->ruleMinDistanceFromBigBen->save();

        // Delivery Type 2 - conforms all but one requirements - not returned
        $this->rulePostCodeRaffleHotelAndDowningStreet->delivery_type_id = $deliveryTypes[1]->id;
        $this->rulePostCodeRaffleHotelAndDowningStreet->save();

        $ruleMinWeightFail = new DeliveryRule();
        $ruleMinWeightFail->min_weight = 5000;

        $ruleMinWeightFail->delivery_type_id = $deliveryTypes[1]->id;
        $ruleMinWeightFail->save();

        $this->ruleMaxWeight->delivery_type_id = $deliveryTypes[1]->id;
        $this->ruleMaxWeight->save();

        $this->ruleMaxDistanceFromBigBen->delivery_type_id = $deliveryTypes[1]->id;
        $this->ruleMaxDistanceFromBigBen->save();

        $this->ruleMinDistanceFromBigBen->delivery_type_id = $deliveryTypes[1]->id;
        $this->ruleMinDistanceFromBigBen->save();

        // Delivery Type 3 does not have any rules, therefore returned

        $product = Product::factory()->state(['weight' => 1500])->create();
        $cart = new Cart();
        $cart->save();
        $cart->products()->attach($product->id, ['quantity' => 2]);

        $res = $this->sendRequest([
                'address' => $this->tenDowningStreet->toArray(),
                'address_id' => "", // It works with null in real requests, but seems to be failing in tests as it is likely to be removed from the request if null.
                'cart_id' => $cart->id
            ]);

        $this->assertCount(2, $res->json('data'));
    }


    protected function sendRequest($data = []): TestResponse
    {
        return $this->postJson(route('api.checkout.get.available-delivery-types', $data));
    }

}
