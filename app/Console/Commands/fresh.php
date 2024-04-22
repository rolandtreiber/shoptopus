<?php

namespace App\Console\Commands;

use App\Models\Product;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\PaymentProviderSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\TestData\AddressSeeder;
use Database\Seeders\TestData\CartSeeder;
use Database\Seeders\TestData\OrderSeeder;
use Database\Seeders\TestStore1Seeder;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;

class fresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shop:fresh {--cypress}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refreshes all databases';

    private $choices = [
        "Wipe the databases without seeding any data",
        "Wipe the databases and seed randomly generated data",
        "Wipe the databases and seed the fix test store data (id-s, timestamps and translations (if configured so) are generated on the fly)",
        "Wipe the databases and restore the snapshot saved earlier"
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $cypress = $this->option('cypress');

        if (!$cypress) {
            $mode = $this->choice(
                'What would you like to do?',
                $this->choices,
                0
            );
        } else {
            $mode = 'cypress';
        }

        $file = new Filesystem;
        $file->cleanDirectory('public/uploads');
        $file->cleanDirectory('storage/app/paid');
        $this->call('db:wipe', ['--database' => 'logs']);

        switch ($mode) {
            case $this->choices[0]:
                $this->call('migrate:fresh');
                $this->info('Database refreshed');
                break;
            case $this->choices[1]:
                $this->call('migrate:fresh', ['--seed' => true]);
                $this->info('Database refreshed and seeded');
                break;
            case $this->choices[2]:
                if (config('app.auto_translate_test_data') === true) {
                    if ($this->confirm('Auto-translation is turned on. This will take significantly longer and use the Google Translate Api quite a bit. Are you sure you want to proceed?', true)) {
                        $this->call('migrate:fresh');
                        $this->call('db:seed', ['--class' => "TestStore1Seeder"]);
                        $this->info('Database refreshed and seeded');
                    } else {
                        return 0;
                    }
                } else {
                    $this->call('migrate:fresh');
                    $this->call('db:seed', ['--class' => "TestStore1Seeder"]);
                }
                break;
            case $this->choices[3]:
                $this->call('db:seed', ['--class' => "DumpImportSeeder"]);
                $this->call('migrate:refresh', ['--path' => '/database/migrations/2021_09_10_075846_create_audits_table.php']);
                $this->info('Database refreshed and seeded');
                break;
            case 'cypress':
                $this->call('migrate:fresh');
                $this->call('db:seed', ['--class' => "TestStore1Seeder"]);
        }

        $this->info('Indexing products in Elasticsearch');
        $this->call('scout:flush', ['model' => Product::class]);
        $this->call('scout:import', ['model' => Product::class]);
        $this->info('Products indexed in Elasticsearch');

        Artisan::call('passport:install');
        $output = Artisan::output();
        $this->info($output);

        preg_match_all('/^Client ID: (.+)$/m', $output, $clientIDMatches);
        preg_match_all('/^Client secret: (.+)$/m', $output, $matches);

        $id = $clientIDMatches[1][1];
        $secret = $matches[1][1];

        $envFile = file_get_contents('.env');

        $envFile = preg_replace('/^PASSPORT_GRANT_ID=(.*)$/m', 'PASSPORT_GRANT_ID=' . $id, $envFile);
        $envFile = preg_replace('/^PASSPORT_SECRET=(.*)$/m', 'PASSPORT_SECRET=' . $secret, $envFile);

        file_put_contents('.env', $envFile);
        $this->info('.env file updated with new id / secret: ' . $id . ' / ' . $secret);

        return 0;
    }
}
