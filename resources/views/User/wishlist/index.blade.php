@extends('user.master')
@section('title', 'Wishlist')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="d-flex flex-row">
            <h2 class="fw-bold py-3 mb-4">Your Wishlist:</h2>
            <div class="ms-auto">
                <a href="{{ route('user.orders.create') }}" class="btn btn-primary float-end">
                    <i class="bx bx-cart"></i>
                    <span id="cart-count">Cart ({{ Cart::count() }})</span>
                </a>
            </div>
        </div>
        <div class="d-flex flex-row">
            @if ($wishlistItems->count() > 0)
                @foreach ($wishlistItems as $item)
                    <div class="col-md-6 col-lg-4 me-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="position-relative d-flex">
                                    <h5 class="card-title">{{ $item->product->name }}</h5>
                                </div>
                                <img class="img-fluid d-flex mx-auto my-4" style="max-width: 200px; max-height: 200px;"
                                    src="{{ $item->product->image }}" alt="Card image cap" />
                                <div>
                                    Price:
                                    @php
                                        $discountedPrice =
                                            $item->product->price -
                                            ($item->product->price * $item->product->discount) / 100;
                                        $isDiscountActive =
                                            $item->product->discount > 0 &&
                                            strtotime($item->product->discount_end_date) >= time();
                                    @endphp
                                    @if ($isDiscountActive)
                                        <span class="discounted-price text-danger me-1 ">${{ $discountedPrice }}</span>
                                        instead of :
                                        <span class="original-price ms-2"
                                            style="text-decoration: line-through;">${{ $item->product->price }}</span>
                                    @else
                                        <span class="price">${{ $item->product->price }}</span>
                                    @endif
                                </div>
                                <div class="my-2">
                                    @if ($item->product->stock <= 0)
                                        <span class="badge bg-danger">Out of stock</span>
                                    @else
                                        <p class="card-text">Stock: {{ $item->product->stock }}</p>
                                    @endif
                                </div>
                                <form class="card-link" action="{{ route('user.cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $item->product->id }}">
                                    <button type="submit" class="btn btn-primary"
                                        @if ($item->product->stock <= 0) disabled @endif>
                                        <i class="bx bx-cart pb-1"></i>
                                        @if ($item->product->stock <= 0)
                                            Out of Stock
                                        @else
                                            Add to cart
                                        @endif
                                    </button>
                                </form>
                                <form class="card-link" action="{{ route('user.wishlist.remove', $item->product->id) }}"
                                    method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>

                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <p>Your wishlist is empty.</p>
            @endif
        </div>
    </div>
    </div>
@endsection
