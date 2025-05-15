@extends('admin.layout')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('res/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('res/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css')}}">
<link rel="stylesheet" href="{{asset('res/vendor/libs/sweetalert2/sweetalert2.css')}}" />
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
<script src="{{asset('js/modules/users.js')}}"></script>
<script type="text/javascript">
var dt_basic_table = $('.datatables-basic');
if (dt_basic_table.length) {
    var dt_basic = dt_basic_table.DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('api.users.index') }}',
        searching: true,
        ordering: true,
        columns: [
            { data: 'id' },
            { data: 'username' },
            { data: 'is_2fa' },
        ],
        order: [[0, 'desc']],
        columnDefs: [
            {
                targets: 1,
                render: function (data, type, full, meta) {
                    var $username = full['username'];
                    var $output = '<img src="https://mc-heads.net/avatar/' + $username + '/25" alt="' + $username + '" class="rounded" onerror="this.src=\'{{ asset('res/img/question-icon.png') }}\';">';                    var $row_output =
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
                    var status = full['is_2fa'];
                    var statusMap = {
                        '0': { text: "{{ __('DISABLED') }}", class: 'danger' },
                        '1': { text: "{{ __('ENABLED') }}", class: 'success' },
                    };

                    var $output = '<span class="badge bg-' + statusMap[status].class + '">' + statusMap[status].text + '</span>';
                    return $output;
                },
            },
            {
                targets: 3,
                render: function (data, type, full, meta) {
                    var $row_output =
                        `<a href="{{ route('users.show', '/') }}/${full['id']}" class="btn btn-sm text-primary btn-icon item-edit"><i class="bx bxs-edit"></i></a>` +
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
                    url: "{{ route('users.destroy', '') }}/" + tableItemId,
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
@endsection

@section('content')

<div class="col-12 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h4 class="text-body fw-light mb-0"> {{ __('Teams') }}</h4>
        </div>
        <div class="col-md-6 pt-4 pt-md-0 d-flex justify-content-end">
            <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm fs-6 d-flex align-items-center gap-1">
                <span class="tf-icon bx bx-plus-circle bx-xs"></span>
                {{ __('New user') }}
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
                  <th>ID</th>
                  <th>{{ __('Username') }}</th>
                  <th>{{ __('2FA') }}</th>
                  <th>{{ __('Action') }}</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
    </div>
</div>
@endsection
