@extends('admin.layout')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('res/vendor/libs/bs-stepper/bs-stepper.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/animate-css/animate.css')}}">
<link rel="stylesheet" href="{{asset('res/vendor/libs/bs-stepper/bs-stepper.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/select2/select2.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('res/vendor/libs/bs-stepper/bs-stepper.js')}}"></script>
<script src="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('res/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('res/vendor/libs/moment/moment.js')}}"></script>
<script src="{{asset('res/vendor/libs/bs-stepper/bs-stepper.js')}}"></script>
<script src="{{asset('res/vendor/libs/select2/select2.js')}}"></script>

@endsection

@section('page-script')
<script src="{{asset('res/js/forms-selects.js')}}"></script>
<script src="{{asset('res/js/forms-extras.js')}}"></script>
<script src="//unpkg.com/alpinejs" defer></script>
<script src="{{asset('js/modules/servers.js')}}"></script>
<script>
const verticalStepper = (function() {
    let verticalStepper = null;
    const wizardVertical = document.querySelector("#wizard-create-deal"),
        wizardVerticalBtnNext = wizardVertical.querySelectorAll(".btn-next")[0],
        wizardVerticalBtnPrevList = [].slice.call(wizardVertical.querySelectorAll(".btn-prev")),
        wizardVerticalBtnSubmit = wizardVertical.querySelector(".btn-submit");

    if (typeof wizardVertical !== undefined && wizardVertical !== null) {
        verticalStepper = new Stepper(wizardVertical, {
            linear: false
        });
        wizardVerticalBtnNext.addEventListener("click", event => {
            verticalStepper.next();
        });
        if (wizardVerticalBtnPrevList) {
            wizardVerticalBtnPrevList.forEach(wizardVerticalBtnPrev => {
                wizardVerticalBtnPrev.addEventListener("click", event => {
                    verticalStepper.previous();
                });
            });
        }
    }
    return verticalStepper;
})();
</script>
@endsection

@section('content')

<h4 class="fw-bold py-3 mb-1">
  <span class="text-body fw-light">{{ __('Connect a New Minecraft Server') }}
  </span>
