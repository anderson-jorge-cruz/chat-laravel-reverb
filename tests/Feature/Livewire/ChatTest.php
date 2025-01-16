<?php

use App\Models\User;
use App\Livewire\Chat;
use Livewire\Livewire;
use App\Models\ChatMessage;
use App\Events\NewChatMessageEvent;
use Illuminate\Support\Facades\Event;
use Symfony\Component\Process\Process;
use function Pest\Laravel\assertDatabaseHas;

beforeAll(function () {
    $process = new Process(['php', 'artisan', 'reverb:start']);
    $process->start();
});


it('renders successfully', function () {
    Livewire::test(Chat::class)
        ->assertStatus(200);
});

it('renders the contact list', function () {
    $user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com'
    ]);

    $guest = User::factory()->create();

    Livewire::actingAs($user)->test(Chat::class)->assertCount('contacts', 1);
});

it('should be able to set the active user', function () {
    $user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com'
    ]);

    $guest = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Chat::class)
        ->call('setActiveUser', $guest)
        ->assertSet('activeUser', $guest);
});

it('should be able to create a chat message', function () {
    $user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com'
    ]);

    $guest = User::factory()->create();

    $test = Livewire::actingAs($user)
        ->test(Chat::class)
        ->call('setActiveUser', $guest)
        ->set('message', 'This is the message')
        ->call('newMessage');

    assertDatabaseHas('chat_messages', [
        'message' => 'This is the message',
        'sent_by' => $user->id,
        'sent_to' => $guest->id
    ]);
});

it('should be able to load the chat from the active user', function () {
    $user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com'
    ]);

    $guest = User::factory()->create();

    $test = Livewire::actingAs($user)
        ->test(Chat::class)
        ->call('setActiveUser', $guest)
        ->assertSet('messages', ChatMessage::query()
            ->where(function ($query) use ($guest, $user) {
                $query->where('sent_to', $guest->id)
                    ->where('sent_by', $user->id);
            })
            ->orWhere(function ($query) use ($guest, $user) {
                $query->where('sent_to', $user->id)
                    ->where('sent_by', $guest->id);
            })
            ->orderBy('created_at')
            ->get()
        );
});

it('should dispatch the new message event', function () {
    Event::fake();
    
    $user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com'
    ]);

    $guest = User::factory()->create();

    $message = ChatMessage::factory()->create([
        'sent_to' => $guest->id,
        'sent_by' => $user->id,
        'message' => 'This is the message'
    ]);

    NewChatMessageEvent::dispatch($message, $message->sent_to);

    Event::assertDispatched(NewChatMessageEvent::class, function ($event) use ($message) {
        return $event->message->id === $message->id
            && $event->message->sent_to === $message->sent_to;
    });
});