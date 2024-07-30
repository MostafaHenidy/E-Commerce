@extends('admin.master')
@section('orders-active','active')
@section('title', 'Orders')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex flex-row">
            <h4 class="fw-bold py-3 mb-4">Orders</h4>
        </div>

        <div class="row mb-5">
            @if ($orders->count() > 0)
                @foreach ($orders as $order)
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card">
                            <div class="card-header bold">Order ID: {{ $order->id }}</div>
                            <div class="card-body">
                                <h5 class="card-title">Total Price: {{ $order->total_amount }}$</h5>
                                <p class="card-text">Status: <span
                                        class="badge bg-{{ $order->status === 'pending' ? 'warning' : ($order->status === 'success' ? 'success' : 'danger') }}">{{ $order->status }}</span>
                                </p>
                                <h6 class="card-title">Created at: {{ $order->created_at->format('d/m/Y') }}</h6>
                                <a href="{{ route('admin.orders.show', $order->id) }}"
                                    onclick="this.closest('form').submit();return false;" class="btn btn-primary">More
                                    Details</a>

                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <span>There is no orders</span>
            @endif
        </div>
    </div>

@endsection
