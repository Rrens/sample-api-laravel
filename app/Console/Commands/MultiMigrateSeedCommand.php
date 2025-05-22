<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\DatabaseCleaner;

class MultiMigrateSeedCommand extends Command
{
    protected $signature = 'multi:migrate-seed';
    protected $description = 'Run migration and seeder for MySQL and PostgreSQL databases';

    public function handle(): int
    {
        $this->info('Migrating MySQL default migrations...');
        $this->call('migrate:fresh', [
            '--database' => 'mysql',
        ]);

        $this->info('Migrating MySQL custom migrations...');
        $this->call('migrate', [
            '--path' => 'database/migrations/mysql',
            '--database' => 'mysql',
        ]);

        // $this->info('Migrating PostgreSQL default migrations...');
        // $this->call('migrate:fresh', [
        //     '--database' => 'pgsql',
        // ]);

        $this->info('Migrating PostgreSQL custom migrations...');
        $this->call('migrate:fresh', [
            '--path' => 'database/migrations/pgsql',
            '--database' => 'pgsql',
        ]);



        $this->info('Cleaning MySQL...');
        DatabaseCleaner::clean(['mails', 'users'], 'mysql');

        $this->info('Cleaning PostgreSQL...');
        DatabaseCleaner::clean(['mails', 'users'], 'pgsql');

        $this->info('Seeding Users Mysql');
        $this->call(\Database\Seeders\Pgsql\UserPgsqlSeeder::class);
        $this->info('Seeding Users PostgeSQL');
        $this->call(\Database\Seeders\MySQL\UserSeeder::class);

        $this->info('Seeding Mails Mysql');
        $this->call(\Database\Seeders\Pgsql\MailPgsqlSeeder::class);
        $this->info('Seeding Mails PostgeSQL');
        $this->call(\Database\Seeders\MySQL\MailSeeder::class);

        $this->info('✅ All migrations and seeders completed!');
        return Command::SUCCESS;
    }
}
