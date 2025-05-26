@extends('admin.layout')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('res/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
@endsection

@section('vendor-script')
    <script src="{{asset('res/vendor/libs/moment/moment.js')}}"></script>
    <script src="{{asset('res/vendor/libs/datatables/jquery.dataTables.js')}}"></script>
    <script src="{{asset('res/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
    <script src="{{asset('res/vendor/libs/datatables-responsive/datatables.responsive.js')}}"></script>
    <script src="{{asset('res/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.js')}}"></script>
    <script src="{{asset('res/vendor/libs/datatables-buttons/datatables-buttons.js')}}"></script>
    <script src="{{asset('res/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.js')}}"></script>
@endsection

@section('page-script')
    <script>
        function loadPreview(elementId) {
            const filePreviewElement = document.querySelector('#preview-'+elementId);
            filePreviewElement.src = URL.createObjectURL(event.target.files[0]);
        }
        function clearImage(elementId) {
            document.getElementById('preview-'+elementId).src = "";
            document.getElementById(elementId).value = null;
        }

        var dt_alerts_table = $('.datatables-alerts');
        if (dt_alerts_table.length) {
            var dt_alerts = dt_alerts_table.DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('api.paynow_alerts.index') }}',
                searching: true,
                ordering: true,
                columns: [
                    { data: 'alert_id', searchable: true },
                    { data: 'status' },
                    { data: 'custom_title' },
                    { data: 'custom_message', searchable: true },
                    { data: 'action_link' },
                    { data: 'created_at' },
                ],
                order: [[5, 'desc']],
                columnDefs: [
                    {
                        targets: 0,
                        render: function (data, type, full, meta) {
                            return full['alert_id'];
                        },
                    },
                    {
                        targets: 1,
                        render: function (data, type, full, meta) {
                            var status = full['status'];
                            var statusMap = {
                                'info': { text: 'Information', class: 'primary' },
                                'warning': { text: 'Warning', class: 'warning' },
                                'action_required': { text: 'Action Required', class: 'danger' },
                                'success': { text: 'Success', class: 'success' },
                                'error': { text: 'Error', class: 'danger' },
                            };
                            var $output = '<span class="badge bg-' + statusMap[status].class + ' w-100">' + statusMap[status].text + '</span>';
                            return $output;
                        },
                    },
                    {
                        targets: 2,
                        render: function (data, type, full, meta) {
                            return full['custom_title'] || 'N/A';
                        },
                    },
                    {
                        targets: 3,
                        render: function (data, type, full, meta) {
                            return full['custom_message'] || 'N/A';
                        },
                    },
                    {
                        targets: 4,
                        render: function (data, type, full, meta) {
                            if (full['action_link'] && full['action_required_at']) {
                                return '<a href="' + full['action_link'] + '" class="btn btn-sm text-primary btn-icon" target="_blank"><i class="bx bx-link"></i></a>';
                            }
                            return 'N/A';
                        },
                    },
                    {
                        targets: 5,
                        render: function (data, type, full, meta) {
                            return moment(full['created_at']).format('DD.MM.YYYY HH:mm:ss');
                        },
                    },
                ],
                createdRow: function (row, data, dataIndex) {
                    $(row).attr('id', 'tableItem' + data['alert_id']);
                },
            });
        }
    </script>
@endsection

@section('content')
    <h4 class="fw-bold py-3 mb-1">
        <span class="text-body fw-light">PayNow Checkout Integration Settings</span>
    </h4>

    @if ($errorMessage)
        <div class="alert alert-danger alert-dismissible" role="alert">
            <h5 class="alert-heading d-flex align-items-center mb-1">Oops! Something went wrong.</h5>
            <p class="mb-0">
                {{ $errorMessage }}
            </p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            </button>
        </div>
    @endif

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

