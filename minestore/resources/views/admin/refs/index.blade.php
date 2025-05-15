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
    <script src="{{asset('js/modules/refs.js')}}"></script>
    <script type="text/javascript">
        var dt_basic_table = $('.datatables-basic');
        if (dt_basic_table.length) {
            var dt_basic = dt_basic_table.DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('api.refs.index') }}',
                searching: true,
                ordering: true,
                columns: [
                    { data: 'id' },
                    { data: 'referer' },
                    { data: 'code' },
                    { data: 'percent' },
                    { data: 'amount' },
                    { data: 'refs' },
                ],
                order: [[4, 'desc']],
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
                            return full['id'];
                        },
                    },
                    {
                        targets: 2,
                        render: function (data, type, full, meta) {
                            return full['referer'];
                        },
                    },
                    {
                        targets: 3,
                        render: function (data, type, full, meta) {
                            return full['code'];
                        },
                    },
                    {
                        targets: 4,
                        render: function (data, type, full, meta) {
                            return full['percent'];
                        },
                    },
                    {
                        targets: 5,
                        render: function (data, type, full, meta) {
                            return full['amount'];
                        },
                    },
                    {
                        targets: 6,
                        render: function (data, type, full, meta) {
                            return full['refs'];
                        },
                    },
                    {
                        targets: 7,
                        render: function (data, type, full, meta) {
                            var $row_output =
                                `<a href="{{ route('refs.show','/') }}/${full['id']}" class="btn btn-sm text-primary btn-icon">` +
                                '<i class="bx bxs-edit"></i>' +
                                '</a>' +
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
                            url: "{{ route('refs.destroy', '') }}/" + tableItemId,
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

        $("#isRefEnabled").on("change", function() {
            let value = $(this).prop("checked") ? 1 : 0;
            updateIsRefEnabledSetting(value);
        });
    </script>
@endsection

@section('content')

	<form method="POST" novalidate="novalidate" autocomplete="off">
	@csrf
        <div class="col-12 mb-4">
            <x-card-input type="checkbox" id="isRefEnabled" name="isRefEnabled"
                              :checked="$isRefEnabled" icon="bx-group">
                <x-slot name="title">{{ __('Enable Player Referrals?') }}</x-slot>
                <x-slot name="text">{{ __('Your customers will be asked if they have a referral (creator) code during checkout process.') }}</x-slot>
            </x-card-input>
		</div>
	</form>
    <div class="col-12 mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="text-body fw-light mb-0">
                    {{ __('Referrers list') }}
                </h4>
            </div>
            <div class="col-md-6 pt-4 pt-md-0 d-flex justify-content-end">
                <a href="{{ route('refs.create') }}" class="btn btn-primary btn-sm fs-6 d-flex align-items-center gap-1">
                    <span class="tf-icon bx bx-plus-circle bx-xs"></span>
                    {{ __('Add referrer') }}
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
                            <th>#</th>
                            <th>{{ __('Referrer') }}</th>
                            <th>{{ __('Code') }}</th>
                            <th>{{ __('Percent') }}</th>
                            <th>{{ __('Total sum') }}</th>
                            <th>{{ __('Invite users') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
