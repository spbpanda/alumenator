@php
    $customizerHidden = 'customizer-hide';
    $configData = [
      'layout' => 'vertical',
      'theme' => 'theme-default',
      'style' => Cookie::get('style', 'dark') == 'dark' ? 'dark' : 'light',
      'rtlSupport' => true,
      'rtlMode' => true,
      'textDirection' => true,
      'menuCollapsed' => false,
      'hasCustomizer' => false,
      'colors' => '#fb6604',
    ];
@endphp

@section('title', 'Login to Admin Panel')

@extends('admin.blankLayout')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('res/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
@endsection

@section('page-style')
    <link rel="stylesheet" href="{{asset('res/vendor/css/pages/page-auth.css')}}">
@endsection

@section('vendor-script')
    <script src="{{asset('res/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
    <script src="{{asset('res/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
    <script src="{{asset('res/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script>
    <script src="{{asset('res/vendor/libs/cleavejs/cleave.js')}}"></script>
@endsection

@section('page-script')
    <script>
        const numeralMaskList = document.querySelectorAll('.numeral-mask');
        if (numeralMaskList.length) {
            numeralMaskList.forEach(e => {
                new Cleave(e, {
                    numeral: true
                });
            });

            const keyupHandler = function () {
                let otpFlag = true,
                    otpVal = '';
                numeralMaskList.forEach(numeralMaskEl => {
                    if (numeralMaskEl.value === '') {
                        otpFlag = false;
                        document.querySelector('#otp').value = '';
                    }
                    otpVal = otpVal + numeralMaskEl.value;
                });
                if (otpFlag) {
                    document.querySelector('#otp').value = otpVal;
                }
            };
            numeralMaskList.forEach(numeralMaskEle => {
                numeralMaskEle.addEventListener('keyup', keyupHandler);
            });
        }

        let maskWrapper = document.querySelector('.numeral-mask-wrapper');
        for (let pin of maskWrapper.children)
        {
            pin.onkeyup = function (e) {
                if (pin.nextElementSibling)
                {
                    if (this.value.length === parseInt(this.attributes['maxlength'].value))
                    {
                        pin.nextElementSibling.focus();
                    }
                }
                if (pin.previousElementSibling)
                {
                    if (e.keyCode === 8 || e.keyCode === 46)
                    {
                        pin.previousElementSibling.focus();
                    }
                }
            };
        }

        $('#formAuthentication').on('submit', function (e){
            e.preventDefault();
            $.ajax({
                method: "POST",
                url: `/admin/login`,
                data: {
                    'login': $('#login').val(),
                    'password': $('#password').val(),
                },
            }).done(function (data){
                if (data === "OK"){
                    window.location.href = "/admin";
                } else if (data === "2FA"){
                    $('#form2fa').off('submit');
                    $('#form2fa').on('submit', function (e){
                        e.preventDefault();
                        $.ajax({
                            method: "POST",
                            url: `/admin/verify2fa`,
                            data: {
                                'otp': $('#otp').val(),
                            },
                        }).done(function (data){
                            if (data === "OK"){
                                window.location.href = "/admin";
                            } else {
                                toastr.error("{{ __('Invalid 2FA code!') }}");
                            }
                        });
                    });
                    $('#2faModal').modal('show');
                } else {
                    toastr.error("{{ __('Incorrect Login or Password!') }}");
                }
            });
        });
    </script>
    <script>
        let togglePasswordIcon = document.querySelector('.toggle-password i');
        let passwordInput = document.querySelector('.form-password-toggle input[type="password"]');

        togglePasswordIcon.addEventListener('click', function() {
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                togglePasswordIcon.classList.remove('bx-hide');
                togglePasswordIcon.classList.add('bx-show');
            } else {
                passwordInput.type = "password";
                togglePasswordIcon.classList.remove('bx-show');
                togglePasswordIcon.classList.add('bx-hide');
            }
        });
    </script>

@endsection

@section('content')
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <!-- Register -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center">
                            <a href="{{url('/')}}" class="app-brand-link gap-2">
                                <span class="app-brand-logo demo">
                                    <img class="app-brand-logo demo" style="height: 50px;" src="/res/img/logo-{{ $configData['style'] == 'dark' ? 'white' : 'colored' }}.png">
                                </span>
                            </a>
                        </div>
                        <!-- /Logo -->
                        <h4 class="mb-2">{{ __('Welcome to the Dashboard') }} 👋</h4>
                        <p class="mb-4">{{ __('Please sign in to your account and start manage your store') }}</p>

                        <form id="formAuthentication" class="mb-3">
                            <div class="mb-3">
                                <label for="login" class="form-label">{{ __('Login') }}</label>
                                <input type="text" class="form-control" id="login" name="login" placeholder="{{ __('Enter your login') }}" autofocus>
                            </div>
                            <div class="mb-3 form-password-toggle">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="password">{{ __('Password') }}</label>
                                </div>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
                                    <span class="input-group-text cursor-pointer toggle-password"><i class="bx bx-hide"></i></span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember-me">
                                    <label class="form-check-label" for="remember-me">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-primary d-grid w-100" type="submit">{{ __('Sign in') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- /Register -->
            </div>
        </div>

        <div class="modal fade" id="2faModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                            <div class="card-body">
                                <!-- Logo -->
                                <div class="app-brand justify-content-center">
                                    <a href="{{url('/')}}" class="app-brand-link gap-2">
                                <span class="app-brand-logo demo">
                                    <img class="app-brand-logo demo" style="height: 50px;" src="/res/img/logo-{{ $configData['style'] == 'light' ? 'colored' : 'white' }}.png">
                                </span>
                                    </a>
                                </div>
                                <!-- /Logo -->
                                <h4 class="mb-2">{{ __('Two Step Verification') }} 💬</h4>
                                <p class="text-start mb-4">
                                    {{  __('Enter the code from the mobile auth app in the field below.') }}
                                </p>
                                <p class="mb-0 fw-semibold">{{ __('Type your 6 digit security code') }}</p>
                                <form id="form2fa" autocomplete="off">
                                    <div class="mb-3">
                                        <div class="auth-input-wrapper d-flex align-items-center justify-content-sm-between numeral-mask-wrapper">
                                            <input type="text" class="form-control auth-input h-px-50 text-center numeral-mask text-center h-px-50 mx-1 my-2" maxlength="1" autofocus>
                                            <input type="text" class="form-control auth-input h-px-50 text-center numeral-mask text-center h-px-50 mx-1 my-2" maxlength="1">
                                            <input type="text" class="form-control auth-input h-px-50 text-center numeral-mask text-center h-px-50 mx-1 my-2" maxlength="1">
                                            <input type="text" class="form-control auth-input h-px-50 text-center numeral-mask text-center h-px-50 mx-1 my-2" maxlength="1">
                                            <input type="text" class="form-control auth-input h-px-50 text-center numeral-mask text-center h-px-50 mx-1 my-2" maxlength="1">
                                            <input type="text" class="form-control auth-input h-px-50 text-center numeral-mask text-center h-px-50 mx-1 my-2" maxlength="1">
                                        </div>
                                        <!-- Create a hidden field which is combined by 3 fields above -->
                                        <input type="hidden" id="otp" name="otp" />
                                    </div>
                                    <button class="btn btn-primary d-grid w-100 mb-3" type="submit">
                                        {{ __('Verify my account') }}
                                    </button>
                                </form>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
