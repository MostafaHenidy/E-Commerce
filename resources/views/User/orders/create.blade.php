@extends('user.master')
@section('title', 'Create Order')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">Your Cart</h4>

        @if ($cart->count() > 0)
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach ($cart as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->options->description }}</td>
                                <td>{{ $item->price }}</td>
                                <td>{{ $item->qty }}</td>
                                <td>{{ $item->price * $item->qty }}</td>
                                <td>
                                    <form action="{{ route('user.cart.remove', $item->rowId) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger"><i class="bx bx-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <form action="{{ route('user.orders.store') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary">Checkout</button>
                </form>
            </div>
        @else
            <p>Your cart is empty.</p>
        @endif
    </div>
@endsection
