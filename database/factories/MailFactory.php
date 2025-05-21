<?php

namespace Database\Factories;

use App\Models\Mail;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mail>
 */
class MailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $users = User::whereRaw('id::text ~ ?', ['^[a-f0-9]{8}-[a-f0-9]{4}-4[a-f0-9]{3}-[89ab][a-f0-9]{3}-[a-f0-9]{12}$'])
            ->whereNull('deleted_at')
            ->get();

        if ($users->count() < 2) {
            throw new Exception('Tidak cukup user dengan ID UUID valid untuk membuat mail');
        }

        $user = $users->random();
        $sender = $users->where('id', '!=', $user->id)->random();

        return [
            'id' => Str::uuid(),
            'subject' => $this->faker->sentence(),
            'user_id' => $user->id,
            'body' => $this->faker->paragraph(),
            'sender_id' => $sender->id,
            'mail_type' => $this->faker->randomElement(['official', 'non_official']),
            'is_read' => $this->faker->randomElement(['read', 'unread']),
        ];
    }
}
