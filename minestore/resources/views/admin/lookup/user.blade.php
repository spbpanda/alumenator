@extends('admin.layout')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
@endsection

@section('vendor-script')
@endsection

@section('page-script')
    <script src="{{asset('js/modules/bans.js')}}"></script>
    <script src="{{asset('js/modules/whitelist.js')}}"></script>
    <script>
        let isBanned = {{ $ban != null ? 'true' : 'false' }};
        let banId = {{ $ban->id ?? 'undefined' }};

        $(".ban-button").click(async function() {
            if (isBanned && banId !== undefined) {
                unbanUser(banId).done(function(r) {
                    isBanned = false;
                    banId = undefined;
                    toastr.success("User was unbanned!");
                    switchMainBanButton($("#main-ban-button"));
                    switchSecondaryBanButton($("#secondary-ban-button"));
                }).fail(function(r) {
                    if (r.status === 410) {
                        toastr.error(r.responseJSON.message);
                    }
                    else{
                        toastr.error("{{ __('Unable to unban user!') }}");
                    }
                });
            } else {
                banUser("{{$username}}").done(function(r) {
                    isBanned = true;
                    banId = r.id;
                    toastr.success("{{ __('User was banned!') }}");
                    switchMainBanButton($("#main-ban-button"));
                    switchSecondaryBanButton($("#secondary-ban-button"));
                }).fail(function(r) {
                    if (r.status === 410) {
                        toastr.error(r.responseJSON.message);
                    }
                    else{
                        toastr.error("{{ __('Unable to ban user!') }}");
                    }
                });
            }
        });
        $("#whitelist-button").click(function() {
            addUserToWhitelist("{{$username}}").done(function(r) {
                toastr.success("{{ __('User was added to whitelist!') }}");
            }).fail(function(r) {
                if (r.status === 410) {
                    toastr.error(r.responseJSON.message);
                }
                else{
                    toastr.error("{{ __('Unable to add user to whitelist!') }}");
                }
            });
        });
    </script>
@endsection

