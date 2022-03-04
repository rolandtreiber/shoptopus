<?php

namespace Tests\Feature\AdminBaseCRUD;

use App\Models\Banner;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\AdminControllerTestCase;

/**
 * @group admin-base-crud
 * @group banners
 * @see \App\Http\Controllers\Admin\BannersController
 */
class BannerControllerTest extends AdminControllerTestCase
{
    /**
     * @test
     */
    public function test_banners_can_be_listed()
    {
        $banners = Banner::factory()->count(2)->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.api.index.banners', [
            'page' => 1,
            'paginate' => 20,
            'filters' => []
        ]));
        $response->assertJsonFragment([
            'id' => $banners[0]->id
        ]);
        $response->assertJsonFragment([
            'id' => $banners[1]->id
        ]);
    }

    /**
     * @test
     */
    public function test_banner_can_be_shown()
    {
        $banner = Banner::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->get(route('admin.api.show.banner', [
            'banner' => $banner->id,
            'page' => 1,
            'paginate' => 20,
            'filters' => []
        ]));
        $response->assertJsonFragment([
            'id' => $banner->id
        ]);
    }

    /**
     * @test
     */
    public function test_banner_can_be_created()
    {
        Storage::fake('uploads');
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->post(route('admin.api.create.banner'), [
            'title' => json_encode([
                'en' => 'New Products',
                'de' => 'Neue Prodikte'
            ]),
            'description' => json_encode([
                'en' => 'Description',
                'de' => 'Bezeichnung'
            ]),
            'button_text' => json_encode([
               'en' => 'Click Here',
               'de' => 'Clicken Sie Hier'
            ]),
            'show_button' => true,
            'button_url' => 'https://google.com',
            'background_image' => UploadedFile::fake()->image('banner.jpg'),
            'enabled' => true
        ]);
        $bannerId = $response->json()['data']['id'];
        $banner = Banner::find($bannerId);
        Storage::disk('uploads')->assertExists($banner->background_image->file_name);
        $this->assertEquals('New Products', $banner->setLocale('en')->title);
        $this->assertEquals('Neue Prodikte', $banner->setLocale('de')->title);
        $this->assertEquals('Description', $banner->setLocale('en')->description);
        $this->assertEquals('Bezeichnung', $banner->setLocale('de')->description);
        $this->assertEquals('Click Here', $banner->setLocale('en')->button_text);
        $this->assertEquals('Clicken Sie Hier', $banner->setLocale('de')->button_text);
        $this->assertEquals(true, $banner->setLocale('de')->show_button);
        $this->assertEquals(true, $banner->setLocale('de')->enabled);
    }

    /**
     * @test
     */
    public function test_banner_can_be_updated()
    {
        Storage::fake('uploads');
        $banner = Banner::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->patch(route('admin.api.update.banner', [
            'banner' => $banner
        ]), [
            'title' => json_encode([
                'en' => 'New Products',
                'de' => 'Neue Prodikte'
            ]),
            'description' => json_encode([
                'en' => 'Description',
                'de' => 'Bezeichnung'
            ]),
            'button_text' => json_encode([
                'en' => 'Click Here',
                'de' => 'Clicken Sie Hier'
            ]),
            'show_button' => true,
            'button_url' => 'https://google.com',
            'background_image' => UploadedFile::fake()->image('banner.jpg'),
            'enabled' => true
        ]);
        $response->assertOk();
        $bannerId = $response->json()['data']['id'];
        $banner = Banner::find($bannerId);
        Storage::disk('uploads')->assertExists($banner->background_image->file_name);
        $this->assertEquals('New Products', $banner->setLocale('en')->title);
        $this->assertEquals('Neue Prodikte', $banner->setLocale('de')->title);
        $this->assertEquals('Description', $banner->setLocale('en')->description);
        $this->assertEquals('Bezeichnung', $banner->setLocale('de')->description);
        $this->assertEquals('Click Here', $banner->setLocale('en')->button_text);
        $this->assertEquals('Clicken Sie Hier', $banner->setLocale('de')->button_text);
        $this->assertEquals(true, $banner->setLocale('de')->show_button);
        $this->assertEquals(true, $banner->setLocale('de')->enabled);    }

    /**
     * @test
     */
    public function test_banner_can_be_deleted()
    {
        $banner = Banner::factory()->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $this->delete(route('admin.api.delete.banner', $banner));

        $this->assertSoftDeleted($banner);
    }

    /**
     * @test
     */
    public function test_banner_creation_validation()
    {
        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->post(route('admin.api.create.banner'), [
            'description' => json_encode([
                'en' => 'Does not contain meat or dairy products',
                'de' => 'Enthält kein Fleisch oder Milchprodukte'
            ]),
            'display_badge' => 1
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function test_banner_creation_requires_appropriate_permission()
    {
        $this->actingAs(User::where('email', 'customer@m.com')->first());
        $response = $this->post(route('admin.api.create.banner'), [
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
    public function test_banner_updating_requires_appropriate_permission()
    {
        $banner = Banner::factory()->create();
        $this->actingAs(User::where('email', 'customer@m.com')->first());
        $response = $this->patch(route('admin.api.update.banner', [
            'banner' => $banner->id
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
