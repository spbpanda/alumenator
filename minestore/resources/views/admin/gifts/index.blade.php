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
    <script src="{{asset('js/modules/giftcards.js')}}"></script>
    <script type="text/javascript">
        var dt_basic_table = $('.datatables-basic');
        if (dt_basic_table.length) {
            var dt_basic = dt_basic_table.DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('api.giftcards.index') }}',
                searching: true,
                ordering: true,
                columns: [
                    { data: null },
                    { data: 'name' },
                    { data: 'start_balance' },
                    { data: 'end_balance', },
                    { data: 'status' },
                    { data: 'created_at' },
                    { data: 'expire_at' },
                    { data: 'action' }
                ],
                order: [[1, 'desc']],
                columnDefs: [
                    {
                        targets: 0,
                        orderable: false,
                        checkboxes: true,
                        render: function() {
                            return '<input type="checkbox" class="dt-checkboxes form-check-input">';
                        },
                        checkboxes: {
                            selectAllRender: '<input type="checkbox" class="form-check-input">'
                        },
                    },
                    {
                        targets: 1,
                        render: function (data, type, full, meta) {
                            return full['name'];
                        },
                    },
                    {
                        targets: 2,
                        render: function (data, type, full, currency, meta) {
                            const start_balance = full['start_balance'];
                            var currency = dt_basic.ajax.json()['currency']['currency'];

                            const $output = start_balance + ' ' + currency;

                            return $output;
                        },
                    },
                    {
                        targets: 3,
                        render: function (data, type, full, currency, meta) {
                            var current_balance = full['end_balance'];
                            var currency = dt_basic.ajax.json()['currency']['currency'];

                            var $output = current_balance + ' ' + currency;

                            return $output;
                        },
                    },
                    {
                        targets: 4,
                        render: function (data, type, full, meta) {
                            const giftcard_currentBalance = full['end_balance'];
                            const expire_at = full['expire_at'];

                            const currentDate = new Date();
                            const expired = (new Date(expire_at) < currentDate);

                            let badgeClass, badgeText;
                            if (expired) {
                                badgeClass = 'danger';
                                badgeText = 'EXPIRED';
                            } else {
                                badgeClass = (giftcard_currentBalance > 0) ? 'success' : 'warning';
                                badgeText = (giftcard_currentBalance > 0) ? 'ACTIVE' : 'ELIMINATED';
                            }

                            const $output = '<span class="badge w-100 bg-' + badgeClass + '">' + badgeText + '</span>';
                            return $output;
                        },
                    },
                    {
                        targets: 5,
                        render: function (data, type, full, meta) {
                            const dateObject = new Date(full['created_at']);
                            const readableDate = dateObject.toLocaleString();

                            return readableDate;
                        },
                    },
                    {
                        targets: 6,
                        render: function (data, type, full, meta) {
                            const dateObject = new Date(full['expire_at']);
                            const readableDate = dateObject.toLocaleString();

                            return readableDate;
                        },
                    },
                    {
                        targets: 7,
                        render: function (data, type, full, meta) {
                            const $row_output =
                                '<a href="{{ route('gifts.store') }}/' + full['id'] + '/edit" class="btn btn-sm text-primary btn-icon item-edit"><i class="bx bxs-edit"></i></a>' +
                                `<button class="tf-icons bx bx-x text-danger btn-table deleteButton" data-id="${full['id']}"></button>`;
                            return $row_output;
                        },
                    },
                ],
                createdRow: function (row, data, dataIndex) {
                    // Add a attribute to each row based on the 'id' value
                    $(row).attr('id', 'tableItem' + data['id']);
                },
            });
            $('.datatables-basic tbody').on('click', '.deleteButton', function () {
                const datatablesRow = $(this);
                Swal.fire({
                    title: "{{ __('Are you sure?') }}",
                    text: "{!! __('You won\'t be able to revert this!') !!}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{ __('Yes, delete it!') }}",
                    customClass: {
                        confirmButton: 'btn btn-primary me-1',
                        cancelButton: 'btn btn-label-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.value) {
                        const tableItemId = datatablesRow.attr('data-id');
                        $.ajax({
                            method: "POST",
                            url: "{{ route('gifts.destroy', '') }}/" + tableItemId,
                            data: {
                                '_method': 'DELETE',
                                'ajax': true,
                            },
                            success: function() {
                                dt_basic.row(datatablesRow.parents('tr')).remove().draw();
                                toastr.success("{{ __('Deleted Successfully!') }}");
                            },
                            error: function() {
                                toastr.error("{{ __('Unable to Delete!') }}");
                            }
                        });
                    }
                });
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
    @if(count($gifts) == 0)
        <h4 class="fw-bold py-3 mb-1">
            <span class="text-body fw-light">{{ __('Gift Cards') }}</span>
        </h4>
        <div class="col-12 mb-4">
            <div class="card">
                <div class="row text-center">
                    <div class="card-body mt-2 mb-3">
                        <i class="bx bxs-gift p-4 bx-lg bx-border-circle d-inline-block mb-4"></i>
                        <p class="card-text mb-2">
                            {{ __('Here you can create a gift card to make user pay as with prepaid wallet.') }}
                        </p>
                        <a class="btn btn-primary btn-lg mt-2" href="{{ route('gifts.create') }}"><span class="tf-icon bx bx-plus bx-xs"></span> {{ __('Add a Gift Card') }}</a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="col-12 mb-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h4 class="text-body fw-light mb-0">
                        {{ __('Gift Cards List') }}
                    </h4>
                </div>
                <div class="col-md-6 pt-4 pt-md-0 d-flex justify-content-end">
                    <a href="{{ route('gifts.create') }}" class="btn btn-primary btn-sm fs-6 d-flex align-items-center gap-1">
                        <span class="tf-icon bx bx-plus-circle bx-xs"></span>
                        {{ __('Create a Gift Card') }}
                    </a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-12 mb-4">
                <div class="card">
                    <div class="card-datatable table-responsive">
                        <table class="datatables-basic table border-top">
                            <thead>
                            <tr>
                                <th></th>
                                <th>{{ __('Gift Card Identifier') }}</th>
                                <th>{{ __('Start Balance') }}</th>
                                <th>{{ __('Current Balance') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Creation Date') }}</th>
                                <th>{{ __('Expire Date') }}</th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection
