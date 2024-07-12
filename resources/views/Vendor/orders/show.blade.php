@extends('vendor.master')
@section('title', 'Order Details')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4>Order Details</h4>

        <div class="card">
            <div class="card-header">
                Order #{{ $order->id }}
            </div>
            <div class="card-body">
                <h5 class="card-title">Order Date: {{ $order->created_at->format('d/m/Y') }}</h5>
                <h6 class="card-subtitle mb-2 text-muted">Total Amount: ${{ $order->total_amount }}</h6>
                <p class="card-text">Status: <span
                        class="badge bg-{{ $order->status === 'pending' ? 'warning' : ($order->status === 'success' ? 'success' : 'danger') }}">{{ $order->status }}</span>
                </p>

                <h5 class="card-title mt-4">Items</h5>
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product Code</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @if ($order->orderItems->isEmpty())
                                <p class="card-text">No items found for this order.</p>
                            @else
                                @foreach ($order->orderItems as $item)
                                    <tr>
                                        <td>{{ $item->product->name ?? 'Product Not Found' }}</td>
                                        <td>${{ $item->price }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>${{ $item->price * $item->quantity }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                    <form action="{{ route('vendor.orders.update', $order->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label for="status" class="form-label">Update Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="success" {{ $order->status == 'success' ? 'selected' : '' }}>Success
                                </option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled
                                </option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Order Status</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
