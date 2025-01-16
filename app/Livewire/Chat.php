<?php

namespace App\Livewire;

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Chat extends Component
{
    public User|null $activeUser = null;
    public $messages = null;
    public string $message = '';

    public function setActiveUser(User $user): void
    {
        $currentUserId = Auth::id();
        $this->activeUser = $user;
        $this->messages = ChatMessage::query()
            ->where(function ($query) use ($currentUserId) {
                $query->where('sent_to', $this->activeUser->id)
                    ->where('sent_by', $currentUserId);
            })
            ->orWhere(function ($query) use ($currentUserId) {
                $query->where('sent_to', $currentUserId)
                    ->where('sent_by', $this->activeUser->id);
            })
            ->orderBy('created_at')
            ->get();
    }

    public function newMessage()
    {
        $message = ChatMessage::query()->create([
            'message' => $this->message,
            'sent_to' => $this->activeUser->id,
            'sent_by' => Auth::id()
        ]);

        $this->messages->push($message);

        $this->reset('message');
    }

    public function render()
    {
        $contacts = User::all()->except(Auth::id());

        return view('livewire.chat')->with([
            'contacts' => $contacts->count() < 1 ? null : $contacts,
        ]);
    }
}