</h4>
<!-- Create Server Wizard -->
<div x-data="{ deliveryType: 'plugin', name: '', secret_key: '', rconHost: '', rconPort: '', password: '' }">
	<div id="wizard-create-deal" class="bs-stepper vertical mt-2 mb-4">
	  <div class="bs-stepper-header">
		<div class="step" data-target="#delivery-method">
		  <button type="button" class="step-trigger">
			<span class="bs-stepper-circle">
			  <i class='bx bx-package'></i>
			</span>
			<span class="bs-stepper-label">
			  <span class="bs-stepper-title">{{ __('Delivery Method') }}</span>
			  <span class="bs-stepper-subtitle">{{ __('Choose Delivery Method.') }}</span>
			</span>
		  </button>
		</div>
		<div class="line"></div>
		<div class="step" data-target="#server-details">
		  <button type="button" class="step-trigger">
			<span class="bs-stepper-circle">
			  <i class='bx bx-cog'></i>
			</span>
			<span class="bs-stepper-label">
			  <span class="bs-stepper-title">{{ __('Server Details') }}</span>
			  <span class="bs-stepper-subtitle">{{ __('Configure Server Details.') }}</span>
			</span>
		  </button>
		</div>
		<div class="line"></div>
		<div class="step" data-target="#instruction">
		  <button type="button" class="step-trigger">
			<span class="bs-stepper-circle">
			  <i class='bx bx-download'></i>
			</span>
			<span class="bs-stepper-label">
			  <span class="bs-stepper-title">{{ __('Instruction') }}</span>
			  <span class="bs-stepper-subtitle">{{ __('Almost done. Few steps left.') }}</span>
			</span>
		  </button>
		</div>
	  </div>
	  <div class="bs-stepper-content">
		<div id="wizard-create-deal-form">
		  <!-- Delivery method -->
		  <div id="delivery-method" class="content">
			<div class="row g-3">
			  <div class="col-12">
				<div class="row">
				  <div class="col-md mb-md-4 mb-4">
					<div class="form-check custom-option custom-option-icon">
					  <label class="form-check-label custom-option-content" for="radioPlugin">
						<span class="custom-option-body">
						  <i class='bx bxs-component bx-lg'></i>
						  <span class="custom-option-title"> {{ __('Plugin') }} </span>
								  <span class="badge bg-primary">{{ __('Recommended') }}</span>
								  <br>
						  <small>{{ __('Execute commands by using Official Minecraft plugin for MineStoreCMS.') }}</small>
						</span>
						<input name="method" value="plugin" class="form-check-input" type="radio" id="radioPlugin" x-model="deliveryType" checked />
					  </label>
					</div>
				  </div>
				  <div class="col-md mb-md-4 mb-4">
					<div class="form-check custom-option custom-option-icon">
					  <label class="form-check-label custom-option-content" for="radioRCON">
						<span class="custom-option-body">
						  <i class='bx bx-terminal bx-lg'></i>
						  <span class="custom-option-title"> RCON </span>
								  <span class="badge bg-secondary">{{ __('Outdated') }}</span>
								  <br>
						  <small>{{ __('Execute commands on your server by using RCON.') }}</small>
						</span>
						<input name="method" value="rcon" class="form-check-input" type="radio" x-model="deliveryType" id="radioRCON" />
					  </label>
					</div>
				  </div>
				</div>
			  </div>
			  <div class="col-12 d-flex justify-content-between mt-4">
				<button class="btn btn-primary btn-next" type="button">
				  <span class="align-middle d-sm-inline-block d-none me-sm-1">{{ __('Next') }}</span>
				  <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
				</button>
			  </div>
			</div>
		  </div>
		  <!-- General Configuration -->
		  <div id="server-details" class="content">
			<template x-if="deliveryType === 'plugin'">
				<div class="row g-3">
				  <div class="col-sm-12">
					<label class="form-label" for="serverName">{{ __('Server Name') }}</label>
					<input autocomplete="false" type="text" id="serverName" name="name" x-model="name" class="form-control" placeholder="LifeSteal Server" />
				  </div>
				  <div class="col-sm-12 mb-4">
					<label class="form-label" for="serverSecret">{{ __('Server Secret Token') }}</label>
                      <div x-data="{ showPassword: false }">
                          <div class="input-group form-password-toggle">
                                <input autocomplete="false" :type="showPassword ? 'text' : 'password'" name="secret_key" x-model="$store.secret_key" class="form-control" id="serverSecret" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="serverSecret2" />
                                <span @click="refreshKey" class="input-group-text cursor-pointer"><i class="bx bx-refresh"></i></span>
                                <span @click="showPassword = !showPassword" class="input-group-text cursor-pointer">
                                    <i x-show="!showPassword" class="bx bx-show password-toggle-button"></i>
                                    <i x-show="showPassword" class="bx bx-hide password-toggle-button"></i>
                                </span>
                          </div>
                      </div>
				  </div>
				</div>
			</template>
			<template x-if="deliveryType === 'rcon'">
				<div class="row g-3">
				  <div class="col-sm-12">
					<label class="form-label" for="serverName">{{ __('Server Name') }}</label>
					<input autocomplete="false" type="text" id="serverName" name="name" x-model="name" class="form-control" placeholder="Survival Server" />
				  </div>
                  <div class="col-sm-6">
                    <label class="form-label" for="rconHost">{{ __('RCON Host IP') }}</label>
                    <input autocomplete="false" type="text" id="rconHost" name="rconHost" x-model="rconHost" class="form-control" placeholder="127.0.0.1" />
                  </div>
                  <div class="col-sm-6">
                    <label class="form-label" for="rconHost">{{ __('Port for RCON') }}</label>
                    <input autocomplete="false" type="text" id="rconPort" name="rconPort" x-model="rconPort" class="form-control" placeholder="8076" />
                  </div>
				  <div class="col-sm-12 mb-4">
					<label class="form-label" for="serverSecret">{{ __('RCON Password') }}</label>
                    <div x-data=" { showPassword: false }">
                      <div class="input-group form-password-toggle">
                        <input autocomplete="false" :type="showPassword ? 'text' : 'password'" name="password" x-model="$store.password" class="form-control" id="serverPassword" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="serverSecret2" />
                        <span onclick="refreshPassword(event)" class="input-group-text cursor-pointer"><i class="bx bx-refresh"></i></span>
                          <span @click="showPassword = !showPassword" class="input-group-text cursor-pointer">
                                    <i x-show="!showPassword" class="bx bx-show password-toggle-button"></i>
                                    <i x-show="showPassword" class="bx bx-hide password-toggle-button"></i>
                          </span>
                      </div>
                    </div>
				  </div>
                </div>
			</template>
				  <div class="col-12 d-flex justify-content-between mt-4">
					<button class="btn btn-label-secondary btn-prev" type="button"> <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
					  <span class="align-middle d-sm-inline-block d-none">{{ __('Previous') }}</span>
					</button>
					<button class="btn btn-primary btn-next" type="button" @click="createEvent" id="btn-save">
                        <span class="align-middle d-sm-inline-block d-none me-sm-1">{{ __('Next') }}</span>
                        <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
					</button>
				  </div>
				</div>
		  </div>
		  <!-- Instruction -->
		  <div id="instruction" class="content">
			<div class="row g-3">

			 <div class="col-12 mb-0">
			<h3>{{ __('Almost done!') }} 🚀</h3>
			<p>{{ __('Few things left. We tried to make the setup process as easy as possible. So this commands will fill your plugin config but if you want to change something manually, edit your config.') }}</p>
		  </div>
			<template x-if="deliveryType === 'plugin'">
			  <div class="col-lg-12">
				<div class="row">
					<div class="col-sm-12 mb-2">
						<div class="bg-lighter border rounded p-3 mb-3">
							<span class="switch-label mb-2">{{ __('1. Download the MineStoreCMS Official Plugin for your Minecraft Server:') }}</span>
							<div class="row">
								<div class="col-md-8">
									<a class="btn btn-sm btn-primary" href="https://minestorecms.com/plugin" style="margin-right: 5px;"><span class="tf-icons bx bxs-download"></span> Spigot / Bukkit Plugin</a>
									<a class="btn btn-sm btn-primary" href="https://minestorecms.com/plugin" style="margin-right: 5px;"><span class="tf-icons bx bxs-download"></span> Sponge Plugin</a>
									<a class="btn btn-sm btn-primary" href="https://minestorecms.com/plugin" style="margin-right: 5px;"><span class="tf-icons bx bxs-download"></span> BungeeCord Plugin</a>
									<a class="btn btn-sm btn-primary" href="https://minestorecms.com/plugin" style="margin-right: 5px;"><span class="tf-icons bx bxs-download"></span> Velocity Plugin</a>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-12 mb-2">
						<div class="bg-lighter border rounded p-3 mb-1">
							<span class="switch-label mb-2">{{ __('2. Install the plugin into your plugin path and restart your Minecraft Server.') }}</span>
						</div>
					</div>
					<div class="col-sm-12">
						<div class="bg-lighter border rounded p-3 mb-3">
							<span class="switch-label mb-2">{{ __('3. Copy and paste the next command into your Minecraft Server Console:') }}</span>
							<div class="row">
								<div class="col-md-12">
									<div class="input-group">
                                        <code type="text" readonly class="form-control" x-html="`ms autosetup 'https://{{ $_SERVER['HTTP_HOST'] }}' {{ $apiKey }} ${$store.secret_key}`"></code>
                                    </div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-12">
						<div class="bg-lighter border rounded p-3 mb-3">
							<span class="switch-label mb-2">{{ __('4. Press this button to check connection with your plugin:') }}</span>
							<div class="row">
								<div class="col-md-8">
									<button type="button" class="btn btn-primary" @click="checkEvent" style="margin-right: 5px;"><span class="tf-icons bx bx-refresh"></span> {{ __('Test Plugin Connection') }}</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			  </div>
			</template>
			<template x-if="deliveryType === 'rcon'">
			  <div class="col-lg-12">
				<div class="row">
					<div class="col-sm-12 mb-2">
						<div class="bg-lighter border rounded p-3 mb-1">
							<span class="switch-label">{{ __('1. Configure file') }} <code>server.properties</code> {{ __('according to your settings.') }}</span>
						</div>
					</div>
					<div class="col-sm-12">
						<div class="bg-lighter border rounded p-3 mb-3">
							<span class="switch-label">{{ __('2. Save the file and restart your Minecraft Server.') }}</span>
						</div>
					</div>
					<div class="col-sm-12">
						<div class="bg-lighter border rounded p-3 mb-3">
							<span class="switch-label mb-2">{{ __('3. Press this button to check connection with your webstore and RCON:') }}</span>
							<div class="row">
								<div class="col-md-8">
									<button type="button" class="btn btn-primary" @click="checkEvent" style="margin-right: 5px;"><span class="tf-icons bx bx-refresh"></span> {{ __('Test RCON Connection') }}</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			  </div>
			</template>
			  <div class="col-12 d-flex justify-content-between">
				<button class="btn btn-primary btn-prev" type="button"> <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
				  <span class="align-middle d-sm-inline-block d-none">{{ __('Previous') }}</span>
				</button>
				<a href="{{ route('settings.servers.index') }}" class="btn btn-success btn-submit btn-next" type="submit">{{ __('Finish') }}</a>
			  </div>
			</div>
		  </div>
		</div>
	  </div>
	</div>
