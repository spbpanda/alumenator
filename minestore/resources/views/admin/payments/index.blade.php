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
<script src="{{asset('js/modules/payments.js')}}"></script>
<script type="text/javascript">
var dt_basic_table = $('.datatables-basic');
if (dt_basic_table.length) {
    var dt_basic = dt_basic_table.DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('api.payments.index') }}',
        searching: true,
        ordering: true,
        columns: [
            { data: null },
            { data: 'payments.id' },
            { data: 'users.username' },
            { data: 'payments.price' },
            { data: 'payments.status' },
            { data: 'payments.updated_at' },
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
                    return full['id'];
                },
            },
            {
                targets: 2,
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
                targets: 3,
                render: function (data, type, full, meta) {
                    return full['price'] + ' ' + full['currency'];
                },
            },
            {
                targets: 4,
                render: function (data, type, full, meta) {
                    var status = full['status'];
                    var statusMap = {
                        '0': { text: "{{ __('PENDING') }}", class: 'warning' },
                        '1': { text: "{{ __('COMPLETED') }}", class: 'success' },
                        '2': { text: "{{ __('ERROR') }}", class: 'danger' },
                        '3': { text: "{{ __('COMPLETED') }}", class: 'success' },
                        '4': { text: "{{ __('CHARGEBACK') }}", class: 'danger' },
                        '5': { text: "{{ __('REFUNDED') }}", class: 'secondary' },
                    };

                    var $output = '<span class="badge bg-' + statusMap[status].class + ' w-100">' + statusMap[status].text + '</span>';
                    return $output;
                },
            },
            {
                targets: 5,
                render: function (data, type, full, meta) {
                    return moment(full['updated_at']).format('DD.MM.YYYY HH:mm:ss');
                },
            },
            {
                targets: 6,
                render: function (data, type, full, meta) {
                    var $row_output =
                      '<a href="{{ route('payments.show', '/') }}/'+full['id']+'" class="btn btn-sm text-primary btn-icon item-edit"><i class="bx bx-show"></i></a>' +
                      `<button class="tf-icons bx bx-x text-danger btn-table deleteButton" data-id="${full['id']}"></button>`;
                    return $row_output;
                },
            },
        ],
        createdRow: function (row, data, dataIndex) {
            $(row).attr('id', 'tableItem' + data['id']);
        },
    });

    $('.datatables-basic tbody').on('click', '.deleteButton', function () {
        const datatablesRow = $(this);
        const tableItemId = datatablesRow.attr('data-id');

        Swal.fire({
            title: "{{ __('Are you sure?') }}",
            text: "{!! __('You won\'t be able to revert this!') !!}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            customClass: {
                confirmButton: 'btn btn-primary me-1',
                cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.value) {
                deletePayment(tableItemId).done(function() {
                    dt_basic.row(datatablesRow.parents('tr')).remove().draw();
                    toastr.success("{{ __('Deleted Successfully!') }}");
                }).fail(function(r) {
                    if (r.status === 410) {
                        toastr.error(r.responseJSON.message);
                    } else {
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

        $("#isDetailsEnabled").on("change", function() {
            let value = $(this).prop("checked") ? 1 : 0;
            updateIsDetailsEnabledSetting(value);
        });
</script>
@endsection

@section('content')

@if (!$payNowEnabled)
<form method="POST" novalidate="novalidate" autocomplete="off">
	@csrf
        <div class="col-12 mb-4">
            <x-card-input type="checkbox" id="isDetailsEnabled" name="isDetailsEnabled"
                              :checked="$isDetailsEnabled" icon="bx-money-withdraw">
                <x-slot name="title">{!! __('Enable Collecting Client\'s Data?') !!}</x-slot>
                <x-slot name="text">{!! __('Your customers will be asked about personal client\'s info during checkout process.') !!}</x-slot>
            </x-card-input>
		</div>
</form>
@endif
<div class="col-12 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h4 class="text-body fw-light mb-0">
                {{ __('Payments List') }}
            </h4>
        </div>
        <div class="col-md-6 pt-4 pt-md-0 d-flex justify-content-end">
            <a href="{{ route('payments.create') }}" class="btn btn-primary btn-sm fs-6 d-flex align-items-center gap-1">
                <span class="tf-icon bx bx-plus-circle bx-xs"></span>
                {{ __('Add manual payment') }}
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
				  <th>ID</th>
				  <th>{{ __('Username') }}</th>
				  <th>{{ __('Amount') }}</th>
				  <th>{{ __('Status') }}</th>
				  <th>{{ __('Time') }}</th>
				  <th>{{ __('Action') }}</th>
				</tr>
			  </thead>
			</table>
		  </div>
		</div>
	</div>
</div>

@endsection
