@extends('admin.layout')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('res/vendor/libs/select2/select2.js')}}"></script>
@endsection

@section('page-script')
@endsection

@section('content')
<style>
.settings_icon {
    width: 100px;
    height: 100px;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 0.5rem;
	font-size: 2.5rem;
}
.settings_icon i {
    font-size: 3.5rem;
}
</style>
<h4 class="fw-bold py-3 mb-1">
  <span class="text-body fw-light">{{ __('Authorization Settings') }}</span>
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
											  <i class="fas fa-gamepad"></i>
										  </div>
										</div>
									</div>
									<div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
										<h4>
                                            {{ __('Authorization Game') }}
										</h4>
										<div class="mb-3 col-md-10">
											<p class="card-text">{{ __('Select the authorization game for your webstore.') }}</p>
										</div>
									</div>
								</div>
							</div>
							<div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
								<select id="auth_game" class="selectpicker w-100" data-style="btn-default">
									<option value="minecraft" selected>{{ __('Minecraft') }}</option>
								</select>
							</div>
					</div>
				</div>
			</div>
	</div>
	<div class="col-12 mb-4">
			<div class="card">
				<div class="card-body">
					<div class="row d-flex w-100 align-self-center">
							<div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
								<div class="row align-self-center h-100">
									<div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
										<div class="d-flex justify-content-center mb-4">
										  <div class="settings_icon bg-label-primary">
											  <i class="fas fa-user-check"></i>
										  </div>
										</div>
									</div>
									<div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
										<h4>
                                            {{ __('Authorization Type') }}
										</h4>
										<div class="mb-3 col-md-10">
											<p class="card-text">{{ __('Select the authorization type for your store.') }}</p>
										</div>
									</div>
								</div>
							</div>
							<div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
								<select id="auth_type" name="auth_type" class="selectpicker w-100" data-style="btn-default">
									<option value="username" {{ $settings->auth_type == 'username' ? 'selected' : '' }}>{{ __('Username') }}</option>
									<option value="ingame" {{ $settings->auth_type == 'ingame' ? 'selected' : '' }}>{{ __('In-Game Verification (Plugin Required)') }}</option>
								</select>
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
