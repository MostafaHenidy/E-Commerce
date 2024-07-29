<div>
    <div wire:poll>
        @if ($messages->count())
            @foreach ($messages as $message)
                @if ($message->user_id === Auth::id())
                    <li class="clearfix">
                        <div class="message my-message float-end">{{ $message->message }}</div>
                    </li>
                @else
                    <li class="clearfix">
                        <div class="message other-message float-start">{{ $message->message }}</div>
                    </li>
                @endif
            @endforeach
        @else
            <p>No messages yet..</p>
        @endif
    </div>
    <div class="chat-message clearfix">
        <div class="input-group mb-0">
            <form class="input-group d-flex" wire:submit.prevent='send'>
                <button type="submit" class="input-group-text"><i class="bi bi-send"></i></button>
                <input wire:model='messageText' type="text" class="form-control" placeholder="Enter text here...">
            </form>
        </div>
    </div>
</div>