<div class="row d-flex align-items-stretch">
    <div class="col-xl-5 mb-xl-0">
        <div class="card h-100">
            <div class="card-header mb-0 pb-0 d-flex justify-content-center align-items-center gap-2">
                <img src="{{ asset('res/img/logos/paynow.svg') }}" alt="PayNow" class="img-fluid" style="max-height: 46px; height: auto;">
            </div>
            <hr>
            <div class="card-body d-flex flex-column">
                <ul class="timeline">
                    @foreach ($timelineEvents as $event)
                        <li class="timeline-item timeline-item-transparent">
                            <span class="timeline-point timeline-point-{{ $event['type'] }}"></span>
                            <div class="timeline-event pt-4 pb-0">
                                <div class="timeline-header border-bottom mb-3 d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0 text-{{ $event['type'] }}">{{ $event['title'] }}</h6>
                                        <small class="text-muted" style="font-size: 0.75rem;">{{ date('jS F', strtotime($event['date'])) }}</small>
                                    </div>
                                    <div>
                                        <small class="text-muted">{{ $event['timeAgo'] }}</small>
                                    </div>
                                </div>
                                <p>{!! $event['description'] !!}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    <div class="col-xl-7">
        <div class="card h-100">
            <div class="card-header mb-0 pb-2 d-flex justify-content-between align-items-center">
                <div class="invisible">PayNow Support</div>
                <h4 class="card-title mb-0">PayNow Checkout Setup</h4>
                <a href="https://discord.gg/paynow" target="_blank" class="btn btn-primary btn-sm">
                    PayNow Support
                </a>
            </div>
            <hr>
            <div class="card-body d-flex flex-column">
                <form method="POST" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Status Row -->
                            <div class="mb-3 pb-2 border-bottom">
                                <div class="d-flex align-items-center mb-2">
                                    <h5 class="mb-0 me-3">Integration Status:</h5>
                                    <span class="badge bg-label-{{ $diagnostics['integration']['badge'] }} d-flex align-items-center">
                                        <i class="bx {{ $diagnostics['integration']['icon'] }} me-1"></i> {{ $diagnostics['integration']['message'] }}
                                    </span>
                                </div>
                            </div>

                            <!-- Currency Row -->
                            <div class="mb-3 pb-2 border-bottom">
                                <div class="d-flex align-items-center mb-2">
                                    <h5 class="mb-0 me-3">Webstore Currency:</h5>
                                    @if ($diagnostics['currency']['status'])
                                        <span class="badge bg-label-{{ $diagnostics['currency']['badge'] }} d-inline-flex align-items-center">
                                            <i class="bx {{ $diagnostics['currency']['icon'] }} me-1"></i> {{ $diagnostics['currency']['message'] }}
                                        </span>

                                        @if(!empty($payNowStore) && isset($payNowStore['currency']))
                                            <a href="{{ route('settings.currencyManagement') }}">
                                                <i class="bx bx-help-circle text-muted ms-2" style="margin-bottom: 3px;"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-placement="top"
                                                   title="Click here to set your webstore currency to {{ strtoupper($payNowStore['currency']) }}">
                                                </i>
                                            </a>
                                        @else
                                            <a href="{{ route('settings.currencyManagement') }}">
                                                <i class="bx bx-help-circle text-muted ms-2" style="margin-bottom: 3px;"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-placement="top"
                                                   title="Click here to manage your webstore currency settings">
                                                </i>
                                            </a>
                                        @endif
                                    @endif
                                </div>

                                @if ($diagnostics['currency']['description'])
                                    <p class="text-danger mb-0" style="font-size: 0.875rem;">
                                        {!! $diagnostics['currency']['description'] !!}
                                    </p>
                                @endif
                            </div>

                            <!-- Syncing Row -->
                            <div class="mb-3 pb-2 border-bottom">
                                <div class="d-flex align-items-center mb-2">
                                    <h5 class="mb-0 me-3">Last Syncing:</h5>
                                    @if ($diagnostics['sync']['status'])
                                        <span class="badge bg-label-{{ $diagnostics['sync']['badge'] }} d-inline-flex align-items-center">
                                            <i class="bx {{ $diagnostics['sync']['icon'] }} me-1"></i> {{ $diagnostics['sync']['message'] }}
                                        </span>
                                    @endif
                                    @if (isset($diagnostics['sync']['lastSync']))
                                        <i class="bx bx-help-circle text-muted ms-2" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="The last time your PayNow Webstore was synced with MineStoreCMS was {{ $diagnostics['sync']['lastSync'] }}."></i>
                                    @endif
                                </div>

                                @if ($diagnostics['sync']['description'])
                                    <p class="text-danger mb-0" style="font-size: 0.875rem;">
                                        {!! $diagnostics['sync']['description'] !!}
                                    </p>
                                @endif
                            </div>

                            <!-- Tax Syncing Row -->
                            <div class="mb-3 pb-2 border-bottom">
                                <div class="d-flex align-items-center mb-2">
                                    <h5 class="mb-0 me-3">VAT Rates:</h5>
                                    @if ($diagnostics['taxSync']['status'])
                                        <span class="badge bg-label-{{ $diagnostics['taxSync']['badge'] }} d-inline-flex align-items-center">
                                            <i class="bx {{ $diagnostics['taxSync']['icon'] }} me-1"></i> {{ $diagnostics['taxSync']['message'] }}
                                        </span>
                                    @endif
                                    @if (isset($diagnostics['taxSync']['lastSync']))
                                        <i class="bx bx-help-circle text-muted ms-2" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="The last time your PayNow tax rates were synced was {{ $diagnostics['taxSync']['lastSync'] }}."></i>
                                    @endif
                                </div>

                                @if ($diagnostics['taxSync']['description'])
                                    <p class="text-{{ $diagnostics['taxSync']['badge'] }} mb-0" style="font-size: 0.875rem;">
                                        {!! $diagnostics['taxSync']['description'] !!}
                                    </p>
                                @endif
                            </div>

                            <div class="mb-3 pb-2 border-bottom">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="bg-lighter border rounded p-3 mb-3">
                                            <label class="switch switch-square">
                                                <input type="hidden" name="enabled" value="0">
                                                <input type="checkbox" class="switch-input" id="enabled" name="enabled" value="1" {{ $config['enabled'] ? "checked" : "" }} />
                                                <span class="switch-toggle-slider">
                                                    <span class="switch-on"></span>
                                                    <span class="switch-off"></span>
                                                </span>
                                                <span class="switch-label">PayNow Enabled</span>
                                                <i class="bx bx-help-circle text-muted ms-2" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="Enabling this option you will disable all other payment methods that has the same payment methods as PayNow."></i>
                                                @error('enabled')
                                                    <p class="text-danger mb-0">{{ $message }}</p>
                                                @enderror
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="bg-lighter border rounded p-3 mb-3">
                                            <label class="switch switch-square">
                                                <input type="hidden" name="tax_mode" value="0">
                                                <input type="checkbox" class="switch-input" id="tax_mode" name="tax_mode" value="1" {{ $config['tax_mode'] ? "checked" : "" }} />
                                                <span class="switch-toggle-slider">
                                                    <span class="switch-on"></span>
                                                    <span class="switch-off"></span>
                                                </span>
                                                <span class="switch-label">Base Price Includes Tax?</span>
                                                <i class="bx bx-help-circle text-muted ms-2" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="Taxes are already included in the final package price. We recommend you to keep this option disabled, so your customers will pay the tax amount separately."></i>
                                                @error('tax_mode')
                                                    <p class="text-danger mb-0">{{ $message }}</p>
                                                @enderror
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Store ID Input Row -->
                            <div class="mb-3">
                                <label for="store_id" class="form-label">Store ID* <a href="https://dashboard.paynow.gg/stores/settings" target="_blank" class="text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="Click here to view your Store ID on PayNow."><i class="bx bx-help-circle"></i></a></label>
                                <input type="text" class="form-control" id="store_id" name="store_id" required placeholder="Enter your Store ID" value="{{ $config['storefront_id'] }}">
                                @error('store_id')
                                    <p class="text-danger mb-0">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- API Key Input Row -->
                            <div class="mb-3 form-password-toggle">
                                <label for="api_key" class="form-label">API Key* <a href="https://dashboard.paynow.gg/api-keys" target="_blank" class="text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="Click here to view your API Key on PayNow."><i class="bx bx-help-circle"></i></a></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="api_key" name="api_key" value="{{ $config['api_key'] }}" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                                    <span id="api_key_btn" class="input-group-text cursor-pointer"><i class="bx bx-hide password-toggle-button"></i></span>
                                </div>
                                @error('api_key')
                                    <p class="text-danger mb-0">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-4">
                            <div class="h-100">
                                <div class="card-body d-flex flex-column align-items-center justify-content-center text-center p-4">
                                    <img
                                        src="{{ $payNowStore['logo_url'] ?? '' }}"
                                        alt="Logo"
                                        id="preview-logo"
                                        class="d-block rounded mb-4"
                                        height="100"
                                        width="100"
                                        onerror="this.src='{{ asset('/res/img/question-icon.png') }}';"
                                    />
                                    <div class="d-grid gap-2 w-100 mb-3">
                                        <label for="logo" class="btn btn-primary" tabindex="0">
                                            <span>{{ __('Upload Logo') }}</span>
                                            <input
                                                type="file"
                                                id="logo"
                                                name="logo"
                                                onchange="loadPreview('logo')"
                                                class="account-file-input"
                                                hidden
                                                accept="image/png, image/jpeg, image/gif"
                                            />
                                        </label>
                                        <button type="button" onclick="clearImage('logo')" class="btn btn-secondary">
                                            {{ __('Reset') }}
                                        </button>
                                    </div>
                                    <p class="text-muted mb-0">{{ __('Allowed PNG, JPEG, GIF') }}</p>
                                    @error('logo')
                                        <p class="text-danger mb-0">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-6 mb-4">
                            <div class="h-100">
                                <div class="card-body d-flex flex-column align-items-center justify-content-center text-center p-4">
                                    <img
                                        src="{{ $payNowStore['logo_square_url'] ?? '' }}"
                                        alt="Logo Square"
                                        id="preview-logo-square"
                                        class="d-block rounded mb-4"
                                        height="100"
                                        width="100"
                                        onerror="this.src='{{ asset('/res/img/question-icon.png') }}';"
                                    />
                                    <div class="d-grid gap-2 w-100 mb-3">
                                        <label for="logo_square" class="btn btn-primary" tabindex="0">
                                            <span>{{ __('Upload Favicon') }}</span>
                                            <input
                                                type="file"
                                                id="logo_square"
                                                name="logo_square"
                                                onchange="loadPreview('logo_square')"
                                                class="account-file-input"
                                                hidden
                                                accept="image/png, image/jpeg"
                                            />
                                        </label>
                                        <button type="button" onclick="clearImage('logo_square')" class="btn btn-secondary">
                                            {{ __('Reset') }}
                                        </button>
                                    </div>
                                    <p class="text-muted mb-0">{{ __('Allowed PNG, JPEG (256x256px)') }}</p>
                                    @error('logo_square')
                                        <p class="text-danger mb-0">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-auto">
                        <div class="d-grid gap-2 col-lg-12 mx-auto">
                            <button class="btn btn-primary btn-lg" type="submit"><span class="tf-icon bx bx-save bx-xs"></span> {{ __('Apply Changes') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="col-12 mb-3 mt-4">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h4 class="text-body fw-light mb-0">
                {{ __('Received Alerts') }}
            </h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-12 mb-4">
        <div class="card">
            <div class="card-datatable table-responsive">
                <table class="datatables-alerts table table-bordered">
                    <thead>
                    <tr>
                        <th>{{ __('ID') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Title') }}</th>
                        <th>{{ __('Description') }}</th>
                        <th>{{ __('Action') }}</th>
                        <th>{{ __('Received At') }}</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
