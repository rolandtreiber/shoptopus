<?php

namespace App\Console\Commands;

use App\Services\Local\Auth\AuthServiceInterface;
use Illuminate\Console\Command;

class flushRolesAndPermissionsCommand extends Command
{
    private AuthServiceInterface $authService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:flush-roles-and-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @param AuthServiceInterface $authService
     */
    public function __construct(AuthServiceInterface $authService)
    {
        parent::__construct();
        $this->authService = $authService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->authService->flushRolesAndPermissions();
        return 0;
    }
}
