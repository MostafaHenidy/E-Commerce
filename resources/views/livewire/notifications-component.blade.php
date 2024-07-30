<div class="notification-icon">
    {{-- wire:poll.5s="loadNotifications" --}}
    <a data-bs-toggle="modal" data-bs-target="#smallModal">
        <i class="bi @if ($unreadCount > 0) bi-bell-fill @else bi-bell @endif"></i>
        <span class="text-primary">{{ $unreadCount }}</span>
    </a>

    <div class="modal fade" id="smallModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Notifications</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($notifications->count() > 0)
                        @foreach ($notifications as $notification)
                            <div class="row">
                                <div class="col-auto">
                                    <i class="bi bi-file-earmark-check fa-2x"></i>
                                </div>
                                <div
                                    class="col @if ($notification->read_at == null) text-primary @else text-secondary @endif">
                                    <small><strong>User Placed an Order!</strong></small>
                                    <p class="my-0 small">{{ $notification->data['message'] }}</p>
                                    <small>{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p>No notifications available.</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button wire:click='markAsRead' class="btn btn-primary">Mark All as Read</button>
                    <button wire:click='clearAll' class="btn btn-secondary">Clear All </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('assets-vendor') }}/js/config.js"></script>

<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = false;

    var pusher = new Pusher('e09126a1f015f2b05a1b', {
        cluster: 'eu'
    });

    var channel = pusher.subscribe('order_placed_channel');
    channel.bind('App\\Events\\UserPlacedOrderEvent', function(data) {
        // alert(JSON.stringify(data));
        console.log((data['message']));
        Livewire.dispatch('notificationRead');
    });
</script>
