<div class="flex gap-2 h-[400px]">
    <div class="w-1/4 p-4 overflow-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-track]:rounded-full
        [&::-webkit-scrollbar-track]:bg-gray-100
        [&::-webkit-scrollbar-thumb]:rounded-full
      [&::-webkit-scrollbar-thumb]:bg-gray-300">
        {{-- Contacts List --}}

        <ul class="flex flex-col gap-2">
        @if($contacts)
            @foreach ($contacts as $user)
                <li class="w-full items-center text-left @if ($this->activeUser?->id == $user->id)
                    bg-slate-300
                @else
                    bg-slate-100
                @endif p-2 hover:bg-slate-300 cursor-pointer rounded"
                    wire:key="{{$user->id}}"
                    wire:click="setActiveUser({{ $user }})"
                >
                    {{$user->name}}
                </li>
            @endforeach
        @endif

        @empty($contacts)
            <li>There is no users to show here.</li>
        @endempty

        </ul>
    </div>

    <div class="flex flex-col p-4 w-3/4">
        {{-- Chat --}}
        <div class="w-full h-full flex flex-col-reverse rounded bg-slate-100 p-2 overflow-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-track]:rounded-full
        [&::-webkit-scrollbar-track]:bg-gray-100
        [&::-webkit-scrollbar-thumb]:rounded-full
      [&::-webkit-scrollbar-thumb]:bg-gray-300">
            {{-- Messages --}}
            <ul>
            @if($this->messages && $this->messages->count() != 0)
                @foreach ($this->messages as $message)
                    <div class="w-full mb-3 @if($message->sent_to == $this->activeUser->id) messageFromMe @else messageToMe @endif">
                        <p class="inline-block p-2 rounded-md">{{$message->message}}</p>
                        <span class="block mt-1 text-xs text-gray-500">{{ $message->created_at->format('d/m/Y H:i:s') }}</span>
                    </div>
                @endforeach
            @else
                <li class="text-xl text-center mb-8">There is no messages to show in this chat.</li>
            @endif
            </ul>
        </div>

        @isset($this->activeUser)
            <div class="border-t-2 pt-2 w-full">
                {{-- Message Form --}}
                <form wire:submit.prevent="newMessage" class="flex">
                    <input wire:model.live="message" type="text" class="w-3/4 border-slate-300 rounded-l placeholder:text-slate-500 focus:border-indigo-500" placeholder="Text here">
                    <button class="w-1/4 bg-indigo-500 hover:bg-indigo-700 text-white rounded-r" @if($this->message == '') disabled @endif>Send</button>
                </form>
            </div>
        @endisset
    </div>
</div>
