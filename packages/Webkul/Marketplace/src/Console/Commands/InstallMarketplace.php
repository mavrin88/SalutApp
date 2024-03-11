<?php

namespace Webkul\Marketplace\Console\Commands;

use Illuminate\Console\Command;

class InstallMarketplace extends Command
{
    /**
     * Holds the execution signature of the command needed
     * to be executed for installing marketplace
     */
    protected $signature = 'marketplace:install';

    /**
     * Will inhibit the description related to this
     * command's role
     */
    protected $description = 'Install Marketplace Module';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Does the all sought of lifting required to be performed for
     * installing marketplace
     */
    public function handle()
    {
        // running `composer dump-autoload`
        $this->warn("Step 1: running 'composer dump-autoload'");
        $autoload = shell_exec('composer dump-autoload');
        $this->info($autoload);

        // running `composer require laravel/helpers`
        $this->warn("Step 2: running 'composer require laravel/helpers'");
        $laravel_helpers = shell_exec('composer require laravel/helpers');
        $this->info($laravel_helpers);

        // running `php artisan optimize`
        $optimize = shell_exec('php artisan optimize:clear');
        $this->info($optimize);

        // running `php artisan migrate`
        $this->warn("Step 3: Migrating all tables into database (will take a while)...");
        $migrate = shell_exec('php artisan migrate');
        $this->info($migrate);

        // running `php artisan db:seed`
        $this->warn('Step 4: Seeding data into database tables...');
        if ($this->confirm('Confirm: Please confirm, If you are windows user then press Y else N.')) {
            $seeding = shell_exec('php artisan db:seed --class=Webkul\\Marketplace\\Database\\Seeders\\DatabaseSeeder');
        } else {
            $seeding = shell_exec('php artisan db:seed --class="Webkul\Marketplace\Database\Seeders\DatabaseSeeder"');
        }
        $this->info($seeding);

        // running `php artisan vendor:publish --all`
        $this->warn('Step 5: Publishing Assets and Configurations...');
        $result = $this->call('vendor:publish', [
            '--provider' => \Webkul\Marketplace\Providers\MarketplaceServiceProvider::class,
            '--force'    => true,
        ]);
        $this->info($result);

        // running `php artisan optimize`
        $optimize = shell_exec('php artisan optimize');
        $this->info($optimize);
    }
}
