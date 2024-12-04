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
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class TakeStoreSnapshot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:snapshot {--take} {--upload} {--nointeraction} {--trace-level=} {--name=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Either takes or restores full store snapshots.';

    private function take(bool $nointeraction, bool $upload, string|null $name, string|null $traceLevel) {
        // Take snapshot of the databases
        $date = Carbon::now();
        $snapshotPrefix = $date->format("Y-m-d H:i:s");
        if (!$name && !$nointeraction) {
            $name = $this->ask("Please enter a name or leave it empty to use the name: \"".$snapshotPrefix."\"");
        }
        if ($name !== null) {
            $snapshotPrefix = $name;
        }
        $connections = ["mysql", "logs"];
        foreach ($connections as $connection) {
            $snapshotName = config("app.name") . "-" . $connection . "-" . $snapshotPrefix;
            if ($traceLevel === "DEBUG") {
                $this->line("Creating a DB snapshot for the database: ".$snapshotName);
            }
            $this->call('snapshot:create', ["name" => $snapshotName, "--connection" => $connection]);
            if ($traceLevel === "DEBUG") {
                $this->line("DB snapshot for the database: ".$snapshotName." was successfully created.");
            }
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
        $s3 = "s3-backups";

        Storage::disk($to)->makeDirectory($snapshotPrefix);
        Storage::disk($to)->makeDirectory($snapshotPrefix . '-tmp-img');
        foreach (Storage::disk('uploads')->files() as $file) {
            if (in_array($file, $filesInUse)) {
                if (!Storage::disk($to)->exists($snapshotPrefix . '-tmp-img' . '/' . $file)) {
                    Storage::disk($to)->writeStream($snapshotPrefix . '-tmp-img' . '/' . $file, Storage::disk($from)->readStream($file));
                    if ($traceLevel === "DEBUG") {
                        $this->line("Copied: $file");
                    }
                }
            }
        }

        $zip = new ZipArchive;

        if ($upload) {
            if ($traceLevel === "DEBUG") {
                $this->line("Creating folder in S3: ".config('app.backups_folder')."/".$snapshotPrefix);
            }
            Storage::disk('s3-backups')->makeDirectory(config('app.backups_folder')."/".$snapshotPrefix);
        }
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
        if ($upload) {
            if ($traceLevel === "DEBUG") {
                $this->line("Uploading the media zip file to following location in the s3 bucket: ".config('app.backups_folder') . "/" . $snapshotPrefix . "/media.zip");
            }
            Storage::disk($s3)->writeStream(config('app.backups_folder')."/".$snapshotPrefix."/media.zip", Storage::disk('store-backups')->readStream($snapshotPrefix . "/media.zip"));
            if ($traceLevel === "DEBUG") {
                $this->line("Done, operation successful.");
            }
        }

        // Copying and zipping DB dumps
        if (true === ($zip->open(storage_path('app/store-backups') . "/" . $snapshotPrefix . "/databases.zip", ZipArchive::CREATE | ZipArchive::OVERWRITE))) {
            foreach ($connections as $connection) {
                $fileName = config("app.name") . "-" . $connection . "-" . $snapshotPrefix . ".sql";
                $file = database_path('snapshots')."/".$fileName;
                file_put_contents($file, implode('',
                    array_map(function($data) {
                        return str_replace(config("app.url"), "[APP_BASE_URL]", $data);
                    }, file($file))
                ));

                $zip->addFile(database_path('snapshots')."/".$fileName, $fileName);
            }
            $zip->close();
        }
        if ($upload) {
            if ($traceLevel === "DEBUG") {
                $this->line("Uploading the databases zip file to following location in the s3 bucket: ".config('app.backups_folder') . "/" . $snapshotPrefix . "/databases.zip");
            }
            Storage::disk($s3)->writeStream(config('app.backups_folder') . "/" . $snapshotPrefix . "/databases.zip", Storage::disk('store-backups')->readStream($snapshotPrefix . "/databases.zip"));
            if ($traceLevel === "DEBUG") {
                $this->line("Done, operation successful.");
            }
        }
    }

    private function restore(string|null $name) {
        if ($name && $name !== "") {
            $selected = $name;
        } else {
            $files = Storage::disk('s3-backups')->allFiles(config('app.backups_folder'));
            $files = array_map(function($file) {
                $folder = str_replace(config('app.backups_folder')."/", "", $file);
                $folder = str_replace("/databases.zip", "", $folder);
                $folder = str_replace("/media.zip", "", $folder);
                return $folder;
            }, $files);
            $selected = $this->choice("Which snapshot you would like to restore?", ["None, exit", ...array_unique($files)]);
        }
        if ($selected !== "None, exit") {
            $zip = new ZipArchive;
            Storage::disk('store-backups')->writeStream("/restore-temp/media.zip", Storage::disk('s3-backups')->readStream(config('app.backups_folder'). "/" . $selected . "/media.zip"));
            Storage::disk('store-backups')->writeStream("/restore-temp/databases.zip", Storage::disk('s3-backups')->readStream(config('app.backups_folder'). "/" . $selected . "/databases.zip"));
            $res = $zip->open(storage_path('app/store-backups') . "/restore-temp/databases.zip");
            if ($res === TRUE) {
                $zip->extractTo(storage_path('app/store-backups') . "/restore-temp/databases/");
                $zip->close();
            }

            $files = Storage::disk('store-backups')->allFiles("/restore-temp/databases");
            foreach ($files as $file) {
                $file = storage_path('app/store-backups')."/".$file;
                file_put_contents($file, implode('',
                    array_map(function($data) {
                        return str_replace("[APP_BASE_URL]", config("app.url"), $data);
                    }, file($file))
                ));
                $sql = file_get_contents($file);
                if ($sql) {
                    DB::unprepared($sql);
                }
            }

            $res = $zip->open(storage_path('app/store-backups') . "/restore-temp/media.zip");
            if ($res === TRUE) {
                $zip->extractTo(storage_path('app/store-backups') . "/restore-temp/media/");
                $zip->close();
            }

            $file = new Filesystem;
            $file->cleanDirectory('public/uploads');

            $files = Storage::disk('store-backups')->allFiles("/restore-temp/media");
//            var_dump($files);
//            return 0;
            foreach ($files as $file) {
                Storage::disk('uploads')->writeStream(str_replace("restore-temp/media/", "", $file), Storage::disk('store-backups')->readStream($file));
            }

        }
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $take = $this->option('take');
        $nointeraction = $this->option('nointeraction');
        $upload = $this->option('upload');
        $name = $this->option('name');
        $traceLevel = $this->option('trace-level');
        if (!$take) {
            $operation = $this->choice("What would you like to do?", [
                "Take a snapshot locally",
                "Take a snapshot and upload it to S3",
                "Restore a snapshot from S3",
                "Exit"
            ]);

            switch ($operation) {
                case "Take a snapshot locally":
                    $take = true;
                    break;
                case "Take a snapshot and upload it to S3":
                    $take = true;
                    $upload = true;
                    break;
                case "Restore a snapshot from S3":
                    $take = false;
                    break;
            }
        }
        if ($take) {
            $this->take($nointeraction, $upload, $name, $traceLevel);
        } else {
            $this->restore($name);
        }
        return 0;
    }
}
