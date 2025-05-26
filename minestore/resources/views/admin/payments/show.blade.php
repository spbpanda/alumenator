@php use App\Models\CommandHistory;use App\Models\Item;use App\Models\Payment;use App\Models\Server; @endphp
@extends('admin.layout')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/sweetalert2/sweetalert2.css')}}" />
@endsection

@section('vendor-script')
    <script src="{{asset('res/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
@endsection

@section('page-script')
    <script src="{{asset('js/modules/bans.js')}}"></script>
    <script src="{{asset('js/modules/payments.js')}}"></script>
    <script>
        let isBanned = {{ $ban != null ? 'true' : 'false' }};
        let banId = {{ $ban->id ?? 'undefined' }};

        $("#ban-button").click(function() {
            if (isBanned && banId !== undefined) {
                unbanUser(banId).done(function(r) {
                    isBanned = false;
                    banId = undefined;
                    toastr.success("{{ __('User was Unbanned!') }}");
                    switchMainBanButton($("#ban-button"));
                }).fail(function(r) {
                    if (r.status === 410) {
                        toastr.error(r.responseJSON.message);
                    } else {
                        toastr.error("{{ __('Unable to Unban this User!') }}");
                    }
                });
            } else {
                banUser("{{$payment->user->username}}").done(function(r) {
                    isBanned = true;
                    banId = r.id;
                    toastr.success("{{ __('User was Banned!') }}");
                    switchMainBanButton($("#ban-button"));
                }).fail(function(r) {
                    if (r.status === 410) {
                        toastr.error(r.responseJSON.message);
                    } else {
                        toastr.error("{{ __('Unable to Ban this User!') }}");
                    }
                });
            }
        });

        $("#refund-button").click(function() {
            Swal.fire({
                title: "{{ __('Are you sure?') }}",
                text: "{!! __('Do you want to refund this payment?') !!}",
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
                    refundPayment({{$payment->id}}).done(function(r) {
                        toastr.success("{{ __('Refund was successfully completed!') }}");
                        setTimeout(function() {
                            location.reload();
                        }, 500);
                    }).fail(function(r) {
                        toastr.error(r.responseJSON.message);
                    });
                }
            });
        });

        $("#delivery-button").click(function() {
            deliveryItems({{$payment->id}}).done(function(r) {
                toastr.success("{{ __('All commands were successfully delivered!') }}");
            }).fail(function(r) {
                toastr.error(r.responseJSON.message);
            });
        });

        $(".resend-button").click(function() {
            Swal.fire({
                title: "{{ __('Are you sure?') }}",
                text: "{!! __('Do you want to resend this command?') !!}",
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
                    reSendCommand($(this).attr('data-cmd-id')).done(function(r) {
                        toastr.success("{{ __('Command was successfully resent!') }}");
                        setTimeout(function() {
                            location.reload();
                        }, 300);
                    }).fail(function(r) {
                        toastr.error(r.responseJSON.message);
                    });
                }
            });
        });

        $(".mark-paid").click(function() {
            Swal.fire({
                title: "{{ __('Are you sure to complete this payment?') }}",
                text: "{!! __('All commands attached to this payment will be executed') !!}",
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
                    Swal.fire({
                        title: "{{ __('Processing...') }}",
                        text: "{{ __('Please wait while the payment is being processed.') }}",
                        icon: 'info',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                            markPaid({{$payment->id}})
                                .done(function(response) {
                                    Swal.fire({
                                        title: "{{ __('Success!') }}",
                                        text: "{{ __('Payment successfully completed!') }}",
                                        icon: 'success',
                                        confirmButtonText: "{{ __('OK') }}"
                                    }).then(() => {
                                        location.reload();
                                    });
                                })
                                .fail(function(error) {
                                    Swal.fire({
                                        title: "{{ __('Error!') }}",
                                        text: error.responseJSON?.message || "{{ __('An error occurred. Please try again.') }}",
                                        icon: 'error',
                                        confirmButtonText: "{{ __('OK') }}"
                                    });
                                });
                        }
                    });
                }
            });
        });

        $("#resend-all-button").click(function() {
            Swal.fire({
                title: "{{ __('Are you sure?') }}",
                text: "{!! __('Do you want to resend all commands?') !!}",
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
                    resendAllCommands({{$payment->id}}).done(function(r) {
                        toastr.success("{{ __('All commands were successfully resent!') }}");
                        setTimeout(function() {
                            location.reload();
                        }, 500);
                    }).fail(function(r) {
                        toastr.error(r.responseJSON.message);
                    });
                }
            });
        });

        $(".delete-cmd-button").click(function() {
            Swal.fire({
                title: "{{ __('Are you sure?') }}",
                text: "{{ __('Do you want to delete this command?') }}",
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
                    deleteCommand($(this).attr('data-cmd-id')).done(function(r) {
                        toastr.success("{{ __('Command was successfully deleted!') }}");
                        setTimeout(function() {
                            location.reload();
                        }, 300);
                    }).fail(function(r) {
                        toastr.error(r.responseJSON.message);
                    });
                }
            });
        });

        $("#delete-button").click(function() {
            Swal.fire({
                title: "{{ __('Are you sure?') }}",
                text: "{{ __('Do you want to delete this payment?') }}",
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
                    deletePayment({{$payment->id}}).done(function(r) {
                        toastr.success("The payment was deleted!");
                        setTimeout(function() {
                            window.location.href = "{{ route('payments.index') }}";
                        }, 700);
                    }).fail(function(r) {
                        if (r.status === 410) {
                            toastr.error(r.responseJSON.message);
                        } else {
                            toastr.error("{{ __('Unable to Delete the Payment!') }}");
                        }
                    });
                }
            });
        });

        $("#note-button").click(function() {
            const note = $("#paymentNote").val();
            addPaymentNote({{$payment->id}}, note).done(function(r) {
                toastr.success("{{ __('Note was Added!') }}");
                $("#note").text(note);
            }).fail(function(r) {
                toastr.error("{{ __('Unable to Add the Note!') }}");
            });
        });
    </script>
