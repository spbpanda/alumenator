@php use App\Models\Subscription @endphp
@extends('admin.layout')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/sweetalert2/sweetalert2.css')}}" />
@endsection

@section('vendor-script')
    <script src="{{asset('res/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
@endsection

@section('page-script')
    <script src="{{ asset('js/modules/subscriptions.js') }}"></script>
    <script>
        $("#close-subscription-button").click(function() {
            Swal.fire({
                title: "{{ __('Are you sure?') }}",
                text: "{!! __('Do you want to close this subscription?') !!}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ __('Yes') }}",
                customClass: {
                    confirmButton: 'btn btn-primary me-1',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    closeSubscription({{ $subscription->id }}).done(function(r) {
                        toastr.success("{{ __('Subscription closed successfully!') }}");
                        setTimeout(function() {
                            location.reload();
                        }, 500);
                    }).fail(function(r) {
                        toastr.error(r.responseJSON.message);
                    });
                }
            });
        });
    </script>
@endsection

@section('content')
    <h4 class="fw-bold py-3 mb-1">
        <span class="text-body fw-light">{{ __('Subscription Details') }} #{{$subscription->id}}</span>
    </h4>
    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-5 mb-2 d-flex justify-content-start">
                                <a href="{{ route('subscriptions.index') }}" class="btn btn-primary"
                                   style="margin-right: 5px;">
                                    <span class="tf-icons bx bx-arrow-back me-1"></span>{{ __('Back') }}
                                </a>
                            </div>
                            <div class="col-md-7 mb-2 d-flex justify-content-end">
                                @if ($subscription->status === Subscription::ACTIVE && in_array($payment->gateway, ['Stripe', 'PayNow', 'stripe']))
                                    <button type="button" class="btn btn-danger" id="close-subscription-button">
                                        <span class="tf-icons bx bx-x-circle me-1"></span>{{ __('Close Subscription') }}
                                    </button>
                                @endif
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="table-responsive table-striped">
                                <table class="table table-striped table-bordered">
                                    <tbody>
                                    <tr>
                                        <td style="font-weight: 500;">
                                            {{ __('Subscription ID:') }}
                                        </td>
                                        <td>
                                            {{ empty($subscription->sid) ? 'N/A' : $subscription->sid }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 500;">
                                            {{ __('Status:') }}
                                        </td>
                                        <td>
                                            @if ($subscription->status === Subscription::ACTIVE)
                                                <span class="badge bg-success">{{ __('Active') }}</span>
                                            @elseif ($subscription->status === Subscription::CANCELLED)
                                                <span class="badge bg-danger">{{ __('Cancelled') }}</span>
                                            @elseif ($subscription->status === Subscription::PENDING)
                                                <span class="badge bg-warning">{{ __('Pending') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {{ __('Payment Gateway:') }}
                                        </td>
                                        <td>
                                            {{ empty($payment->gateway) ? 'N/A' : ucfirst($payment->gateway) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 500;">
                                            {{ __('Date:') }}
                                        </td>
                                        <td>
                                            {{ $subscription->creation_date }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 500;">
                                            {{ __('Renewal Date:') }}
                                        </td>
                                        <td>
                                            {{ $subscription->renewal ?? 'N/A' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 500;">
                                            {{ __('Billing Period:') }}
                                        </td>
                                        <td>
                                            {{ $subscription->interval_days }} {{ __('days') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 500;">
                                            {{ __('Amount of Cycles:') }}
                                        </td>
                                        <td>
                                            {{ $subscription->count ?? 0 }} {{ __('Subscription Cycles') }}
                                        </td>
                                    </tr>
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
                        <img src="https://mc-heads.net/avatar/{{ $payment->user->uuid ?? $payment->user->username }}"
                             alt="{{ $payment->user->username }}"
                             onerror="this.src='{{ asset('res/img/question-icon.png') }}';"
                             style="width: 155px; border-radius: 4px;transform: scale(-1, 1); margin-bottom: 5px;">
                        <h5 style="font-size: 24px; font-weight: 500;">{{ $payment->user->username }}</h5>
                        <h6 style="font-size: 14px;">UUID: {{ $payment->user->uuid }}</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('payments.show', $payment->id) }}" target="_blank"
                                   class="btn btn-lg btn-primary">
                                    <span class="tf-icons bx bx-link me-1"></span>
                                    Payment
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ route('lookup.search', $payment->user->username) }}" target="_blank"
                                   class="btn btn-lg btn-warning">
                                    <span class="tf-icons bx bx-search me-1"></span>
                                    {{ __('Lookup') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
