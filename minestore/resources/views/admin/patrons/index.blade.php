@extends('admin.layout')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/tagify/tagify.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
@endsection

@section('vendor-script')
    <script src="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
    <script src="{{asset('res/vendor/libs/tagify/tagify.js')}}"></script>
@endsection

@section('page-script')
    <script src="{{asset('res/js/forms-tagify.js')}}"></script>
@endsection

@section('content')
    <h4 class="fw-bold py-3 mb-1">
        <span class="text-body fw-light">{{ __('Patrons Module') }}</span>
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
            <h5 class="alert-heading d-flex align-items-center mb-1">{{ __('Oh snap') }}! 🧐</h5>
            <p class="mb-0">{{ session('error') }}</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            </button>
        </div>
    @endif

    <form method="POST" enctype="multipart/form-data" autocomplete="off">
        @csrf
        <div class="row">
            <div class="col-12 mb-4">
                <x-card-input type="checkbox" name="patrons_enabled" :checked="$settings->patrons_enabled" icon="bx-question-mark">
                    <x-slot name="title">{{ __('Enable this module?') }}</x-slot>
                    <x-slot name="text">{{ __('You need to enable "Patrons" module to make it available at your Webstore.') }}</x-slot>
                </x-card-input>
            </div>

            <div class="col-12 mb-4">
                <x-card-input type="text" name="patrons_description" icon="bx-font-family" :value="$settings->patrons_description">
                    <x-slot name="title">{{ __('Page Description') }}</x-slot>
                    <x-slot name="tooltip">{{ __('Description will be displayed at the top of the Patrons page.') }}</x-slot>
                    <x-slot name="text">{{ __('This description will be displayed at the top of the Patrons page. HTML is allowed.') }}</x-slot>
                </x-card-input>
            </div>

            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row d-flex w-100 align-self-center">
                            <div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
                                <div class="row align-self-center h-100">
                                    <div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
                                        <div class="d-flex justify-content-center mb-4">
                                            <div class="settings_icon bg-label-primary">
                                                <i class="bx bxs-group"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
                                        <h4>
                                            {{ __('Values to Group Patrons') }}
                                        </h4>
                                        <div class="mb-3 col-md-10">
                                            <p class="card-text">{{ __('The values that will be used to group patrons. Digits only.') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="action col-12 col-xl-3 col-lg-4 align-self-center mx-auto">
                                <input id="TagifyBasic" type="text" class="form-control" name="patrons_groups"
                                       value="{{ old('patrons_groups', $patronsGroupsString) }}" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="d-grid gap-2 col-lg-12 mx-auto">
                <button class="btn btn-primary btn-lg" type="submit"><span class="tf-icon bx bx-save bx-xs"></span> {{ __('Save Settings') }}</button>
            </div>
        </div>

    </form>
@endsection
