<!-- resources/views/livewire/chat.blade.php -->
<div>
    <div class="chat-messages mb-3">
        @foreach ($messages as $message)
            <div
                class="d-flex mb-2 @if ($message->sender_id == (Auth::guard('admin')->check() ? Auth::guard('admin')->id() : Auth::id())) justify-content-end @else justify-content-start @endif">
                <div
                    class="message p-2 rounded @if ($message->sender_type == 'App\\Models\\Admin') bg-secondary text-white @else bg-primary text-white @endif">
                    <strong>{{ $message->sender_type == 'App\\Models\\Admin' ? 'Admin' : $message->sender->name }}</strong>:<br>
                    {{ $message->message }}<br>
                    <small class="text-muted">{{ $message->created_at->diffForHumans() }}</small>
                </div>
            </div>
        @endforeach
    </div>

    <form class="d-flex" wire:submit.prevent="sendMessage">
        <input class="form-control me-2" type="text" wire:model="message" placeholder="Type a message" />
        <button class="btn btn-primary" type="submit"><i class="fa-solid fa-paper-plane"></i></button>
    </form>
    <style>
        .message {
            max-width: 60%;
            word-wrap: break-word;
        }
    </style>
</div>

<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    Pusher.logToConsole = false;

    var pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
        cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
        encrypted: true
    });

    var channel = pusher.subscribe('chat');
    channel.bind('App\\Events\\MessageSent', function(data) {
        Livewire.dispatch('messageReceived');
    });
</script>
