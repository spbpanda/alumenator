@extends('admin.layout')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('res/vendor/libs/quill/editor.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<style>#qrcode canvas {background-color: white;padding: 10px;}</style>
@endsection

@section('page-style')
    <link rel="stylesheet" href="{{asset('res/vendor/css/pages/page-account-settings.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('res/vendor/libs/quill/katex.js')}}"></script>
<script src="{{asset('res/vendor/libs/quill/quill.js')}}"></script>
<script src="{{asset('res/js/qrcode.min.js')}}"></script>
<script src="{{asset('res/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('res/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('res/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('res/js/pages-account-settings-security.js')}}"></script>
<script>
    @if(!Auth::guard('admins')->user()->is_2fa)
    new QRCode(document.getElementById("qrcode"), {
        text: "otpauth://totp/MinestoreCMS:{{Auth::guard('admins')->user()->username}}@<?php echo($_SERVER['HTTP_HOST']); ?>?secret={{$otp}}&issuer=MinestoreCMS",
        width: 365,
        height: 365,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H,
    });
    $('#setOTP').on('click', function (){
        $.ajax({
            method: "GET",
            url: '/admin/profile/setOTP/'+$('#twoFactorAuthInput').val(),
        }).done(function(r) {
            if(r === "OK"){
                window.location.reload();
            } else {
                toastr.error("{{ __('Incorrect code, try again') }}");
            }
        }).fail(function(r) {
            toastr.error("{{ __('Something went wrong!') }}");
        });
    });
    @endif
</script>
<script>
    let togglePasswordIcons = document.querySelectorAll('.form-password-toggle .input-group-text');
    let passwordInputs = document.querySelectorAll('.form-password-toggle input[type="password"]');

    togglePasswordIcons.forEach(function(icon, index) {
        icon.addEventListener('click', function() {
            if (passwordInputs[index].type === "password") {
                passwordInputs[index].type = "text";
                icon.innerHTML = '<i class="bx bx-show"></i>';
            } else {
                passwordInputs[index].type = "password";
                icon.innerHTML = '<i class="bx bx-hide"></i>';
            }
        });
    });
</script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-1">
    <span class="text-body fw-light">{{ __('Profile Settings') }}</span>
</h4>

@csrf
<div class="col-12 mb-4">
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible mb-4" role="alert">
                <h6 class="alert-heading d-flex align-items-center mb-1">{{ __('Error') }} 😞</h6>
                <p class="mb-0">{{ session('error') }}</p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                </button>
            </div>
        @endif
        @if (session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                  <h6 class="alert-heading d-flex align-items-center mb-1">{{ __('Well done') }} 👍</h6>
                  <p class="mb-0">{{ session('success') }}</p>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                  </button>
            </div>
        @endif
        @if (session('status'))
            <div class="alert alert-warning alert-dismissible" role="alert">
                 <h6 class="alert-heading d-flex align-items-center mb-1">{{ __('Be Aware') }} ☝️</h6>
                 <p class="mb-0">{{ session('status') }}</p>
                 <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                 </button>
            </div>
        @endif
    <!-- Change Password -->
    <div class="card mb-4">
        <h5 class="card-header">{{ __('Change Password') }}</h5>
        <div class="card-body">
            <form action="{{route('profile.changePassword')}}" id="formAccountSettings" method="POST">
                @csrf
                @method('POST')
                <input type="hidden" name="user" value="{{Auth::guard('admins')->user()->username}}">
                <div class="row">
                    <div class="mb-3 col-md-6 form-password-toggle">
                        <label class="form-label" for="currentPassword">{{ __('Current Password') }}</label>
                        <div class="input-group input-group-merge">
                            <input class="form-control" type="password" name="currentPassword" id="currentPassword" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required />
                            <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                        </div>
                        @error('currentPassword')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="mb-3 col-md-6 form-password-toggle">
                        <label class="form-label" for="newPassword">{{ __('New Password') }}</label>
                        <div class="input-group input-group-merge">
                            <input class="form-control" type="password" id="newPassword" name="newPassword" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required />
                            <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                        </div>
                        @error('newPassword')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3 col-md-6 form-password-toggle">
                        <label class="form-label" for="confirmPassword">{{ __('Confirm New Password') }}</label>
                        <div class="input-group input-group-merge">
                            <input class="form-control" type="password" name="confirmPassword" id="confirmPassword" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required />
                            <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                        </div>
                        @error('confirmPassword')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-12 mb-4">
                        <p class="fw-semibold mt-2">{{ __('Password Requirements:') }}</p>
                        <ul class="ps-3 mb-0">
                            <li class="mb-1">
                                {{ __('Minimum 8 characters long - the more, the better') }}
                            </li>
                            <li class="mb-1">{{ __('At least one lowercase character') }}</li>
                            <li>{{ __('At least one number, symbol, or whitespace character') }}</li>
                        </ul>
                    </div>
                    <div class="col-12 mt-1">
                        <button type="submit" form="formAccountSettings" class="btn btn-primary me-2">{{ __('Save changes') }}</button>
                        <button type="reset" form="formAccountSettings" class="btn btn-label-secondary">{{ __('Cancel') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!--/ Change Password -->

    <!-- Two-steps verification -->
    @if(!Auth::guard('admins')->user()->is_2fa)
    <div class="card mb-4">
        <h5 class="card-header">{{ __('Two-steps verification') }}</h5>
        <div class="card-body">
            <p class="fw-semibold text-danger mb-3">{{ __('Two-factor authentication is not enabled yet.') }}</p>
            <p class="w-75">{{ __('Two-factor authentication adds a layer of security to your account by requiring more than just a password to log in.') }}
            </p>
            <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#twoFactorAuthGenerateCode">{{ __('Enable Two-Factor Authentication') }}</button>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="twoFactorAuthGenerateCode" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-simple">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-2">
                        <h3 class="mb-0">{{ __('Add Authenticator App') }}</h3>
                    </div>
                    <h5 class="mb-2 pt-1 text-break">{{ __('Authenticator Apps') }}</h5>
                    <p class="mb-4">{{ __('Using an authenticator app like Google Authenticator, Microsoft Authenticator, Authy, or 1Password, scan the QR code. It will generate a 6-digit code for you to enter below.') }}</p>
                    <div class="text-center">
                        <div id="qrcode"></div>
                    </div>
                    <div class="alert alert-warning alert-dismissible my-3 text-center" role="alert">
                        <h5 class="alert-heading mb-2 text-bold" style="overflow-wrap: anywhere;">{{$otp}}</h5>
                        <p class="mb-0">{!! __('If you\'re having trouble using the QR code, select manual entry on your app') !!}</p>
                    </div>
                    <div class="mb-4">
                        <input type="text" class="form-control" id="twoFactorAuthInput" placeholder="{{ __('Enter Authentication Code') }}">
                    </div>
                    <div class="col-12 text-end">
                        <button type="button" class="btn btn-label-secondary me-sm-3 me-1" data-bs-toggle="modal" data-bs-target="#twoFactorAuth"><i class="bx bx-left-arrow-alt bx-xs me-1 scaleX-n1-rtl"></i><span class="align-middle">{{ __('Back') }}</span></button>
                        <button type="button" class="btn btn-primary" id="setOTP"><span class="align-middle">{{ __('Continue') }}</span><i class="bx bx-right-arrow-alt bx-xs ms-1 scaleX-n1-rtl"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Modal -->
    @else
        <form action="{{route('profile.store')}}" id="disableForm" method="POST">
            @csrf
            @method('POST')
            <div class="card mb-4">
                <h5 class="card-header">{{ __('Two-steps verification') }}</h5>
                <div class="card-body">
                    <p class="fw-semibold text-success mb-3">{{ __('Two-factor authentication is enabled.') }}</p>
                    <p class="w-75">{{ __('Two-factor authentication adds an additional layer of security to your account by requiring more than just a password to log in.') }}
                    </p>
                    <button class="btn btn-danger mt-2" type="submit" name="disable2fa" value="1" form="disableForm">{{ __('Disable Two-Factor Authentication') }}</button>
                </div>
            </div>
        </form>
    @endif
    <!--/ Two-steps verification -->
</div>
<div class="row">
	<div class="d-grid gap-2 col-lg-12 mx-auto">
        <button class="btn btn-primary btn-lg" type="submit" form="formAccountSettings"><span class="tf-icon bx bx-save bx-xs"></span> {{ __('Save Settings') }}</button>
    </div>
</div>
@endsection

