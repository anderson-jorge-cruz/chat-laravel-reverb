<?php

namespace Database\Seeders;

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class ChatMessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sentBy = User::query()->find(1)->id;
        User::all()->except($sentBy)->each(function (User $user) use ($sentBy) {
            ChatMessage::factory()->count(5)->create([
                'sent_by' => $sentBy,
                'sent_to' => $user->id
            ]);

            ChatMessage::factory()->count(5)->create([
                'sent_by' => $user->id,
                'sent_to' => $sentBy
            ]);
        });
    }
}
