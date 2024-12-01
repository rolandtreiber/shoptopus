<?php

namespace App\Console\Commands;

use App\Models\Banner;
use App\Models\FileContent;
use App\Models\PaidFileContent;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeOption;
use App\Models\ProductCategory;
use App\Models\ProductTag;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class TakeStoreSnapshot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:take-store-snapshot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Takes a DB snapshot as well as cleans and zips the uploads folder';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Take snapshot of the databases
        $date = Carbon::now();
        $snapshotPrefix = $date->format("Y-m-d H:i:s");
        $connections = ["mysql", "logs"];
        foreach ($connections as $connection) {
            $snapshotName = config("app.name") . "-" . $connection . "-" . $snapshotPrefix;
            $this->call('snapshot:create', ["name" => $snapshotName, "--connection" => "mysql"]);
        }

        // Take a list of the files in use

        // File Contents
        $filesInUse = [];
        $fileContents = FileContent::all()->pluck('file_name')->toArray();
        $filesInUse = array_merge($filesInUse, $fileContents);

        // Banner BG images
        $bannerBgImageFilenames = Banner::all()->pluck("background_image.file_name")->toArray();
        $filesInUse = array_merge($filesInUse, $bannerBgImageFilenames);

        // Paid file contents
        $fileNames = PaidFileContent::all()->pluck("file_name")->toArray();
        $filesInUse = array_merge($filesInUse, $fileNames);

        // Attribute Options
        $fileNames = ProductAttributeOption::all()->pluck("image.file_name")->toArray();
        $filesInUse = array_merge($filesInUse, $fileNames);

        // Attributes
        $fileNames = ProductAttribute::all()->pluck("image.file_name")->toArray();
        $filesInUse = array_merge($filesInUse, $fileNames);

        // Categories
        $fileNames = ProductCategory::all()->pluck("menu_image.file_name")->toArray();
        $filesInUse = array_merge($filesInUse, $fileNames);

        $fileNames = ProductCategory::all()->pluck("header_image.file_name")->toArray();
        $filesInUse = array_merge($filesInUse, $fileNames);

        // Tags
        $fileNames = ProductTag::all()->pluck("badge.file_name")->toArray();
        $filesInUse = array_merge($filesInUse, $fileNames);

        // Products
        $fileNames = Product::all()->pluck("cover_photo.file_name")->toArray();
        $filesInUse = array_merge($filesInUse, $fileNames);

        // Users
        $fileNames = User::all()->pluck("avatar.file_name")->toArray();
        $filesInUse = array_merge($filesInUse, $fileNames);

        $filesInUse = array_filter($filesInUse);

        // Copying and zipping media files
        $from = "uploads";
        $to = "store-backups";

        Storage::disk($to)->makeDirectory($snapshotPrefix);
        Storage::disk($to)->makeDirectory($snapshotPrefix . '-tmp-img');
        foreach (Storage::disk('uploads')->files() as $file) {
            if (in_array($file, $filesInUse)) {
                if (!Storage::disk($to)->exists($snapshotPrefix . '-tmp-img' . '/' . $file)) {
                    Storage::disk($to)->writeStream($snapshotPrefix . '-tmp-img' . '/' . $file, Storage::disk($from)->readStream($file));
                    $this->line("Copied: $file");
                }
            }
        }

        $zip = new ZipArchive;

        if (true === ($zip->open(storage_path('app/store-backups') . "/" . $snapshotPrefix . "/media.zip", ZipArchive::CREATE | ZipArchive::OVERWRITE))) {
            foreach (Storage::disk($to)->allFiles($snapshotPrefix . '-tmp-img') as $file) {
                $name = basename($file);
                if ($name !== '.gitignore') {
                    $zip->addFile(storage_path('app/store-backups') . "/" . $file, $name);
                }
            }
            $zip->close();
            Storage::disk("store-backups")->deleteDirectory($snapshotPrefix . '-tmp-img');
        }

        // Copying and zipping DB dumps
        if (true === ($zip->open(storage_path('app/store-backups') . "/" . $snapshotPrefix . "/databases.zip", ZipArchive::CREATE | ZipArchive::OVERWRITE))) {
            foreach ($connections as $connection) {
                $fileName = config("app.name") . "-" . $connection . "-" . $snapshotPrefix . ".sql";
                $file = database_path('snapshots')."/".$fileName;
                $zip->addFile(database_path('snapshots')."/".$fileName, $file);
            }
            $zip->close();
        }

    }
}
