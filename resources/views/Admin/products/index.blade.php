@extends('admin.master')
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
                                <th>Vendor ID</th>
                                <th>Category name</th>
                                <th>Stock</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach ($products as $product)
                                <tr>
                                    <td><i class="fab fa-angular fa-lg text-danger me-3"></i>
                                        <strong>{{ $product->id }}</strong>
                                    </td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->description }}</td>
                                    <td>{{ $product->price }}</td>
                                    <td>{{ $product->vendor_id }}</td>
                                    <td>{{ $product->category->name }}</td>
                                    <td>{{ $product->stock }}</td>
                                    <td>
                                        <form action="{{ route('admin.products.delete', $product->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger"><i
                                                    class=" bx bx-trash"></i></button>
                                        </form>
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
                            <form action="{{ route('admin.products.store') }}" method="POST">
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
                                    <label for="nameExLarge" class="col-sm-2 col-form-label">Description</label>
                                    <div class="col-sm-10">
                                        <input type="text"
                                            class="form-control @error('description') is-invalid @enderror" id="nameExLarge"
                                            name="description" placeholder="Enter description">
                                        @error('description')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col mb-0">
                                        <label for="nameExLarge" class="col-sm-2 col-form-label">Price</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control @error('price') is-invalid @enderror"
                                                id="nameExLarge" name="price" placeholder="Enter price">
                                            @error('price')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col mb-0">
                                        <label for="nameExLarge" class="col-sm-2 col-form-label">Vendor ID</label>
                                        <div class="col-sm-10">
                                            <input type="text"
                                                class="form-control @error('vendor_id') is-invalid @enderror"
                                                id="nameExLarge" name="vendor_id" placeholder="Enter vendor id">
                                            @error('vendor_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col mb-0">
                                        <label for="nameExLarge" class="col-sm-2 col-form-label">Category ID</label>
                                        <div class="col-sm-10">
                                            <input type="text"
                                                class="form-control @error('category_id') is-invalid @enderror"
                                                id="nameExLarge" name="category_id" placeholder="Enter category id">
                                            @error('category_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col mb-0">
                                        <label for="nameExLarge" class="col-sm-2 col-form-label">Stock</label>
                                        <div class="col-sm-10">
                                            <input type="text"
                                                class="form-control @error('stock') is-invalid @enderror"
                                                id="nameExLarge" name="stock" placeholder="Enter stock">
                                            @error('stock')
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
        </div>
    @endsection
