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
<link rel="stylesheet" href="{{asset('res/vendor/libs/sweetalert2/sweetalert2.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('res/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
@endsection

@section('page-script')
    <script>
        $('.deleteButton').on('click', function (){
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
                    $(this).prop('disabled', true);
                    const tableItemId = $(this).attr('data-id');
                    $.ajax({
                        method: "POST",
                        url: "{{ route('donation_goals.destroy', '') }}/" + tableItemId,
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
                            $(this).prop('disabled', false);
                            toastr.error("{{ __('Unable to Delete!') }}");
                        }
                    });
                }
            });
        });
    </script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-1">
    <span class="text-body fw-light">{{ __('Donation Goals') }}</span>
</h4>

@if(count($donation_goals) == 0)
<div class="col-12 mb-4">
	<div class="card">
		<div class="row text-center">
		  <div class="card-body mt-2 mb-3">
			<i class="bx bx-candles p-4 bx-lg bx-border-circle d-inline-block mb-4"></i>
			<p class="card-text mb-2">
                {{ __('A Donation Goal encourages your customers to spend money on a common community goal.') }}
			</p>
			<a class="btn btn-primary btn-lg mt-2" href="{{ route('donation_goals.create') }}"><span class="tf-icon bx bx-plus bx-xs"></span> {{ __('Add a Donation Goal') }}</a>
		  </div>
		</div>
	</div>
</div>
@else
<div class="col-12 mb-4">
	<div class="col-12 mb-3">
		<div class="row align-items-center">
		    <div class="col-12 pt-4 pt-md-0 d-flex justify-content-end">
		        <a href="{{ route('donation_goals.create') }}" class="btn btn-primary btn-sm fs-6 d-flex align-items-center gap-1">
		            <span class="tf-icon bx bx-plus-circle bx-xs"></span>
                    {{ __('Create a Donation Goal') }}
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
			  <th>{{ __('Status') }}</th>
			  <th>{{ __('Current Amount') }}</th>
			  <th>{{ __('Goal Amount') }}</th>
			  <th>{{ __('Finished At') }}</th>
			  <th></th>
			</tr>
		  </thead>
		  <tbody class="table-border-bottom-0">
		  	@foreach($donation_goals as $donation_goal)
			<tr id="tableItem{{$donation_goal->id}}">
			  <td><strong>{{ $donation_goal->name }}</strong></td>
			  <td>
                  @if($donation_goal->status == 0)
                    <span class="badge bg-warning">{{ __('Not Active') }}</span>
                  @elseif($donation_goal->status == 1)
                    <span class="badge bg-success">{{ __('Active') }}</span>
                  @endif
              </td>
			  <td>{{ $donation_goal->current_amount }}</td>
			  <td>{{ $donation_goal->goal_amount }}</td>
			  <td>
                  {{ $donation_goal->reached_at ?? __('Not Reached Yet') }}
              </td>
			  <td>
				<div class="d-flex">
					<a href="{{ route('donation_goals.update', $donation_goal->id) }}">
						<span class="tf-icons bx bx-edit-alt text-primary"></span>
					</a>
                    <button class="tf-icons bx bx-x text-danger btn-table deleteButton" data-id="{{$donation_goal->id}}"></button>
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
