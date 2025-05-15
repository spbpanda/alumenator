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
<script src="{{asset('js/modules/chargeback.js')}}"></script>
<script type="text/javascript">
var dt_basic_table = $('.datatables-basic');
if (dt_basic_table.length) {
    var dt_basic = dt_basic_table.DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('api.chargeback.index') }}',
        searching: true,
        ordering: true,
        columns: [
            { data: null },
            { data: 'chargebacks.id' },
            { data: 'users.username' },
            { data: 'payments.price' },
            { data: 'chargebacks.status' },
            { data: 'chargebacks.creation_date' },
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
                    console.log(full)
                    var $username = full['username'];
                    var $output = '<img src="https://mc-heads.net/avatar/' + $username + '/25" alt="' + $username + '" class="rounded">';
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
                    if (full['status'] === 0) {
                        return '<span class="badge bg-warning">{{ __('PENDING') }}</span>';
                    }
                    else if(full['status'] === 1) {
                        return '<span class="badge bg-success">{{ __('COMPLETED') }}</span>';
                    }
                    else if(full['status'] === 2) {
                        return '<span class="badge bg-danger" style="background: #0076ff;">{{ __('CHARGEBACK') }}</span>';
                    }
                },
            },
            {
                targets: 5,
                render: function (data, type, full, meta) {
                    return moment(full['creation_date']).format('DD.MM.YYYY HH:mm:ss');
                },
            },
            {
                targets: 6,
                render: function (data, type, full, meta) {
                    var $row_output =
                      // '<div class="d-inline-block">' +
                      // '<a href="javascript:;" class="btn btn-sm text-primary btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></a>' +
                      // '<ul class="dropdown-menu dropdown-menu-end">' +
                      // '<li><a href="javascript:;" class="dropdown-item">Details</a></li>' +
                      // '<li><a href="javascript:;" class="dropdown-item">Archive</a></li>' +
                      // '<div class="dropdown-divider"></div>' +
                      // '<li><a href="javascript:;" class="dropdown-item text-danger delete-record">Delete</a></li>' +
                      // '</ul>' +
                      // '</div>' +
                        '<a href="{{ route('chargeback.show', '/') }}/'+full['id']+'" class="btn btn-sm text-primary btn-icon item-edit"><i class="bx bx-show"></i></a>'
                     + `<span data-id="${full['id']}" class="btn btn-sm text-primary btn-icon item-edit done-button"><i class="bx bxs-check-circle"></i></span>`
                        + `<span data-id="${full['id']}" class="btn btn-sm text-primary btn-icon item-edit delete-button"><i class='bx bx-trash'></i></span>`;
                    return $row_output;
                },
            },
        ],
    });

    $('.datatables-basic tbody').on('click', '.delete-button', function () {
        deleteChargeback($(this).data('id')).done(function(r) {
            toastr.success("{{ __('Chargeback was successfully deleted!') }}");
        }).fail(function(r) {
            toastr.error("{{ __('Unable to delete the chargeback!') }}");
        });
        dt_basic.row($(this).parents('tr')).remove().draw();
    });

    $('.datatables-basic tbody').on('click', '.done-button', function () {
        finishChargeback($(this).data('id')).done(function(r) {
            toastr.success("{{ __('Chargeback marked as completed!') }}");
        }).fail(function(r) {
            toastr.error("{{ __('Unable to finish the chargeback!') }}");
        });
        dt_basic.row($(this).parents('tr')).remove().draw();
    });
}
</script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-1"><span class="text-muted fw-light">{{ __('Chargebacks List') }}</span></h4>
<div class="row">
    <div class="col-md-12 col-12 mb-4">
        <div class="card">
          <div class="card-datatable table-responsive">
            <table class="datatables-basic table border-top">
              <thead>
                <tr>
                  <th></th>
                  <th>{{ __('ID') }}</th>
                  <th>{{ __('Username') }}</th>
                  <th>{{ __('Amount') }}</th>
                  <th>{{ __('Status') }}</th>
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
