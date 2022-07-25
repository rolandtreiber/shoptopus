<?php

namespace App\Console\Commands;

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
    protected $signature = 'shop:fresh {--seed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refreshes all databases';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $file = new Filesystem;
        $file->cleanDirectory('public/uploads');
        $seed = $this->option('seed');
        $this->call('db:wipe', ['--database' => 'logs']);
        if ($seed !== true) {
            $this->call('migrate:fresh');
            $this->info('Database refreshed');
        } else {
            $this->call('migrate:fresh', ['--seed' => true]);
            $this->info('Database refreshed and seeded');
        }

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
        return 1;
    }
}
