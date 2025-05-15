@extends('admin.layout')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
@endsection

@section('vendor-script')
@endsection

@section('page-script')
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-1">
  <span class="text-body fw-light">{!! __("Ban username or a customer's IP") !!}</span>
</h4>

<form action="{{ route('bans.store') }}" method="POST">
@csrf

<div class="col-12 mb-4">
    <div class="alert alert-primary d-flex" role="alert">
      <span class="badge badge-center rounded-pill bg-primary border-label-primary p-3 me-2"><i class="bx bx-command fs-6"></i></span>
      <div class="d-flex flex-column ps-1">
        <h6 class="alert-heading d-flex align-items-center fw-bold mb-1">{{ __('Important') }}</h6>
        <span>{{ __('You need to fill at least ONE field related to the user. If you have both type of information, fill it with asked information!') }}</span>
      </div>
    </div>
</div>

<div class="col-12 mb-4">
	<div class="card mb-4">
	  <div class="card-body">
		<div class="row">
			<div class="col-md-6 mb-3">
				<label for="username" class="form-label">
                    {{ __('Username') }}
				</label>
				<input class="form-control" type="text" id="username" name="username" placeholder="Notch">
			</div>
			<div class="col-md-6 mb-3">
				<label for="ip" class="form-label">
						{{  __('IP (IPv4 or IPv6) to Ban') }}
				</label>
				<input class="form-control" type="text" id="ip" name="ip" placeholder="127.0.0.1">
			</div>
            <div class="col-md-12">
                <label for="reason" class="form-label">
                    Reason
                    <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;"
                       data-bs-toggle="tooltip" data-bs-placement="top"
                       title="{{ __('Enter a reason for ban.') }}"></i>
                </label>
                <textarea id="reason" name="reason" class="form-control"></textarea>
            </div>
		</div>
	  </div>
	</div>
</div>
<div class="row">
	<div class="d-grid gap-2 col-lg-12 mx-auto">
        <button class="btn btn-primary btn-lg" type="submit"><span class="tf-icon bx bx-plus-circle bx-xs"></span> {{ __('Ban User') }}</button>
    </div>
</div>

</form>
@endsection

