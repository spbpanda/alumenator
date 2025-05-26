@extends('admin.layout')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('res/vendor/libs/bs-stepper/bs-stepper.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/select2/select2.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/tagify/tagify.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/typeahead-js/typeahead.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
@endsection

@section('vendor-script')
    <script src="{{asset('res/vendor/libs/bs-stepper/bs-stepper.js')}}"></script>
    <script src="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
    <script src="{{asset('res/vendor/libs/select2/select2.js')}}"></script>
    <script src="{{asset('res/vendor/libs/tagify/tagify.js')}}"></script>
    <script src="{{asset('res/vendor/libs/jquery-repeater/jquery-repeater.js')}}"></script>

    <script src="{{asset('res/vendor/libs/moment/moment.js')}}"></script>
@endsection

@section('page-script')
    <script src="{{asset('res/js/form-wizard-numbered.js')}}"></script>
    <script src="{{asset('res/js/forms-file-upload.js')}}"></script>
    <script src="{{asset('res/js/forms-selects.js')}}"></script>
    <script src="{{asset('res/js/forms-tagify.js')}}"></script>
    <script src="{{asset('res/js/forms-typeahead.js')}}"></script>
    <script src="{{asset('res/js/forms-pickers.js')}}"></script>
    <script src="{{asset('res/js/forms-extras.js')}}"></script>
    <script src="{{asset('res/js/forms-tagify.js')}}"></script>

    <script src="{{asset('js/modules/bans.js')}}"></script>
    <script src="{{asset('js/modules/chargeback.js')}}"></script>
    <script>
        let isBanned = {{ $ban != null ? 'true' : 'false' }};
        let banId = {{ $ban->id ?? 'undefined' }};

        $("#ban-button").click(function() {
            if (isBanned && banId !== undefined) {
                unbanUser(banId).done(function(r) {
                    isBanned = false;
                    banId = undefined;
                    toastr.success("User was unbanned!");
                    switchMainBanButton($("#ban-button"));
                }).fail(function(r) {
                    if (r.status === 410) {
                        toastr.error(r.responseJSON.message);
                    } else {
                        toastr.error("Unable to unban user!");
                    }
                });
            } else {
                banUser("{{$chargeback->payment->user->username}}").done(function(r) {
                    isBanned = true;
                    banId = r.id;
                    toastr.success("User was banned!");
                    switchMainBanButton($("#ban-button"));
                }).fail(function(r) {
                    if (r.status === 410) {
                        toastr.error(r.responseJSON.message);
                    } else {
                        toastr.error("Unable to ban user!");
                    }
                });
            }
        });

        $("#submit-button").click(function() {
            submitChargeback("{{$chargeback->id}}").done(function(r) {
                toastr.success("Evidences were sent successfully!");
            }).fail(function(r) {
                if (r.status === 503) {
                    toastr.error(r.responseJSON.message);
                } else {
                    toastr.error("Unable to submit evidences!");
                }
            });
        });
    </script>
@endsection

@section('content')
    <h4 class="fw-bold py-3 mb-1">
        <span class="text-body fw-light">{{ __('Chargeback Case #') }} {{ $chargeback->id }}</span>
    </h4>

    <div class="alert alert-primary alert-dismissible" role="alert">
        <h5 class="alert-heading d-flex align-items-center fw-bold mb-1">{{ __('Important Note') }} ☝️</h5>
        <p class="mb-0">
            {!! __('This is the chargeback case for this payment. It will display as much details as possible regarding the chargeback and gateway API.<br>Please control and respond to the chargeback as normal through your payment gateway and the details will update here!') !!}
        </p>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
        </button>
    </div>

    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-5 mb-2 d-flex justify-content-start">
                                <a href="{{ route('chargeback.index') }}" class="btn btn-primary"
                                   style="margin-right: 5px;">
                                    <span class="tf-icons bx bx-arrow-back me-1"></span>{{ __('Back') }}
                                </a>
                                <a href="{{ route('chargeback.download', $chargeback->id) }}" target="_blank"
                                   class="btn btn-warning">
                                    <span
                                        class="tf-icons bx bxs-download me-1"></span>{{ __('Generate Evidence (PDF)') }}
                                </a>
                            </div>
                            @if (strtolower($chargeback->payment->gateway) === 'stripe')
                                <div class="col-md-7 mb-2 d-flex justify-content-end">
                                    <button type="button" class="btn btn-success" id="submit-button">
                                        <span
                                            class="tf-icons bx bxs-check-shield me-1"></span>{{ __('Submit to Stripe') }}
                                    </button>
                                </div>
                            @endif
                        </div>
                        <hr>
                        <div class="row">
                            <div class="table-responsive table-striped">
                                <table class="table table-striped table-bordered">
                                    <tbody>
                                    <tr>
                                        <td style="font-weight: 500;">
                                            {{ __('Dispute ID:') }}
                                        </td>
                                        <td>
                                            {{ $chargeback->sid }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 500;">
                                            {{ __('Status:') }}
                                        </td>
                                        <td>
                                            <span class="badge bg-danger">{{ __('Chargeback') }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {{ __('Payment Gateway:') }}
                                        </td>
                                        <td>
                                            {{ $chargeback->payment->gateway }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 500;">
                                            {{ __('Creation Date:') }}
                                        </td>
                                        <td>
                                            {{ $chargeback->creation_date }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 500;">
                                            {{ __('Username:') }}
                                        </td>
                                        <td>
                                            <?php $username = $chargeback->payment->user->username; ?>
                                            <img width="20" height="20"
                                                 src="https://minotar.net/avatar/{{ $username }}/25"
                                                 alt="{{ $username }}"> {{ $username }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 500;">
                                            UUID:
                                        </td>
                                        <td>
                                            {{ $chargeback->payment->user->uuid }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 500;">
                                            IP
                                        </td>
                                        <td>
                                            {{ $chargeback->payment->ip }}
                                        </td>
                                    </tr>
                                    @foreach(json_decode($chargeback->details) as $detail_key => $detail_val)
                                        <tr>
                                            <td style="font-weight: 500;">
                                                {{ $detail_key }}:
                                            </td>
                                            <td>
                                                {{ $detail_val }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header mb-0 pb-2">
                        <h4 class="card-title" style="text-align: center;">{{ __('User information') }}</h4>
                    </div>
                    <hr>
                    <div class="card-body text-center">
                        <img src="https://mc-heads.net/body/{{ $chargeback->payment->user->uuid }}"
                             alt="{{ $username }}"
                             style="width: 140px; border-radius: 4px;transform: scale(-1, 1); margin-bottom: 5px;">
                        <h5 style="font-size: 24px; font-weight: 500;">{{ $username }}</h5>
                        <h6 style="font-size: 14px;">UUID: {{ $chargeback->payment->user->uuid }}</h6>
                        <div class="row">
                            <div class="col-6 col-md-6">
                                @if ($ban == null)
                                    <button class="btn btn-lg btn-danger" id="ban-button">
                                        <span class="tf-icons bx bx-message-square-x me-1"></span>{{ __('Ban User') }}
                                    </button>
                                @else
                                    <button class="btn btn-lg btn-success" id="ban-button">
                                        <span class="tf-icons bx bx-check-square me-1"></span>{{ __('Unban User') }}
                                    </button>
                                @endif
                            </div>
                            <div class="col-6 col-md-6">
                                <a href="{{ route('lookup.search', $username) }}" target="_blank"
                                   class="btn btn-lg btn-warning">
                                    <span class="tf-icons bx bx-search me-1"></span>{{ __('Lookup') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
