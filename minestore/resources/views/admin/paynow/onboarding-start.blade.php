@extends('admin.layout')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('res/vendor/libs/bs-stepper/bs-stepper.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/select2/select2.css')}}" />
@endsection

@section('page-style')
    <link rel="stylesheet" href="{{asset('res/vendor/css/pages/page-auth.css')}}">
    <style>
        .hidden { display: none !important; }
        body { overflow-x: hidden; }
    </style>
@endsection

@section('vendor-script')
    <script src="{{asset('res/vendor/libs/bs-stepper/bs-stepper.js')}}"></script>
    <script src="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
    <script src="{{asset('res/vendor/libs/select2/select2.js')}}"></script>
@endsection

@section('page-script')
    <script src="{{asset('res/js/form-wizard-icons.js')}}"></script>
    <script>
        function loadPreview(e, elementId) {
            const filePreviewElement = document.querySelector('#preview-'+elementId);
            filePreviewElement.src = URL.createObjectURL(e.currentTarget.files[0]);
        }
        function clearImage(elementId) {
            document.getElementById('preview-'+elementId).src = "";
            document.getElementById(elementId).value = null;
        }
    </script>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-primary alert-dismissible" role="alert">
            <h5 class="alert-heading d-flex align-items-center mb-1">{{ __('Well done') }} 👍</h5>
            <p class="mb-0">{{ session('success') }}</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            </button>
        </div>
    @elseif (session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            <h5 class="alert-heading d-flex align-items-center mb-1">{{ __('Oops! Something went wrong') }} 😢</h5>
            <p class="mb-0">{{ session('error') }}</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="image-container d-flex justify-content-center align-items-center gap-3">
                <img src="{{ asset('res/img/logos/paynow.svg') }}" alt="PayNow" class="img-fluid" style="max-height: 50px;">
                <img src="{{ asset('res/img/logos/minestorecms.svg') }}" alt="MineStoreCMS" class="img-fluid" style="max-height: 50px;">
            </div>
        </div>
    </div>
    <div class="row d-flex align-items-center justify-content-center" id="startIntegration">
        <div class="col-lg-12 authentication-bg pt-3 p-4">
            <div class="bs-stepper wizard-vertical vertical wizard-vertical-icons-example mt-2">
                <div class="bs-stepper-header">
                    <div class="step" data-target="#createAccount">
                        <button type="button" class="step-trigger">
                            <span class="bs-stepper-circle"><i class="bx bx-user-plus"></i></span>
                            <span class="bs-stepper-label mt-1">
                                <span class="bs-stepper-title">Create Account</span>
                                <span class="bs-stepper-subtitle">Register & Verify Email</span>
                            </span>
                        </button>
                    </div>
                    <div class="line"></div>
                    <div class="step" data-target="#createStore">
                        <button type="button" class="step-trigger">
                            <span class="bs-stepper-circle"><i class="bx bx-store"></i></span>
                            <span class="bs-stepper-label mt-1">
                                <span class="bs-stepper-title">Create Store</span>
                                <span class="bs-stepper-subtitle">Set up PayNow</span>
                            </span>
                        </button>
                    </div>
                    <div class="line"></div>
                    <div class="step" data-target="#kycPayouts">
                        <button type="button" class="step-trigger">
                            <span class="bs-stepper-circle"><i class="bx bx-file"></i></span>
                            <span class="bs-stepper-label mt-1">
                                <span class="bs-stepper-title">KYC & Payouts</span>
                                <span class="bs-stepper-subtitle">Verify Identity & Payouts</span>
                            </span>
                        </button>
                    </div>
                    <div class="line"></div>
                    <div class="step" data-target="#getAPIKey">
                        <button type="button" class="step-trigger">
                            <span class="bs-stepper-circle"><i class="bx bx-key"></i></span>
                            <span class="bs-stepper-label mt-1">
                                <span class="bs-stepper-title">Get API Keys</span>
                                <span class="bs-stepper-subtitle">Generate API credentials</span>
                            </span>
                        </button>
                    </div>
                    <div class="line"></div>
                    <div class="step" data-target="#connectMineStore">
                        <button type="button" class="step-trigger">
                            <span class="bs-stepper-circle"><i class="bx bx-check-circle"></i></span>
                            <span class="bs-stepper-label mt-1">
                                <span class="bs-stepper-title">Complete Integration</span>
                                <span class="bs-stepper-subtitle">Sync MineStoreCMS & PayNow</span>
                            </span>
                        </button>
                    </div>
                </div>
                <div class="bs-stepper-content">
                    <form id="integrationForm" method="post" enctype="multipart/form-data">
                        @csrf
                        <!-- Create Account -->
                        <div id="createAccount" class="content">
                            <div class="content-header mb-3">
                                <h6 class="mb-0">Create Your PayNow Account</h6>
                                <small>Register and verify your email. <a href="https://docs.minestorecms.com/features/paynow/onboarding#step-1">Detailed guide</a>.</small>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <ol class="ps-3">
                                                <li class="mb-2">Go to PayNow <a href="https://dashboard.paynow.gg/auth/register" target="_blank">Sign Up Page</a>, select <strong>"I want to monetize my game server"</strong>.</li>
                                                <li class="mb-2"><strong>Fill in details</strong>: country/region, email, full name, password.</li>
                                                <li class="mb-2">Accept <a href="https://paynow.gg/terms-of-service" target="_blank">Terms</a>, <a href="https://paynow.gg/user-agreement" target="_blank">Agreement</a>, and <a href="https://paynow.gg/privacy-policy" target="_blank">Privacy Policy</a>.</li>
                                                <li class="mb-2">Click <strong>"Register"</strong> and verify email via confirmation link.</li>
                                            </ol>
                                        </div>
                                        <div class="col-12">
                                            <div class="alert alert-primary">
                                                <i class="bx bx-info-circle me-2"></i>
                                                <span>Check <strong>spam/junk folder</strong> if verification email is not in inbox.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <img src="{{ asset('res/img/paynow-integration/onboarding/step-1.png') }}" alt="PayNow Registration" class="img-fluid rounded shadow-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-label-secondary btn-prev" disabled>
                                    <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                    <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                </button>
                                <button type="button" class="btn btn-primary btn-next">
                                    <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                                    <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Create Store -->
                        <div id="createStore" class="content">
                            <div class="content-header mb-3">
                                <h6 class="mb-0">Create PayNow Checkout Store</h6>
                                <small>Set up your store details. <a href="https://docs.minestorecms.com/features/paynow/onboarding#step-3">Detailed guide</a>.</small>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <ol class="ps-3">
                                                <li class="mb-2">Click <strong>"Create Store"</strong> after login.</li>
                                                <li class="mb-2">Enter <strong>store name</strong> (e.g., GrassCraft Network), <strong>slug</strong> (e.g., <code>grasscraft-network</code>), <strong>currency</strong> (matches <strong>MineStoreCMS</strong> webstore primary currency).</li>
                                                <li class="mb-2">Select Platform: <strong>"Minecraft (Offline)"</strong>, Integration Type: <span class="text-primary fw-bold">"Third-Party Integration"</span>, and <span class="text-primary fw-bold">"MineStoreCMS (Third-Party)"</span>.</li>
                                                <li class="mb-2">Provide <strong>MineStoreCMS Webstore URL</strong> (e.g., <code>https://store.grasscraft.net</code>) and support email.</li>
                                            </ol>
                                        </div>
                                        <div class="col-12">
                                            <div class="alert alert-warning">
                                                <i class="bx bx-error-circle me-2"></i>
                                                <span>Ensure currency <strong>matches MineStoreCMS</strong>. Keep in mind, PayNow currency <strong>cannot be changed later</strong>.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="h-150">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <img src="{{ asset('res/img/paynow-integration/onboarding/step-2.png') }}" alt="Store Creation" class="img-fluid rounded shadow-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-primary btn-prev">
                                    <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                    <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                </button>
                                <button type="button" class="btn btn-primary btn-next">
                                    <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                                    <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- KYC & Payouts -->
                        <div id="kycPayouts" class="content">
                            <div class="content-header mb-3">
                                <h6 class="mb-0">Complete KYC & Set Up Payouts</h6>
                                <small>Verify identity and configure payouts. <a href="https://docs.minestorecms.com/features/paynow/onboarding#step-4">Detailed guide</a>.</small>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <ol class="ps-3">
                                                <li class="mb-2">Go to <strong>"Verify Your Identity"</strong> tab, submit ID and selfie.</li>
                                                <li class="mb-2">Go to <strong>"Setup Payouts"</strong> tab, choose PayPal or bank transfer, provide tax info.</li>
                                                <li class="mb-2">Use <strong>same legal entity</strong> for <strong>KYC</strong> and <strong>payouts</strong>.</li>
                                                <li class="mb-2"><strong>Submit and await verification</strong> (~15 minutes, however up to 3 business days).</li>
                                            </ol>
                                        </div>
                                        <div class="col-12">
                                            <div class="alert alert-primary">
                                                <i class="bx bx-shield me-2"></i>
                                                <span><strong>KYC and payouts</strong> are <strong>mandatory</strong> to use <strong>PayNow Checkout</strong>. Use <strong>real details</strong> to avoid issues.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="h-100">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <img src="{{ asset('res/img/paynow-integration/onboarding/step-3.png') }}" alt="KYC & Payouts" class="img-fluid rounded shadow-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-primary btn-prev">
                                    <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                    <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                </button>
                                <button type="button" class="btn btn-primary btn-next">
                                    <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                                    <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Get API Key -->
                        <div id="getAPIKey" class="content">
                            <div class="content-header mb-3">
                                <h6 class="mb-0">Generate API Key</h6>
                                <small>Retrieve your PayNow API credentials. <a href="https://docs.minestorecms.com/features/paynow/onboarding#step-6">Detailed guide</a>.</small>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <ol class="ps-3">
                                                <li class="mb-2">Visit <a href="https://dashboard.paynow.gg/onboarding" target="_blank">PayNow Onboarding</a> Page to find API configuration.</li>
                                                <li class="mb-2">Copy <strong>Store ID</strong> (e.g., <code>1237457373828282</code>).</li>
                                                <li class="mb-2">Click <strong>"Generate API Key"</strong> and <strong>copy API Key</strong> (e.g., <code>pnapi_v1_1233427890abcdef</code>).</li>
                                            </ol>
                                        </div>
                                        <div class="col-12">
                                            <div class="alert alert-danger">
                                                <i class="bx bx-lock-alt me-2"></i>
                                                <span>
                                                    <strong>Do not share your API Key</strong> with anyone. It is used to authenticate your requests to the PayNow API.
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="h-100">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <img src="{{ asset('res/img/paynow-integration/onboarding/step-4.png') }}" alt="API Key Generation" class="img-fluid rounded shadow-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-primary btn-prev">
                                    <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                    <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                </button>
                                <button type="button" class="btn btn-primary btn-next">
                                    <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                                    <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Connect MineStore -->
                        <div id="connectMineStore" class="content">
                            <div class="content-header mb-3">
                                <h6 class="mb-0">Connect to MineStoreCMS</h6>
                                <small>Enter API credentials in MineStoreCMS. <a href="https://docs.minestorecms.com/features/paynow/onboarding#step-7">Detailed guide</a>.</small>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="col-sm-12 mb-3">
                                        <label class="form-label" for="store_id">
                                            {{ __('PayNow Storefront ID') }}*
                                            <a href="https://dashboard.paynow.gg/onboarding" target="_blank">(Click Here)</a>
                                        </label>
                                        <input type="text" id="store_id" name="store_id" class="form-control" placeholder="727X645X4XXXXX74748XX" />
                                        @error('store_id')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-sm-12 mb-3">
                                        <label class="form-label" for="api_key">
                                            {{ __('PayNow API Key') }}*
                                            <a href="https://dashboard.paynow.gg/api-keys" target="_blank">(Click Here)</a>
                                        </label>
                                        <input type="text" id="api_key" name="api_key" class="form-control" placeholder="pnapi_v1_x7338xxXXxXx7383837XX" />
                                        @error('api_key')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="h-100">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <img src="{{ asset('res/img/paynow-integration/onboarding/step-5.png') }}" alt="MineStoreCMS Setup" class="img-fluid rounded shadow-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-primary btn-prev">
                                    <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                    <span class="align-middle d-sm-inline-block d-none">Previous</span>
                                </button>
                                <button class="btn btn-success" type="submit" id="integrationForm_submit">
                                    <i class="bx bx-check me-1"></i>
                                    <span>Complete Setup</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
