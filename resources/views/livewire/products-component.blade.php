<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex flex-row">
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
        <div class="d-flex">
            <div class="me-3">
                <select wire:model.live="category_id" class="form-select">
                    <option value="">Category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="me-3">
                <input type="number" wire:model.live="min_price" class="form-control"
                    placeholder="Min Price (Including Discounts)">
            </div>
            <div class="me-3">
                <input type="number" wire:model.live="max_price" class="form-control"
                    placeholder="Max Price (Including Discounts)">
            </div>
        </div>
        <div class="ms-5">
            <div class="card">
                <div class="navbar-nav align-items-center">
                    <div class="nav-item d-flex align-items-center">
                        <a wire:click="filterProducts" href="javascript:void(0)"><i
                                class="bx bx-search fs-4 lh-0 ps-3"></i></a>
                        <input type="text" wire:model.live="searchTerm" class="form-control border-0 shadow-none"
                            placeholder="Search...">
                    </div>
                </div>
            </div>
        </div>
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
                                    <button type="submit" class="btn rounded-pill btn-icon btn-primary">
                                        <span class="tf-icons bx {{ $inWishlist ? 'bxs-heart' : 'bx-heart' }}"></span>
                                    </button>
                                </form>
                            </div>
                            <img class="img-fluid d-flex mx-auto my-4" style="max-width: 200px; max-height: 200px;"
                                src="{{ $product->image }}" alt="Card image cap" />
                            <div>
                                Price:
                                @php
                                    $discountedPrice = $product->price - ($product->price * $product->discount) / 100;
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
                            <a href="{{ route('user.products.show', $product->id) }}" class="card-link">More
                                Details</a>
                            <button wire:click.prevent="addToCart({{ $product->id }})"
                                class="btn btn-primary card-link" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                                <i class="bx bx-cart pb-1"></i>
                                {{ $product->stock <= 0 ? 'Out of Stock' : 'Add to cart' }}
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
