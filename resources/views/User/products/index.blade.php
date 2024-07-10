@extends('user.master')
@section('title', 'Products')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex flex-row">
            <h4 class="fw-bold py-3 mb-4">Products Table</h4>
            <div class="ms-auto">
                <button type="button" onclick="redirectToOrders()" class="btn btn-primary float-end">
                    <i class="bx bx-cart"></i>
                    <span id="cart-count" class="badge bg-secondary">{{ session('cart_count', 0) }}</span>
                </button>
            </div>
        </div>

        <div class="row mb-5">
            @foreach ($products as $product)
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <h6 class="card-subtitle text-muted">{{ $product->description }}</h6>
                            <img class="img-fluid d-flex mx-auto my-4" src="{{ $product->image }}" alt="Card image cap" />
                            <p class="card-text">{{ $product->price }}</p>
                            <a href="{{ route('user.products.show', $product->id) }}" class="card-link">More Details</a>
                            <a href="javascript:void(0);" onclick="addToCart({{ $product->id }})"
                                class="card-link add-to-cart-btn">
                                <i class="bx bx-cart px-2"></i>Add to cart
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        function addToCart(productId) {
            fetch('{{ route('user.cart.add') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        product_id: productId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let cartCountEl = document.getElementById('cart-count');
                        cartCountEl.innerText = data.cart_count;
                    } else {
                        alert('Failed to add to cart.');
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function redirectToOrders() {
            window.location.href = '{{ route('user.orders.create') }}';
        }
    </script>
@endsection
