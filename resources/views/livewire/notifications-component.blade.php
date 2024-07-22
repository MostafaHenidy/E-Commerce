<div>
    <a wire:poll.15s="loadNotifications" data-bs-toggle="modal" data-bs-target="#smallModal" class="notificationsIcon">
        <i class="bi @if ($unreadCount > 0) bi-bell-fill @else bi-bell @endif">
            <span>
                <i class="bi text-danger">{{ $unreadCount }}</i>
            </span>
        </i>
    </a>

    <div class="modal fade" id="smallModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel2">Notifications</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body notificationModal"wire:click.prevent='markasRead'>
                    @foreach ($notifications as $notification)
                        <div class="row @if ($notification->read_at == null) bg-light @else bg-transparent @endif">
                            <div class="col-auto">
                                <span class="bi bi-cart-check-fill fa-2x"></span>
                            </div>
                            <div class="col">
                                <small><strong>Order Placed</strong></small>
                                <div class="my-0 text-muted small">{{ $notification->data['message'] }}</div>
                                <small
                                    class="badge badge-pill badge-light text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click.prevent="clearNotifications">Clear
                        All</button>
                </div>
            </div>
        </div>
    </div>
</div>
