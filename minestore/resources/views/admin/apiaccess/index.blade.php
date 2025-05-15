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
  <span class="text-body fw-light">{{ __('API Access Settings') }}</span>
</h4>

<form method="POST" autocomplete="off">
@csrf

<div class="row">
	<div class="col-12 mb-4">
		<div class="card">
			<div class="card-body">
				<div class="row d-flex w-100 align-self-center">
					<div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
						<div class="row align-self-center h-100">
							<div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
								<div class="d-flex justify-content-center mb-4">
								  <div class="settings_icon bg-label-primary">
									  <i class="bx bxs-lock"></i>
								  </div>
								</div>
							</div>
							<div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
								<h4>
                                    {{ __('API Authentication Key') }}
                                </h4>
                                <div class="mb-3 col-md-10">
                                    <p class="card-text">{{ __('Having this key will allow you to add additional authentication to your API requests.') }} <br> {{ __('Example') }}: <code>/api/{auth-key}/categories</code></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
                        <div class="form-password-toggle">
                            <div class="input-group">
                                <input type="password" class="form-control" id="api_secret" name="api_secret" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" value="{{ $api_secret }}" required />
								<span id="smtp_pass_btn" class="input-group-text cursor-pointer"><i class="bx bx-hide password-toggle-button"></i></span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row mb-4">
	<div class="d-grid gap-2 col-lg-12 mx-auto">
       <button class="btn btn-primary btn-lg" type="submit"><span class="tf-icon bx bx-save bx-xs"></span> {{ __('Save Changes') }}</button>
    </div>
</div>

</form>
@endsection
