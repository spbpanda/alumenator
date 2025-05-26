@extends('admin.layout')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
@endsection

@section('vendor-script')
@endsection

@section('page-script')
@endsection

@section('content')
    <h4 class="fw-bold py-3 mb-1">
        <span class="text-body fw-light">MineStoreCMS & PayNow Integration Onboarding</span>
    </h4>

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

    <div class="row d-flex align-items-stretch mb-4">
        <div class="col-12">
            <div class="card h-100">
                <div class="card-header mb-0 pb-0 d-flex justify-content-center align-items-center gap-4">
                    <img src="{{ asset('res/img/logos/paynow.svg') }}" alt="PayNow" class="img-fluid" style="max-height: 60px; height: auto;">
                    <img src="{{ asset('res/img/logos/minestorecms.svg') }}" alt="MineStoreCMS" class="img-fluid" style="max-height: 60px; height: auto;">
                </div>
                <hr>
                <div class="card-body d-flex flex-column">
                    <h4 class="card-title text-center mb-4">Seamless Integration of PayNow Checkout & MineStoreCMS</h4>
                    <p class="text-center mb-4">Unlock the full potential of your online store with the powerful integration of PayNow and MineStoreCMS. Streamline payments, manage subscriptions, and grow your business globally with ease.</p>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <h5 class="fw-bold mb-3">Key Features</h5>
                            <ul class="list-unstyled">
                                <li class="mb-3 d-flex align-items-start">
                                    <i class="bx bx-check-circle text-primary me-2" style="font-size: 1.25rem;"></i>
                                    <div>
                                        <strong>Subscription Management</strong><br>
                                        Automate billing, renewals, and customer plans effortlessly with PayNow's robust subscription tools, perfectly integrated with MineStoreCMS.
                                    </div>
                                </li>
                                <li class="mb-3 d-flex align-items-start">
                                    <i class="bx bx-check-circle text-primary me-2" style="font-size: 1.25rem;"></i>
                                    <div>
                                        <strong>Global Tax Compliance</strong><br>
                                        Stay compliant worldwide with automated tax calculations and reporting, allowing you to focus on scaling your MineStoreCMS-powered business.
                                    </div>
                                </li>
                                <li class="mb-3 d-flex align-items-start">
                                    <i class="bx bx-check-circle text-primary me-2" style="font-size: 1.25rem;"></i>
                                    <div>
                                        <strong>Full Chargeback Protection</strong><br>
                                        Eliminate fraud risks and protect your revenue with PayNow’s chargeback coverage, ensuring secure transactions for your MineStoreCMS store.
                                    </div>
                                </li>
                                <li class="mb-3 d-flex align-items-start">
                                    <i class="bx bx-check-circle text-primary me-2" style="font-size: 1.25rem;"></i>
                                    <div>
                                        <strong>Borderless Payments</strong><br>
                                        Reach global customers with 75+ payment methods, fully supported by PayNow and seamlessly integrated into your MineStoreCMS platform.
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6 mb-4">
                            <h5 class="fw-bold mb-3">Why Choose This Integration?</h5>
                            <ul class="list-unstyled">
                                <li class="mb-3 d-flex align-items-start">
                                    <i class="bx bx-star text-warning me-2" style="font-size: 1.25rem;"></i>
                                    <div>
                                        <strong>Cost-Effective</strong><br>
                                        Enjoy a competitive 3.99% platform fee, making it a better alternative to other analogue platforms.
                                    </div>
                                </li>
                                <li class="mb-3 d-flex align-items-start">
                                    <i class="bx bx-star text-warning me-2" style="font-size: 1.25rem;"></i>
                                    <div>
                                        <strong>All Money in One Place</strong><br>
                                        Consolidate your earnings in the PayNow dashboard, ready for easy withdrawal to your bank account or PayPal.
                                    </div>
                                </li>
                                <li class="mb-3 d-flex align-items-start">
                                    <i class="bx bx-star text-warning me-2" style="font-size: 1.25rem;"></i>
                                    <div>
                                        <strong>Time-Saving Automation</strong><br>
                                        Automate complex processes like subscriptions and tax compliance, freeing up time to focus on growing your business.
                                    </div>
                                </li>
                                <li class="mb-3 d-flex align-items-start">
                                    <i class="bx bx-star text-warning me-2" style="font-size: 1.25rem;"></i>
                                    <div>
                                        <strong>Forget About Taxes</strong><br>
                                        PayNow handles all tax calculations and reporting, ensuring compliance with local laws and regulations (such as VAT, Sales Taxes and etc).
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12 text-center">
                            <a href="{{ route('paynow.onboarding.start') }}" class="btn btn-primary btn-lg">
                                <i class="bx bx-rocket me-2"></i> Get Started with <strong>PayNow & MineStoreCMS</strong>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
