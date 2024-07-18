<nav class="navbar navbar-example navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('vendor.products.index') }}">E-Commerce</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-ex-2"
            aria-controls="navbar-ex-2" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbar-ex-2">
            <div class="navbar-nav me-auto">
                <a class="nav-item nav-link active" href="{{ route('vendor.products.index') }}">Home</a>
                <a class="nav-item nav-link" href="{{ route('vendor.index') }}">Profile</a>
                <a class="nav-item nav-link" href="{{ route('vendor.orders.index') }}">Orders</a>
            </div>
            <a data-bs-toggle="modal" data-bs-target="#smallModal" class="notificationsIcon">
                <i class="bi  @if (count(Auth::guard('vendor')->user()->unreadnotifications) > 0) bi-bell-fill @else bi-bell @endif">
                    <span>
                        <i class="bi text-danger">{{ count(Auth::guard('vendor')->user()->unreadnotifications) }}
                        </i>
                    </span>
                </i>

            </a>

            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                <div class="avatar avatar-online">
                    <img src="{{ Auth::guard('vendor')->user()->avatar }}" alt class="w-px-40 h-px-40 rounded-circle" />
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="#">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar avatar-online">
                                    <img src="{{ Auth::guard('vendor')->user()->avatar }}" alt
                                        class="w-px-40 h-px-40 rounded-circle" />
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <span class="fw-semibold d-block">{{ Auth::guard('vendor')->user()->name }}</span>
                                <small class="text-muted">User</small>
                            </div>
                        </div>
                    </a>
                </li>
                <li>
                    <div class="dropdown-divider"></div>
                </li>
                <li>
                    <a class="dropdown-item" href="#">
                        <i class="bx bx-cog me-2"></i>
                        <span class="align-middle">Settings</span>
                    </a>
                </li>
                <li>
                    <form action="{{ route('vendor.logout') }}" method="POST">
                        @csrf
                        <a class="dropdown-item" href="javascript:{}"
                            onclick="this.closest('form').submit();return false;">
                            <i class="bx
                            bx-power-off me-2"></i>
                            <span class="align-middle">Log Out</span>
                        </a>
                    </form>
                </li>
            </ul>

        </div>
    </div>
</nav>
<div class="modal fade" id="smallModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel2">Notifications</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body notificationModal">
                @foreach (Auth::guard('vendor')->user()->notifications as $notification)
                    <div class="row @if ($notification->read_at == null) bg-light @else bg-transparent @endif">
                        <div class="col-auto">
                            <span class="bi bi-cart-check-fill fa-2x"></span>
                        </div>
                        <div class="col">
                            <small><strong>Order Placed</strong></small>
                            <div class="my-0 text-muted small">{{ $notification->data['message'] }}
                            </div>
                            <small
                                class="badge badge-pill badge-light text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary notificationClear">Clear All</button>
            </div>
        </div>
    </div>
</div>
