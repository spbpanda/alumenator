@extends('admin.layout')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('res/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('res/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css')}}">
<link rel="stylesheet" href="{{asset('res/vendor/libs/datatables-checkboxes-jquery/select.dataTables.min.css')}}">
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
@endsection

@section('page-script')
<script src="{{asset('js/modules/whitelist.js')}}"></script>
<script type="text/javascript">
var dt_basic_table = $('.datatables-basic');
if (dt_basic_table.length) {
    var dt_basic = dt_basic_table.DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('api.whitelist.index') }}',
        searching: true,
        ordering: true,
        columns: [
            { data: 'id' },
            { data: 'username' },
            { data: 'ip' },
            { data: 'date' },
        ],
        order: [[1, 'desc']],
        select: {
            style: 'os',
            selector: 'td:first-child'
        },
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
                    return !full['ip'] ? 'None' : full['ip'];
                },
            },
            {
                targets: 4,
                render: function (data, type, full, meta) {
                    var $row_output =
                        `<span data-user-id="${full['id']}" class="btn btn-sm text-primary btn-icon delete-record">` +
                        '<i class="bx bx-x"></i>' +
                        '</span>';
                    return $row_output;
                },
            },
        ],
    });

    $('.datatables-basic tbody').on('click', '.delete-record', function () {
        removeUserFromWhitelist($(this).data('user-id'));
        dt_basic.row($(this).parents('tr')).remove().draw();
    });
}
</script>
@endsection

@section('content')
<div class="col-12 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h4 class="text-body fw-light mb-0"> {{ __('Users whitelist') }}</h4>
        </div>
        <div class="col-md-6 pt-4 pt-md-0 d-flex justify-content-end">
            <a href="{{ route('whitelist.create') }}" class="btn btn-primary btn-sm fs-6 d-flex align-items-center gap-1">
                <span class="tf-icon bx bx-plus-circle bx-xs"></span>
                {{ __('Whitelist a New User') }}
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
                  <th>{{ __('Username') }}</th>
                  <th>IP</th>
                  <th>{{ __('Date') }}</th>
                  <th>{{ __('Action') }}</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
    </div>
</div>
@endsection
