<?php

namespace Database\Seeders\MySQL;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'id' => '11111111-1111-1111-1111-111111111111',
                'name' => 'Alice',
                'email' => 'alice@example.com',
                'password' => bcrypt('password'),
            ],
            [
                'id' => '22222222-2222-2222-2222-222222222222',
                'name' => 'Bob',
                'email' => 'bob@example.com',
                'password' => bcrypt('password'),
            ],
        ];

        // foreach ($users as $user) {
        //     User::updateOrCreate(['id' => $user['id']], $user);
        // }

        DB::connection('mysql')->table('users')->delete();
        DB::connection('mysql')->table('users')->insert($users);
    }
}
