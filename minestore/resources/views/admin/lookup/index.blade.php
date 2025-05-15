@extends('admin.layout')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
@endsection

@section('vendor-script')
@endsection

@section('page-script')
    <script>
        $("#find").click(function() {
            let username = $("#lookup_username").val();
            window.location.href = '{{route('lookup.search','/')}}/' + username;
        });
    </script>
@endsection

@section('content')
    <h4 class="fw-bold py-3 mb-1">
        <span class="text-body fw-light">{{ __('User Lookup') }}</span>
    </h4>
    <div class="col-12 mb-4">
        <div class="card">
            <div class="row text-center">
                <div class="card-body mt-2 mb-3">
                    <i class="bx bx-search p-4 bx-lg bx-border-circle d-inline-block mb-4"></i>
                    <p class="card-text mb-2">
                        {{ __('Lookup the activity of a customer or IP address over the entire MineStore network. Useful in
                        determining if a customer has been fraudulent in the past on another MineStore webstore.') }}
                    </p>
                    <hr>
                    <div class="row justify-content-md-center">
                        <div class="col-4">
                            <div class="input-group">
                                <input type="text" id="lookup_username" class="form-control"
                                       placeholder="Enter the username..." aria-label="username"
                                       aria-describedby="button-addon2">
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary btn-lg mt-3" id="find"><span
                            class="tf-icon bx bx-search bx-xs"></span> {{ __('Search for a Customer') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
