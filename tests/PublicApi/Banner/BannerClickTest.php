<?php

namespace PublicApi\Banner;

use App\Models\Banner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @group banner_click
 */

class BannerClickTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function banner_click_increases_total_clicks_number()
    {
        $banners = Banner::factory()->state([
            'total_clicks' => 0
        ])->count(2)->create();

        $response = $this->sendRequest([
            'banner' => $banners[0]->id
        ]);
        $response->assertStatus(200);

        $this->assertDatabaseHas('banners', [
           'id' => $banners[0]->id,
           'total_clicks' => 1
        ]);
        $this->assertDatabaseHas('banners', [
            'id' => $banners[1]->id,
            'total_clicks' => 0
        ]);
    }
    protected function sendRequest($data = []): \Illuminate\Testing\TestResponse
    {
        return $this->post(route('api.banner.clicked', $data));
    }

}
