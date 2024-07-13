@extends('user.master')
@section('title', 'Product Details')
@section('content')
    <div class="ms-auto position-relative mt-3 mx-4">
        <a href="{{ route('user.orders.create') }}" class="btn btn-primary">
            <i class="bx bx-cart"></i>
            <span id="cart-count">Cart ({{ Cart::count() }})</span>
        </a>
    </div>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="container-fluid">
                <div class="wrapper row">
                    <div class="preview col-md-6">
                        <div class="preview-pic tab-content">
                            <div class="tab-pane active" id="pic-1"><img alt="no image"
                                    style="max-width: 300px; max-height: 300px;" src="{{ $product->image }}" />
                            </div>
                        </div>
                    </div>
                    <div class="details col-md-6 my-3">
                        <h3 class="product-title">{{ $product->name }}</h3>
                        <div class="rating">
                            <div class="stars">
                                @for ($i = 1; $i <= 5; $i++)
                                    <span class="fa fa-star @if ($i <= round($averageRating)) checked @endif"></span>
                                @endfor
                            </div>
                            <span class="review-no">{{ $reviews->count() }} reviews</span>
                        </div>
                        <p class="product-description mt-2 text-wrap">{{ $product->description }}</p>
                        <h4 class="price">current price: <span>{{ $product->price }}$</span></h4>
                        <h6 class="sizes">Avaliable sizes: <span>{{ $product->sizes }}</span></h6>
                        <h6 class="colors">Avaliable colours: <span>{{ $product->colors }}</span></h6>
                        <h6 class="vendor_id">Vendor: <span>{{ $product->vendor->name }}</span></h6>
                        <div class="action">
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
            </div>
        </div>

        @if ($reviews->count() > 0)
            @foreach ($reviews as $review)
                <div class="card mt-4">
                    <div class="container-fluid">
                        <div class="wrapper row">
                            <div class="p-3">
                                <h5>{{ $review->user->name }}</h5>
                                <div class="stars">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <span class="fa fa-star @if ($i <= $review->rating) checked @endif"></span>
                                    @endfor
                                </div>
                                <p class="pt-2">{{ $review->comment }}</p>
                                @if ($review->user_id == Auth::user()->id)
                                    <form class="float-end" action="{{ route('user.reviews.delete') }}"method='POST'>
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-danger m-2">Delete Review</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="card mt-3">
                <h5 class="fw-semibold pt-3 px-2">There are no reviews for this product</h5>
            </div>
        @endif

        <div class="card mt-3">
            <div class="container-fluid">
                <div class="wrapper row">
                    @if ($reviews->where('user_id', Auth::user()->id)->count() > 0)
                        <form action="{{ route('user.reviews.update') }}" method="POST">
                            @csrf
                            @method('PATCH')
                            {{-- <input type="hidden" name="review_id" value="{{ $reviewId }}"> --}}
                            <div class="input-group p-3">
                                <span class="input-group-text">Update the review</span>
                                <textarea name="comment" class="form-control p-1" aria-label="With textarea" placeholder="Review"></textarea>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="rating-stars">
                                    <input type="radio" name="rating" value="1" id="star1"><label
                                        for="star1"><span class="fa fa-star"></span></label>
                                    <input type="radio" name="rating" value="2" id="star2"><label
                                        for="star2"><span class="fa fa-star"></span></label>
                                    <input type="radio" name="rating" value="3" id="star3"><label
                                        for="star3"><span class="fa fa-star"></span></label>
                                    <input type="radio" name="rating" value="4" id="star4"><label
                                        for="star4"><span class="fa fa-star"></span></label>
                                    <input type="radio" name="rating" value="5" id="star5"><label
                                        for="star5"><span class="fa fa-star"></span></label>
                                </div>
                                <button class="btn btn-primary m-2">Update</button>
                            </div>
                        </form>
                    @else
                        <form action="{{ route('user.reviews.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <div class="input-group p-3">
                                <span class="input-group-text">Leave a review</span>
                                <textarea name="comment" class="form-control p-1" aria-label="With textarea" placeholder="Review"></textarea>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="rating-stars">
                                    <input type="radio" name="rating" value="1" id="star1"><label
                                        for="star1"><span class="fa fa-star"></span></label>
                                    <input type="radio" name="rating" value="2" id="star2"><label
                                        for="star2"><span class="fa fa-star"></span></label>
                                    <input type="radio" name="rating" value="3" id="star3"><label
                                        for="star3"><span class="fa fa-star"></span></label>
                                    <input type="radio" name="rating" value="4" id="star4"><label
                                        for="star4"><span class="fa fa-star"></span></label>
                                    <input type="radio" name="rating" value="5" id="star5"><label
                                        for="star5"><span class="fa fa-star"></span></label>
                                </div>
                                <button class="btn btn-primary m-2">Post</button>
                            </div>
                        </form>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection

<style>
    .rating-stars input[type="radio"] {
        display: none;
    }

    .rating-stars label {
        color: #ccc;
        font-size: 2em;
        cursor: pointer;
    }

    .rating-stars label:hover,
    .rating-stars label:hover~label,
    .rating-stars input[type="radio"]:checked~label {
        color: #f5c518;
    }

    .stars .fa-star.checked {
        color: #f5c518;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const stars = document.querySelectorAll('.rating-stars label');
        stars.forEach(star => {
            star.addEventListener('click', function() {
                // Clear previous checked state
                stars.forEach(s => s.classList.remove('checked'));
                this.classList.add('checked');
                let prev = this.previousElementSibling;
                while (prev) {
                    prev.classList.add('checked');
                    prev = prev.previousElementSibling;
                }
            });

            star.addEventListener('mouseover', function() {
                stars.forEach(s => s.style.color = '#ccc');
                this.style.color = '#f5c518';
                let prev = this.previousElementSibling;
                while (prev) {
                    prev.style.color = '#f5c518';
                    prev = prev.previousElementSibling;
                }
            });
        });

        document.querySelector('.rating-stars').addEventListener('mouseleave', function() {
            stars.forEach(star => {
                if (!star.classList.contains('checked')) {
                    star.style.color = '#ccc';
                }
            });
        });
    });
</script>
