@extends('admin.master')
@section('title', 'Product Details')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="container-fluid">
                <div class="wrapper row">
                    <div class="preview col-md-6">
                        <div class="preview-pic tab-content">
                            <div class="tab-pane active" id="main-image">
                                <img alt="no image" style="width: 300px; height: 300px;" src="{{ $product->image }}" />
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-1">
                                <button id="prev-image" class="btn btn-light me-3">
                                    <i class="fa fa-arrow-left mt-4"></i>
                                </button>
                            </div>
                            <div class="col-md-10 d-flex justify-content-between">
                                @foreach ($productImages as $image)
                                    <div class="thumbnail">
                                        <img alt="no image" style="max-width: 100px; max-height: 100px;"
                                            src="{{ $image->image }}" onclick="changeMainImage('{{ $image->image }}')" />
                                    </div>
                                @endforeach
                            </div>
                            <div class="col-md-1">
                                <button id="next-image" class="btn btn-light">
                                    <i class="fa fa-arrow-right mt-4"></i>
                                </button>
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
                        @if ($product->vendor_id == Auth::guard('admin')->user()->id)
                            <div class="action">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        Manage Product
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <form class="btn"
                                                action="{{ route('vendor.products.update', $product->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="button" class="btn btn-success update-product-btn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#updateProductModal{{ $product->id }}">
                                                    <i class="bx bx-recycle mb-1"></i>
                                                    Update Product
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <form class="btn"
                                                action="{{ route('vendor.products.delete', $product->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger"><i
                                                        class="bx bx-trash mb-1"></i>
                                                    Delete Product</button>
                                            </form>
                                        </li>
                                        <li>
                                            <form class="btn"
                                                action="{{ route('vendor.products.multi', $product->id) }}" method="POST"
                                                enctype="multipart/form-data">
                                                @csrf
                                                <button type="button" class="btn btn-secondary update-product-btn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#uploadImagesModal{{ $product->id }}">
                                                    <i class="bx bx-upload mb-1"></i>
                                                    Upload product images
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <form class="btn"
                                                action="{{ route('vendor.products.multidelete', $product->id) }}"
                                                method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger ">
                                                    <i class="bx bx-trash mb-1"></i>
                                                    Delete product images
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        @endif
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
                    @if ($reviews->where('user_id', Auth::guard('admin')->user()->id)->count() > 0)
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let thumbnails = document.querySelectorAll('.thumbnail img');
        let mainImage = document.querySelector('#main-image img');
        let mainImageSrc = mainImage.src; // Store the main image source URL
        let currentIndex = 0;

        thumbnails.forEach((thumbnail, index) => {
            thumbnail.addEventListener('click', function() {
                mainImage.src = this.src;
                currentIndex = index;
            });
        });

        document.getElementById('prev-image').addEventListener('click', function() {
            if (currentIndex > 0) {
                currentIndex--;
                mainImage.src = thumbnails[currentIndex].src;
            } else if (currentIndex === 0) {
                mainImage.src = mainImageSrc; // Reset to main image if at first thumbnail
                currentIndex--;
            }
        });

        document.getElementById('next-image').addEventListener('click', function() {
            if (currentIndex < thumbnails.length - 1) {
                currentIndex++;
                mainImage.src = thumbnails[currentIndex].src;
            } else if (currentIndex === thumbnails.length - 1) {
                mainImage.src = mainImageSrc; // Reset to main image if at last thumbnail
                currentIndex++;
            }
        });
    });

    function changeMainImage(imageSrc) {
        document.querySelector('#main-image img').src = imageSrc;
    }
</script>
<style>
    .thumbnail img {
        cursor: pointer;
        border: 2px solid transparent;
        transition: border 0.3s;
    }

    .thumbnail img:hover {
        border: 2px solid #007bff;
    }
</style>
