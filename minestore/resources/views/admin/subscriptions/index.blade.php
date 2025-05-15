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
<script type="text/javascript">
var dt_basic_table = $('.datatables-basic');
if (dt_basic_table.length) {
    var dt_basic = dt_basic_table.DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('subscriptions.datatables') }}',
        searching: false,
        ordering: false,
        columns: [
            { data: 'id' },
            { data: 'user.username' },
            { data: 'sid' },
            { data: 'payment.gateway' },
            { data: 'status' },
            { data: 'renewal' },
        ],
        order: [[0, 'desc']],
        columnDefs: [
            {
                targets: 1,
                render: function (data, type, full, meta) {
                    var $username = full['user']['username'];
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
                targets: 4,
                render: function (data, type, full, meta) {
                    if (full['status'] === 0) {
                        return '<span class="badge bg-success w-100"> {{ __('ACTIVE') }}</span>';
                    }
                    else if(full['status'] === 1) {
                        return '<span class="badge bg-danger w-100">{{ __('CANCELLED') }}</span>';
                    }
                    else if(full['status'] === 2) {
                        return '<span class="badge bg-warning w-100" style="background: #0076ff;">{{ __('APPROVAL FOR PENDING') }}</span>';
                    }
                },
            },
            {
                targets: 5,
                render: function (data, type, full, meta) {
                    return moment(full['renewal']).format('DD/MM/YYYY');
                },
            },
            {
                targets: 6,
                render: function (data, type, full, meta) {
                    var $row_output =
                      '<a href="{{ route('payments.show', '/') }}/'+full['payment_id']+'" class="btn btn-sm text-primary btn-icon item-edit"><i class="bx bx-show"></i></a>';
                    return $row_output;
                },
            },
        ],
    });
    $('.datatables-basic tbody').on('click', '.delete-record', function () {
        dt_basic.row($(this).parents('tr')).remove().draw();
    });
}
</script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-1"><span class="text-muted fw-light">{{ __('Subscription List') }}</span></h4>
<div class="row">
    <div class="col-md-12 col-12 mb-4">
        <div class="card">
          <div class="card-datatable table-responsive">
            <table class="datatables-basic table border-top">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>{{ __('Username') }}</th>
                  <th>{{ __('Subscription ID') }}</th>
                  <th>{{ __('Gateway') }}</th>
                  <th>{{ __('Status') }}</th>
                  <th>{{ __('Renewal Date (D/M/Y)') }}</th>
                  <th>{{ __('Action') }}</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
    </div>
</div>

@endsection
