@extends('vendor.master')
@section('title', 'Products')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="d-flex flex-row">
                <h4 class="fw-bold py-3 mb-4">Products Table</h4>
                <div class="ms-auto"> <button type="submit" data-bs-toggle="modal" data-bs-target="#exLargeModal"
                        class="btn btn-primary float-end">
                        <i class="bx bx-folder-plus"></i>
                    </button>
                </div>
                <div class="ms-3"> <button type="submit" data-bs-toggle="modal" data-bs-target="#discountModal"
                        class="btn btn-success float-end">
                        <i class="bi bi-currency-dollar"></i>
                    </button>
                </div>
            </div>

            <!-- Basic Bootstrap Table -->
            <div class="card">
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Price</th>
                                <th>Category name</th>
                                <th>Category ID</th>
                                <th>Stock</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach ($products as $product)
                                <tr>
                                    <td>
                                        <strong>{{ $product->id }}</strong>
                                    </td>
                                    <td><a href="{{ route('vendor.products.show', $product->id) }}"
                                            class="fw-bold">{{ $product->name }}</a></td>
                                    <td>{{ $product->description }}</td>
                                    <td>{{ $product->price }}</td>
                                    <td>{{ $product->category->name }}</td>
                                    <td>{{ $product->category_id }}</td>
                                    <td>
                                        @if ($product->stock <= 0)
                                            <span class="badge bg-danger">Out of stock</span>
                                        @else
                                            {{ $product->stock }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($product->vendor_id == Auth::guard('vendor')->user()->id)
                                            <div class="dropdown">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-primary dropdown-toggle"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        Actions
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <form class="dropdown-item"
                                                                action="{{ route('vendor.products.update', $product->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="button"
                                                                    class="btn btn-success update-product-btn"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#updateProductModal{{ $product->id }}">
                                                                    <i class="bx bx-recycle"></i>
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form class="dropdown-item"
                                                                action="{{ route('vendor.products.delete', $product->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger"><i
                                                                        class=" bx bx-trash"></i></button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        @endif
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal fade" id="exLargeModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel4">Create Product</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('vendor.products.store') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row mb-3">
                                    <label for="nameExLarge" class="col-sm-2 col-form-label">Name</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="nameExLarge" name="name" placeholder="Enter product Name">
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
                                                id="descriptionExLarge" name="description" placeholder="Enter description">
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
                                                id="priceExLarge" name="price" placeholder="Enter price">
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
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
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
                                            <input type="text"
                                                class="form-control @error('stock') is-invalid @enderror"
                                                id="stockExLarge" name="stock" placeholder="Enter stock">
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
                                    <button type="submit" class="btn btn-primary">Save Product</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="discountModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel4">Make a discount for All products</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('vendor.products.discount') }}" method="POST">
                                @csrf
                                <div class="row mb-3">
                                    <label for="discount" class="col-sm-2 col-form-label">Discount Percentage</label>
                                    <div class="col-sm-10">
                                        <input type="number" class="form-control @error('discount') is-invalid @enderror"
                                            id="discount" name="discount" placeholder="Enter discount percentage">
                                        @error('discount')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="discount_start_date" class="col-sm-2 col-form-label">Start Date</label>
                                    <div class="col-sm-10">
                                        <input type="datetime-local"
                                            class="form-control @error('discount_start_date') is-invalid @enderror"
                                            id="discount_start_date" name="discount_start_date"
                                            placeholder="Enter discount start date">
                                        @error('discount_start_date')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="discount_end_date" class="col-sm-2 col-form-label">End Date</label>
                                    <div class="col-sm-10">
                                        <input type="datetime-local"
                                            class="form-control @error('discount_end_date') is-invalid @enderror"
                                            id="discount_end_date" name="discount_end_date"
                                            placeholder="Enter discount end date">
                                        @error('discount_end_date')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Apply Discount</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>




            @foreach ($products as $product)
                <div class="modal fade" id="updateProductModal{{ $product->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel4">Update Product ({{ $product->name }})
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
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
                                            <label for="descriptionExLarge"
                                                class="col-sm-2 col-form-label">Description</label>
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
                                                <input type="text"
                                                    class="form-control @error('price') is-invalid @enderror"
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
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}">{{ $category->name }}
                                                        </option>
                                                    @endforeach
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
                                                <input type="text"
                                                    class="form-control @error('stock') is-invalid @enderror"
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
            @endforeach
        </div>
    </div>
@endsection
