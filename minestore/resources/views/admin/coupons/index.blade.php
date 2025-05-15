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
    <script src="{{asset('js/modules/coupons.js')}}"></script>
    <script type="text/javascript">
        var dt_basic_table = $('.datatables-basic');
        if (dt_basic_table.length) {
            var dt_basic = dt_basic_table.DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('api.coupons.index') }}',
                searching: true,
                ordering: true,
                columns: [
                    { data: null },
                    { data: 'name' },
                    { data: 'discount' },
                    { data: 'type', render: function (data, type, full, meta) {
                            return ['Percent', 'Money'][data];
                        }},
                    { data: 'status' },
                    { data: 'uses' },
                    { data: 'available' },
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
                            var discount = full['discount'];
                            var type = full['type'];
                            if (type === 0) {
                                discount = discount + '%';
                            } else {
                                discount = discount + ' ' + dt_basic.ajax.json()['currency']['currency'];
                            }
                            return discount;
                        },
                    },
                    {
                        targets: 3,
                        render: function (data, type, full, meta) {
                            const $output = '<span class="badge bg-primary w-100">' + full['type'] + '</span>';
                            return $output;
                        },
                    },
                    {
                        targets: 4,
                        render: function (data, type, full, meta) {
                            const couponUses = full['uses'];
                            const couponAvailable = full['available'];
                            const expireAt = full['expire_at'];

                            const currentDate = new Date();
                            const expired = (new Date(expireAt) < currentDate);

                            let badgeClass, badgeText;
                            if (expired) {
                                badgeClass = 'warning';
                                badgeText = 'EXPIRED';
                            } else if (couponUses < couponAvailable || couponAvailable == null) {
                                badgeClass = 'success';
                                badgeText = 'ACTIVE';
                            } else if (couponUses >= couponAvailable) {
                                badgeClass = 'danger';
                                badgeText = 'OUT OF STOCK';
                            }

                            const $output = '<span class="badge w-100 bg-' + badgeClass + '">' + badgeText + '</span>';
                            return $output;
                        },
                    },
                    {
                        targets: 5,
                        render: function (data, type, full, meta) {
                            return full['uses'];
                        },
                    },
                    {
                        targets: 6,
                        render: function (data, type, full, meta) {
                            const available = full['available'];
                            if (available === null || 0) {
                                return 'Unlimited';
                            }
                            return available;
                        },
                    },
                    {
                        targets: 7,
                        render: function (data, type, full, meta) {
                            const $row_output =
                                '<a href="{{ route('coupons.store') }}/' + full['id'] + '/edit" class="btn btn-sm text-primary btn-icon item-edit"><i class="bx bxs-edit"></i></a>' +
                                `<button class="tf-icons bx bx-x text-danger btn-table deleteButton" data-id="${full['id']}"></button>`;
                            return $row_output;
                        },
                    },
                ],
                createdRow: function (row, data, dataIndex) {
                    // Add an attribute to each row based on the 'id' value
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
                            url: "{{ route('coupons.destroy', '') }}/" + tableItemId,
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
    @if(count($coupons) == 0)
    <h4 class="fw-bold py-3 mb-1">
        <span class="text-body fw-light">{{ __('Coupons') }}</span>
    </h4>
        <div class="col-12 mb-4">
            <div class="card">
                <div class="row text-center">
                    <div class="card-body mt-2 mb-3">
                        <i class="bx bxs-discount p-4 bx-lg bx-border-circle d-inline-block mb-4"></i>
                        <p class="card-text mb-2">
                            {{ __('A voucher entitling to the customer for a discount off a particular package.') }}
                        </p>
                        <a class="btn btn-primary btn-lg mt-2" href="{{ route('coupons.create') }}"><span class="tf-icon bx bx-plus bx-xs"></span> {{ __('Add a Coupon') }}</a>
                    </div>
                </div>
            </div>
        </div>
    @else
    <div class="col-12 mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="text-body fw-light mb-0">
                    {{ __('Coupons List') }}
                </h4>
            </div>
            <div class="col-md-6 pt-4 pt-md-0 d-flex justify-content-end">
                <a href="{{ route('coupons.create') }}" class="btn btn-primary btn-sm fs-6 d-flex align-items-center gap-1">
                    <span class="tf-icon bx bx-plus-circle bx-xs"></span>
                    {{ __('Create a Coupon') }}
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
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Discount') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Uses') }}</th>
                            <th>{{ __('Max Uses') }}</th>
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
