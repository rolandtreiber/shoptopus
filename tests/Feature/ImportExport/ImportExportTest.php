<?php

namespace Tests\Feature\ImportExport;

use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeOption;
use App\Models\ProductCategory;
use App\Models\ProductTag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Shoptopus\ExcelImportExport\ModelExport;
use Shoptopus\ExcelImportExport\ModelExportSheet;
use Shoptopus\ExcelImportExport\ModelTemplateExport;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tests\AdminControllerTestCase;

/**
 * @group import-export
 */
class ImportExportTest extends AdminControllerTestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function test_template_can_be_exported_with_the_right_headings()
    {
        Excel::fake();

        $this->actingAs(User::where('email', 'superadmin@m.com')->first())
            ->get('/io/template/?name=product-categories&model=ProductCategory');

        $categoryExportableFields = (new ProductCategory())->getExportableFields();
        $categoryExportableRelationships = (new ProductCategory())->getExportableRelationships();

        $allHeaderFields = array_merge($categoryExportableFields, $categoryExportableRelationships);

        Excel::assertDownloaded('product-categories - TEMPLATE.xlsx', function(ModelTemplateExport $export) use ($allHeaderFields) {
            return !array_diff($export->headings(), $allHeaderFields);
        });
    }

    /**
     * @test
     */
    public function test_export_has_all_required_sheets()
    {
        Excel::fake();

        $this->actingAs(User::where('email', 'superadmin@m.com')->first())
            ->get('/io/export?name=products&models[]=Product&models[]=ProductCategory&models[]=ProductAttribute&models[]=ProductAttributeOption&models[]=ProductTag');

        Excel::assertDownloaded('products.xlsx', function(ModelExport $export) {
            $modelsFromSheets = array_map(function(ModelExportSheet $sheet) {
                return $sheet->getModelClass();
            }, $export->sheets());

            $models = [
                Product::class,
                ProductCategory::class,
                ProductAttribute::class,
                ProductAttributeOption::class,
                ProductTag::class
            ];

            return !array_diff($modelsFromSheets, $models);
        });
    }

    /**
     * @test
     */
    public function test_export_has_the_right_data()
    {
        Excel::fake();

        $products = Product::factory()->count(2)->create();
        $this->actingAs(User::where('email', 'superadmin@m.com')->first())
            ->get('/io/export?name=products&models[]=Product');

        Excel::assertDownloaded('products.xlsx', function(ModelExport $export) use ($products) {
            /** @var ModelExportSheet $sheet */
            $sheet = $export->sheets()[0];
            return $sheet->collection()->contains($products[0]) && $sheet->collection()->contains($products[1]);
        });
    }

    /**
     * @test
     */
    public function test_import_file_validates_success()
    {
        $path  = __DIR__.'/TestData/product-categories-import.xlsx';
        $file = new UploadedFile ($path, 'product-categories-import.xlsx', null, null, true);

        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->post('/io/validate', [
            'file' => $file
        ]);

        $response->assertJsonFragment([
            "status" => "success"
        ]);
    }

    /**
     * @test
     */
    public function test_import_file_validates_fail()
    {
        $path  = __DIR__.'/TestData/product-categories-import-invalid.xlsx';
        $file = new UploadedFile ($path, 'product-categories-import-invalid.xlsx', null, null, true);

        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->post('/io/validate', [
            'file' => $file
        ]);

        $response->assertJsonFragment([
            "errors" => "Invalid slugs found: INVALID"
        ]);

        $response->assertJsonFragment([
            "status" => "invalid data"
        ]);
    }

    /**
     * @test
     */
    public function test_import_file_imports_data()
    {
        $path  = __DIR__.'/TestData/product-categories-import.xlsx';
        $file = new UploadedFile ($path, 'product-categories-import.xlsx', null, null, true);

        $this->actingAs(User::where('email', 'superadmin@m.com')->first());
        $response = $this->post('/io/import', [
            'file' => $file
        ]);

        $this->assertDatabaseHas('product_categories', [
            'slug' => 'furniture'
        ]);
        $response->assertJsonFragment([
            "status" => "success"
        ]);
    }

    /**
     * @test
     */
    public function test_export_requires_permission()
    {

        $response = $this->actingAs(User::where('email', 'customer@m.com')->first())
            ->get('/io/export?name=products&models[]=Product&models[]=ProductCategory&models[]=ProductAttribute&models[]=ProductAttributeOption&models[]=ProductTag');

        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_template_Export_requires_permission()
    {
        $response = $this->actingAs(User::where('email', 'customer@m.com')->first())
            ->get('/io/export?name=products&models[]=Product');

        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_import_requires_permission()
    {
        $path  = __DIR__.'/TestData/product-categories-import.xlsx';
        $file = new UploadedFile ($path, 'product-categories-import.xlsx', null, null, true);

        $this->actingAs(User::where('email', 'customer@m.com')->first());
        $response = $this->post('/io/import', [
            'file' => $file
        ]);

        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function test_import_validation_requires_permission()
    {
        $path  = __DIR__.'/TestData/product-categories-import.xlsx';
        $file = new UploadedFile ($path, 'product-categories-import.xlsx', null, null, true);

        $this->actingAs(User::where('email', 'customer@m.com')->first());
        $response = $this->post('/io/validate', [
            'file' => $file
        ]);

        $response->assertForbidden();
    }

}