</div>

<script>

	document.addEventListener('alpine:init', () => {
		Alpine.store('secret_key', '');
	})

    function refreshKey() {
        event.preventDefault();

        let newPassword = generatePassword();

        // Set the new value
        $('#serverSecret').val(newPassword);

        // Trigger a change event
        Alpine.store('secret_key', newPassword);
    }

    function refreshPassword() {
        event.preventDefault();

        let newPassword = generatePassword();

        // Set the new value
        $('#serverPassword').val(newPassword);

        // Trigger a change event
        Alpine.store('password', newPassword);
    }

    function generatePassword() {
        let chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
        let pass = "";
        const length = 20;
        for (let x = 0; x < length; x++) {
            let i = Math.floor(Math.random() * chars.length);
            pass += chars.charAt(i);
        }

        return pass;
    }
</script>

<script>
    let serverId;

    function createEvent(event) {
        const name = this.name;
        const secret_key = Alpine.store('secret_key');
        const rconHost = this.rconHost;
        const rconPort = this.rconPort;
        const password = Alpine.store('password');

        if (this.deliveryType === "plugin") {
            serverSavePlugin(name, secret_key).done(function(r) {
                serverId = r.id;
                verticalStepper.next();
            }).fail(function(r) {
                if (r.status === 410) {
                    // show validate error
                } else {
                    toastr.error("{{ __('Unable to Save a Minecraft Server!') }}");
                }
            });
        } else {
            serverSaveRCON(name, rconHost, rconPort, password).done(function(r) {
                serverId = r.id;
                verticalStepper.next();
            }).fail(function(r) {
                if (r.status === 410) {
                    // show validate error
                } else {
                    toastr.error("{{ __('Unable to Save a Minecraft Server!') }}");
                }
            });
        }
    }

    function checkEvent(){
        serverCheck(serverId).done(function(r) {
            toastr.success("{{ __('Connection Successful!') }}");
        }).fail(function(r) {
            toastr.error("{{ __('Failed Connection to the Minecraft Server!') }}");
        });
    }
</script>
@endsection
