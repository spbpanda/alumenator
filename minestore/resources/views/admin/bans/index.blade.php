@extends('admin.layout')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('res/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('res/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css')}}">
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
<script src="{{asset('js/modules/bans.js')}}"></script>
<script type="text/javascript">
var dt_basic_table = $('.datatables-basic');
if (dt_basic_table.length) {
    var dt_basic = dt_basic_table.DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('api.banlist.index') }}',
        searching: true,
        ordering: true,
        columns: [
            { data: 'id' },
            { data: 'username' },
            { data: 'uuid' },
            { data: 'ip' },
            { data: 'date' },
        ],
        /*dom: 'Bfrtip',
        buttons: [
            {
                extend: 'collection',
                className: 'btn btn-label-primary dropdown-toggle me-2',
                text: '<i class="bx bx-show me-1"></i>Export',
                buttons: [
                    {
                        extend: 'csv',
                        text: '<i class="bx bx-file me-1" ></i>Csv',
                        className: 'dropdown-item',
                        exportOptions: { columns: [3, 4, 5, 6, 7] }
                    },
                    {
                        extend: 'excel',
                        text: 'Excel',
                        className: 'dropdown-item',
                        exportOptions: { columns: [3, 4, 5, 6, 7] }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="bx bxs-file-pdf me-1"></i>Pdf',
                        className: 'dropdown-item',
                        exportOptions: { columns: [3, 4, 5, 6, 7] }
                    },
                    {
                        extend: 'copy',
                        text: '<i class="bx bx-copy me-1" ></i>Copy',
                        className: 'dropdown-item',
                        exportOptions: { columns: [3, 4, 5, 6, 7] }
                    }
                ]
            },
            {
                text: '<i class="bx bx-plus me-1"></i> <span class="d-none d-lg-inline-block">Add New Record</span>',
                className: 'create-new btn btn-primary'
            }
        ],*/
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
                    var $username = full['username'];
                    var $output = '<img src="https://mc-heads.net/avatar/' + $username + '/25" alt="' + $username + '" class="rounded" onerror="this.src=\'{{ asset('res/img/question-icon.png') }}\';">';
                    var $row_output =
                        '<a href="/admin/lookup/' + full['username'] + '" class="d-flex justify-content-start align-items-center user-name">' +
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
                        '</a>';
                    return $row_output;
                },
            },
            {
                targets: 2,
                render: function (data, type, full, meta) {
                    return !full['uuid'] ? 'Undefined' : full['uuid'];
                },
            },
            {
                targets: 3,
                render: function (data, type, full, meta) {
                    return !full['ip'] ? 'None' : full['ip'];
                },
            },
            {
                targets: 4,
                render: function (data, type, full, meta) {
                    return moment(full['date']).format('YYYY-MM-DD HH:mm:ss');
                },
            },
            {
                targets: 5,
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
        unbanUser($(this).data('user-id')).done(function(r) {
            toastr.success("{{ __('User was unbanned!') }}");
        }).fail(function(r) {
            if(r.status === 410){
                toastr.error(r.responseJSON.message);
            }
            else{
                toastr.error("{{ __('Unable to unban user!') }}");
            }
        });
        dt_basic.row($(this).parents('tr')).remove().draw();
    });
}
</script>
@endsection

@section('content')
<div class="col-12 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h4 class="text-body fw-light mb-0">
                {{ __('Users ban list') }}
            </h4>
        </div>
        <div class="col-md-6 pt-4 pt-md-0 d-flex justify-content-end">
            <a href="{{ route('bans.create') }}" class="btn btn-primary btn-sm fs-6 d-flex align-items-center gap-1">
                <span class="tf-icon bx bx-plus-circle bx-xs"></span>
                {{ __('Ban a New User') }}
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
                  <th>{{ __('UUID') }}</th>
                  <th>{{ __('IP') }}</th>
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
