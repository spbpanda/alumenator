@extends('admin.layout')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('res/vendor/libs/bs-stepper/bs-stepper.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/tagify/tagify.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/sweetalert2/sweetalert2.css')}}">
@endsection

@section('vendor-script')
<script src="{{asset('res/vendor/libs/bs-stepper/bs-stepper.js')}}"></script>
<script src="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('res/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('res/vendor/libs/tagify/tagify.js')}}"></script>
<script src="{{asset('res/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('res/js/forms-selects.js')}}"></script>
<script src="{{ asset('js/modules/servers.js') }}"></script>
<script>
    $("#check-smtp").click(function(){
        serverSMTPCheck().done(function(r) {
            console.log(r)
            Swal.fire({
                title: "{{ __('Success') }}",
                text: "{{ __('SMTP server works properly!') }}",
                icon: 'success',
                timer: 4000,
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false,
            });
        }).fail(function(r) {
            console.log(r)
            Swal.fire({
                title: "{{ __('Error') }}",
                text: "{{ __('Failed to connect the SMTP server!') }}",
                icon: 'danger',
                timer: 4000,
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false,
            });
        });
    });
</script>
<script>
const tagifyBasicEl = document.querySelector('#maintenance_ips');
const TagifyBasic = new Tagify(tagifyBasicEl);
</script>
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

<form method="POST" autocomplete="off">
@csrf

<h4 class="fw-bold py-3 mb-1">
  <span class="text-body fw-light">{{ __('Email Settings') }}</span>
</h4>
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
									  <i class="bx bx-mail-send"></i>
								  </div>
								</div>
							</div>
							<div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
								<h4>
                                    {{ __('Enable Email Customers Notifications?') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('You need to setup SMTP settings to use this feature.') }}"></i>
								</h4>
								<div class="mb-3 col-md-10">
									<p class="card-text">{{ __('Your customers will receive notifications about purchases.') }}</p>
								</div>
							</div>
						</div>
					</div>
					<div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
						<span class="badge bg-primary" style="position: absolute; right: 15px; top: 10px;">{{ __('New Feature') }}</span>
						<label class="switch switch-square" for="smtp_enable">
						  <input type="checkbox" class="switch-input" id="smtp_enable" name="smtp_enable" {{ $settings->smtp_enable == 1 ? 'checked' : '' }} />
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
		  <div class="col-12 mb-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h4 class="text-body fw-light mb-0">
                        {{ __('SMTP/IMAP Settings') }}
                    </h4>
                </div>
                <div class="col-md-6 pt-4 pt-md-0 d-flex justify-content-end">
                    <button type="button" id="check-smtp" class="btn btn-primary btn-sm fs-6 d-flex align-items-center gap-1">
                        <span class="tf-icon bx bx-mail-send bx-xs"></span>
                        {{ __('Test an SMTP Settings') }}
                    </button>
                </div>
            </div>
		  </div>
		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-md-6">
						<label for="smtp_hostname" class="form-label">{{ __('SMTP Hostname') }}</label>
						<div class="input-group mb-2">
						  <input type="text" id="smtp_host" name="smtp_host" class="form-control" placeholder="mail.example.com" aria-label="SMTP Hostname" value="{{ $settings->smtp_host }}">
						</div>
					</div>
					<div class="col-md-6">
						<label for="smtp_hostname" class="form-label">{{ __('SMTP Port') }}</label>
						<div class="input-group mb-2">
						  <input type="text" id="smtp_port" name="smtp_port" class="form-control" placeholder="587" aria-label="SMTP Port" value="{{ $settings->smtp_port }}">
						</div>
					</div>
					<div class="col-md-6 mb-2">
						<label for="smtp_user" class="form-label">{{ __('SMTP User') }}</label>
						<input type="text" id="smtp_user" name="smtp_user" class="form-control" placeholder="no-reply" aria-label="SMTP User" value="{{ $settings->smtp_user }}">
					</div>
                    <div class="col-md-6 mb-2">
                        <label for="smtp_from" class="form-label">{{ __('From Address') }}</label>
                        <input type="text" id="smtp_from" name="smtp_from" class="form-control" placeholder="support@yourstore.com" aria-label="From Address" value="{{ $settings->smtp_from }}">
                    </div>
					<div class="col-md-12 mb-2 form-password-toggle">
						<label for="smtp_password" class="form-label">{{ __('SMTP Password') }}</label>
						<div class="input-group">
							<input type="password" class="form-control" id="smtp_pass" name="smtp_pass" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" value="{{ $smtp_pass }}" />
							<span id="smtp_pass_btn" class="input-group-text cursor-pointer"><i class="bx bx-hide password-toggle-button"></i></span>
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
