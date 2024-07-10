@section('title', 'Verify Emial')
<!DOCTYPE html>
<html lang="en" class="light-style customizer-hide" dir="ltr" data-theme="theme-default"
    data-assets-path="{{ asset('assets') }}/" data-template="vertical-menu-template-free">

@include('user.partials.authHead')

<body>
    <!-- Content -->

    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <!-- Register Card -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        @include('user.partials.authLogo')
                        <!-- /Logo -->

                        <p class="mb-4">Thanks for signing up! Before getting started, could you verify your email
                            address by clicking on the link we just emailed to you? If you didn\'t receive the email, we
                            will gladly send you another.</p>
                        @if (session('status') == 'verification-link-sent')
                            <div class="mb-4 font-medium text-sm text-green-600">
                                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                            </div>
                        @endif
                        <form id="formAuthentication" class="mb-3" action="{{ route('verification.send') }}"
                            method="POST">
                            @csrf
                            <button class="btn btn-primary d-grid w-100">Resend Verification Email</button>
                        </form>

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <div class="text-center">
                                <a href="javascript:{}" onclick="this.closest('form').submit();return false;">
                                    <i class="bx
                                    bx-power-off me-2"></i>
                                    <span class="align-middle">Log Out</span>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Register Card -->
            </div>
        </div>
    </div>

    <!-- / Content -->
    @include('user.partials.authscripts')

</body>

</html>
