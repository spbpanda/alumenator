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
    <form action="{{ route('migrations.store') }}" method="POST" autocomplete="off"
          class="form-repeater">
        @csrf

        <h4 class="fw-bold py-3 mb-1">
            <span class="text-body fw-light">{{ __('New Platform Migration') }}</span>
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
                                <div class="col-md-12 mb-2">
                                    <label for="select2Basic" class="form-label">{{ __('Platform') }}</label>
                                    <select id="select2Basic" class="select2 form-select form-select-lg" name="platform_name"
                                            data-allow-clear="true">
                                        @foreach ($platforms as $label => $value)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('platform_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="needs-validation">
                                        <div class="mb-3">
                                            <label class="form-label" for="platform_token">
                                                {{ __('Headless API Public Key') }}
                                                <a href="https://creator.tebex.io/developers/api-keys">
                                                    <i class="bx bx-help-circle text-muted ms-2" style="margin-bottom: 3px;"
                                                       data-bs-toggle="tooltip"
                                                       data-bs-placement="top"
                                                       title="Click here to find this key.">
                                                    </i>
                                                </a>
                                            </label>
                                            <input type="text" class="form-control" id="platform_token" name="platform_token" value="{{ old('platform_token') }}"
                                                   placeholder="mxxx-45dXXXXXXf52d24XXXXXa7a9c2344565XXX" required />
                                        </div>
                                    </div>
                                    @error('platform_token')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="needs-validation">
                                        <div class="mb-3">
                                            <label class="form-label" for="platform_key">
                                                {{ __('Plugin API Secret') }}
                                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;"
                                                   data-bs-toggle="tooltip" data-bs-placement="top"
                                                   title="{{ __('Find the game server in your list of game servers, and copy the Secret Key.') }}"></i>
                                            </label>
                                            <input type="text" class="form-control" id="platform_key" name="platform_key" value="{{ old('platform_key') }}"
                                                   placeholder="jaduu9zx*******************************************j78991h2k" />
                                        </div>
                                        @error('platform_key')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="bg-lighter border rounded p-3">
                                        <label class="switch switch-square">
                                            <input type="hidden" name="migrate_payments" value="false">
                                            <input type="checkbox" class="switch-input" id="migrate_payments" value="true" name="migrate_payments">
                                            <span class="switch-toggle-slider">
                                                <span class="switch-on"></span>
                                                <span class="switch-off"></span>
                                            </span>
                                            <span class="switch-label">
                                                {{ __('Import also the completed payments from the platform? (Not Recommended)') }}
                                            </span>
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
                        class="tf-icon bx bx-plus-circle bx-xs"></span> {{ __('Create a Platform Migration Task') }}
                </button>
            </div>
        </div>
    </form>
@endsection
