<?php

namespace App\Livewire;

use App\Events\NewChatMessageEvent;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Chat extends Component
{
    public User|null $activeUser = null;
    public $messages;
    public string $message = '';
    public Collection $contacts;

    public function mount()
    {
        $this->contacts = User::all()->except(Auth::id());
    }

    public function getListeners()
    {
        return [
            "echo-private:chat-messages." . Auth::id() . ",.NewChatMessageEvent" => "loadBroadcastedMessage"
        ];
    }

    public function setActiveUser(User $user): void
    {
        $this->activeUser = $user;
        $this->loadMessages();
    }

    
    public function loadBroadcastedMessage(array $data)
    {
        $message = ChatMessage::find($data['message']);
        if ($message->sent_to == Auth::id() && $message->sent_by == $this->activeUser?->id) {
            $this->messages->push($message);
        } else {
            $this->contacts->filter(function (User $user) use ($message) {
                if ($user->id == $message->SentBy->id) {
                    $user->chatNotification = true;
                }
            });
        }
    }

    public function loadMessages()
    {
        $currentUserId = Auth::id();
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

    public function newMessage(): void
    {
        $message = ChatMessage::query()->create([
            'message' => $this->message,
            'sent_to' => $this->activeUser->id,
            'sent_by' => Auth::id()
        ]);

        if (is_null($this->messages)) {
            $this->loadMessages();
        } else {
            $this->messages->push($message);
        }

        $this->reset('message');

        NewChatMessageEvent::dispatch($message, $message->sent_to);   
    }

    public function render()
    {
        return view('livewire.chat')->with([
            'contacts' => $this->contacts->count() < 1 ? null : $this->contacts,
        ]);
    }
}
