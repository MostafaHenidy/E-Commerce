@extends('vendor.master')
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
                        <h5>Price:
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
                        </h5>

                        <h6 class="sizes">Available sizes: <span>{{ $product->sizes }}</span></h6>
                        <h6 class="colors">Available colors: <span>{{ $product->colors }}</span></h6>
                        <h6 class="vendor_id">Vendor: <span>{{ $product->vendor->name }}</span></h6>
                        <div class="action">
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    Manage Product
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <form class="btn" action="{{ route('vendor.products.update', $product->id) }}"
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
                                        <form class="btn" action="{{ route('vendor.products.delete', $product->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger"><i class="bx bx-trash mb-1"></i>
                                                Delete Product</button>
                                        </form>
                                    </li>
                                    <li>
                                        <form class="btn" action="{{ route('vendor.products.multi', $product->id) }}"
                                            method="POST" enctype="multipart/form-data">
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
                    </div>
                </div>
            </div>
        </div>


        <div>
            <p class="mt-3 fw-bolder fs-5">Customer Reviews:</p>
            @if ($reviews->count() > 0)
                @foreach ($reviews as $review)
                    <div class="card mt-4">
                        <div class="container-fluid">
                            <div class="wrapper row">
                                <div class="p-3">
                                    <h5>{{ $review->user->name }}</h5>
                                    <div class="stars">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <span
                                                class="fa fa-star @if ($i <= $review->rating) checked @endif"></span>
                                        @endfor
                                    </div>
                                    <p class="pt-2">{{ $review->comment }}</p>
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
        </div>

        <div class="modal fade" id="updateProductModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel4">Update Product ({{ $product->name }})
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('vendor.products.update', $product->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row mb-3">
                                <label for="nameExLarge" class="col-sm-2 col-form-label">Name</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="nameExLarge" value="{{ $product->name }}" name="name"
                                        placeholder="Enter product Name">
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col mb-0">
                                    <label for="descriptionExLarge" class="col-sm-2 col-form-label">Description</label>
                                    <div class="col-sm-10">
                                        <input type="text"
                                            class="form-control @error('description') is-invalid @enderror"
                                            id="descriptionExLarge" name="description"
                                            value="{{ $product->description }}" placeholder="Enter description">
                                        @error('description')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col mb-0">
                                    <label for="priceExLarge" class="col-sm-2 col-form-label">Price</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control @error('price') is-invalid @enderror"
                                            value="{{ $product->price }}" id="priceExLarge" name="price"
                                            placeholder="Enter price">
                                        @error('price')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                            </div>
                            <div class="row mb-3">
                                <div class="col mb-0">
                                    <label for="categoryExLarge" class="col-sm-2 col-form-label">Category</label>
                                    <div class="col-sm-10">
                                        <select class="form-select" id="categoryExLarge" name="category_id">
                                            <option value="{{ $product->category->id }}">{{ $product->category->name }}
                                            </option>
                                        </select>
                                        @error('category_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col mb-0">
                                    <label for="stockExLarge" class="col-sm-2 col-form-label">Stock</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control @error('stock') is-invalid @enderror"
                                            value="{{ $product->stock }}" id="stockExLarge" name="stock"
                                            placeholder="Enter stock">
                                        @error('stock')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="input-group">
                                    <input type="file" class="form-control" id="image" name="image" />
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col mb-0">
                                    <label for="sizesExLarge" class="col-sm-2 col-form-label">Sizes</label>
                                    <div class="col-sm-10">
                                        @foreach (['XS', 'S', 'M', 'L', 'XL', 'XXL'] as $size)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="sizes[]"
                                                    value="{{ $size }}" id="size_{{ $size }}">
                                                <label class="form-check-label" for="size_{{ $size }}">
                                                    {{ $size }}
                                                </label>
                                            </div>
                                        @endforeach
                                        @error('sizes')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col mb-0">
                                    <label for="colorsExLarge" class="col-sm-2 col-form-label">Colors</label>
                                    <div class="col-sm-10">
                                        @foreach (['Red', 'Green', 'Blue', 'Black', 'White', 'Yellow'] as $color)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="colors[]"
                                                    value="{{ $color }}" id="color_{{ $color }}">
                                                <label class="form-check-label" for="color_{{ $color }}">
                                                    {{ $color }}
                                                </label>
                                            </div>
                                        @endforeach
                                        @error('colors')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary"
                                    data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Update Product</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="uploadImagesModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel4">Upload Images for ({{ $product->name }})
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('vendor.products.multi', $product->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <label for="Images" class="col-sm-2 col-form-label fw-bold mt-1">Max:5 images only</label>
                            <div class="row mb-3">
                                <div class="col-sm-10">
                                    <div class="input-group">
                                        <input type="file" class="form-control" id="images[]" name="images[]"
                                            multiple />
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary"
                                    data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Upload Images</button>
                            </div>
                        </form>
                    </div>
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
