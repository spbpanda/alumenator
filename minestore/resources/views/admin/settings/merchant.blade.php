@extends('admin.layout')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('res/vendor/libs/bs-stepper/bs-stepper.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/tagify/tagify.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('res/vendor/libs/bs-stepper/bs-stepper.js')}}"></script>
<script src="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('res/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('res/vendor/libs/tagify/tagify.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('res/js/form-wizard-numbered.js')}}"></script>
<script src="{{asset('res/js/forms-selects.js')}}"></script>
<script src="{{asset('res/js/forms-tagify.js')}}"></script>
<script src="{{asset('res/js/forms-pickers.js')}}"></script>
<script src="{{asset('res/js/forms-extras.js')}}"></script>
<script src="{{asset('res/js/forms-tagify.js')}}"></script>
<script src="{{asset('js/modules/merchant.js')}}"></script>
<script>
    // Should be moved in separate file
    toastr.options = {
        "closeButton": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }

    $(".state-switcher").on('change', function(){
        let value = $(this).prop('checked') ? 1 : 0;
        updateMerchantState($(this).data('name'), value);
    });

    $(".save-button").click(function(){
        let formArray = $(this).closest(".collapse").serializeArray();

        let data = {};
        $.each(formArray, function(index, field) {
            data[field.name] = field.value;
        });

        updateMerchantConfig($(this).data('name'), data).done(function(r) {
            toastr.success("{{ __('Changes successfully saved!') }}");
        }).fail(function(r) {
            if (r.status === 422) {
                toastr.error("{{ __('Invalid data, check your inputs') }}");
            } else if (r.status === 403) {
                toastr.error("{{ __('You do not have permission to perform this action!') }}");
            } else if (r.status === 404) {
                toastr.error("{{ __('Start onboarding process before enabling PayNow.') }}");
            } else {
                toastr.error("{{ __('Unable to save changes!') }}");
            }
        });
    })
</script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-1">
  <span class="text-body fw-light">{{ __('Payment Methods') }}</span>
</h4>
<div class="row">
    <!-- PayNow Method -->
    <div class="col-12 mb-4">
        <div class="card border border-primary">
            <div class="card-body">
                <div class="row d-flex w-100 align-self-center">
                    <div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
                        <div class="row align-self-center h-100">
                            <div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
                                <img src="{{ asset('res/img/logos/paynow.svg') }}" class="w-px-100" style="width: 115px !important;" alt="PayNow Logo">
                            </div>
                            <div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
                                <h4>
                                    PayNow Checkout
                                    <label for="PayNow_enable" class="switch switch-square" style="margin-left: 10px;">
                                        <input id="PayNow_enable" name="enable" data-name="paynow" {{ $methods['paynow']['enable'] == 1 ? 'checked' : '' }} type="checkbox" class="switch-input state-switcher" />
                                        <span class="switch-toggle-slider">
												<span class="switch-on"></span>
												<span class="switch-off"></span>
											  </span>
                                    </label>
                                </h4>
                                <div class="mb-3 col-md-10">
                                    <p class="card-text">Provides <strong>Full Chargeback Protection, Global Tax Handling</strong>, Support for 75+ Payment Methods, and Easy Subscription Management.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
                        <span class="badge bg-primary" style="position: absolute; right: 15px; top: 10px;">{{ __('Official Partner') }} & {{ __('Subscriptions Support') }}</span>
                        <a class="btn btn-primary me-1" href="{{ route('paynow.onboarding.start') }}" aria-expanded="false" aria-controls="PayNow">
                            {{ __('Configure') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- PayNow Method (END) -->
    <!-- PayPal IPN Method -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="row d-flex w-100 align-self-center">
                    <div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
                        <div class="row align-self-center h-100">
                            <div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
                                <img src="{{asset('res/svg/payment_methods/paypal.svg')}}" class="w-px-100">
                            </div>
                            <div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
                                <h4>
                                    PayPal (IPN)
                                    <label for="PayPalIPN_enable" class="switch switch-square" style="margin-left: 10px;">
                                        <input id="PayPalIPN_enable" name="enable"  data-name="paypalipn" {{ $methods['PayPalIPN']['enable'] == 1 ? 'checked' : '' }} type="checkbox" class="switch-input state-switcher" />
                                        <span class="switch-toggle-slider">
												<span class="switch-on"></span>
												<span class="switch-off"></span>
											  </span>
                                    </label>
                                </h4>
                                <div class="mb-3 col-md-10">
                                    <p class="card-text">{{ __('Newest PayPal Method. We recommend to use this method for PayPal payments.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
                        <span class="badge bg-primary" style="position: absolute; right: 15px; top: 10px;">{{ __('Subscriptions Support') }}</span>
                        <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse" data-bs-target="#PayPal_IPN" aria-expanded="false" aria-controls="PayPal_IPN">
                            {{ __('Configure') }}
                        </button>
                    </div>
                </div>
                <form class="collapse" id="PayPal_IPN">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="bg-lighter border rounded p-3 mb-3">
                                <span class="card-text">IPN URL: <code style="margin-left:3px; background: #e65e04;color: #ffffff;padding: 2px 4px;border-radius: 4px;font-size:90%">https://{{ $_SERVER['HTTP_HOST'] }}/api/payments/handle/paypalIPN</code></span>
                            </div>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label for="paypalIPN_business" class="form-label">
                                {{ __('PayPal Email') }}
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the PayPal email that you want to use to accept payments.') }}"></i>
                            </label>
                            <input class="form-control" type="text" id="paypalIPN_business" name="paypal_business" value="{{ $methods['PayPalIPN']['config']['paypal_business'] }}" placeholder="example@gmail.com">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="paypalIPN_currency_code" class="form-label">
                                {{ __('Currency Code') }}
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Select the currency code to accept money on your PayPal account.') }}"></i>
                            </label>
                            <input class="form-control" type="text" id="paypalIPN_currency_code" name="paypal_currency_code" value="{{ $methods['PayPalIPN']['config']['paypal_currency_code'] }}" placeholder="USD">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="paypalIPN_test" class="form-label">
                                {{ __('Payment Method Mode') }}
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('If you use SANDBOX mode, you need to use SANDBOX credentials.') }}"></i>
                            </label>
                            <select id="paypalIPN_test" name="test" class="selectpicker w-100" data-style="btn-default">
                                <option value="0" @if(!$methods['PayPalIPN']['config']['test']) selected @endif>{{ __('Production') }}</option>
                                <option value="1" @if($methods['PayPalIPN']['config']['test']) selected @endif>{{ __('Sandbox') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="d-grid gap-2 col-lg-12 mx-auto">
                            <button class="btn btn-primary btn-lg save-button" type="button" data-name="paypalipn"><span class="tf-icon bx bx-save bx-xs"></span> {{ __('Save Changes') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- PayPal IPN Method (END) -->

	<!-- PayPal Legacy Method -->
	<div class="col-12 mb-4">
			<div class="card">
				<div class="card-body">
					<div class="row d-flex w-100 align-self-center">
							<div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
								<div class="row align-self-center h-100">
									<div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
										<img src="{{ asset('res/svg/payment_methods/paypal.svg') }}" class="w-px-100">
									</div>
									<div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
										<h4>
											PayPal (Legacy)
											<label class="switch switch-square" for="PayPal_enable" style="margin-left: 10px;">
											  <input type="checkbox" id="PayPal_enable" name="enable" data-name="paypal" {{ $methods['PayPal']['enable'] == 1 ? 'checked' : '' }} class="switch-input state-switcher" />
											  <span class="switch-toggle-slider">
												<span class="switch-on"></span>
												<span class="switch-off"></span>
											  </span>
											</label>
										</h4>
										<div class="mb-3 col-md-10">
											<p class="card-text">{{ __('Outdated PayPal Method. We recommend to use PayPal IPN Method over this.') }}</p>
										</div>
									</div>
								</div>
							</div>
							<div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
							  <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse" data-bs-target="#PayPal_Legacy" aria-expanded="false" aria-controls="PayPal_Legacy">
                                  {{ __('Configure') }}
							  </button>
							</div>
					</div>
					<form class="collapse" id="PayPal_Legacy">
						<div class="row">
							<div class="col-md-6 mb-2">
								<label for="paypal_user" class="form-label">
                                    {{ __('User API Login') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('You can find it when you generate credentials in your PayPal Developer Settings.') }}"></i>
								</label>
								<input class="form-control" type="text" id="paypal_user" name="paypal_user" placeholder="xx-00mcix0000000_api1.business.example.com" value="{{ $methods['PayPal']['config']['paypal_user'] }}">
							</div>
							<div class="col-md-6 mb-2 form-password-toggle">
								<label for="paypal_password" class="form-label">
                                    {{ __('User API Password') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('You can find it when you generate credentials in your PayPal Developer Settings.') }}"></i>
								</label>
								<div class="input-group">
									<input type="password" class="form-control" id="paypal_password" name="paypal_password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="paypal_user_api_password" value="{{ $methods['PayPal']['config']['paypal_password'] }}">
									<span id="paypal_user_api_password" class="input-group-text cursor-pointer"><i class="bx bx-hide password-toggle-button"></i></span>
								</div>
							</div>
							<div class="col-md-12 mb-2">
								<label for="paypal_signature" class="form-label">
                                    {{ __('User API Signature') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('56-characters signature that you can find in your PayPal Developer Settings.') }}"></i>
								</label>
								<input class="form-control" type="text" id="paypal_signature" name="paypal_signature" placeholder="XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX" value="{{ $methods['PayPal']['config']['paypal_signature'] }}">
							</div>
							<div class="col-md-6 mb-4">
								<label for="paypal_currency_code" class="form-label">
                                    {{ __('Currency Code') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Select the currency code to accept money on your PayPal account.') }}"></i>
								</label>
								<input class="form-control" type="text" id="paypal_currency_code" name="paypal_currency_code" placeholder="USD" value="{{ $methods['PayPal']['config']['paypal_currency_code'] }}">
							</div>
							<div class="col-md-6 mb-4">
								<label for="paypal_mode" class="form-label">
                                    {{ __('Payment Method Mode') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('If you use SANDBOX mode, you need to use SANDBOX credentials.') }}"></i>
								</label>
								<select id="paypal_mode" name="test" class="selectpicker w-100" data-style="btn-default">
									<option value="0" @if(!$methods['PayPal']['config']['test']) selected @endif>{{ __('Production') }}</option>
									<option value="1" @if($methods['PayPal']['config']['test']) selected @endif>{{ __('Sandbox') }}</option>
								</select>
							</div>
						</div>
						<div class="row">
                            <div class="d-grid gap-2 col-lg-12 mx-auto">
                               <button class="btn btn-primary btn-lg save-button" type="button" data-name="paypal"><span class="tf-icon bx bx-save bx-xs"></span> {{ __('Save Changes') }}</button>
                            </div>
						</div>
					</form>
				</div>
			</div>
	</div>
	<!-- PayPal Legacy Method (END) -->

    <!-- Cordarium Method -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="row d-flex w-100 align-self-center">
                    <div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
                        <div class="row align-self-center h-100">
                            <div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
                                <img src="{{ asset('res/svg/payment_methods/cordarium.svg') }}" class="w-px-100">
                            </div>
                            <div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
                                <h4>
                                    Cordarium
                                    <label for="Cordarium_enable" class="switch switch-square" style="margin-left: 10px;">
                                        <input id="Cordarium_enable" name="enable" data-name="cordarium" {{ $methods['Cordarium']['enable'] == 1 ? 'checked' : '' }} type="checkbox" class="switch-input state-switcher" />
                                        <span class="switch-toggle-slider">
												<span class="switch-on"></span>
												<span class="switch-off"></span>
											  </span>
                                    </label>
                                </h4>
                                <div class="mb-3 col-md-10">
                                    <p class="card-text">{{ __('The ultimate crypto payment gateway: instant, secure, and businesses-free to accept crypto currency effortlessly. ') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
                        <span class="badge bg-primary" style="position: absolute; right: 15px; top: 10px;">{{ __('Official Partner') }}</span>
                        <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse" data-bs-target="#cordarium" aria-expanded="false" aria-controls="cordarium">
                            {{ __('Configure') }}
                        </button>
                    </div>
                </div>
                <form class="collapse mt-4" id="cordarium">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="bg-lighter border rounded p-3 mb-3">
                                <span class="card-text">{{ __('Notification/Webhook URL:') }} <code style="margin-left:3px; background: #e65e04;color: #ffffff;padding: 2px 4px;border-radius: 4px;font-size:90%">https://{{ $_SERVER['HTTP_HOST'] }}/api/payments/handle/cordarium</code></span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2 form-password-toggle">
                            <label for="cordarium_server_id" class="form-label">
                                {{ __('Server ID') }}
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the Server ID you can find in the "Webstore Settings".') }}"></i>
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="cordarium_server_id" name="server_id" value="{{ $methods['Cordarium']['config']['server_id'] }}" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="cordarium_server_id2" />
                                <span id="cordarium_server_id2" class="input-group-text cursor-pointer"><i class="bx bx-hide password-toggle-button"></i></span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2 form-password-toggle">
                            <label for="cordarium_public_key" class="form-label">
                                {{ __('Public Key') }}
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the Public Key you can find in the "Webstore Settings".') }}"></i>
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="cordarium_public_key" name="public_key" value="{{ $methods['Cordarium']['config']['public_key'] }}" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="cordarium_public_key2" />
                                <span id="cordarium_public_key2" class="input-group-text cursor-pointer"><i class="bx bx-hide password-toggle-button"></i></span>
                            </div>
                        </div>
                        <div class="col-md-12 mb-4 form-password-toggle">
                            <label for="cordarium_public_key" class="form-label">
                                {{ __('Secret Key') }}
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the Public Key you can find in the "Webstore Settings".') }}"></i>
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="cordarium_secret_key" name="secret_key" value="{{ $methods['Cordarium']['config']['secret_key'] }}" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="cordarium_secret_key2" />
                                <span id="cordarium_secret_key2" class="input-group-text cursor-pointer"><i class="bx bx-hide password-toggle-button"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="d-grid gap-2 col-lg-12 mx-auto">
                            <button class="btn btn-primary btn-lg save-button" type="button" data-name="cordarium"><span class="tf-icon bx bx-save bx-xs"></span> {{ __('Save Changes') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Cordarium Method (END) -->

    <!-- Coinbase Method -->
	<div class="col-12 mb-4">
			<div class="card">
				<div class="card-body">
					<div class="row d-flex w-100 align-self-center">
							<div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
								<div class="row align-self-center h-100">
									<div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
										<img src="{{ asset('res/svg/payment_methods/coinbase.svg') }}" class="w-px-100">
									</div>
									<div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
										<h4>
											Coinbase
											<label for="Coinbase_enable" class="switch switch-square" style="margin-left: 10px;">
											  <input id="Coinbase_enable" name="enable" data-name="coinbase" {{ $methods['Coinbase']['enable'] == 1 ? 'checked' : '' }} type="checkbox" class="switch-input state-switcher" />
											  <span class="switch-toggle-slider">
												<span class="switch-on"></span>
												<span class="switch-off"></span>
											  </span>
											</label>
										</h4>
										<div class="mb-3 col-md-10">
											<p class="card-text">{{ __('Coinbase is a secure online platform for buying & selling cryptocurrency.') }}</p>
										</div>
									</div>
								</div>
							</div>
							<div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
							  <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse" data-bs-target="#coinbase" aria-expanded="false" aria-controls="coinbase">
                                  {{ __('Configure') }}
							  </button>
							</div>
					</div>
					<form class="collapse" id="coinbase">
						<div class="row">
							<div class="col-md-12">
								<div class="bg-lighter border rounded p-3 mb-3">
									<span class="card-text">{{ __('Notification/Webhook URL:') }} <code style="margin-left:3px; background: #e65e04;color: #ffffff;padding: 2px 4px;border-radius: 4px;font-size:90%">https://{{ $_SERVER['HTTP_HOST'] }}/api/payments/handle/coinbase</code></span>
								</div>
							</div>
							<div class="col-md-6 mb-2 form-password-toggle">
								<label for="coinbase_apiKey" class="form-label">
                                    {{ __('API Key') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the API key that you can find in Coinbase account settings.') }}"></i>
								</label>
								<div class="input-group">
									<input type="password" class="form-control" id="coinbase_apiKey" name="api_key" value="{{ $methods['Coinbase']['config']['api_key'] }}" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="coinbase_apiKey2" />
									<span id="coinbase_apiKey2" class="input-group-text cursor-pointer"><i class="bx bx-hide password-toggle-button"></i></span>
								</div>
							</div>
							<div class="col-md-6 mb-2 form-password-toggle">
								<label for="coinbase_webhookSecret" class="form-label">
                                    {{ __('Webhook Secret') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Select the currency code to accept money on your PayPal account.') }}"></i>
								</label>
								<div class="input-group">
									<input type="password" class="form-control" id="coinbase_webhookSecret" name="webhookSecret" value="{{ $methods['Coinbase']['config']['webhookSecret'] }}" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="coinbase_webhookSecret2" />
									<span id="coinbase_webhookSecret2" class="input-group-text cursor-pointer"><i class="bx bx-hide password-toggle-button"></i></span>
								</div>
							</div>
							<div class="col-md-12 mb-4">
								<label for="coinbase_currency" class="form-label">
                                    {{ __('Currency Code') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Select the currency code to accept money on your Coinbase account.') }}"></i>
								</label>
								<input class="form-control" type="text" id="coinbase_currency" name="coinbase_currency" placeholder="USD" value="{{ $methods['Coinbase']['config']['coinbase_currency'] }}">
							</div>
						</div>
						<div class="row">
								<div class="d-grid gap-2 col-lg-12 mx-auto">
								   <button class="btn btn-primary btn-lg save-button" type="button" data-name="coinbase"><span class="tf-icon bx bx-save bx-xs"></span> {{ __('Save Changes') }}</button>
								</div>
						</div>
					</form>
				</div>
			</div>
	</div>
	<!-- Coinbase Method (END) -->
	<!-- Coinpayments Method -->
	<div class="col-12 mb-4">
			<div class="card">
				<div class="card-body">
					<div class="row d-flex w-100 align-self-center">
							<div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
								<div class="row align-self-center h-100">
									<div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
										<img src="{{asset('res/svg/payment_methods/coinpayments.svg')}}" class="w-px-100">
									</div>
									<div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
										<h4>
											CoinPayments
											<label for="CoinPayments_enable" class="switch switch-square" style="margin-left: 10px;">
											  <input id="CoinPayments_enable" name="enable" data-name="CoinPayments" {{ $methods['CoinPayments']['enable'] == 1 ? 'checked' : '' }} type="checkbox" class="switch-input state-switcher" />
											  <span class="switch-toggle-slider">
												<span class="switch-on"></span>
												<span class="switch-off"></span>
											  </span>
											</label>
										</h4>
										<div class="mb-3 col-md-10">
											<p class="card-text">{{ __('Industry-leading cryptocurrency payment gateway and wallet with online merchant services.') }}</p>
										</div>
									</div>
								</div>
							</div>
							<div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
							  <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse" data-bs-target="#coinpayments" aria-expanded="false" aria-controls="zen">
                                  {{ __('Configure') }}
							  </button>
							</div>
					</div>
					<form class="collapse" id="coinpayments">
						<div class="row">
							<div class="col-md-12">
								<div class="bg-lighter border rounded p-3 mb-3">
									<span class="card-text">IPN URL: <code style="margin-left:3px; background: #e65e04;color: #ffffff;padding: 2px 4px;border-radius: 4px;font-size:90%">https://{{ $_SERVER['HTTP_HOST'] }}api/payments/handle/coinpayments</code></span>
								</div>
							</div>
							<div class="col-md-12 mb-2">
								<label for="coinpayments_merchant" class="form-label">
                                    {{ __('Merchant ID') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the Merchant ID, you can find in the Merchant Settings.') }}"></i>
								</label>
								<input class="form-control" type="text" id="coinpayments_merchant" name="merchant" value="{{ $methods['Coinpayments']['config']['merchant'] }}" placeholder="19b692d0-00b9-48f7-92ca-1509f055df2e">
							</div>
							<div class="col-md-6 mb-4 form-password-toggle">
								<label for="coinpayments_secret" class="form-label">
                                    {{ __('Merchant Secret (Signature)') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the secret signature, you need to generate in the Merchant Settings.') }}"></i>
								</label>
								<div class="input-group">
									<input type="password" class="form-control" id="coinpayments_secret" name="secret" value="{{ $methods['Coinpayments']['config']['secret'] }}" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="coinpayments_secret2" />
									<span id="coinpayments_secret2" class="input-group-text cursor-pointer"><i class="bx bx-hide password-toggle-button"></i></span>
								</div>
							</div>
							<div class="col-md-6 mb-4">
								<label for="coinpayments_currency" class="form-label">
                                    {{ __('Currency Code') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Currency in ISO 4217 alphabetic code of the transaction.') }}"></i>
								</label>
								<input class="form-control" type="text" id="coinpayments_currency" name="currency" value="{{ $methods['Coinpayments']['config']['currency'] }}" placeholder="EUR">
							</div>
						</div>
						<div class="row">
								<div class="d-grid gap-2 col-lg-12 mx-auto">
								   <button class="btn btn-primary btn-lg save-button" type="button" data-name="coinpayments"><span class="tf-icon bx bx-save bx-xs"></span> {{ __('Save Changes') }}</button>
								</div>
						</div>
					</form>
				</div>
			</div>
	</div>
	<!-- Coinpayments Method (END) -->
	<!-- Terminal3 Method -->
	<div class="col-12 mb-4">
			<div class="card">
				<div class="card-body">
					<div class="row d-flex w-100 align-self-center">
							<div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
								<div class="row align-self-center h-100">
									<div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
										<img src="{{asset('res/svg/payment_methods/terminal3.svg')}}" class="w-px-100">
									</div>
									<div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
										<h4>
											Terminal3.com
											<label for="Terminal3_enable" class="switch switch-square" style="margin-left: 10px;">
											  <input id="Terminal3_enable" name="enable" data-name="terminal3" {{ $methods['Terminal3']['enable'] == 1 ? 'checked' : '' }} type="checkbox" class="switch-input state-switcher" />
											  <span class="switch-toggle-slider">
												<span class="switch-on"></span>
												<span class="switch-off"></span>
											  </span>
											</label>
										</h4>
										<div class="mb-3 col-md-10">
											<p class="card-text">{{ __('Over 200+ unique local payment methods by Terminal3 (ex-Paymentwall).') }}</p>
										</div>
									</div>
								</div>
							</div>
							<div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
                              <span class="badge bg-primary" style="position: absolute; right: 15px; top: 10px;">{{ __('Subscriptions Support') }}</span>
							  <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse" data-bs-target="#terminal3" aria-expanded="false" aria-controls="terminal3">
                                  {{ __('Configure') }}
							  </button>
							</div>
					</div>
					<form class="collapse" id="terminal3">
						<div class="row">
							<div class="col-md-12">
								<div class="bg-lighter border rounded p-3 mb-3">
									<span class="card-text">{{ __('Pingback URL:') }} <code style="margin-left:3px; background: #e65e04;color: #ffffff;padding: 2px 4px;border-radius: 4px;font-size:90%">https://{{ $_SERVER['HTTP_HOST'] }}/api/payments/handle/terminal3</code></span>
								</div>
							</div>
							<div class="col-md-12 mb-2">
								<label for="terminal3_projectKey" class="form-label">
                                    {{ __('Project Key') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the project key that you can find in the Developer Settings.') }}"></i>
								</label>
								<input class="form-control" type="text" id="terminal3_public" name="public" value="{{ $methods['Terminal3']['config']['public'] }}" placeholder="19b692d0-00b9-48f7-92ca-1509f055df2e">
							</div>
							<div class="col-md-12 mb-4 form-password-toggle">
								<label for="terminal3_secretKey" class="form-label">
                                    {{ __('Secret Key') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the terminal UUID that you can find in API & Documentation section. Example:') }} c8c93c452d38acf3183q2n08fee60aa7"></i>
								</label>
								<div class="input-group">
									<input type="password" class="form-control" id="terminal3_private" name="private" value="{{ $methods['Terminal3']['config']['private'] }}" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="terminal3_private" />
									<span id="terminal3_private" class="input-group-text cursor-pointer"><i class="bx bx-hide password-toggle-button"></i></span>
								</div>
							</div>
						</div>
						<div class="row">
								<div class="d-grid gap-2 col-lg-12 mx-auto">
								   <button class="btn btn-primary btn-lg save-button" type="button" data-name="terminal3"><span class="tf-icon bx bx-save bx-xs"></span> {{ __('Save Changes') }}</button>
								</div>
						</div>
					</form>
				</div>
			</div>
	</div>
	<!-- Terminal3 Method (END) -->
	<!-- Stripe Method -->
	<div class="col-12 mb-4">
			<div class="card">
				<div class="card-body">
					<div class="row d-flex w-100 align-self-center">
							<div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
								<div class="row align-self-center h-100">
									<div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
										<img src="{{asset('res/svg/payment_methods/stripe.svg')}}" class="w-px-100">
									</div>
									<div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
										<h4>
											Stripe
											<label for="Stripe_enable" class="switch switch-square" style="margin-left: 10px;">
											  <input id="Stripe_enable" name="enable" data-name="stripe" {{ $methods['Stripe']['enable'] == 1 ? 'checked' : '' }} type="checkbox" class="switch-input state-switcher" />
											  <span class="switch-toggle-slider">
												<span class="switch-on"></span>
												<span class="switch-off"></span>
											  </span>
											</label>
										</h4>
										<div class="mb-3 col-md-10">
											<p class="card-text">{{ __('Powerful payment method to pay by all type of local and global cards.') }}</p>
										</div>
									</div>
								</div>
							</div>
							<div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
							  <span class="badge bg-primary" style="position: absolute; right: 15px; top: 10px;">Subscriptions Support</span>
							  <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse" data-bs-target="#stripe" aria-expanded="false" aria-controls="stripe">
                                  {{ __('Configure') }}
							  </button>
							</div>
					</div>
					<form class="collapse" id="stripe">
						<div class="row">
							<div class="col-md-12">
								<div class="bg-lighter border rounded p-3 mb-3">
									<span class="card-text">{{ __('Webhook URL:') }} <code style="margin-left:3px; background: #e65e04;color: #ffffff;padding: 2px 4px;border-radius: 4px;font-size:90%">https://{{ $_SERVER['HTTP_HOST'] }}/api/payments/handle/stripe</code></span>
								</div>
							</div>
							<div class="col-md-12 mb-2">
								<label for="stripe_publicKey" class="form-label">
                                    {{ __('Public Key') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the public key that you can find in the Developer Settings.') }}"></i>
								</label>
								<input class="form-control" type="text" id="stripe_publicKey" name="public" value="{{ $methods['Stripe']['config']['public'] }}" placeholder="pk_live_XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX">
							</div>
							<div class="col-md-12 mb-4 form-password-toggle">
								<label for="stripe_privateKey" class="form-label">
                                    {{ __('Private Key') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the private key that you can find in API & Documentation section. Example:') }} sk_live_XXXXXXXXXXXXXXXX"></i>
								</label>
								<div class="input-group">
									<input type="password" class="form-control" id="stripe_privateKey" name="private" value="{{ $methods['Stripe']['config']['private'] }}" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="stripe_privateKey2" />
									<span id="stripe_privateKey2" class="input-group-text cursor-pointer"><i class="bx bx-hide password-toggle-button"></i></span>
								</div>
							</div>
							<div class="col-md-6 mb-4 form-password-toggle">
								<label for="stripe_webhookSecret" class="form-label">
                                    {{ __('Webhook Secret') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the webhook that you can find in API & Documentation section. Example:') }} whsec_XXXXXXXXXXXXXXXX"></i>
								</label>
								<div class="input-group">
									<input type="password" class="form-control" id="stripe_webhookSecret" name="whsec" value="{{ $methods['Stripe']['config']['whsec'] }}" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="stripe_webhookSecret2" />
									<span id="stripe_webhookSecret2" class="input-group-text cursor-pointer"><i class="bx bx-hide password-toggle-button"></i></span>
								</div>
							</div>
							<div class="col-md-6 mb-4">
								<label for="stripe_payment_methods" class="form-label">
                                    {{ __('Available Payment Methods') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Select payment methods that you allow your customers to pay with. You need to configure it in your Stripe Dashboard before activate here.') }}"></i>
								</label>
                                @php($stripePaymentMethods = $methods['Stripe']['config']['payment_methods'])
								<select id="stripe_payment_methods" name="payment_methods[]" class="select2 form-select" multiple>
									<option value="card" @if(in_array('card', $stripePaymentMethods)) selected @endif>Credit/Debit Card</option>
									<option value="giropay" @if(in_array('giropay', $stripePaymentMethods)) selected @endif>Giropay (Germany)</option>
									<option value="blik" @if(in_array('blik', $stripePaymentMethods)) selected @endif>BLIK (Poland)</option>
									<option value="alipay" @if(in_array('alipay', $stripePaymentMethods)) selected @endif>AliPay</option>
									<option value="pix" @if(in_array('pix', $stripePaymentMethods)) selected @endif>Pix (Brazil)</option>
									<option value="ideal" @if(in_array('ideal', $stripePaymentMethods)) selected @endif>iDEAL</option>
									<option value="p24" @if(in_array('p24', $stripePaymentMethods)) selected @endif>Przelewy24 (Poland)</option>
									<option value="us_bank_account" @if(in_array('us_bank_account', $stripePaymentMethods)) selected @endif>ACH Direct Debit (USA)</option>
									<option value="wechat_pay" @if(in_array('wechat_pay', $stripePaymentMethods)) selected @endif>WeChat Pay</option>
									<option value="accs_debit" @if(in_array('accs_debit', $stripePaymentMethods)) selected @endif>ACSS (Canada)</option>
									<option value="affirm" @if(in_array('affirm', $stripePaymentMethods)) selected @endif>Affirm (USA)</option>
									<option value="afterpay_clearpay" @if(in_array('afterpay_clearpay', $stripePaymentMethods)) selected @endif>Afterpay/Clearpay</option>
									<option value="au_becs_debit" @if(in_array('au_becs_debit', $stripePaymentMethods)) selected @endif>BECS Direct Debit (Australia)</option>
									<option value="bacs_debit" @if(in_array('bacs_debit', $stripePaymentMethods)) selected @endif>BACS Direct Debit (UK)</option>
									<option value="bancotact" @if(in_array('bancotact', $stripePaymentMethods)) selected @endif>Bancontact</option>
									<option value="boleto" @if(in_array('boleto', $stripePaymentMethods)) selected @endif>Boleto (Brazil)</option>
									<option value="eps" @if(in_array('eps', $stripePaymentMethods)) selected @endif>EPS (Austria)</option>
									<option value="fpx" @if(in_array('fpx', $stripePaymentMethods)) selected @endif>FPX (Malaysia)</option>
									<option value="grabpay" @if(in_array('grabpay', $stripePaymentMethods)) selected @endif>GrabPay</option>
									<option value="klarna" @if(in_array('klarna', $stripePaymentMethods)) selected @endif>Klarna</option>
									<option value="Konbini" @if(in_array('Konbini', $stripePaymentMethods)) selected @endif>Konbini (Japan)</option>
									<option value="oxxo" @if(in_array('oxxo', $stripePaymentMethods)) selected @endif>OXXO (Mexico)</option>
									<option value="paynow" @if(in_array('paynow', $stripePaymentMethods)) selected @endif>PayNow (Singapore)</option>
									<option value="promptpay" @if(in_array('promptpay', $stripePaymentMethods)) selected @endif>PromptPay (Thailand)</option>
									<option value="sepa_debit" @if(in_array('sepa_debit', $stripePaymentMethods)) selected @endif>SEPA Direct Debit</option>
									<option value="sofort" @if(in_array('sofort', $stripePaymentMethods)) selected @endif>Sofort (Europe)</option>
								</select>
							</div>
						</div>
						<div class="row">
								<div class="d-grid gap-2 col-lg-12 mx-auto">
								   <button class="btn btn-primary btn-lg save-button" type="button" data-name="stripe"><span class="tf-icon bx bx-save bx-xs"></span> {{ __('Save Changes') }}</button>
								</div>
						</div>
					</form>
				</div>
			</div>
	</div>
	<!-- Stripe Method (END) -->
	<!-- Mollie Method -->
	<div class="col-12 mb-4">
			<div class="card">
				<div class="card-body">
					<div class="row d-flex w-100 align-self-center">
							<div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
								<div class="row align-self-center h-100">
									<div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
										<img src="{{asset('res/svg/payment_methods/mollie.svg')}}" class="w-px-100">
									</div>
									<div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
										<h4>
											Mollie
											<label for="Mollie_enable" class="switch switch-square" style="margin-left: 10px;">
											  <input id="Mollie_enable" name="enable" data-name="mollie" {{ $methods['Mollie']['enable'] == 1 ? 'checked' : '' }} type="checkbox" class="switch-input state-switcher" />
											  <span class="switch-toggle-slider">
												<span class="switch-on"></span>
												<span class="switch-off"></span>
											  </span>
											</label>
										</h4>
										<div class="mb-3 col-md-10">
											<p class="card-text">{{ __('All leading payment methods. One of the best localised experience.') }}</p>
										</div>
									</div>
								</div>
							</div>
							<div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
							  <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse" data-bs-target="#mollie" aria-expanded="false" aria-controls="mollie">
                                  {{ __('Configure') }}
							  </button>
							</div>
					</div>
					<form class="collapse" id="mollie">
						<div class="row">
							<div class="col-md-12">
								<div class="bg-lighter border rounded p-3 mb-3">
									<span class="card-text">{{ __('Notification URL:') }} <code style="margin-left:3px; background: #e65e04;color: #ffffff;padding: 2px 4px;border-radius: 4px;font-size:90%">https://{{ $_SERVER['HTTP_HOST'] }}/api/payments/handle/mollie</code></span>
								</div>
							</div>
							<div class="col-md-12 mb-4">
								<label for="mollie_apiKey" class="form-label">
                                    {{ __('API Key') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the production or test API key that you can find in the Developer Settings.') }}"></i>
								</label>
								<input class="form-control" type="text" id="mollie_apiKey" name="apiKey" value="{{ $methods['Mollie']['config']['apiKey'] }}" placeholder="live_XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX">
							</div>
						</div>
						<div class="row">
								<div class="d-grid gap-2 col-lg-12 mx-auto">
								   <button class="btn btn-primary btn-lg save-button" type="button" data-name="mollie"><span class="tf-icon bx bx-save bx-xs"></span> {{ __('Save Changes') }}</button>
								</div>
						</div>
					</form>
				</div>
			</div>
	</div>
	<!-- Stripe Method (END) -->
	<!-- PayTM Method -->
	<div class="col-12 mb-4">
			<div class="card">
				<div class="card-body">
					<div class="row d-flex w-100 align-self-center">
							<div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
								<div class="row align-self-center h-100">
									<div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
										<img src="{{asset('res/svg/payment_methods/paytm.svg')}}" class="w-px-100">
									</div>
									<div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
										<h4>
											PayTM
											<label for="PayTM_enable" class="switch switch-square" style="margin-left: 10px;">
											  <input id="PayTM_enable" name="enable" data-name="paytm" {{ $methods['Paytm']['enable'] == 1 ? 'checked' : '' }} type="checkbox" class="switch-input state-switcher" />
											  <span class="switch-toggle-slider">
												<span class="switch-on"></span>
												<span class="switch-off"></span>
											  </span>
											</label>
										</h4>
										<div class="mb-3 col-md-10">
											<p class="card-text">{!! __('India\'s most-loved payment method for digital items.') !!}</p>
										</div>
									</div>
								</div>
							</div>
							<div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
							  <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse" data-bs-target="#paytm" aria-expanded="false" aria-controls="paytm">
                                  {{ __('Configure') }}
							  </button>
							</div>
					</div>
					<form class="collapse" id="paytm">
						<div class="row">
							<div class="col-md-12">
								<div class="bg-lighter border rounded p-3 mb-3">
									<span class="card-text">{{ __('Callback URL:') }} <code style="margin-left:3px; background: #e65e04;color: #ffffff;padding: 2px 4px;border-radius: 4px;font-size:90%">https://{{ $_SERVER['HTTP_HOST'] }}/api/payments/handle/paytm</code></span>
								</div>
							</div>
							<div class="col-md-12 mb-4">
								<label for="PayTM_mid" class="form-label">
                                    {{ __('Merchant ID') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the Merchant ID, you can find in the PayTM Settings page.') }}"></i>
								</label>
								<input class="form-control" type="text" id="PayTM_mid" name="mid" value="{{ $methods['Paytm']['config']['mid'] }}" placeholder="UttDLnXXXXXX83764X78754XX">
							</div>
							<div class="col-md-6 mb-4 form-password-toggle">
								<label for="paytm_merchantKey" class="form-label">
                                    {{ __('Merchant Key') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the Merchant Key, you can find in the PayTM Settings page.') }}"></i>
								</label>
								<div class="input-group">
									<input type="password" class="form-control" id="paytm_merchantKey" name="mkey" value="{{ $methods['Paytm']['config']['mkey'] }}" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="paytm_merchantKey2" />
									<span id="paytm_merchantKey2" class="input-group-text cursor-pointer"><i class="bx bx-hide password-toggle-button"></i></span>
								</div>
							</div>
							<div class="col-md-6 mb-4">
								<label for="paytm_mdoe" class="form-label">
                                    {{ __('Payment Method Mode') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('If you use SANDBOX mode, you need to use SANDBOX credentials.') }}"></i>
								</label>
								<select id="paytm_mdoe" name="test" class="selectpicker w-100" data-style="btn-default">
									<option value="0" @if(!$methods['Paytm']['config']['test']) selected @endif>{{ __('Production') }}</option>
									<option value="1" @if(!$methods['Paytm']['config']['test']) selected @endif>{{ __('Sandbox') }}</option>
								</select>
							</div>
						</div>
						<div class="row">
								<div class="d-grid gap-2 col-lg-12 mx-auto">
								   <button class="btn btn-primary btn-lg save-button" type="button" data-name="paytm"><span class="tf-icon bx bx-save bx-xs"></span> {{ __('Save Changes') }}</button>
								</div>
						</div>
					</form>
				</div>
			</div>
	</div>
	<!-- PayTM Method (END) -->
	<!-- CashFree Method -->
	<div class="col-12 mb-4">
			<div class="card">
				<div class="card-body">
					<div class="row d-flex w-100 align-self-center">
							<div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
								<div class="row align-self-center h-100">
									<div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
										<img src="{{asset('res/svg/payment_methods/cashfree.svg')}}" class="w-px-100">
									</div>
									<div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
										<h4>
											CashFree
											<label for="CashFree_enable" class="switch switch-square" style="margin-left: 10px;">
											  <input id="CashFree_enable" name="enable" data-name="cashfree" {{ $methods['CashFree']['enable'] == 1 ? 'checked' : '' }} type="checkbox" class="switch-input state-switcher" />
											  <span class="switch-toggle-slider">
												<span class="switch-on"></span>
												<span class="switch-off"></span>
											  </span>
											</label>
										</h4>
										<div class="mb-3 col-md-10">
											<p class="card-text">{{ __('Another payment method for Indian clients.') }}</p>
										</div>
									</div>
								</div>
							</div>
							<div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
							  <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse" data-bs-target="#cashfree" aria-expanded="false" aria-controls="cashfree">
                                  {{ __('Configure') }}
							  </button>
							</div>
					</div>
					<form class="collapse" id="cashfree">
						<div class="row">
							<div class="col-md-12 mb-4">
								<label for="cashfree_appID" class="form-label">
                                    {{ __('Application ID') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the Application ID, you can find in the Cashfree Merchant Settings page.') }}"></i>
								</label>
								<input class="form-control" type="text" id="cashfree_appID" name="appId" value="{{ $methods['CashFree']['config']['appId'] }}" placeholder="73248238XXXXXX834748XXX">
							</div>
							<div class="col-md-12 mb-4 form-password-toggle">
								<label for="cashfree_secretKey" class="form-label">
                                    {{ __('Application Secret Key') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the Application Secret Key, you can find in the Cashfree Merchant Settings page.') }}"></i>
								</label>
								<div class="input-group">
									<input type="password" class="form-control" id="cashfree_secretKey" name="secret" value="{{ $methods['CashFree']['config']['secret'] }}" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="cashfree_secretKey2" />
									<span id="cashfree_secretKey2" class="input-group-text cursor-pointer"><i class="bx bx-hide password-toggle-button"></i></span>
								</div>
							</div>
						</div>
						<div class="row">
								<div class="d-grid gap-2 col-lg-12 mx-auto">
								   <button class="btn btn-primary btn-lg save-button" type="button" data-name="cashfree"><span class="tf-icon bx bx-save bx-xs"></span> {{ __('Save Changes') }}</button>
								</div>
						</div>
					</form>
				</div>
			</div>
	</div>
	<!-- Cashfree Method (END) -->
	<!-- MercadoPago Method -->
	<div class="col-12 mb-4">
			<div class="card">
				<div class="card-body">
					<div class="row d-flex w-100 align-self-center">
							<div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
								<div class="row align-self-center h-100">
									<div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
										<img src="{{asset('res/svg/payment_methods/mercadopago.svg')}}" class="w-px-100">
									</div>
									<div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
										<h4>
											MercadoPago
											<label for="MercadoPago_enable" class="switch switch-square" style="margin-left: 10px;">
											  <input id="MercadoPago_enable" name="enable" data-name="mercadopago" {{ $methods['MercadoPago']['enable'] == 1 ? 'checked' : '' }} type="checkbox" class="switch-input state-switcher" />
											  <span class="switch-toggle-slider">
												<span class="switch-on"></span>
												<span class="switch-off"></span>
											  </span>
											</label>
										</h4>
										<div class="mb-3 col-md-10">
											<p class="card-text">{{ __('The most popular payment method for South and Latin American clients.') }}</p>
										</div>
									</div>
								</div>
							</div>
							<div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
							  <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse" data-bs-target="#mercadopago" aria-expanded="false" aria-controls="mercadopago">
                                  {{ __('Configure') }}
							  </button>
							</div>
					</div>
					<form class="collapse" id="mercadopago">
						<div class="row">
							<div class="col-md-12">
								<div class="bg-lighter border rounded p-3 mb-3">
									<span class="card-text">{{ __('Notification URL:') }} <code style="margin-left:3px; background: #e65e04;color: #ffffff;padding: 2px 4px;border-radius: 4px;font-size:90%">https://{{ $_SERVER['HTTP_HOST'] }}/api/payments/handle/mercadopago</code></span>
								</div>
							</div>
							<div class="col-md-12 mb-4 form-password-toggle">
								<label for="mercadopago_accessToken" class="form-label">
                                    {{ __('Access Token') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the Application Access Token, you can find in the MercadoPago Developer Settings page.') }}"></i>
								</label>
								<div class="input-group">
									<input type="password" class="form-control" id="mercadopago_accessToken" name="token" value="{{ $methods['MercadoPago']['config']['token'] }}" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="cashfree_secretKey2" />
									<span id="cashfree_secretKey2" class="input-group-text cursor-pointer"><i class="bx bx-hide password-toggle-button"></i></span>
								</div>
							</div>
							<div class="col-md-6 mb-4">
								<label for="mercadopago_currency" class="form-label">
                                    {{ __('Currency') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Select your MercadoPago Merchant Currency.') }}"></i>
								</label>
								<select id="mercadopago_currency" name="currency" class="selectpicker w-100" data-style="btn-default">
                                    @foreach(['ARS', 'BRL', 'CLP', 'MXN', 'COP', 'PEN', 'UYU'] as $mp_currency)
                                    <option value="{{ $mp_currency }}" @if(isset($methods['MercadoPago']['config']['currency']) && $methods['MercadoPago']['config']['currency'] == $mp_currency) selected @endif>{{ $mp_currency }}</option>
                                    @endforeach
								</select>
							</div>
							<div class="col-md-6 mb-4">
								<label for="mercadopago_mode" class="form-label">
                                    {{ __('Payment Method Mode') }}
                                    <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('If you use SANDBOX mode, you need to use SANDBOX credentials.') }}"></i>
								</label>
								<select id="mercadopago_mode" name="test" class="selectpicker w-100" data-style="btn-default">
									<option value="0" @if(!$methods['MercadoPago']['config']['test']) selected @endif>{{ __('Production') }}</option>
									<option value="1" @if($methods['MercadoPago']['config']['test']) selected @endif>{{ __('Sandbox') }}</option>
								</select>
							</div>
                            <div class="col-sm-12">
                                <div class="bg-lighter border rounded p-3 mb-3">
                                    <label class="switch switch-square">
                                        <input type="hidden" name="pix" value="0" />
                                        <input type="checkbox"
                                               class="switch-input"
                                               id="mercadopago_pix"
                                               name="pix"
                                               value="1"
                                            {{ isset($methods['MercadoPago']['pix']) && $methods['MercadoPago']['pix'] == 1 ? 'checked' : '' }} />
                                        <span class="switch-toggle-slider">
                                            <span class="switch-on"></span>
                                            <span class="switch-off"></span>
                                        </span>
                                        <span class="switch-label">{{ __('Do you want to enable Pix as separate payment method?') }}</span>
                                    </label>
                                </div>
                            </div>
						</div>
						<div class="row">
								<div class="d-grid gap-2 col-lg-12 mx-auto">
								   <button class="btn btn-primary btn-lg save-button" type="button" data-name="mercadopago"><span class="tf-icon bx bx-save bx-xs"></span> {{ __('Save Changes') }}</button>
								</div>
						</div>
					</form>
				</div>
			</div>
	</div>
	<!-- MercadoPago Method (END) -->
	<!-- GoPay Method -->
	<div class="col-12 mb-4">
			<div class="card">
				<div class="card-body">
					<div class="row d-flex w-100 align-self-center">
							<div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
								<div class="row align-self-center h-100">
									<div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
										<img src="{{asset('res/svg/payment_methods/gopay.svg')}}" class="w-px-100">
									</div>
									<div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
										<h4>
											GoPay
											<label for="GoPay_enable" class="switch switch-square" style="margin-left: 10px;">
											  <input id="GoPay_enable" name="enable" data-name="gopay" {{ $methods['GoPay']['enable'] == 1 ? 'checked' : '' }} type="checkbox" class="switch-input state-switcher" />
											  <span class="switch-toggle-slider">
												<span class="switch-on"></span>
												<span class="switch-off"></span>
											  </span>
											</label>
										</h4>
										<div class="mb-3 col-md-10">
											<p class="card-text">{{ __('The most popular payment method for Czech, Polish, Slovak, Hungarian clients.') }}</p>
										</div>
									</div>
								</div>
							</div>
							<div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
							  <span class="badge bg-primary" style="position: absolute; right: 15px; top: 10px;">{{ __('Subscriptions Support') }}</span>
							  <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse" data-bs-target="#gopay" aria-expanded="false" aria-controls="gopay">
								{{ __('Configure') }}
							  </button>
							</div>
					</div>
					<form class="collapse" id="gopay">
						<div class="row">
							<div class="col-md-12">
								<div class="bg-lighter border rounded p-3 mb-3">
									<span class="card-text">{{ __('Webhook/Notification URL:') }} <code style="margin-left:3px; background: #e65e04;color: #ffffff;padding: 2px 4px;border-radius: 4px;font-size:90%">https://{{ $_SERVER['HTTP_HOST'] }}/api/payments/handle/gopay</code></span>
								</div>
							</div>
							<div class="col-md-6 mb-4">
								<label for="gopay_goID" class="form-label">
									GoID
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the Client Secret, you can find in the GoPay Settings page.') }}"></i>
								</label>
								<input class="form-control" type="text" id="gopay_goID" name="goid" value="{{ $methods['GoPay']['config']['goid'] }}" placeholder="73248238XXXXXX834748XXX">
							</div>
							<div class="col-md-6 mb-4">
								<label for="gopay_clientID" class="form-label">
                                    {{ __('Client ID') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the Client ID, you can find in the GoPay Settings page.') }}"></i>
								</label>
								<input class="form-control" type="text" id="gopay_clientID" name="ClientID" value="{{ $methods['GoPay']['config']['ClientID'] }}" placeholder="1445238X34">
							</div>
							<div class="col-md-6 mb-4 form-password-toggle">
								<label for="gopay_clientSecret" class="form-label">
                                    {{ __('Client Secret') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the Client Secret, you can find in the GoPay Settings page.') }}"></i>
								</label>
								<div class="input-group">
									<input type="password" class="form-control" id="gopay_clientSecret" name="ClientSecret" value="{{ $methods['GoPay']['config']['ClientSecret'] }}" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="cashfree_secretKey" />
									<span id="cashfree_secretKey" class="input-group-text cursor-pointer"><i class="bx bx-hide password-toggle-button"></i></span>
								</div>
							</div>
							<div class="col-md-6 mb-4">
								<label for="gopay_mode" class="form-label">
                                    {{ __('Payment Method Mode') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('If you use SANDBOX mode, you need to use SANDBOX credentials.') }}"></i>
								</label>
                                <select id="gopay_mode" name="test" class="selectpicker w-100" data-style="btn-default">
                                    <option value="0" @selected((int) $methods['GoPay']['config']['test'] === 0)>{{ __('Production') }}</option>
                                    <option value="1" @selected((int) $methods['GoPay']['config']['test'] === 1)>{{ __('Sandbox') }}</option>
                                </select>
							</div>
						</div>
						<div class="row">
								<div class="d-grid gap-2 col-lg-12 mx-auto">
								   <button class="btn btn-primary btn-lg save-button" type="button" data-name="gopay"><span class="tf-icon bx bx-save bx-xs"></span> {{ __('Save Changes') }}</button>
								</div>
						</div>
					</form>
				</div>
			</div>
	</div>
	<!-- GoPay Method (END) -->
	<!-- RazorPay Method -->
	<div class="col-12 mb-4">
			<div class="card">
				<div class="card-body">
					<div class="row d-flex w-100 align-self-center">
							<div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
								<div class="row align-self-center h-100">
									<div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
										<img src="{{ asset('res/svg/payment_methods/razorpay.svg') }}" class="w-px-100">
									</div>
									<div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
										<h4>
											RazorPay
											<label for="RazorPay_enable" class="switch switch-square" style="margin-left: 10px;">
											  <input id="RazorPay_enable" name="enable" data-name="razorpay" {{ $methods['RazorPay']['enable'] == 1 ? 'checked' : '' }} type="checkbox" class="switch-input state-switcher" />
											  <span class="switch-toggle-slider">
												<span class="switch-on"></span>
												<span class="switch-off"></span>
											  </span>
											</label>
										</h4>
										<div class="mb-3 col-md-10">
											<p class="card-text">{{ __('Another popular payment method for Indian customers.') }}</p>
										</div>
									</div>
								</div>
							</div>
							<div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
							  <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse" data-bs-target="#razorpay" aria-expanded="false" aria-controls="razorpay">
                                  {{ __('Configure') }}
							  </button>
							</div>
					</div>
					<form class="collapse" id="razorpay">
						<div class="row">
							<div class="col-md-6 mb-4">
								<label for="razorpay_apiKey" class="form-label">
                                    {{ __('API Key') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the API Key, you can find in the RazorPay Developer Settings page.') }}"></i>
								</label>
								<input class="form-control" type="text" id="razorpay_apiKey" name="api_key" value="{{ $methods['RazorPay']['config']['api_key'] }}" placeholder="rzp_live_dk3XXXXXXXXX">
							</div>
							<div class="col-md-6 mb-4 form-password-toggle">
								<label for="razorpay_apiSecret" class="form-label">
                                    {{ __('API Secret') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the API Secret, you can find in the RazorPay Developer Settings page.') }}"></i>
								</label>
								<div class="input-group">
									<input type="password" class="form-control" id="razorpay_apiSecret" name="api_secret" value="{{ $methods['RazorPay']['config']['api_secret'] }}" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="razorpay_apiSecret" />
									<span id="razorpay_apiSecret" class="input-group-text cursor-pointer"><i class="bx bx-hide password-toggle-button"></i></span>
								</div>
							</div>
							<div class="col-md-12 mb-4">
								<label for="razorpay_mode" class="form-label">
                                    {{ __('Payment Method Mode') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('If you use SANDBOX mode, you need to use SANDBOX credentials.') }}"></i>
								</label>
								<select id="razorpay_mode" name="test" class="selectpicker w-100" data-style="btn-default">
									<option value="0" @if(!$methods['RazorPay']['config']['test']) selected @endif>{{ __('Production') }}</option>
									<option value="1" @if($methods['RazorPay']['config']['test']) selected @endif>{{ __('Sandbox') }}</option>
								</select>
							</div>
						</div>
						<div class="row">
								<div class="d-grid gap-2 col-lg-12 mx-auto">
								   <button class="btn btn-primary btn-lg save-button" type="button" data-name="razorpay"><span class="tf-icon bx bx-save bx-xs"></span> {{ __('Save Changes') }}</button>
								</div>
						</div>
					</form>
				</div>
			</div>
	</div>
	<!-- RazorPay Method (END) -->
	<!-- Unitpay Method -->
	<div class="col-12 mb-4">
			<div class="card">
				<div class="card-body">
					<div class="row d-flex w-100 align-self-center">
							<div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
								<div class="row align-self-center h-100">
									<div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
										<img src="{{asset('res/svg/payment_methods/unitpay.svg')}}" class="w-px-100">
									</div>
									<div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
										<h4>
											Unitpay
											<label for="Unitpay_enable" class="switch switch-square" style="margin-left: 10px;">
											  <input id="Unitpay_enable" name="enable" data-name="unitpay" {{ $methods['UnitPay']['enable'] == 1 ? 'checked' : '' }} type="checkbox" class="switch-input state-switcher" />
											  <span class="switch-toggle-slider">
												<span class="switch-on"></span>
												<span class="switch-off"></span>
											  </span>
											</label>
										</h4>
										<div class="mb-3 col-md-10">
											<p class="card-text">{{ __('Popular payment method for ex-Soviet Union countries.') }}</p>
										</div>
									</div>
								</div>
							</div>
							<div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
							  <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse" data-bs-target="#unitpay" aria-expanded="false" aria-controls="unitpay">
                                  {{ __('Configure') }}
							  </button>
							</div>
					</div>
					<form class="collapse" id="unitpay">
						<div class="row">
							<div class="col-md-12">
								<div class="bg-lighter border rounded p-3 mb-3">
									<span class="card-text">{{ __('Callback URL:') }} <code style="margin-left:3px; background: #e65e04;color: #ffffff;padding: 2px 4px;border-radius: 4px;font-size:90%">https://{{ $_SERVER['HTTP_HOST'] }}/api/payments/handle/unitpay</code></span>
								</div>
							</div>
							<div class="col-md-12 mb-4">
								<label for="unitpay_shopID" class="form-label">
                                    {{ __('Shop ID') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the Shop ID, you can find in the Merchant Settings page.') }}"></i>
								</label>
								<input class="form-control" type="text" id="unitpay_shopID" name="id" value="{{ $methods['UnitPay']['config']['id'] }}" placeholder="245343XX23672">
							</div>
							<div class="col-md-12 mb-4 form-password-toggle">
								<label for="unitpay_shopSecret" class="form-label">
                                    {{ __('API Secret Key') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the Secret Key, you can find in the Merchant Settings page.') }}"></i>
								</label>
								<div class="input-group">
									<input type="password" class="form-control" id="unitpay_shopSecret" name="key" value="{{ $methods['UnitPay']['config']['key'] }}" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="unitpay_shopSecret2" />
									<span id="unitpay_shopSecret2" class="input-group-text cursor-pointer"><i class="bx bx-hide password-toggle-button"></i></span>
								</div>
							</div>
						</div>
						<div class="row">
								<div class="d-grid gap-2 col-lg-12 mx-auto">
								   <button class="btn btn-primary btn-lg save-button" type="button" data-name="unitpay"><span class="tf-icon bx bx-save bx-xs"></span> {{ __('Save Changes') }}</button>
								</div>
						</div>
					</form>
				</div>
			</div>
	</div>
	<!-- Unitpay Method (END) -->
	<!-- Enot Method -->
	<div class="col-12 mb-4">
			<div class="card">
				<div class="card-body">
					<div class="row d-flex w-100 align-self-center">
							<div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
								<div class="row align-self-center h-100">
									<div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
										<img src="{{asset('res/svg/payment_methods/enot.svg')}}" class="w-px-100">
									</div>
									<div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
										<h4>
											Enot
											<label for="Enot_enable" class="switch switch-square" style="margin-left: 10px;">
											  <input id="Enot_enable" name="enable" data-name="enot" {{ $methods['Enot']['enable'] == 1 ? 'checked' : '' }} type="checkbox" class="switch-input state-switcher" />
											  <span class="switch-toggle-slider">
												<span class="switch-on"></span>
												<span class="switch-off"></span>
											  </span>
											</label>
										</h4>
										<div class="mb-3 col-md-10">
											<p class="card-text">{{ __('Popular payment method for ex-Soviet Union countries.') }}</p>
										</div>
									</div>
								</div>
							</div>
							<div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
							  <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse" data-bs-target="#enot" aria-expanded="false" aria-controls="enot">
                                  {{ __('Configure') }}
							  </button>
							</div>
					</div>
					<form class="collapse" id="enot">
						<div class="row">
							<div class="col-md-12">
								<div class="bg-lighter border rounded p-3 mb-3">
									<span class="card-text">{{ __('Notification URL:') }} <code style="margin-left:3px; background: #e65e04;color: #ffffff;padding: 2px 4px;border-radius: 4px;font-size:90%">https://{{ $_SERVER['HTTP_HOST'] }}/api/payments/handle/enot</code></span>
								</div>
							</div>
							<div class="col-md-12 mb-4">
								<label for="enot_merchantID" class="form-label">
                                    {{ __('Merchant ID') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the Merchant ID, you can find in the Merchant Settings page.') }}"></i>
								</label>
								<input class="form-control" type="text" id="enot_merchantID" name="id" value="{{ $methods['Enot']['config']['id'] }}" placeholder="245343XX23672">
							</div>
							<div class="col-md-6 mb-4 form-password-toggle">
								<label for="enot_secretKey1" class="form-label">
                                    {{ __('API Secret Key') }} #1
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the Secret Key, you can find in the Merchant Settings page.') }}"></i>
								</label>
								<div class="input-group">
									<input type="password" class="form-control" id="enot_secretKey1" name="secret1" value="{{ $methods['Enot']['config']['secret1'] }}" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="enot_secretKey12" />
									<span id="enot_secretKey12" class="input-group-text cursor-pointer"><i class="bx bx-hide password-toggle-button"></i></span>
								</div>
							</div>
							<div class="col-md-6 mb-4 form-password-toggle">
								<label for="enot_secretKey2" class="form-label">
                                    {{ __('API Secret Key') }} #2
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the Secret Key, you can find in the Merchant Settings page.') }}"></i>
								</label>
								<div class="input-group">
									<input type="password" class="form-control" id="enot_secretKey2" name="secret2" value="{{ $methods['Enot']['config']['secret2'] }}" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="enot_secretKey22" />
									<span id="enot_secretKey22" class="input-group-text cursor-pointer"><i class="bx bx-hide password-toggle-button"></i></span>
								</div>
							</div>
						</div>
						<div class="row">
								<div class="d-grid gap-2 col-lg-12 mx-auto">
								   <button class="btn btn-primary btn-lg save-button" type="button" data-name="enot"><span class="tf-icon bx bx-save bx-xs"></span> {{ __('Save Changes') }}</button>
								</div>
						</div>
					</form>
				</div>
			</div>
	</div>
	<!-- Enot Method (END) -->
	<!-- Freekassa Method -->
	<div class="col-12 mb-4">
			<div class="card">
				<div class="card-body">
					<div class="row d-flex w-100 align-self-center">
							<div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
								<div class="row align-self-center h-100">
									<div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
										<img src="{{asset('res/svg/payment_methods/freekassa.svg')}}" class="w-px-100">
									</div>
									<div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
										<h4>
											Freekassa
											<label for="Freekassa_enable" class="switch switch-square" style="margin-left: 10px;">
											  <input id="Freekassa_enable" name="enable" data-name="freekassa" {{ $methods['FreeKassa']['enable'] == 1 ? 'checked' : '' }} type="checkbox" class="switch-input state-switcher" />
											  <span class="switch-toggle-slider">
												<span class="switch-on"></span>
												<span class="switch-off"></span>
											  </span>
											</label>
										</h4>
										<div class="mb-3 col-md-10">
											<p class="card-text">{{ __('Popular payment method for ex-Soviet Union countries.') }}</p>
										</div>
									</div>
								</div>
							</div>
							<div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
							  <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse" data-bs-target="#freekassa" aria-expanded="false" aria-controls="freekassa">
                                  {{ __('Configure') }}
							  </button>
							</div>
					</div>
					<form class="collapse" id="freekassa">
						<div class="row">
							<div class="col-md-12">
								<div class="bg-lighter border rounded p-3 mb-3">
									<span class="card-text">{{ __('Notification URL:') }} <code style="margin-left:3px; background: #e65e04;color: #ffffff;padding: 2px 4px;border-radius: 4px;font-size:90%">https://{{ $_SERVER['HTTP_HOST'] }}/api/payments/handle/freekassa</code></span>
								</div>
							</div>
							<div class="col-md-12 mb-4">
								<label for="freekassa_merchantID" class="form-label">
                                    {{ __('Merchant ID') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the Merchant ID, you can find in the Merchant Settings page.') }}"></i>
								</label>
								<input class="form-control" type="text" id="freekassa_merchantID" name="id" value="{{ $methods['FreeKassa']['config']['id'] }}" placeholder="245343XX23672">
							</div>
							<div class="col-md-12 mb-4 form-password-toggle">
								<label for="freekassa_secretKey" class="form-label">
                                    {{ __('API Secret Key') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the Secret Key, you can find in the Merchant Settings page.') }}"></i>
								</label>
								<div class="input-group">
									<input type="password" class="form-control" id="freekassa_secretKey" name="secret" value="{{ $methods['FreeKassa']['config']['secret'] }}" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="freekassa_secretKey2" />
									<span id="freekassa_secretKey2" class="input-group-text cursor-pointer"><i class="bx bx-hide password-toggle-button"></i></span>
								</div>
							</div>
						</div>
						<div class="row">
								<div class="d-grid gap-2 col-lg-12 mx-auto">
								   <button class="btn btn-primary btn-lg save-button" type="button" data-name="freekassa"><span class="tf-icon bx bx-save bx-xs"></span> {{ __('Save Changes') }}</button>
								</div>
						</div>
					</form>
				</div>
			</div>
	</div>
	<!-- Freekassa Method (END) -->
	<!-- Qiwi Method -->
	<div class="col-12 mb-4">
			<div class="card">
				<div class="card-body">
					<div class="row d-flex w-100 align-self-center">
							<div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
								<div class="row align-self-center h-100">
									<div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
										<img src="{{asset('res/svg/payment_methods/qiwi.svg')}}" class="w-px-100">
									</div>
									<div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
										<h4>
											Qiwi
											<label for="Qiwi_enable" class="switch switch-square" style="margin-left: 10px;">
											  <input id="Qiwi_enable" name="enable" data-name="qiwi" {{ $methods['Qiwi']['enable'] == 1 ? 'checked' : '' }} type="checkbox" class="switch-input state-switcher" />
											  <span class="switch-toggle-slider">
												<span class="switch-on"></span>
												<span class="switch-off"></span>
											  </span>
											</label>
										</h4>
										<div class="mb-3 col-md-10">
											<p class="card-text">{{ __('Popular payment method for ex-Soviet Union countries.') }}</p>
										</div>
									</div>
								</div>
							</div>
							<div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
							  <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse" data-bs-target="#qiwi" aria-expanded="false" aria-controls="qiwi">
                                  {{ __('Configure') }}
							  </button>
							</div>
					</div>
					<form class="collapse" id="qiwi">
						<div class="row">
							<div class="col-md-12">
								<div class="bg-lighter border rounded p-3 mb-3">
									<span class="card-text">{{ __('Notification URL:') }} <code style="margin-left:3px; background: #e65e04;color: #ffffff;padding: 2px 4px;border-radius: 4px;font-size:90%">https://{{ $_SERVER['HTTP_HOST'] }}/api/payments/handle/qiwi</code></span>
								</div>
							</div>
							<div class="col-md-12 mb-4">
								<label for="qiwi_merchantID" class="form-label">
                                    {{ __('API Public Key') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the API Public Key, you can find in the Merchant Settings page.') }}"></i>
								</label>
								<input class="form-control" type="text" id="qiwi_merchantID" name="public_key" value="{{ $methods['Qiwi']['config']['public_key'] }}" placeholder="245343XX23672">
							</div>
							<div class="col-md-12 mb-4 form-password-toggle">
								<label for="qiwi_secretKey" class="form-label">
                                    {{ __('API Secret Key') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the Secret Key, you can find in the Merchant Settings page.') }}"></i>
								</label>
								<div class="input-group">
									<input type="password" class="form-control" id="qiwi_secretKey" name="private_key" value="{{ $methods['Qiwi']['config']['private_key'] }}" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="qiwi_secretKey" />
									<span id="qiwi_secretKey" class="input-group-text cursor-pointer"><i class="bx bx-hide password-toggle-button"></i></span>
								</div>
							</div>
						</div>
						<div class="row">
								<div class="d-grid gap-2 col-lg-12 mx-auto">
								   <button class="btn btn-primary btn-lg save-button" type="button" data-name="qiwi"><span class="tf-icon bx bx-save bx-xs"></span> {{ __('Save Changes') }}</button>
								</div>
						</div>
					</form>
				</div>
			</div>
	</div>
	<!-- Qiwi Method (END) -->
	<!-- PayU Method -->
	<div class="col-12 mb-4">
			<div class="card">
				<div class="card-body">
					<div class="row d-flex w-100 align-self-center">
							<div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
								<div class="row align-self-center h-100">
									<div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
										<img src="{{asset('res/svg/payment_methods/payu.svg')}}" class="w-px-100">
									</div>
									<div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
										<h4>
											PayU
											<label for="PayU_enable" class="switch switch-square" style="margin-left: 10px;">
											  <input id="PayU_enable" name="enable" data-name="payu" {{ $methods['PayU']['enable'] == 1 ? 'checked' : '' }} type="checkbox" class="switch-input state-switcher" />
											  <span class="switch-toggle-slider">
												<span class="switch-on"></span>
												<span class="switch-off"></span>
											  </span>
											</label>
										</h4>
										<div class="mb-3 col-md-10">
											<p class="card-text">{{ __('Popular European payment method for V4 countries and rest of Eastern Europe.') }}</p>
										</div>
									</div>
								</div>
							</div>
							<div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
							  <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse" data-bs-target="#payu" aria-expanded="false" aria-controls="payu">
                                  {{ __('Configure') }}
							  </button>
							</div>
					</div>
					<form class="collapse" id="payu">
						<div class="row">
							<div class="col-md-12">
								<div class="bg-lighter border rounded p-3 mb-3">
									<span class="card-text">{{ __('Webhook URL:') }} <code style="margin-left:3px; background: #e65e04;color: #ffffff;padding: 2px 4px;border-radius: 4px;font-size:90%">https://{{ $_SERVER['HTTP_HOST'] }}/api/payments/handle/payu</code></span>
								</div>
							</div>
							<div class="col-md-6 mb-4">
								<label for="payu_posID" class="form-label">
									POS ID
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the POS ID, you can find in the Merchant Settings page.') }}"></i>
								</label>
								<input class="form-control" type="text" id="payu_posID" name="pos_id" value="{{ $methods['PayU']['config']['pos_id'] }}" placeholder="245343XX23672">
							</div>
							<div class="col-md-6 mb-4 form-password-toggle">
								<label for="payu_secret" class="form-label">
									Secret Key (MD5)
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the POS Secret Key, you can find in the Merchant Settings page.') }}"></i>
								</label>
								<div class="input-group">
									<input type="password" class="form-control" id="payu_secret" name="key" value="{{ $methods['PayU']['config']['key'] }}" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="payu_secret" />
									<span id="payu_secret" class="input-group-text cursor-pointer"><i class="bx bx-hide password-toggle-button"></i></span>
								</div>
							</div>
							<div class="col-md-6 mb-4">
								<label for="payu_oauth_client_id" class="form-label">
									OAuth Client ID
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the OAuth Client ID, you can find in the Merchant Settings page.') }}"></i>
								</label>
								<input class="form-control" type="text" id="payu_oauth_client_id" name="oauth_id" value="{{ $methods['PayU']['config']['oauth_id'] }}" placeholder="245343XX23672">
							</div>
							<div class="col-md-6 mb-4 form-password-toggle">
								<label for="payu_oauth_client_secret" class="form-label">
									OAuth Client Secret
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the OAuth Client Secret, you can find in the Merchant Settings page.') }}"></i>
								</label>
								<div class="input-group">
									<input type="password" class="form-control" id="payu_oauth_client_secret" name="oauth_secret" value="{{ $methods['PayU']['config']['oauth_secret'] }}" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="payu_oauth_client_secret2" />
									<span id="payu_oauth_client_secret2" class="input-group-text cursor-pointer"><i class="bx bx-hide password-toggle-button"></i></span>
								</div>
							</div>
							<div class="col-md-12 mb-4">
								<label for="payu_currency" class="form-label">
                                    {{ __('Currency') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Select your PayU Merchant Currency.') }}"></i>
								</label>
                                <select id="payu_currency" name="currency" class="selectpicker w-100" data-style="btn-default">
                                    <option value="PLN" @if($methods['PayU']['config']['currency'] === 'PLN') selected @endif>PLN</option>
                                    <option value="CHF" @if($methods['PayU']['config']['currency'] === 'CHF') selected @endif>CHF</option>
                                    <option value="CZK" @if($methods['PayU']['config']['currency'] === 'CZK') selected @endif>CZK</option>
                                    <option value="DKK" @if($methods['PayU']['config']['currency'] === 'DKK') selected @endif>DKK</option>
                                    <option value="EUR" @if($methods['PayU']['config']['currency'] === 'EUR') selected @endif>EUR</option>
                                    <option value="GBP" @if($methods['PayU']['config']['currency'] === 'GBP') selected @endif>GBP</option>
                                    <option value="HRK" @if($methods['PayU']['config']['currency'] === 'HRK') selected @endif>HRK</option>
                                    <option value="HUF" @if($methods['PayU']['config']['currency'] === 'HUF') selected @endif>HUF</option>
                                    <option value="NOK" @if($methods['PayU']['config']['currency'] === 'NOK') selected @endif>NOK</option>
                                    <option value="RON" @if($methods['PayU']['config']['currency'] === 'RON') selected @endif>RON</option>
                                    <option value="UAH" @if($methods['PayU']['config']['currency'] === 'UAH') selected @endif>UAH</option>
                                    <option value="SEK" @if($methods['PayU']['config']['currency'] === 'SEK') selected @endif>SEK</option>
                                    <option value="USD" @if($methods['PayU']['config']['currency'] === 'USD') selected @endif>USD</option>
                                </select>
							</div>
						</div>
						<div class="row">
								<div class="d-grid gap-2 col-lg-12 mx-auto">
								   <button class="btn btn-primary btn-lg save-button" type="button" data-name="payu"><span class="tf-icon bx bx-save bx-xs"></span> {{ __('Save Changes') }}</button>
								</div>
						</div>
					</form>
				</div>
			</div>
	</div>
	<!-- PayU Method (END) -->
	<!-- PayU India Method -->
	<div class="col-12 mb-4">
			<div class="card">
				<div class="card-body">
					<div class="row d-flex w-100 align-self-center">
							<div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
								<div class="row align-self-center h-100">
									<div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
										<img src="{{asset('res/svg/payment_methods/payu.svg')}}" class="w-px-100">
									</div>
									<div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
										<h4>
											PayU (India)
											<label for="PayUIndia_enable" class="switch switch-square" style="margin-left: 10px;">
											  <input id="PayUIndia_enable" name="enable" data-name="payuindia" {{ $methods['PayUIndia']['enable'] == 1 ? 'checked' : '' }} type="checkbox" class="switch-input state-switcher" />
											  <span class="switch-toggle-slider">
												<span class="switch-on"></span>
												<span class="switch-off"></span>
											  </span>
											</label>
										</h4>
										<div class="mb-3 col-md-10">
											<p class="card-text">{{ __('Popular Indian payment method for India and rest of Asian countries.') }}</p>
										</div>
									</div>
								</div>
							</div>
							<div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
							  <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse" data-bs-target="#payuindia" aria-expanded="false" aria-controls="payuindia">
                                  {{ __('Configure') }}
							  </button>
							</div>
					</div>
					<form class="collapse" id="payuindia">
						<div class="row">
							<div class="col-md-12">
								<div class="bg-lighter border rounded p-3 mb-3">
									<span class="card-text">{{ __('Webhook URL:') }} <code style="margin-left:3px; background: #e65e04;color: #ffffff;padding: 2px 4px;border-radius: 4px;font-size:90%">https://{{ $_SERVER['HTTP_HOST'] }}/api/payments/handle/payuindia</code></span>
								</div>
							</div>
							<div class="col-md-6 mb-4">
								<label for="PayUIndia_key" class="form-label">
									Key
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the Key, you can find in the Merchant Settings page.') }}"></i>
								</label>
								<input class="form-control" type="text" id="PayUIndia_key" name="key" value="{{ $methods['PayUIndia']['config']['key'] }}" placeholder="245343XX23672">
							</div>
							<div class="col-md-6 mb-4 form-password-toggle">
								<label for="payuindia_salt" class="form-label">
									Salt
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the POS Salt, you can find in the Merchant Settings page.') }}"></i>
								</label>
								<div class="input-group">
									<input type="password" class="form-control" id="payuindia_salt" name="salt" value="{{ $methods['PayUIndia']['config']['salt'] }}" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="payuindia_salt" />
									<span id="payuindia_salt" class="input-group-text cursor-pointer"><i class="bx bx-hide password-toggle-button"></i></span>
								</div>
							</div>
							<div class="col-md-12 mb-4">
								<label for="payuindia_mode" class="form-label">
                                    {{ __('Payment Method Mode') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('If you use SANDBOX mode, you need to use SANDBOX credentials.') }}"></i>
								</label>
								<select id="payuindia_mode" name="sandbox" class="selectpicker w-100" data-style="btn-default">
									<option value="0" @if(!$methods['PayUIndia']['config']['sandbox']) selected @endif>{{ __('Production') }}</option>
									<option value="1" @if($methods['PayUIndia']['config']['sandbox']) selected @endif>{{ __('Sandbox') }}</option>
								</select>
							</div>
						</div>
						<div class="row">
								<div class="d-grid gap-2 col-lg-12 mx-auto">
								   <button class="btn btn-primary btn-lg save-button" type="button" data-name="payuindia"><span class="tf-icon bx bx-save bx-xs"></span> {{ __('Save Changes') }}</button>
								</div>
						</div>
					</form>
				</div>
			</div>
	</div>
	<!-- PayU India Method (END) -->
	<!-- HotPay Method -->
	<div class="col-12 mb-4">
			<div class="card">
				<div class="card-body">
					<div class="row d-flex w-100 align-self-center">
							<div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
								<div class="row align-self-center h-100">
									<div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
										<img src="{{asset('res/svg/payment_methods/hotpay.svg')}}" class="w-px-100">
									</div>
									<div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
										<h4>
											HotPay.pl
											<label for="HotPay_enable" class="switch switch-square" style="margin-left: 10px;">
											  <input id="HotPay_enable" name="enable" data-name="hotpay" {{ $methods['HotPay']['enable'] == 1 ? 'checked' : '' }} type="checkbox" class="switch-input state-switcher" />
											  <span class="switch-toggle-slider">
												<span class="switch-on"></span>
												<span class="switch-off"></span>
											  </span>
											</label>
										</h4>
										<div class="mb-3 col-md-10">
											<p class="card-text">{{ __('Popular payment method in Poland.') }}</p>
										</div>
									</div>
								</div>
							</div>
							<div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
							  <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse" data-bs-target="#hotpay" aria-expanded="false" aria-controls="hotpay">
                                  {{ __('Configure') }}
							  </button>
							</div>
					</div>
					<form class="collapse" id="hotpay">
						<div class="row">
							<div class="col-md-12">
								<div class="bg-lighter border rounded p-3 mb-3">
									<span class="card-text">{{ __('Notification URL:') }} <code style="margin-left:3px; background: #e65e04;color: #ffffff;padding: 2px 4px;border-radius: 4px;font-size:90%">https://{{ $_SERVER['HTTP_HOST'] }}/api/payments/handle/hotpay</code></span>
								</div>
							</div>
							<div class="col-md-12 mb-4 form-password-toggle">
								<label for="hotpay_secretHash" class="form-label">
									Secret Hash
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the Secret Hash, you can find in the Merchant Settings page.') }}"></i>
								</label>
								<div class="input-group">
									<input type="password" class="form-control" id="hotpay_secretHash" name="sekret" value="{{ $methods['HotPay']['config']['sekret'] }}" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="hotpay_secretHash2" />
									<span id="hotpay_secretHash2" class="input-group-text cursor-pointer"><i class="bx bx-hide password-toggle-button"></i></span>
								</div>
							</div>
						</div>
						<div class="row">
								<div class="d-grid gap-2 col-lg-12 mx-auto">
								   <button class="btn btn-primary btn-lg save-button" type="button" data-name="hotpay"><span class="tf-icon bx bx-save bx-xs"></span> {{ __('Save Changes') }}</button>
								</div>
						</div>
					</form>
				</div>
			</div>
	</div>
	<!-- HotPay Method (END) -->
	<!-- Interkassa Method -->
	<div class="col-12 mb-4">
			<div class="card">
				<div class="card-body">
					<div class="row d-flex w-100 align-self-center">
							<div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
								<div class="row align-self-center h-100">
									<div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
										<img src="{{asset('res/svg/payment_methods/interkassa.svg')}}" class="w-px-50">
									</div>
									<div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
										<h4>
											Interkassa
											<label for="Interkassa_enable" class="switch switch-square" style="margin-left: 10px;">
											  <input id="Interkassa_enable" name="enable" data-name="interkassa" {{ $methods['InterKassa']['enable'] == 1 ? 'checked' : '' }} type="checkbox" class="switch-input state-switcher" />
											  <span class="switch-toggle-slider">
												<span class="switch-on"></span>
												<span class="switch-off"></span>
											  </span>
											</label>
										</h4>
										<div class="mb-3 col-md-10">
											<p class="card-text">{{ __('Popular payment method in ex-Soviet Union countries.') }}</p>
										</div>
									</div>
								</div>
							</div>
							<div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
							  <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse" data-bs-target="#interkassa" aria-expanded="false" aria-controls="interkassa">
                                  {{ __('Configure') }}
							  </button>
							</div>
					</div>
					<form class="collapse" id="interkassa">
						<div class="row">
							<div class="col-md-12">
								<div class="bg-lighter border rounded p-3 mb-3">
									<span class="card-text">{{ __('Notification URL:') }} <code style="margin-left:3px; background: #e65e04;color: #ffffff;padding: 2px 4px;border-radius: 4px;font-size:90%">https://{{ $_SERVER['HTTP_HOST'] }}/api/payments/handle/interkassa</code></span>
								</div>
							</div>
							<div class="col-md-12 mb-4 form-password-toggle">
								<label for="interkassa_cashboxID" class="form-label">
									Cashbox ID
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the Cashbox ID, you can find in the Merchant Settings page.') }}"></i>
								</label>
								<div class="input-group">
									<input type="password" class="form-control" id="interkassa_cashboxID" name="cashbox_id" value="{{ $methods['InterKassa']['config']['cashbox_id'] }}" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="interkassa_cashboxID2" />
									<span id="interkassa_cashboxID2" class="input-group-text cursor-pointer"><i class="bx bx-hide password-toggle-button"></i></span>
								</div>
							</div>
						</div>
						<div class="row">
								<div class="d-grid gap-2 col-lg-12 mx-auto">
								   <button class="btn btn-primary btn-lg save-button" type="button" data-name="interkassa"><span class="tf-icon bx bx-save bx-xs"></span> {{ __('Save Changes') }}</button>
								</div>
						</div>
					</form>
				</div>
			</div>
	</div>
	<!-- Interkassa Method (END) -->
    <!-- Skrill Method -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="row d-flex w-100 align-self-center">
                    <div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
                        <div class="row align-self-center h-100">
                            <div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
                                <img src="{{asset('res/svg/payment_methods/skrill.svg')}}" class="w-px-100">
                            </div>
                            <div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
                                <h4>
                                    Skrill Quick Checkout (Paysafecard)
                                    <label for="Skrill_enable" class="switch switch-square" style="margin-left: 10px;">
                                        <input id="Skrill_enable" name="enable" data-name="Skrill" {{ $methods['Skrill']['enable'] == 1 ? 'checked' : '' }} type="checkbox" class="switch-input state-switcher" />
                                        <span class="switch-toggle-slider">
												<span class="switch-on"></span>
												<span class="switch-off"></span>
											  </span>
                                    </label>
                                </h4>
                                <div class="mb-3 col-md-10">
                                    <p class="card-text">{{ __('Popular payment method that supports Paysafecard and many others payment methods.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
                        <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse" data-bs-target="#skrill" aria-expanded="false" aria-controls="skrill">
                            {{ __('Configure') }}
                        </button>
                    </div>
                </div>
                <form class="collapse" id="skrill">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="skrill_email" class="form-label">
                                {{ __('Pay To Email') }}
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the email where all payments will come through.') }}"></i>
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="skrill_email" name="email" value="{{ $methods['Skrill']['config']['email'] }}" placeholder="email@gmail.com" aria-describedby="skrill_email" />
                            </div>
                        </div>
                        <div class="col-md-6 mb-4 form-password-toggle">
                            <label for="Skrill_signature" class="form-label">
                                {{ __('Secret Key (Signature)') }}
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the secret signature (key), you can find in the Merchant Settings page.') }}"></i>
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="Skrill_signature" name="signature" value="{{ $methods['Skrill']['config']['signature'] }}" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="Skrill_signature" />
                                <span id="Skrill_signature2" class="input-group-text cursor-pointer"><i class="bx bx-hide password-toggle-button"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="d-grid gap-2 col-lg-12 mx-auto">
                            <button class="btn btn-primary btn-lg save-button" type="button" data-name="skrill"><span class="tf-icon bx bx-save bx-xs"></span> {{ __('Save Changes') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Skrill Method (END) -->
    <!-- Fondy Method -->
{{--    <div class="col-12 mb-4">--}}
{{--        <div class="card">--}}
{{--            <div class="card-body">--}}
{{--                <div class="row d-flex w-100 align-self-center">--}}
{{--                    <div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">--}}
{{--                        <div class="row align-self-center h-100">--}}
{{--                            <div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">--}}
{{--                                <img src="{{asset('res/svg/payment_methods/fondy.svg')}}" class="w-px-120">--}}
{{--                            </div>--}}
{{--                            <div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">--}}
{{--                                <h4>--}}
{{--                                    Fondy.eu--}}
{{--                                    <label for="Fondy_enable" class="switch switch-square" style="margin-left: 10px;">--}}
{{--                                        <input id="Fondy_enable" name="enable" data-name="Fondy" {{ $methods['Fondy']['enable'] == 1 ? 'checked' : '' }} type="checkbox" class="switch-input state-switcher" />--}}
{{--                                        <span class="switch-toggle-slider">--}}
{{--												<span class="switch-on"></span>--}}
{{--												<span class="switch-off"></span>--}}
{{--											  </span>--}}
{{--                                    </label>--}}
{{--                                </h4>--}}
{{--                                <div class="mb-3 col-md-10">--}}
{{--                                    <p class="card-text">{{ __('Fondy is a leading one-stop payment solution for marketplaces and platforms enables the means to move money without friction.') }}</p>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">--}}
{{--                        <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse" data-bs-target="#fondy" aria-expanded="false" aria-controls="fondy">--}}
{{--                            {{ __('Configure') }}--}}
{{--                        </button>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <form class="collapse" id="fondy">--}}
{{--                    <div class="row">--}}
{{--                        <div class="col-md-12">--}}
{{--                            <div class="bg-lighter border rounded p-3 mb-3">--}}
{{--                                <span class="card-text">{{ __('Notification URL:') }} <code style="margin-left:3px; background: #e65e04;color: #ffffff;padding: 2px 4px;border-radius: 4px;font-size:90%">https://{{ $_SERVER['HTTP_HOST'] }}/api/payments/handle/fondy</code></span>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="col-md-6 mb-4">--}}
{{--                            <label for="fondy_merchant_id" class="form-label">--}}
{{--                                {{ __('Merchant ID') }}--}}
{{--                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the Merchant ID from the Fondy Merchant Settings.') }}"></i>--}}
{{--                            </label>--}}
{{--                            <div class="input-group">--}}
{{--                                <input type="text" class="form-control" id="fondy_merchant_id" name="merchant_id" value="{{ $methods['Fondy']['config']['merchant_id'] }}" placeholder="23213XXX423" aria-describedby="fondy_merchant_id" />--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="col-md-6 mb-4">--}}
{{--                            <label for="fondy_currency" class="form-label">--}}
{{--                                {{ __('Currency Code') }}--}}
{{--                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter currency code for your Merchant Account.') }}"></i>--}}
{{--                            </label>--}}
{{--                            <div class="input-group">--}}
{{--                                <input type="text" class="form-control" id="fondy_currency" name="currency" value="{{ $methods['Fondy']['config']['currency'] }}" placeholder="USD" aria-describedby="fondy_currency" />--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="col-md-12 mb-4">--}}
{{--                            <label for="Fondy_signature" class="form-label">--}}
{{--                                {{ __('Password Key (Signature)') }}--}}
{{--                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the secret signature (key), you can find in the Merchant Settings page.') }}"></i>--}}
{{--                            </label>--}}
{{--                            <input class="form-control" type="password" id="Fondy_signature" name="password" value="{{ $methods['Fondy']['config']['password'] }}" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;">--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="row">--}}
{{--                        <div class="d-grid gap-2 col-lg-12 mx-auto">--}}
{{--                            <button class="btn btn-primary btn-lg save-button" type="button" data-name="fondy"><span class="tf-icon bx bx-save bx-xs"></span> {{ __('Save Changes') }}</button>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </form>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
    <!-- Fondy Method (END) -->
    <!-- Midtrans Method -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="row d-flex w-100 align-self-center">
                    <div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
                        <div class="row align-self-center h-100">
                            <div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
                                <img src="{{asset('res/svg/payment_methods/midtrans.svg')}}" class="w-px-100">
                            </div>
                            <div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
                                <h4>
                                    Midtrans.com
                                    <label for="Midtrans_enable" class="switch switch-square" style="margin-left: 10px;">
                                        <input id="Midtrans_enable" name="enable" data-name="Midtrans" {{ $methods['Midtrans']['enable'] == 1 ? 'checked' : '' }} type="checkbox" class="switch-input state-switcher" />
                                        <span class="switch-toggle-slider">
												<span class="switch-on"></span>
												<span class="switch-off"></span>
											  </span>
                                    </label>
                                </h4>
                                <div class="mb-3 col-md-10">
                                    <p class="card-text">{{ __('Midtrans is a leading Indonesian payment gateway, widely recognized for its reliability and convenience.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
                        <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse" data-bs-target="#midtrans" aria-expanded="false" aria-controls="midtrans">
                            {{ __('Configure') }}
                        </button>
                    </div>
                </div>
                <form class="collapse" id="midtrans">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="bg-lighter border rounded p-3 mb-3">
                                <span class="card-text">{{ __('Notification URL:') }} <code style="margin-left:3px; background: #e65e04;color: #ffffff;padding: 2px 4px;border-radius: 4px;font-size:90%">https://{{ $_SERVER['HTTP_HOST'] }}/api/payments/handle/midtrans</code></span>
                            </div>
                        </div>
                        <div class="col-md-12 mb-4 form-password-toggle">
                            <label for="Midtrans_serverKey" class="form-label">
                                {{ __('Server Key') }}
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the secret key, you can find in the Merchant Settings page.') }}"></i>
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="Midtrans_serverKey" name="serverKey" value="{{ $methods['Midtrans']['config']['serverKey'] }}" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="Midtrans_serverKey" />
                                <span id="Midtrans_serverKey2" class="input-group-text cursor-pointer"><i class="bx bx-hide password-toggle-button"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="d-grid gap-2 col-lg-12 mx-auto">
                            <button class="btn btn-primary btn-lg save-button" type="button" data-name="midtrans">
                                <span class="tf-icon bx bx-save bx-xs"></span>
                                {{ __('Save Changes') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Midtrans Method (END) -->
    <!-- PayTR Method -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="row d-flex w-100 align-self-center">
                    <div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
                        <div class="row align-self-center h-100">
                            <div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
                                <img src="{{asset('res/svg/payment_methods/paytr.svg')}}" class="w-px-100">
                            </div>
                            <div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
                                <h4>
                                    PayTR.com
                                    <label for="PayTR_enable" class="switch switch-square" style="margin-left: 10px;">
                                        <input id="PayTR_enable" name="enable" data-name="PayTR" {{ $methods['PayTR']['enable'] == 1 ? 'checked' : '' }} type="checkbox" class="switch-input state-switcher" />
                                        <span class="switch-toggle-slider">
												<span class="switch-on"></span>
												<span class="switch-off"></span>
											  </span>
                                    </label>
                                </h4>
                                <div class="mb-3 col-md-10">
                                    <p class="card-text">PayTR is the most popular payment method in Turkey.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
                        <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse" data-bs-target="#paytr" aria-expanded="false" aria-controls="paytr">
                            {{ __('Configure') }}
                        </button>
                    </div>
                </div>
                <form class="collapse" id="paytr">
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <label for="PayTR_merchant_id" class="form-label">
                                {{ __('Merchant ID') }}
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the Merchant ID, you can find in the Merchant Settings page.') }}"></i>
                            </label>
                            <input class="form-control" type="text" id="PayTR_merchant_id" name="merchant_id" value="{{ $methods['PayTR']['config']['merchant_id'] }}" placeholder="123456">
                        </div>
                        <div class="col-md-6 mb-4 form-password-toggle">
                            <label for="PayTR_merchant_key" class="form-label">
                                {{ __('Merchant Key') }}
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the Merchant Key, you can find in the Merchant Settings page.') }}"></i>
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="PayTR_merchant_key" name="merchant_key" value="{{ $methods['PayTR']['config']['merchant_key'] }}" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="PayTR_merchant_key" />
                                <span id="PayTR_merchant_key2" class="input-group-text cursor-pointer"><i class="bx bx-hide password-toggle-button"></i></span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4 form-password-toggle">
                            <label for="PayTR_merchant_salt" class="form-label">
                                {{ __('Merchant Salt') }}
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the merchant salt, you can find in the Merchant Settings page.') }}"></i>
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="PayTR_merchant_salt" name="merchant_salt" value="{{ $methods['PayTR']['config']['merchant_salt'] }}" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="PayTR_merchant_salt" />
                                <span id="PayTR_merchant_salt2" class="input-group-text cursor-pointer"><i class="bx bx-hide password-toggle-button"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="d-grid gap-2 col-lg-12 mx-auto">
                            <button class="btn btn-primary btn-lg save-button" type="button" data-name="paytr">
                                <span class="tf-icon bx bx-save bx-xs"></span>
                                {{ __('Save Changes') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Midtrans Method (END) -->
    <!-- SePay Method -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="row d-flex w-100 align-self-center">
                    <div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
                        <div class="row align-self-center h-100">
                            <div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
                                <img src="{{asset('res/svg/payment_methods/sepay.png')}}" class="w-px-100">
                            </div>
                            <div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
                                <h4>
                                    SePay
                                    <label for="SePay_enable" class="switch switch-square" style="margin-left: 10px;">
                                        <input id="SePay_enable" name="enable" data-name="SePay" {{ $methods['SePay']['enable'] == 1 ? 'checked' : '' }} type="checkbox" class="switch-input state-switcher" />
                                        <span class="switch-toggle-slider">
												<span class="switch-on"></span>
												<span class="switch-off"></span>
											  </span>
                                    </label>
                                </h4>
                                <div class="mb-3 col-md-10">
                                    <p class="card-text">SePay is the most popular payment method in Asian countries.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
                        <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse" data-bs-target="#sepay" aria-expanded="false" aria-controls="sepay">
                            {{ __('Configure') }}
                        </button>
                    </div>
                </div>
                <form class="collapse" id="sepay">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="bg-lighter border rounded p-3 mb-3">
                                <span class="card-text">{{ __('Notification URL:') }} <code style="margin-left:3px; background: #e65e04;color: #ffffff;padding: 2px 4px;border-radius: 4px;font-size:90%">https://{{ $_SERVER['HTTP_HOST'] }}/api/payments/handle/sepay</code></span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="SePay_bank" class="form-label">
                                {{ __('SePay Bank Name') }}
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the Bank Name used for SePay Account.') }}"></i>
                            </label>
                            <input class="form-control" type="text" id="SePay_bank" name="bank" value="{{ $methods['SePay']['config']['bank'] }}" placeholder="Kielongbank">
                        </div>
                        <div class="col-md-6 mb-4 form-password-toggle">
                            <label for="SePay_bank_account" class="form-label">
                                {{ __('Bank Account') }}
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the Bank Account Number used for SePay Account.') }}"></i>
                            </label>
                            <input class="form-control" type="text" id="SePay_bank_account" name="bank_account" value="{{ $methods['SePay']['config']['bank_account'] }}" placeholder="101499100000040XXXXXXX">
                        </div>
                        <div class="col-md-6 mb-4 form-password-toggle">
                            <label for="SePay_bank_owner" class="form-label">
                                {{ __('Bank Account Owner') }}
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the Bank Account Owner.') }}"></i>
                            </label>
                            <input class="form-control" type="text" id="SePay_bank_owner" name="bank_owner" value="{{ $methods['SePay']['config']['bank_owner'] }}" placeholder="Nihao Kailan">
                        </div>
                        <div class="col-md-6 mb-4 form-password-toggle">
                            <label for="SePay_paycode_prefix" class="form-label">
                                {{ __('Pay Code Prefix') }}
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Custom prefix configured by you to scan order IDs. Example: MC') }}"></i>
                            </label>
                            <input class="form-control" type="text" id="SePay_paycode_prefix" name="paycode_prefix" value="{{ $methods['SePay']['config']['paycode_prefix'] }}" placeholder="MC">
                        </div>
                        <div class="col-md-12 mb-4 form-password-toggle">
                            <label for="SePay_webhook_apikey" class="form-label">
                                {{ __('Webhook Secret') }}
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Configure webhook and receive webhook API key secret and enter it in the field below.') }}"></i>
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="SePay_webhook_apikey" name="webhook_apikey" value="{{ $methods['SePay']['config']['webhook_apikey'] }}" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="SePay_webhook_apikey" />
                                <span id="SePay_webhook_apikey2" class="input-group-text cursor-pointer"><i class="bx bx-hide password-toggle-button"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="d-grid gap-2 col-lg-12 mx-auto">
                            <button class="btn btn-primary btn-lg save-button" type="button" data-name="sepay">
                                <span class="tf-icon bx bx-save bx-xs"></span>
                                {{ __('Save Changes') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- SePay Method (END) -->
    <!-- PhonePe Method -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="row d-flex w-100 align-self-center">
                    <div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
                        <div class="row align-self-center h-100">
                            <div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
                                <img src="{{asset('res/svg/payment_methods/phonepe.svg')}}" class="w-px-100">
                            </div>
                            <div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
                                <h4>
                                    PhonePe (India)
                                    <label for="PhonePe_enable" class="switch switch-square" style="margin-left: 10px;">
                                        <input id="PhonePe_enable" name="enable" data-name="PhonePe" {{ $methods['PhonePe']['enable'] == 1 ? 'checked' : '' }} type="checkbox" class="switch-input state-switcher" />
                                        <span class="switch-toggle-slider">
												<span class="switch-on"></span>
												<span class="switch-off"></span>
											  </span>
                                    </label>
                                </h4>
                                <div class="mb-3 col-md-10">
                                    <p class="card-text">PhonePe is one of the most accessible and popular payment methods in India.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
                        <button class="btn btn-primary me-1" type="button" data-bs-toggle="collapse" data-bs-target="#phonepe" aria-expanded="false" aria-controls="phonepe">
                            {{ __('Configure') }}
                        </button>
                    </div>
                </div>
                <form class="collapse" id="phonepe">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="bg-lighter border rounded p-3 mb-3">
                                <span class="card-text">{{ __('Notification URL:') }} <code style="margin-left:3px; background: #e65e04;color: #ffffff;padding: 2px 4px;border-radius: 4px;font-size:90%">https://{{ $_SERVER['HTTP_HOST'] }}/api/payments/handle/phonepe</code></span>
                            </div>
                        </div>
                        <div class="col-md-12 mb-4">
                            <label for="PhonePe_merchant_id" class="form-label">
                                {{ __('Merchant ID') }}
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter Production Merchant ID.') }}"></i>
                            </label>
                            <input class="form-control" type="text" id="PhonePe_merchant_id" name="merchant_id" value="{{ $methods['PhonePe']['config']['merchant_id'] }}" placeholder="000X0000XXXXX">
                        </div>
                        <div class="col-md-10 mb-4 form-password-toggle">
                            <label for="PhonePe_salt_key" class="form-label">
                                {{ __('Salt Key') }}
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the salt key for specified merchant ID.') }}"></i>
                            </label>
                            <div class="input-group">
                                <input class="form-control" type="password" id="PhonePe_salt_key" name="salt_key" value="{{ $methods['PhonePe']['config']['salt_key'] }}" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;">
                                <span id="PhonePe_salt_key2" class="input-group-text cursor-pointer"><i class="bx bx-hide password-toggle-button"></i></span>
                            </div>
                        </div>
                        <div class="col-md-2 mb-4">
                            <label for="PhonePe_salt_index" class="form-label">
                                {{ __('Salt Index') }}
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Enter the salt index for specified salt key (by default 1).') }}"></i>
                            </label>
                            <input class="form-control" type="text" id="PhonePe_salt_index" name="salt_index" value="{{ $methods['PhonePe']['config']['salt_index'] }}" placeholder="1">
                        </div>
                    </div>
                    <div class="row">
                        <div class="d-grid gap-2 col-lg-12 mx-auto">
                            <button class="btn btn-primary btn-lg save-button" type="button" data-name="phonepe">
                                <span class="tf-icon bx bx-save bx-xs"></span>
                                {{ __('Save Changes') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- PhonePe Method (END) -->
</div>
@endsection
