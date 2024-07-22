@extends('user.master')
@section('title', 'Products')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- <div class="d-flex flex-row">
            <h4 class="fw-bold py-3 mb-4">Products</h4>
            <div class="ms-auto">
                <a href="{{ route('user.orders.create') }}" class="btn btn-primary float-end">
                    <i class="bx bx-cart"></i>
                    <span id="cart-count">Cart ({{ Cart::count() }})</span>
                </a>
                <a href="{{ route('user.products.wishlist') }}" class="btn btn-secondary me-2 float-end">
                    <i class="bx bx-heart"></i>
                    <span id="cart-count">Wishlist</span>
                </a>
            </div>
        </div>
        <div class="mb-4 d-flex flex-row">
            <p class="fw-bold mt-2 me-3 d-flex">Filter By:</p>
            <form action="{{ route('user.products.index') }}" method="GET" class="d-flex">
                <!-- Filter form fields -->
                <div class="me-3">
                    <select name="category_id" class="form-select ">
                        <option class="btn btn-primary" value="">Category</option>
                        @foreach ($categories as $category)
                            <option class="btn btn-primary" value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="me-3">
                    <input type="number" name="min_price" class="form-control"
                        placeholder="Min Price (Including Discounts)" value="{{ request('min_price') }}">
                </div>
                <div class="me-3">
                    <input type="number" name="max_price" class="form-control"
                        placeholder="Max Price (Including Discounts)" value="{{ request('max_price') }}">
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </form>


            <form action="{{ route('user.products.search') }}" method="GET" class="d-flex ms-5">
                @csrf
                <div class="card">
                    <div class="navbar-nav align-items-center">
                        <div class="nav-item d-flex align-items-center">
                            <a onclick="this.closest('form').submit();return false;"><i
                                    class="bx bx-search fs-4 lh-0 ps-3"></i></a>
                            <input type="text" name="search" class="form-control border-0 shadow-none"
                                placeholder="Search..." aria-label="Search..." />
                        </div>
                    </div>
                </div>
            </form>
        </div>

        @if ($products->isEmpty())
            <p>No products found</p>
        @else
            <div class="row mb-5">
                @foreach ($products as $product)
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card h-70">
                            <div class="card-body">
                                <div class="position-relative d-flex">
                                    <h5 class="card-title">{{ $product->name }}</h5>
                                    @php
                                        $inWishlist = $wishlistItems->contains('product_id', $product->id);
                                    @endphp
                                    <form class="position-absolute top-0 end-0 mt-2"
                                        action="{{ route('user.wishlist.add', $product->id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="btn rounded-pill btn-icon {{ $inWishlist ? 'btn-primary' : 'btn-primary' }}">
                                            <span class="tf-icons bx {{ $inWishlist ? 'bxs-heart' : 'bx-heart' }}"></span>
                                        </button>
                                    </form>
                                </div>
                                <img class="img-fluid d-flex mx-auto my-4" style="max-width: 200px; max-height: 200px;"
                                    src="{{ $product->image }}" alt="Card image cap" />
                                <div>
                                    Price:
                                    @php
                                        $discountedPrice =
                                            $product->price - ($product->price * $product->discount) / 100;
                                        $isDiscountActive =
                                            $product->discount > 0 && strtotime($product->discount_end_date) >= time();
                                    @endphp
                                    @if ($isDiscountActive)
                                        <span class="discounted-price text-danger me-1 ">${{ $discountedPrice }}</span>
                                        instead of :
                                        <span class="original-price ms-2"
                                            style="text-decoration: line-through;">${{ $product->price }}</span>
                                    @else
                                        <span class="price">${{ $product->price }}</span>
                                    @endif
                                </div>
                                <div class="my-2">
                                    @if ($product->stock <= 0)
                                        <span class="badge bg-danger">Out of stock</span>
                                    @else
                                        <p class="card-text">Stock: {{ $product->stock }}</p>
                                    @endif
                                </div>
                                <form class="card-link" action="{{ route('user.products.show', $product->id) }}"
                                    method="GET">
                                    @csrf
                                    <a href="javascript:{}" onclick="this.closest('form').submit();return false;">More
                                        Details</a>
                                </form>
                                <form class="card-link" action="{{ route('user.cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <button type="submit" class="btn btn-primary"
                                        @if ($product->stock <= 0) disabled @endif>
                                        <i class="bx bx-cart pb-1"></i>
                                        @if ($product->stock <= 0)
                                            Out of Stock
                                        @else
                                            Add to cart
                                        @endif
                                    </button>
                                </form>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif --}}
        @livewire('products-component')
    </div>
@endsection
