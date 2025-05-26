@extends('admin.layout')

@section('vendor-style')
    <style>
        button.btn-table:not(:disabled),button.btn-table:disabled,
        button.btn-table[type=button]:not(:disabled),
        button.btn-table[type=reset]:not(:disabled),
        button.btn-table[type=submit]:not(:disabled) {
            cursor: pointer;
            background: 0;
            border: 0;
        }
    </style>
    <link rel="stylesheet" href="{{asset('/res/flag-icon.min.css?v7')}}">
    <link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('res/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('res/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css')}}">
    <link rel="stylesheet" href="{{asset('res/vendor/libs/sweetalert2/sweetalert2.css')}}" />
@endsection

@section('vendor-script')
    <script src="{{asset('res/vendor/libs/datatables/jquery.dataTables.js')}}"></script>
    <script src="{{asset('res/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
    <script src="{{asset('res/vendor/libs/datatables-responsive/datatables.responsive.js')}}"></script>
    <script src="{{asset('res/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.js')}}"></script>
    <script src="{{asset('res/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.js')}}"></script>
    <script src="{{asset('res/vendor/libs/datatables-buttons/datatables-buttons.js')}}"></script>
    <script src="{{asset('res/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.js')}}"></script>
    <script src="{{asset('res/vendor/libs/moment/moment.js')}}"></script>
    <script src="{{asset('res/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
@endsection

@section('page-script')
    <script type="text/javascript">
        var dt_basic_table = $('.datatables-basic');
        if (dt_basic_table.length) {
            var dt_basic = dt_basic_table.DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('api.customers.index') }}',
                searching: true,
                ordering: true,
                columns: [
                    { data: 'id' },
                    { data: 'username' },
                    { data: 'country_code' },
                    { data: 'uuid' },
                    { data: 'human_date' },
                    { data: 'action' }
                ],
                order: [[0, 'desc']],
                columnDefs: [
                    {
                        targets: 0,
                        orderable: false,
                        render: function (data, type, full, meta) {
                            return full['id'];
                        },
                    },
                    {
                        targets: 1,
                        render: function (data, type, full, meta) {
                            var $username = full['username'];
                            var $output = '<img src="https://mc-heads.net/avatar/' + $username + '/25" alt="' + $username + '" class="rounded" onerror="this.src=\'{{ asset('res/img/question-icon.png') }}\';">';
                            var $row_output =
                                '<div class="d-flex justify-content-start align-items-center user-name">' +
                                '<div class="avatar-wrapper">' +
                                '<div class="avatar me-2">' +
                                $output +
                                '</div>' +
                                '</div>' +
                                '<div class="d-flex flex-column">' +
                                '<span class="emp_name text-truncate">' +
                                $username +
                                '</span>' +
                                '</div>' +
                                '</div>';
                            return $row_output;
                        },
                    },
                    {
                        targets: 2,
                        render: function (data, type, full, meta) {
                            var countryCode = full['country_code'];
                            var countryName = full['country'];

                            if (countryCode && countryName) {
                                return '<span class="flag-icon flag-icon-' + countryCode.toLowerCase() + '"></span> ' + countryName;
                            } else {
                                return 'N/A';
                            }
                        }
                    },
                    {
                        targets: 3,
                        render: function (data, type, full, meta) {
                            var uuid = full['uuid'];

                            if (uuid !== 'undefined' && uuid !== null && uuid !== '') {
                                return '<span class="text-muted">' + uuid + '</span>';
                            } else {
                                return '<span class="text-muted">N/A</span>';
                            }
                        },
                    },
                    {
                        targets: 4,
                        render: function (data, type, full, meta) {
                            return '<span class="text-muted">' + full['human_date'] + '</span>';
                        },
                    },
                    {
                        targets: 5,
                        render: function (data, type, full, meta) {
                            const $row_output =
                                '<div class="">' +
                                '<a href="{{ route('customers.show', ['id' => ':id']) }}'.replace(':id', full['id']) + '" class="btn btn-sm btn-icon"><i class="bx bx-show text-primary"></i></a>' +
                                '</div>';
                            return $row_output;
                        },
                    },
                ],
                createdRow: function (row, data, dataIndex) {
                    $(row).attr('id', 'tableItem' + data['id']);
                }
            });
        }
    </script>
    <script>
        // Should be moved in separate file
        toastr.options = {
            "closeButton": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
    </script>
@endsection

@section('content')
    <div class="flex-grow-1">
        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-lg-4 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="card-info">
                                <p class="card-text">Total Customers</p>
                                <div class="d-flex align-items-end mb-2">
                                    <h4 class="card-title mb-0 me-2">{{ $customers['total'] ?? 0 }}</h4>
                                </div>
                                <small>+{{ $customers['total_this_month'] ?? 0 }} This Month</small>
                            </div>
                            <div class="card-icon">
                                <span class="badge bg-label-primary rounded p-2">
                                    <i class="bx bxs-group bx-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="card-info">
                                <p class="card-text">Countries</p>
                                <div class="d-flex align-items-end mb-2">
                                    <h4 class="card-title mb-0 me-2">{{ $country['total'] ?? 0 }}</h4>
                                </div>
                                <small>Most Popular: {{ $country['top_country'] ?? 'N/A' }}</small>
                            </div>
                            <div class="card-icon">
                                <span class="badge bg-label-primary rounded p-2">
                                    <i class="bx bx-globe bx-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="card-info">
                                <p class="card-text">Average Spend</p>
                                <div class="d-flex align-items-end mb-2">
                                    <h4 class="card-title mb-0 me-2">{{ $averageSpent['total'] ?? '0' }} {{ $averageSpent['currency'] ?? '$' }}</h4>
                                </div>
                                <small>Total {{ $averageSpent['total_payments'] ?? 0 }} Payments</small>
                            </div>
                            <div class="card-icon">
                                <span class="badge bg-label-primary rounded p-2">
                                    <i class="bx bx-dollar-circle bx-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-datatable table-responsive">
                        <table class="datatables-basic table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('Username') }}</th>
                                <th>{{ __('Country') }}</th>
                                <th>{{ __('UUID') }}</th>
                                <th>{{ __('First Seen') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
