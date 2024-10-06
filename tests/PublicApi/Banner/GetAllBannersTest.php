<?php

namespace PublicApi\Banner;

use App\Models\Banner;
use App\Repositories\Local\Banner\BannerRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @group all_banners
 */
class GetAllBannersTest extends TestCase
{

    use RefreshDatabase;

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function it_returns_the_correct_format(): void
    {
        $this->sendRequest()
            ->assertJsonStructure([
                'message',
                'data',
                'next',
                'records',
                'total_records',
            ]);
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function it_returns_all_required_fields(): void
    {
        Banner::factory()->count(2)->state(['enabled' => 1])->create();

        $res = $this->sendRequest();
        $res->assertJsonStructure([
            'data' => [
                app()->make(BannerRepository::class)->getSelectableColumns(false),
            ],
        ]);

        $this->assertCount(2, $res->json('data'));
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function soft_deleted_banners_are_not_returned(): void
    {
        Banner::factory()->count(2)->create(['deleted_at' => now()]);

        $this->assertEmpty($this->sendRequest()->json('data'));
    }

    /**
     * @test
     *
     * @group apiGetAll
     */
    public function it_returns_the_count(): void
    {
        Banner::factory()->count(2)->state(['enabled' => 1])->create();

        $this->assertEquals(2, $this->sendRequest()->json('total_records'));
    }

    protected function sendRequest($data = []): \Illuminate\Testing\TestResponse
    {
        return $this->getJson(route('api.banners.getAll', $data));
    }

}
