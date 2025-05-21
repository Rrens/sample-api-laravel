<?php

namespace Database\Seeders;

use App\Models\Mail;
use App\Models\User;
use Exception;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->count() < 2) {
            throw new \Exception('Need at least 2 users with UUID IDs');
        }

        Mail::factory()
            ->count(10)
            ->create();
    }
}
