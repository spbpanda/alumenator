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
const actions = {
    @foreach(\App\Models\SecurityLog::ACTION as $key => $value)
    '{{ $value }}': '{{ $key }}',
    @endforeach
};
var dt_basic_table = $('.datatables-basic');
if (dt_basic_table.length) {
    var dt_basic = dt_basic_table.DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('api.securityLogs.index') }}',
        searching: true,
        ordering: true,
        columns: [
            { data: 'id' },
            { data: 'username', 'name': 'admin' },
            { data: 'action' },
            { data: 'created_at', render: function (data) {
                    if (data == null) {
                        return 'Unknown Date';
                    }

                    const date = new Date(data);
                    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
            }},
        ],
        columnDefs: [
            {
                targets: 2,
                render: function (data, action, full) {
                    return `${actions[full['action']]} #${full['action_id']} ${['created', 'updated', 'deleted'][full['method']]}` + (!full['extra'] ? '' : ` (${full['extra']})`);
                },
            },
        ],
        order: [[0, 'desc']],
    });
}
</script>
@endsection

@section('content')
<div class="col-12 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h4 class="text-body fw-light mb-0"> {{ __('Security logs') }}</h4>
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
                  <th>{{ __('Admin') }}</th>
                  <th>{{ __('Action') }}</th>
                  <th>{{ __('Date') }}</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
    </div>
</div>
@endsection