@section('content')
    <h4 class="fw-bold py-3 mb-1">
        <span class="text-body fw-light">{{ __('Lookup for user') }} {{ $username }}</span>
    </h4>

    <div class="row">
        <div class="col-sm-4 mb-4">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header mb-0 pb-2">
                        <h4 class="card-title" style="text-align: center;">{{ __('User information') }}</h4>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="card-body text-center">
                            <img src="https://mc-heads.net/avatar/{{ $username }}" alt="{{ $username }}"
                                 style="width: 145px; border-radius: 4px;transform: scale(-1, 1); margin-bottom: 8px;">
                            <h5 style="font-size: 24px; font-weight: 500;">{{ $username }}</h5>
                            <h6 style="font-size: 14px;">UUID: {{ $uuid }}</h6>
                            @if (!$ban)
                                <button class="btn btn-lg btn-danger ban-button" id="main-ban-button">
                                    <span class="tf-icons bx bx-message-square-x me-1"></span>{{ __('Ban User') }}
                                </button>
                            @else
                                <button class="btn btn-lg btn-success ban-button" id="main-ban-button">
                                    <span class="tf-icons bx bx-check-square me-1"></span>{{ __('Unban User') }}
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 mb-2">
                <div class="card">
                    <div class="row mt-2 d-flex justify-content-center text-center">
                        <p style="font-size: 27px; font-weight: 400;"><i class='bx bx-no-entry'
                                                                         style="font-size: 30px;margin-bottom: 8px;color:red;"></i> {{ $info['bans'] }}
                            {{ __('Bans') }}</p>
                    </div>
                    <div class="row d-flex justify-content-center text-center">
                        <p style="font-size: 27px; font-weight: 400;"><i class='bx bx-basket'
                                                                         style="font-size: 30px;margin-bottom: 12px;color: #4caf50;"></i> {{ $info['total'] }}
                            {{ __('Purchases') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-12 mb-2">
                <div class="card">
                    <div class="row mt-2 d-flex justify-content-center text-center">
                        <p class="mb-0" style="text-align:center; font-size: 22px; font-weight: 300;padding-top: 10px;">
                            {{ __('Chargeback rate') }}:</p>
                        <br>
                        <p class="mb-1"
                           style="font-size: 22px; font-weight: 400; margin-left: 5px; color: #ff9800">{{ round($rate, 2) }}
                            %</p>
                    </div>
                    <div class="row d-flex text-center justify-content-center">
                        <a class="mb-2" style="font-size: 16px; font-weight: 400;padding-bottom: 10px; color: #ff9800"
                           href="https://docs.minestorecms.com/features/fraud/chargeback-prevention">{{ __('(How is this calculated?)') }}</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-8 mb-4">
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="row">
                        <div class="card-body mt-2 mb-3">
                            <div class="row">
                                <div class="col-md-5 mb-2 d-flex justify-content-start">
                                    <a href="{{ route('lookup.index') }}" class="btn btn-primary"
                                       style="margin-right: 5px;">
                                        <span class="tf-icons bx bx-arrow-back me-1"></span>{{ __('Back') }}
                                    </a>
                                    <button type="button" class="btn btn-warning" id="whitelist-button">
                                        <span class="tf-icons bx bx-shield-plus me-1"></span>{{ __('Whitelist') }}
                                    </button>
                                </div>
                                <div class="col-md-7 mb-2 d-flex justify-content-end">
                                    @if ($ban == null)
                                        <button type="button" class="btn btn-danger ban-button"
                                                id="secondary-ban-button">
                                            <span class="tf-icons bx bxs-trash-alt me-1"></span>{{ __('Ban User') }}
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-success ban-button"
                                                id="secondary-ban-button">
                                            <span class="tf-icons bx bx-arrow-from-left me-1"></span>{{ __('Unban User') }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <hr>
                            <div class="table-responsive text-nowrap">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Action</th>
                                        <th>IP</th>
                                        <th>{{ __('Details') }}</th>
                                        <th>{{ __('Date') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($history as $row)
                                        <tr>
                                            <td>
                                                @if ($row['action'] == 'purchase')
                                                    <span class="badge bg-primary w-100">
                                                        <i class="bx bx-check"></i> {{ __('Purchase') }}
                                                    </span>
                                                @elseif ($row['action'] == 'chargeback')
                                                    <span class="badge bg-danger w-100">
                                                        <i class="bx bx-x"></i> {{ __('Chargeback') }}
                                                    </span>
                                                @elseif ($row['action'] == 'ban')
                                                    <span class="badge bg-danger w-100">
                                                        <i class="bx bx-x"></i> {{ __('Ban') }}
                                                    </span>
                                                @elseif ($row['action'] == 'whitelist')
                                                    <span class="badge bg-success w-100">
                                                        <i class="bx bx-check"></i> {{ __('Whitelist') }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning w-100">
                                                        <i class="bx bx-info
                                                        "></i> {{ __('Unknown') }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $row['ip'] }}
                                            </td>
                                            <td>
                                                @if ($row['action'] == 'purchase')
                                                    {{ __('Made a payment at your webstore.') }}
                                                    <a href="{{ route('payments.show', ['payment' => $row['reason']]) }}" class="text-primary">{{ __('View') }}</a>
                                                @elseif ($row['action'] == 'chargeback')
                                                    {{ __('Made a chargeback at your webstore.') }}
                                                    <a href="{{ route('payments.show', ['chargeback' => $row['reason']]) }}" class="text-primary">{{ __('View') }}</a>
                                                @elseif ($row['action'] == 'ban')
                                                    {{  $row['reason'] }}
                                                @elseif ($row['action'] == 'whitelist')
                                                    {{ __('Was added to the whitelist.') }}
                                                @endif
                                            </td>
                                            <td>
                                                {{ date_format(date_create($row['date']), 'jS F Y, H:i:s') }}
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
    </div>
@endsection
