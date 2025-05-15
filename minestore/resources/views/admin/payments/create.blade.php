@extends('admin.layout')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('res/vendor/libs/bs-stepper/bs-stepper.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/select2/select2.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/quill/typography.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/quill/editor.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/typeahead-js/typeahead.css')}}" />
@endsection

@section('vendor-script')
    <script src="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
    <script src="{{asset('res/vendor/libs/select2/select2.js')}}"></script>
    <script src="{{asset('res/vendor/libs/quill/katex.js')}}"></script>
    <script src="{{asset('res/vendor/libs/quill/quill.js')}}"></script>
    <script src="{{asset('res/vendor/libs/jquery-repeater/jquery-repeater.js')}}"></script>
    <script src="{{asset('res/vendor/libs/typeahead-js/typeahead.js')}}"></script>
@endsection

@section('page-script')
    <script src="{{asset('res/js/form-basic-inputs.js')}}"></script>
    <script src="{{asset('res/js/forms-typeahead.js')}}"></script>
    <script src="{{asset('res/js/forms-selects.js')}}"></script>
@endsection

@section('content')
<form action="{{ route('payments.store') }}" method="POST" autocomplete="off"
      class="form-repeater">
@csrf

<h4 class="fw-bold py-3 mb-1">
    <span class="text-body fw-light">{{ __('Make a Manual Payment') }}</span>
</h4>

@if (session('warning'))
    <div class="alert alert-primary alert-dismissible" role="alert">
        <h5 class="alert-heading text-bold d-flex align-items-center mb-1">{{ __('Ooopsss! We have a problem...') }} 😢</h5>
            <p class="mb-0">{{ session('warning') }}</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
        </button>
    </div>
@endif

<div class="col-12 mb-4">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 mb-2">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="needs-validation">
                                <div class="mb-3">
                                    <label class="form-label" for="name">
                                        {{ __('Username') }}
                                        <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;"
                                           data-bs-toggle="tooltip" data-bs-placement="top"
                                           title="{{ __('Enter the username to initiate a transaction.') }}"></i>
                                    </label>
                                    <input type="text" class="form-control" id="name" name="username"
                                           placeholder="Notch" required />
                                </div>
                            </div>
                            @error('username')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="price" class="form-label">
                                {{ __('Transaction Price') }}
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;"
                                   data-bs-toggle="tooltip" data-bs-placement="top"
                                   title="{{ __('The price for current transaction.') }}"></i>
                            </label>
                            <div class="input-group mb-2">
                                <input type="text" inputmode="numeric" pattern="^\d*([,.]\d{1,2})?$" class="form-control" id="price" name="price"
                                       placeholder="9,99">
                                <span class="input-group-text">{{ $settings->currency }}</span>
                            </div>
                            @error('price')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <div class="needs-validation">
                                <div class="mb-3">
                                    <label class="form-label" for="name">
                                        {{ __('Email') }}
                                        <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;"
                                           data-bs-toggle="tooltip" data-bs-placement="top"
                                           title="{{ __('Enter the email attached to this transaction.') }}"></i>
                                    </label>
                                    <input type="text" class="form-control" id="name" name="email"
                                           placeholder="customer@gmail.com" required />
                                </div>
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="select2Basic" class="form-label">{{ __('Payment Method') }}</label>
                            <select id="select2Basic" class="select2 form-select form-select-lg" name="gateway"
                                    data-allow-clear="true">
                                @foreach($gateways as $gateway)
                                    <option value="{{ $gateway->name }}">{{ $gateway->name }}</option>
                                @endforeach
                            </select>
                            @error('gateway')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-12 mb-2">
                        <label for="description-editor" class="form-label">
                            {{ __('Note') }}
                            <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;"
                               data-bs-toggle="tooltip" data-bs-placement="top"
                               title="{{ __('The note for this transaction that will see you and your staff team.') }}"></i>
                        </label>
                        <textarea class="form-control" id="description" name="note"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <div class="needs-validation">
                                <div class="mb-3">
                                    <label class="form-label" for="name">
                                        {{ __('Transaction ID') }}
                                        <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;"
                                           data-bs-toggle="tooltip" data-bs-placement="top"
                                           title="{{ __('Transaction ID attached to this purchase, you can leave it empty.') }}"></i>
                                    </label>
                                    <input type="text" class="form-control" id="name" name="transaction"
                                           placeholder="XXX288876543388809XXX" />
                                </div>
                            </div>
                            @error('transaction')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="select2Primary" class="form-label">{{ __('Packages to delivery...') }}</label>
                            <div class="select2-primary">
                                <select id="select2Primary" required name="packages[]" class="select2 form-select"
                                        multiple>
                                    @foreach ($packages as $package)
                                        <option value="{{ $package->id }}">{{ $package->name }}
                                            - {{ $package->price }} {{ $settings->currency }} ({{ $package->category->name }} Category)</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="bg-lighter border rounded p-3 mb-3">
                                <label class="switch switch-square mb-3">
                                    <input type="checkbox" class="switch-input" id="featured" name="payment_is_execute">
                                    <span class="switch-toggle-slider">
                                    <span class="switch-on"></span>
                                    <span class="switch-off"></span>
                                </span>
                                    <span
                                        class="switch-label">{{ __('Execute commands attached to these packages?') }}</span>
                                </label>
                                <br />
                                <label class="switch switch-square">
                                    <input type="hidden" name="send_mail" value="0">
                                    <input type="checkbox" class="switch-input" id="featured" name="send_mail" value="1">
                                    <span class="switch-toggle-slider">
                                    <span class="switch-on"></span>
                                    <span class="switch-off"></span>
                                </span>
                                    <span
                                        class="switch-label">{{ __('Send an email order about successfully purchase?') }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="d-grid gap-2 col-lg-12 mx-auto">
        <button class="btn btn-primary btn-lg" type="submit"><span
                class="tf-icon bx bx-plus-circle bx-xs"></span> {{ __('Create a Payment') }}
        </button>
    </div>
</div>
</form>
@endsection