@endsection

@section('content')
    <h4 class="fw-bold py-3 mb-1">
        <span class="text-body fw-light">{{ __('Payment Details') }} #{{$payment->id}}</span>
    </h4>
    @if ($payment->status == Payment::CHARGEBACK && $payment->chargeback != null)
        <div class="row">
            <div class="col-12">
                <div class="alert alert-danger d-flex" role="alert">
                    <span class="badge badge-center rounded-pill bg-danger border-label-danger p-3 me-2"><i
                            class="bx bx-error fs-6"></i></span>
                    <div class="d-flex flex-column ps-1">
                        <h6 class="alert-heading d-flex align-items-center fw-bold mb-1">{{ __('WARNING !') }}</h6>
                        <span>{{ __('This payment was chargeback by the user.') }} <a style="color: #c57474"
                                                                                      href="{{ route('chargeback.show', $payment->chargeback->id) }}">{{ __('Click here') }} </a> {{ __('to view the case.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($payment->gateway === 'PayNow' && $payment->tax_inclusive === 1 && $payment->cart->tax > 0)
        <div class="row">
            <div class="col-12">
                <div class="alert alert-warning d-flex" role="alert">
                    <span class="badge badge-center rounded-pill bg-warning border-label-warning p-3 me-2"><i
                            class="bx bx-info-circle fs-6"></i></span>
                    <div class="d-flex flex-column ps-1">
                        <h6 class="alert-heading d-flex align-items-center fw-bold mb-1">{{ __('Base Price Includes Tax') }}</h6>
                        <span>Your PayNow Checkout Integration is set to <strong>TAX INCLUSIVE</strong> mode. This means the base price of your products already includes tax. When customers make a payment, they pay the total amount (base price plus any additional fees and taxes). The final amount you'll receive in your PayNow wallet will be this total minus deductions for taxes (including sales taxes), gateway fees (depends on payment method), and platform fee (3.99%).</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-5 mb-2 d-flex justify-content-start">
                                <a href="{{ route('payments.index') }}" class="btn btn-primary"
                                   style="margin-right: 5px;">
                                    <span class="tf-icons bx bx-arrow-back me-1"></span>{{ __('Back') }}
                                </a>
                                @if ($payment->status != Payment::PROCESSED)
                                    <button type="button" class="btn btn-warning" id="delivery-button">
                                        <span class="tf-icons bx bxs-send me-1"></span>{{ __('Re-Delivery') }}
                                    </button>
                                @else
                                    <button type="button" class="btn btn-success mark-paid" id="mark-paid">
                                        <span class="tf-icons bx bx-check-circle me-1"></span>{{ __('Mark as Paid') }}
                                    </button>
                                @endif
                            </div>
                            <div class="col-md-7 mb-2 d-flex justify-content-end">
                                @if (in_array($payment->status, [Payment::COMPLETED, Payment::PAID], true) && in_array($payment->gateway, ['Stripe', 'PayNow']))
                                    <button type="button" class="btn btn-info" style="margin-right: 5px;"
                                            id="refund-button">
                                        <span class="tf-icons bx bx-money-withdraw me-1"></span>{{ __('Refund') }}
                                    </button>
                                @endif
                                <button type="button" class="btn btn-danger" id="delete-button">
                                    <span class="tf-icons bx bxs-trash-alt me-1"></span>{{ __('Delete') }}
                                </button>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="table-responsive table-striped">
                                <table class="table table-striped table-bordered">
                                    <tbody>
                                    <tr>
                                        <td style="font-weight: 500;">
                                            {{ __('Transaction ID:') }}
                                        </td>
                                        <td>
                                            {{empty($payment->transaction) ? 'Unknown' : $payment->transaction}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 500;">
                                            {{ __('Internal ID:') }}
                                        </td>
                                        <td>
                                            {{empty($payment->internal_id) ? 'N/A' : $payment->internal_id}}
                                        </td>
                                    </tr>
                                    @if ($payment->internal_subscription_id)
                                        <tr>
                                            <td style="font-weight: 500;">
                                                {{ __('Subscription ID:') }}
                                            </td>
                                            <td>
                                                <a href="{{ route('subscriptions.show', $payment->internal_subscription_id) }}">
                                                    {{ $payment->internal_subscription_id }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td style="font-weight: 500;">
                                            {{ __('Status:') }}
                                        </td>
                                        <td>
                                            @if ($payment->status === Payment::COMPLETED || $payment->status === Payment::PAID)
                                                <span class="badge bg-success">{{ __('Completed') }}</span>
                                            @elseif ($payment->status === Payment::ERROR)
                                                <span class="badge bg-danger">{{ __('Error') }}</span>
                                                <span
                                                    style="color: #FF5722; font-weight: bold;">{{ $payment->error }}</span>
                                            @elseif ($payment->status === Payment::CHARGEBACK)
                                                <span class="badge bg-danger">{{ __('Chargeback') }}</span>
                                            @elseif($payment->status === Payment::REFUNDED)
                                                <span class="badge bg-secondary">{{ __('Refunded') }}</span>
                                            @else
                                                <span class="badge bg-warning">{{ __('Pending') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {{ __('Payment Gateway:') }}
                                        </td>
                                        <td>
                                            {{ empty($payment->gateway) ? 'Undefined Payment Gateway' : $payment->gateway }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 500;">
                                            {{ __('Amount:') }}
                                        </td>
                                        <td>
                                            {{ $payment->price }} {{ $payment->currency }}
                                            @if(count($system_price) > 0 || $isVirtualCurrency)
                                                <br> ({{ $system_price[0] }} {{ $system_price[1] }})
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 500;">
                                            {{ __('Taxes:') }}
                                        </td>
                                        <td>
                                            {{ $payment->cart->tax }} {{ $payment->currency }}
                                            @if ($payment->gateway === 'PayNow' && $payment->cart->tax > 0)
                                                <span style="color: green; font-weight: 500;">(Already paid through PayNow)</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 500;">
                                            {{ __('Date:') }}
                                        </td>
                                        <td>
                                            {{ $payment->created_at }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 500;">
                                            {{ __('Username:') }}
                                        </td>
                                        <td>
                                            {{ $payment->user->username }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 500;">
                                            UUID:
                                        </td>
                                        <td>
                                            {{ $uuid }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 500;">
                                            {{ __('IP Address:') }}
                                        </td>
                                        <td>
                                            {{ $payment->ip }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 500;">
                                            {{ __('Email:') }}
                                        </td>
                                        <td>
                                            {{ !empty($details) && !empty($details['email']) ? $details['email'] : 'Unknown' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 500;">
                                            {{ __('Full Name:') }}
                                        </td>
                                        <td>
                                            {{ !empty($details) && !empty($details['fullname']) ? $details['fullname'] : 'Unknown' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: 500;">
                                            {{ __('Billing Address:') }}
                                        </td>
                                        <td>
                                            @if(!empty($details))
                                                {{ !empty($details['country']) ? $details['country'].', ' : '' }}{{ !empty($details['region']) ? $details['region'].', ' : '' }}{{ !empty($details['city']) ? $details['city'].', ' : '' }}{{ !empty($details['address1']) ? $details['address1'].', ' : '' }}{{ !empty($details['address2']) ? '('.$details['address2'].'), ' : '' }}{{ !empty($details['zipcode']) ? $details['zipcode'] : '' }}
                                            @endif
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Commands block -->
            <div class="col-12 mb-4">
                <div class="col-12 mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h4 class="text-body fw-light mb-0">
                                {{ __('Commands') }}
                                <i class="bx bxs-terminal" style="margin-bottom: 2px;"></i>
                            </h4>
                        </div>
                        <div class="col-md-6 pt-4 pt-md-0 d-flex justify-content-end">
                            <button type="button" class="btn btn-primary btn-sm fs-6 d-flex align-items-center gap-1"
                                    id="resend-all-button">
                                <span class="tf-icon bx bx-refresh bx-xs"></span>
                                {{ __('Resend All Commands') }}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" style="font-weight: 500;">
                                <thead class="text-primary" style="color: #ff9800;">
                                <tr>
                                    <th style="padding-top: 0; padding-right: 0;">
                                        {{ __('Commands') }}
                                    </th>
                                    <th>
                                        {{ __('Package') }}
                                    </th>
                                    <th>
                                        {{ __('Server') }}
                                    </th>
                                    <th>
                                        {{ __('Updated') }}
                                    </th>
                                    <th>
                                        {{ __('Status') }}
                                    </th>
                                    <th>
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                @php($paymentCommandsHistory = $payment->сommand_history()?->orderBy('type', 'ASC')->get())
                                @foreach(empty($paymentCommandsHistory) ? [] : $paymentCommandsHistory as $command)
                                    <tr>
                                        <td>
                                            {{ $command->cmd }}
                                        </td>
                                        <td>
                                            @if($command->type == CommandHistory::TYPE_ITEM)
                                                @php($itemCMD = Item::where('id', $command->item_id)->select('name')->first())
                                                {{ empty($itemCMD) ? __('Package not found') : $itemCMD->name }}
                                            @elseif ($command->type == CommandHistory::TYPE_GLOBAL)
                                                {{ __('Global command') }}
                                            @elseif ($command->type == CommandHistory::TYPE_REF)
                                                {{ __('Referral command') }}
                                            @elseif ($command->type == CommandHistory::TYPE_VIRTUAL_CURRENCY)
                                                {{ __('Virtual currency') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            {{ Server::where('id', $command->server_id)->select('name')->first()->name }}
                                        </td>
                                        <td>
                                            {{ $command->updated_at }}
                                        </td>
                                        <td>
                                            @if ($command->status == CommandHistory::STATUS_EXECUTED)
                                                <span class="badge bg-label-success">{{ __('Executed') }}</span>
                                            @elseif ($command->status == CommandHistory::STATUS_QUEUE)
                                                <span class="badge bg-label-secondary">{{ __('In queue') }}</span>
                                            @elseif ($command->status == CommandHistory::STATUS_PENDING)
                                                <span class="badge bg-label-primary">{{ __('Pending') }}</span>
                                            @elseif($command->status == CommandHistory::STATUS_DELETED)
                                                <span class="badge bg-label-danger">{{ __('Deleted') }}</span>
                                            @else
                                                <span class="badge bg-label-dark">{{ __('UNKNOWN') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-inline-flex align-items-center gap-2">
                                                <button type="button"
                                                        class="btn btn-primary btn-xs fs-6 d-flex align-items-center justify-content-center gap-1 resend-button"
                                                        data-cmd-id="{{ $command->id }}"
                                                        style="height: 25px;">
                                                    {{ __('Resend') }}
                                                </button>
                                                <button type="button"
                                                        class="btn btn-danger btn-xs fs-6 d-flex align-items-center justify-content-center gap-1 delete-cmd-button"
                                                        data-cmd-id="{{ $command->id }}"
                                                        style="height: 25px;">
                                                    <i class="bx bx-x" style="font-size: 20px;"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of Commands block -->
            <!-- Referrals block -->
            <div class="col-12 mb-4">
                <div class="col-12 mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <h4 class="text-body fw-light mb-0">
                                {{ __('Referrals') }}
                                <i class="bx bxs-terminal" style="margin-bottom: 2px;"></i>
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <!-- Fix Ref Code -->
                        @if (!empty($payment->ref) && $ref_code = $payment->ref_code)
                            <div class="table-responsive">
                                <table class="table" style="font-weight: 500;">
                                    <thead class="text-primary" style="color: #ff9800;">
                                    <tr>
                                        <th style="padding-top: 0; padding-right: 0;">
                                            {{ __('Referral Username') }}
                                        </th>
                                        <th>
                                            {{ __('Share Percent') }} (%)
                                        </th>
                                        <th>
                                            {{ __('Share Amount') }}
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <img src="https://mc-heads.net/avatar/{{ $ref_code->referer }}/30"
                                                 onerror="this.src='{{ asset('res/img/question-icon.png') }}';"
                                                 alt="{{ $ref_code->referer }}" style="width: 30px; margin-right: 3px">
                                            {{ $ref_code->referer }}
                                        </td>
                                        <td>
                                            {{ $ref_code->percent }}%
                                        </td>
                                        <td>
                                            {{ ($ref_code->percent / 100) * $payment->price }} {{ $payment->currency }}
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="card-text text-center"
                               style="font-size: 16px;">{{ __('Referrers were not found for this transaction.') }}</p>
                        @endif
                    </div>
                </div>
            </div>
            <!-- End of Referrals block -->
            <!-- Gift Cards block -->
            <div class="col-12 mb-4">
                <div class="col-12 mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <h4 class="text-body fw-light mb-0">
                                {{ __('Purchased Gift Cards') }}
                                <i class="bx bx-gift" style="margin-bottom: 2px;"></i>
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        @if(!$giftcards->isEmpty())
                            <div class="table-responsive">
                                <table class="table" style="font-weight: 500;">
                                    <thead class="text-primary" style="color: #ff9800;">
                                    <tr>
                                        <th style="padding-top: 0; padding-right: 0;">
                                            {{ __('Identifier') }}
                                        </th>
                                        <th>
                                            {{ __('Status') }}
                                        </th>
                                        <th>
                                            {{ __('Starting Balance') }}
                                        </th>
                                        <th>
                                            {{ __('Current Balance') }}
                                        </th>
                                        <th>
                                            {{ __('Creation Date') }}
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        @foreach ($giftcards as $giftcard)
                                            <td>
                                                {{ $giftcard->name }}
                                            </td>
                                            <td>
                                                @if (\Carbon\Carbon::parse($giftcard->expire_at)->isBefore(\Carbon\Carbon::now()))
                                                    <span class="badge w-100 bg-danger">EXPIRED</span>
                                                @else
                                                    @if ($giftcard->end_balance > 0)
                                                        <span class="badge w-100 bg-success">ACTIVE</span>
                                                    @else
                                                        <span class="badge w-100 bg-warning">ELIMINATED</span>
                                                    @endif
                                                @endif
                                            </td>
                                            <td>
                                                {{ $giftcard->start_balance }} {{ $system_currency->name ?? 'USD' }}
                                            </td>
                                            <td>
                                                {{ $giftcard->end_balance }} {{ $system_currency->name ?? 'USD' }}
                                            </td>
                                            <td>
                                                {{ $giftcard->created_at }}
                                            </td>
                                        @endforeach
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="card-text text-center"
                               style="font-size: 16px;">{{ __('Gift cards were not purchased for this transaction.') }}</p>
                        @endif
                    </div>
                </div>
            </div>
            <!-- End of Gift Cards block -->
            <!-- Discord Roles block -->
            <div class="col-12 mb-4">
                <div class="col-12 mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <h4 class="text-body fw-light mb-0">
                                {{ __('Discord Roles') }}
                                <i class="bx bxl-discord-alt" style="margin-bottom: 2px;"></i>
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        @if(!$discordRoles->isEmpty())
                            <div class="table-responsive">
                                <table class="table" style="font-weight: 500;">
                                    <thead class="text-primary" style="color: #ff9800;">
                                    <tr>
                                        <th>
                                            {{ __('Discord ID') }}
                                        </th>
                                        <th>
                                            {{ __('Role') }}
                                        </th>
                                        <th>
                                            {{ __('Action') }}
                                        </th>
                                        <th>
                                            {{ __('Status') }}
                                        </th>
                                        <th>
                                            {{ __('Error') }}
                                        </th>
                                        <th>
                                            {{ __('Processed At') }}
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        @foreach ($discordRoles as $role)
                                            <td>
                                                {{ $role->discord_id }}
                                            </td>
                                            <td>
                                                {{ $role->role->name }}
                                            </td>
                                            <td>
                                                @if ($role->action == 0)
                                                    <span class="badge bg-label-success">Add</span>
                                                @else
                                                    <span class="badge bg-label-danger">Remove</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($role->processed)
                                                    <span class="badge bg-label-success">Processed</span>
                                                @else
                                                    <span class="badge bg-label-warning">Pending</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($role->error == null)
                                                    N/A
                                                @else
                                                    {{ $role->error }}
                                                @endif
                                            </td>
                                            <td>
                                                @if ($role->processed_at == null)
                                                    N/A
                                                @else
                                                    {{ $role->processed_at }}
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="card-text text-center"
                               style="font-size: 16px;">{{ __('Discord roles were not attached for this transaction.') }}</p>
                        @endif
                    </div>
                </div>
            </div>
            <!-- End of Discord Roles block -->
        </div>
        <div class="col-md-4 mb-4">
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header mb-0 pb-2">
                        <h4 class="card-title" style="text-align: center;">{{ __('User information') }}</h4>
                    </div>
                    <hr>
                    <div class="card-body text-center">
                        <img src="https://mc-heads.net/body/{{ $payment->user->username }}"
                             alt="{{ $payment->user->username }}"
                             onerror="this.src='{{ asset('res/img/question-icon.png') }}';"
                             style="width: 165px; border-radius: 4px;transform: scale(-1, 1); margin-bottom: 5px;">
                        <h5 style="font-size: 24px; font-weight: 500;">{{ $payment->user->username }}</h5>
                        <h6 style="font-size: 14px;">UUID: {{ $uuid }}</h6>
                        <div class="row">
                            <div class="col-6 col-md-6">
                                @if($ban == null)
                                    <button type="button" class="btn btn-lg btn-danger" id="ban-button">
                                        <span class="tf-icons bx bx-x me-1"></span>{{ __('Ban User') }}
                                    </button>
                                @else
                                    <button type="button" class="btn btn-lg btn-success" id="ban-button">
                                        <span class="tf-icons bx bx-check-square me-1"></span>{{ __('Unban User') }}
                                    </button>
                                @endif
                            </div>
                            <div class="col-6 col-md-6">
                                <a href="{{ route('lookup.search', $payment->user->username) }}" target="_blank"
                                   class="btn btn-lg btn-warning"><span
                                        class="tf-icons bx bx-search me-1"></span>{{ __('Lookup') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 mb-4">
                <div class="col-12 mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <h4 class="text-body text-center fw-light mb-0 pt-2">
                                {{ __('Packages') }}
                                <i class="bx bxs-package" style="margin-bottom: 2px;"></i>
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body pt-3 pb-4 p-0">
                        <div class="table-responsive">
                            <table class="table table-striped" style="font-weight: 500;">
                                <thead class="text-primary">
                                <tr>
                                    <th>

                                    </th>
                                    <th>
                                        {{ __('Package') }}
                                    </th>
                                    <th>
                                        QTY
                                    </th>
                                    <th>
                                        {{ __('Price') }}
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($items as $item)
                                    <tr>
                                        <td class="pl-2 pb-0 pt-0">
                                            @if($item->item->image != null)
                                                <img src="/img/items/{{ $item->item->image }}"
                                                     width="40px"
                                                     height="40px"
                                                     alt="Item Image"
                                                     onerror="this.src='{{ asset('res/img/question-icon.png') }}';">
                                            @endif
                                        </td>
                                        <td>
                                            {{ $item->item->name }}
                                        </td>
                                        <td>
                                            {{ $item->count }}
                                        </td>
                                        <td>
                                            {{ $item->price }} {{ $system_currency->name ?? 'USD' }}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 mb-4">
                <div class="col-12 mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <h4 class="text-body text-center fw-light mb-0">
                                {{ __('Applied Coupons') }}
                                <i class="bx bxs-offer" style="margin-bottom: 2px;"></i>
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body pt-3 pb-3 p-0">
                        @if(!empty($payment->cart->coupon_id) && $coupon = $payment->cart->coupon)
                            <div class="table-responsive">
                                <table class="table table-striped" style="font-weight: 500;">
                                    <thead class="text-primary">
                                    <tr>
                                        <th>
                                            {{ __('Coupon Name') }}
                                        </th>
                                        <th>
                                            {{ __('Discount') }}
                                        </th>
                                        <th>
                                            {{ __('Final Price') }}
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>
                                            {{ $coupon->name }}
                                        </td>
                                        <td>
                                            {{ $coupon->discount }} {{ $coupon->type == 0 ? '%' : $settings->currency }}
                                        </td>
                                        <td>
                                            {{ $payment->cart->price }} {{ $payment->currency }}
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="card-text text-center"
                               style="font-size: 16px;">{{ __('No coupons were used for this transaction.') }}</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-12 mb-4">
                <div class="col-12 mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <h4 class="text-body text-center fw-light mb-0">
                                {{ __('Used Giftcards') }}
                                <i class="bx bx-gift" style="margin-bottom: 2px;"></i>
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body pt-3 pb-3 p-0">
                        @if(!empty($payment->cart->gift_id) && $gift = $payment->cart->gift)
                            <div class="table-responsive">
                                <table class="table table-striped" style="font-weight: 500;">
                                    <thead class="text-primary">
                                    <tr>
                                        <th>
                                            {{ __('Giftcard Code') }}
                                        </th>
                                        <th>
                                            {{ __('Price') }}
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>
                                            {{ $gift->name }}
                                        </td>
                                        <td>
                                            {{ $payment->cart->gift_sum }} {{ $settings->currency }}
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="card-text text-center"
                               style="font-size: 16px;">{{ __('No gift cards were used for this transaction.') }}</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-12 mb-4">
                <div class="col-12 mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h4 class="text-body fw-light mb-0">
                                {{ __('Notes') }}
                                <i class="bx bxs-edit" style="margin-bottom: 2px;"></i>
                            </h4>
                        </div>
                        <div class="col-md-6 pt-4 pt-md-0 d-flex justify-content-end">
                            <button type="button" class="btn btn-primary btn-sm fs-6 d-flex align-items-center gap-1"
                                    data-bs-toggle="modal" data-bs-target="#addNote">
                                <span class="tf-icon bx bx-plus-circle bx-xs"></span>
                                {{ __('Create Note') }}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <p class="card-text" style="font-size: 16px;" id="note">{{ $payment->note }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="addNote" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
                <div class="modal-content p-3 p-md-5">
                    <div class="modal-body">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <div class="text-center mb-4">
                            <h3>{{ __('Add a Note') }}</h3>
                            <p>{{ __('Add a note for this transaction.') }}</p>
                        </div>
                        <div class="col-12">
                            <div class="input-group input-group-merge">
                                    <textarea class="form-control" id="paymentNote"
                                              placeholder="{{ __('Enter the note content for this transaction') }}"
                                              rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-12 text-center">
                            <button class="btn btn-primary me-sm-3 me-1 mt-3"
                                    id="note-button">{{ __('Submit') }}</button>
                            <button class="btn btn-label-secondary btn-reset mt-3" type="button"
                                    data-bs-dismiss="modal" aria-label="Close">{{ __('Cancel') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
