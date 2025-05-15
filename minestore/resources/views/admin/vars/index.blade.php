@extends('admin.layout')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('res/vendor/libs/sweetalert2/sweetalert2.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/select2/select2.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
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
<script src="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('res/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('res/vendor/libs/jquery-repeater/jquery-repeater.js')}}"></script>
<script src="{{asset('res/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
@endsection

@section('page-script')
    <script>
        $('.deleteButton').on('click', function (){
            const that = this;
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
                    $(that).prop('disabled', true);
                    const tableItemId = $(this).attr('data-id');
                    $.ajax({
                        method: "POST",
                        url: "{{ route('vars.destroy', '') }}/" + tableItemId,
                        data: {
                            '_method': 'DELETE',
                            'ajax': true,
                        },
                        success: function() {
                            $('#tableItem' + tableItemId).slideUp("normal", function() {
                                $(this).remove();
                            });
                            toastr.success("{{ __('Deleted Successfully!') }}");
                        },
                        error: function() {
                            $(that).prop('disabled', false);
                            toastr.error("{{ __('Unable to Delete!') }}");
                        }
                    });
                }
            });
        });
    </script>
@endsection

@section('content')

@if(count($vars) == 0)
<h4 class="fw-bold py-3 mb-1">
    <span class="text-body fw-light">{{ __('Variables') }}</span>
</h4>
<div class="col-12 mb-4">
	<div class="card">
		<div class="row text-center">
			<div class="card-body mt-2 mb-3">
				<i class="bx bx-code-curly p-4 bx-lg bx-border-circle d-inline-block mb-4"></i>
				<p class="card-text mb-2">
                    {{ __('Variables enable you to create customisable packages such as allowing users to select the colour of their name upon purchase.') }}
				</p>
				<a href="{{ route('vars.create') }}" class="btn btn-primary btn-lg mt-2"><span class="tf-icon bx bx-plus bx-xs"></span> {{ __('Add a Variable') }}</a>
			</div>
		</div>
	</div>
</div>
@else
<div class="col-12 mb-4">
	<div class="col-12 mb-3">
		<div class="row align-items-center">
		    <div class="col-md-6">
		        <h4 class="text-body fw-light mb-0">
                    {{ __('Created Variables') }}
		        </h4>
		    </div>
		    <div class="col-md-6 pt-4 pt-md-0 d-flex justify-content-end">
		        <a href="{{ route('vars.create') }}" class="btn btn-primary btn-sm fs-6 d-flex align-items-center gap-1">
		            <span class="tf-icon bx bx-plus-circle bx-xs"></span>
                    {{ __('Create a Variable') }}
		        </a>
		    </div>
		</div>
	</div>
	<div class="card">
	  <div class="table-responsive text-nowrap">
		<table class="table table-striped">
		  <thead>
			<tr>
			  <th>{{ __('Name') }}</th>
			  <th>{{ __('Variable Identifier') }}</th>
			  <th>{{ __('Actions') }}</th>
			</tr>
		  </thead>
		  <tbody class="table-border-bottom-0">
		  	@foreach($vars as $var)
			<tr id="tableItem{{$var->id}}">
			  <td><strong>{{ $var->name }}</strong></td>
			  <td>{{ $var->identifier }}</td>
			  <td>
				<div class="d-flex">
					<a href="{{ route('vars.edit', $var->id) }}">
						<span class="tf-icons bx bx-edit-alt text-primary"></span>
					</a>
                    <button class="tf-icons bx bx-x text-danger btn-table deleteButton" data-id="{{$var->id}}"></button>
				</div>
			  </td>
			</tr>
			@endforeach
		  </tbody>
		</table>
	  </div>
	</div>
</div>
@endif
@endsection
