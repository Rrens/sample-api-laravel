<?php

namespace Database\Seeders\Pgsql;

use App\Models\Pgsql\UserPgsql;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserPgsqlSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // DB::table('users')->truncate();
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
        //     // Gunakan updateOrCreate biar aman (optional)
        //     UserPgsql::updateOrCreate(['id' => $user['id']], $user);
        // }


        DB::connection('pgsql')->table('users')->delete();
        DB::connection('pgsql')->table('users')->insert($users);
    }
}
