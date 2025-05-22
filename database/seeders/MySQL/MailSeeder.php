<?php

namespace Database\Seeders\MySQL;

use App\Models\Mail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mails = [
            [
                'id' => 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa',
                'subject' => 'Welcome Alice',
                'user_id' => '11111111-1111-1111-1111-111111111111',
                'body' => 'Hello Alice, welcome to our platform!',
                'sender_id' => '22222222-2222-2222-2222-222222222222',
                'blob_file' => null,
                'file_extention' => null,
                'mime_type' => null,
                'original_name' => null,
                'mail_type' => 'non_official',
                'is_read' => 'unread',
                'recheivedAt' => now(),
            ],
            [
                'id' => 'bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb',
                'subject' => 'Hi Bob',
                'user_id' => '22222222-2222-2222-2222-222222222222',
                'body' => 'Hi Bob, this is a test mail.',
                'sender_id' => '11111111-1111-1111-1111-111111111111',
                'blob_file' => null,
                'file_extention' => null,
                'mime_type' => null,
                'original_name' => null,
                'mail_type' => 'non_official',
                'is_read' => 'unread',
                'recheivedAt' => now(),
            ],
            [
                'id' => 'cccccccc-cccc-cccc-cccc-cccccccccccc',
                'subject' => 'Project Update',
                'user_id' => '11111111-1111-1111-1111-111111111111',
                'body' => 'Project has been updated successfully.',
                'sender_id' => '22222222-2222-2222-2222-222222222222',
                'blob_file' => null,
                'file_extention' => null,
                'mime_type' => null,
                'original_name' => null,
                'mail_type' => 'non_official',
                'is_read' => 'unread',
                'recheivedAt' => now(),
            ],
            [
                'id' => 'dddddddd-dddd-dddd-dddd-dddddddddddd',
                'subject' => 'Meeting Reminder',
                'user_id' => '22222222-2222-2222-2222-222222222222',
                'body' => 'Don\'t forget our meeting tomorrow at 10 AM.',
                'sender_id' => '11111111-1111-1111-1111-111111111111',
                'blob_file' => null,
                'file_extention' => null,
                'mime_type' => null,
                'original_name' => null,
                'mail_type' => 'non_official',
                'is_read' => 'unread',
                'recheivedAt' => now(),
            ],
            [
                'id' => 'eeeeeeee-eeee-eeee-eeee-eeeeeeeeeeee',
                'subject' => 'Invoice',
                'user_id' => '11111111-1111-1111-1111-111111111111',
                'body' => 'Please find attached your invoice.',
                'sender_id' => '22222222-2222-2222-2222-222222222222',
                'blob_file' => null,
                'file_extention' => null,
                'mime_type' => null,
                'original_name' => null,
                'mail_type' => 'non_official',
                'is_read' => 'unread',
                'recheivedAt' => now(),
            ],
        ];

        foreach ($mails as $mail) {
            Mail::updateOrCreate(['id' => $mail['id']], $mail);
        }

        // DB::table('mails')->truncate();
        DB::connection('mysql')->table('mails')->delete();
        DB::connection('mysql')->table('mails')->insert($mails);
    }
}
