@extends('admin.layout')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('res/vendor/libs/bs-stepper/bs-stepper.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/dropzone/dropzone.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/tagify/tagify.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/typeahead-js/typeahead.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />

<link rel="stylesheet" href="{{asset('res/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/pickr/pickr-themes.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('res/vendor/libs/bs-stepper/bs-stepper.js')}}"></script>
<script src="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('res/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('res/vendor/libs/dropzone/dropzone.js')}}"></script>
<script src="{{asset('res/vendor/libs/tagify/tagify.js')}}"></script>
<script src="{{asset('res/vendor/libs/typeahead-js/typeahead.js')}}"></script>
<script src="{{asset('res/vendor/libs/bloodhound/bloodhound.js')}}"></script>
<script src="{{asset('res/vendor/libs/jquery-repeater/jquery-repeater.js')}}"></script>

<script src="{{asset('res/vendor/libs/moment/moment.js')}}"></script>
<script src="{{asset('res/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('res/vendor/libs/pickr/pickr.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('res/js/form-wizard-numbered.js')}}"></script>
<script src="{{asset('res/js/forms-editors.js')}}"></script>
<script src="{{asset('res/js/forms-file-upload.js')}}"></script>
<script src="{{asset('res/js/forms-selects.js')}}"></script>
<script src="{{asset('res/js/forms-tagify.js')}}"></script>
<script src="{{asset('res/js/forms-typeahead.js')}}"></script>
<script src="{{asset('res/js/forms-pickers.js')}}"></script>
<script src="{{asset('res/js/forms-extras.js')}}"></script>
<script src="{{asset('res/js/forms-tagify.js')}}"></script>
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
  <span class="text-body fw-light">{{ __('Currency management') }}</span>
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
									  <i class="fas fa-building-columns"></i>
								  </div>
								</div>
							</div>
							<div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
								<h4>
                                    {{ __('Default Currency') }}
								</h4>
								<div class="mb-3 col-md-10">
									<p class="card-text">{{ __('The currency your customers will be charged in.') }}</p>
								</div>
							</div>
						</div>
					</div>
					<div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
						<select id="currency" name="currency" class="selectpicker w-100" data-style="btn-default">
		                    @foreach($currencies as $curr)
		                    <option @if($curr->name == $settings->currency) selected @endif value="{{ $curr->name }}">{{ $curr->name }}</option>
		                    @endforeach
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
									  <i class="fas fa-wallet"></i>
								  </div>
								</div>
							</div>
							<div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
								<h4>
                                    {{ __('Allowed Currency List') }}
								</h4>
								<div class="mb-3 col-md-10">
									<p class="card-text">{{ __('Customers can select currency to display package price.') }}</p>
								</div>
							</div>
						</div>
					</div>
					<div class="action col-12 col-xl-3 col-lg-4 align-self-center mx-auto d-grid">
						<select id="allow_currs" name="allow_currs[]" class="select2 form-select" multiple>
		                    @foreach($currencies as $curr)
		                    <option @if(in_array($curr->name, $allow_currs)) selected @endif value="{{ $curr->name }}">{{ $curr->name }}</option>
		                    @endforeach
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>
	<hr>
	<div class="col-12 mb-4">
		<div class="card">
			<div class="card-body">
				<div class="row d-flex w-100 align-self-center">
					<div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
						<div class="row align-self-center h-100">
							<div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
								<div class="d-flex justify-content-center mb-4">
								  <div class="settings_icon bg-label-primary">
									  <i class="fas fa-coins"></i>
								  </div>
								</div>
							</div>
							<div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
								<h4>
                                    {{ __('Enable Virtual Currency?') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('You need to configure MySQL connection between your Minecraft server plugin and store. Use the same database as you use for a webstore.') }}"></i>
								</h4>
								<div class="mb-3 col-md-10">
								<p class="card-text">{{ __('This option enables Virtual Currency feature and synchronize it with your Minecraft server.') }}</p>
								</div>
							</div>
						</div>
					</div>
					<div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
						<label for="is_virtual_currency" class="switch switch-square">
						  <input type="checkbox" id="is_virtual_currency" name="is_virtual_currency" @if($settings->is_virtual_currency) checked @endif class="switch-input" />
						  <span class="switch-toggle-slider">
							<span class="switch-on"></span>
							<span class="switch-off"></span>
						  </span>
						</label>
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
									  <i class="bx bxs-coin"></i>
								  </div>
								</div>
							</div>
							<div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
								<h4>
                                    {{ __('Virtual Currency Code') }}
								</h4>
								<div class="mb-3 col-md-10">
								<p class="card-text">{{ __('Set which code you want to use for your Virtual Currency.') }} <br>{{ __('Example') }} <code>QQ</code>.</p>
								</div>
							</div>
						</div>
					</div>
					<div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
						<div class="input-group input-group-merge">
							<input type="text" class="form-control" id="virtual_currency" name="virtual_currency" value="{{ $settings->virtual_currency }}" placeholder="QQ" aria-label="Virtual Currency Code" />
						</div>
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
									  <i class="fas fa-terminal"></i>
								  </div>
								</div>
							</div>
							<div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
								<h4>
                                    {{ __('Command to charge Economy on the Minecraft Server') }}
								</h4>
								<div class="mb-3 col-md-10">
								<p class="card-text">{{ __('Available variables to use:') }} <code>{username}</code> - {{ __('the username need to be charged;') }} <code>{amount}</code> - {{ __('amount that will be charged.') }} <br>{{ __('Example:') }} <code>eco remove {user} {amount}</code></p>
								</div>
							</div>
						</div>
					</div>
					<div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
						<div class="input-group input-group-merge">
							<input type="text" class="form-control" id="virtual_currency_cmd" name="virtual_currency_cmd" value="{{ $settings->virtual_currency_cmd }}" placeholder="eco remove {username} {amount}" aria-label="Command to charge Economy on the Minecraft Server." />
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
