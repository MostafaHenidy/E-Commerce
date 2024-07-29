@extends('vendor.master')
@section('dashboard-active','active')
@section('title', 'vendor Dashboard')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="content-wrapper">
            <!-- Content -->

            <div class="container-xxl flex-grow-1 container-p-y">
                <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Account Settings /</span> Account</h4>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-4">
                            <h5 class="card-header">Profile Details</h5>
                            <!-- Account Info Update -->
                            <form id="formAccountSettings" method="POST" action="{{ route('vendor.profile.update') }}"
                                enctype="multipart/form-data">
                                @csrf
                                @method('patch')
                                <div class="card-body">
                                    <div class="d-flex align-items-start align-items-sm-center gap-4">
                                        <img src="{{ Auth::guard('vendor')->user()->avatar }}" alt="user-avatar"
                                            class="d-block rounded" height="100" width="100" id="uploadedAvatar" />
                                        <div class="button-wrapper">
                                            <label for="avatar" class="btn btn-primary me-2 mb-4" tabindex="0">
                                                <span class="d-none d-sm-block">Upload new photo</span>
                                                <i class="bx bx-upload d-block d-sm-none"></i>
                                                <input type="file" id="avatar" name="avatar"
                                                    class="account-file-input" hidden />
                                            </label>
                                            <p class="text-muted mb-0">Allowed JPG, GIF or PNG. </p>
                                        </div>
                                    </div>
                                </div>
                                <hr class="my-0" />
                                <div class="card-body">
                                    <div class="row">
                                        <div class="mb-3 col-md-6">
                                            <label for="name" class="form-label">Name</label>
                                            <input class="form-control" type="text" id="name" name="name"
                                                value="{{ Auth::guard('vendor')->user()->name }}" autofocus />
                                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="email" class="form-label">E-mail</label>
                                            <input class="form-control" type="text" id="email" name="email"
                                                value="{{ Auth::guard('vendor')->user()->email }}"
                                                placeholder="john.doe@example.com" />
                                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label" for="mobile">Phone Number</label>
                                            <div class="input-group input-group-merge">
                                                <span class="input-group-text">EG (+20)</span>
                                                <input type="text" id="mobile" name="mobile" class="form-control"
                                                    value="{{ Auth::guard('vendor')->user()->mobile ?? 'mobile number unavaliable' }}"placeholder="202 555 0111" />
                                            </div>
                                            <x-input-error class="mt-2" :messages="$errors->get('mobile')" />
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="address" class="form-label">Address</label>
                                            <input type="text" class="form-control" id="address" name="address"
                                                value="{{ Auth::guard('vendor')->user()->address ?? 'address unavaliable' }}"
                                                placeholder="Address" />
                                            <x-input-error class="mt-2" :messages="$errors->get('address')" />
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <button type="submit" class="btn btn-primary me-2">Save changes</button>
                                        <button type="reset" class="btn btn-outline-secondary">Cancel</button>
                                    </div>
                                </div>
                            </form>
                            <!-- /Account -->
                        </div>
                        <div class="card mb-4">
                            <h5 class="card-header">Update Password</h5>
                            <div class="card-body">
                                <div class="mb-3 col-12 mb-0">
                                    {{-- Password reset --}}
                                    <form id="formAccountSettings" action="{{ route('vendor.password.update') }}"
                                        method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="form-password-toggle mb-2">
                                            <label class="form-label" for="current-password">Current Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="current-password"
                                                    name="current_password"
                                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                    aria-describedby="current-password" />
                                                <span id="current-password" class="input-group-text cursor-pointer"><i
                                                        class="bx bx-hide"></i></span>
                                            </div>
                                        </div>
                                        <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />

                                        <div class="form-password-toggle mb-2">
                                            <label class="form-label" for="new-password">New Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="new-password"
                                                    name="password"
                                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                    aria-describedby="new-password" />
                                                <span id="new-password" class="input-group-text cursor-pointer"><i
                                                        class="bx bx-hide"></i></span>
                                            </div>
                                        </div>
                                        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />

                                        <div class="form-password-toggle mb-2">
                                            <label class="form-label" for="password-confirmation">Confirm Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="password-confirmation"
                                                    name="password_confirmation"
                                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                    aria-describedby="password-confirmation" />
                                                <span id="password-confirmation"
                                                    class="input-group-text cursor-pointer"><i
                                                        class="bx bx-hide"></i></span>
                                            </div>
                                        </div>
                                        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />

                                        <div class="mt-4 float-end">
                                            <button type="submit" class="btn btn-primary me-2">Save changes</button>
                                        </div>

                                        @if (session('status') === 'password-updated')
                                            <p x-data="{ show: true }" x-show="show" x-transition
                                                x-init="setTimeout(() => show = false, 2000)" class="text-sm text-gray-600">{{ __('Saved.') }}
                                            </p>
                                        @endif
                                    </form>
                                    {{-- /Password Reset --}}
                                </div>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <h5 class="card-header">Delete Account</h5>
                            <div class="card-body">
                                <div class="mb-3 col-12 mb-0">
                                    <div class="alert alert-warning">
                                        <h6 class="alert-heading fw-bold mb-1">Are you sure you want to delete your
                                            account?</h6>
                                        <p class="mb-0">Once you delete your account, there is no going back. Please be
                                            certain.</p>
                                    </div>
                                </div>
                                <form id="formAccountDeactivation" method="post"
                                    action="{{ route('vendor.profile.destroy') }}">
                                    @csrf
                                    @method('delete')
                                    <div class="form-password-toggle mb-3">
                                        <label class="form-label" for="basic-default-password12">
                                            Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="basic-default-password12"
                                                name="password"
                                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                                aria-describedby="basic-default-password2" />
                                            <span id="basic-default-password2" class="input-group-text cursor-pointer"><i
                                                    class="bx bx-hide"></i></span>
                                        </div>
                                    </div>
                                    <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                                    <button type="submit" class="btn btn-danger deactivate-account">Deactivate
                                        Account</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- / Content -->
            <div class="content-backdrop fade"></div>
        </div>
    </div>
@endsection
