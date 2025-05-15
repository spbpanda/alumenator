@php
    $customizerHidden = 'customizer-hide';
    $configData = [
      'layout' => 'vertical',
      'theme' => 'theme-default',
      'style' => 'dark',
      'rtlSupport' => true,
      'rtlMode' => true,
      'textDirection' => true,
      'menuCollapsed' => false,
      'hasCustomizer' => false,
      'colors' => '#fb6604',
    ];
@endphp

@section('title', 'Installation')

@extends('admin.blankLayout')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('res/vendor/libs/bs-stepper/bs-stepper.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/select2/select2.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/spinkit/spinkit.css')}}" />
@endsection

@section('page-style')
    <link rel="stylesheet" href="{{asset('res/vendor/css/pages/page-auth.css')}}">
    <style>
        .hidden {
            display: none!important;
        }
    </style>
@endsection

@section('vendor-script')
    <script src="{{asset('res/vendor/libs/cleavejs/cleave.js')}}"></script>
    <script src="{{asset('res/vendor/libs/cleavejs/cleave-phone.js')}}"></script>
    <script src="{{asset('res/vendor/libs/bs-stepper/bs-stepper.js')}}"></script>
    <script src="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
    <script src="{{asset('res/vendor/libs/select2/select2.js')}}"></script>
@endsection

@section('page-script')
    <script src="{{asset('res/js/form-wizard-icons.js')}}"></script>
    <script>
        function loadPreview(e, elementId) {
            const filePreviewElement = document.querySelector('#preview-'+elementId);
            filePreviewElement.src = URL.createObjectURL(e.currentTarget.files[0]);
        }
        function clearImage(elementId) {
            document.getElementById('preview-'+elementId).src = "";
            document.getElementById(elementId).value = null;
        }
    </script>
    <script>
        const passwordInput = document.getElementById('database_password');
        const toggleButton = document.getElementById('toggle_password');

        toggleButton.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';

            passwordInput.setAttribute('type', type);

            if (type === 'password') {
                toggleButton.innerHTML = '<i class="bx bx-hide"></i>';
            } else {
                toggleButton.innerHTML = '<i class="bx bx-show"></i>';
            }
        });
    </script>
    <script>
        const toggleButtons = document.querySelectorAll('.toggle_password');

        toggleButtons.forEach(function(toggleButton) {
            toggleButton.addEventListener('click', function() {

                const targetId = toggleButton.getAttribute('data-target');
                const targetInput = document.getElementById(targetId);

                const type = targetInput.getAttribute('type') === 'password' ? 'text' : 'password';
                targetInput.setAttribute('type', type);

                if (type === 'password') {
                    toggleButton.innerHTML = '<i class="bx bx-hide"></i>';
                } else {
                    toggleButton.innerHTML = '<i class="bx bx-show"></i>';
                }
            });
        });
    </script>
    <script>
        const form = document.querySelector("#installForm");
        const btn = document.querySelector("#installForm_submit");
        const btnStatusText = document.querySelector("#installForm_status");
        const btnLoader = document.querySelector("#installForm_loader");

        function toggleAllInputs(disabled) {
            const inputs = form.querySelectorAll("input");
            inputs.forEach((input) => {
                input.disabled = disabled;
            });
        }

        const installForm = async (e) => {
            const formData = new FormData(form);
            e.preventDefault();
            btn.disabled = true;
            btnStatusText.textContent = "Installing...";
            btnLoader.classList.remove("hidden");

            try {
                toggleAllInputs(true);

                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

                const response = await fetch("/initiateInstallation", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    body: formData,
                });
                const res = await response.json();
                console.log('res', res);
                if (res.status === "OK") {
                    console.log("Installation successful!");
                    $("#startInstall").addClass("hidden");
                    $("#installationSuccess").removeClass("hidden");
                } else {
                    btn.disabled = false;
                    btn.innerHTML = "Finish Installation";
                    $("#startInstall").addClass("hidden");
                    $("#installationError").removeClass("hidden");
                    $("#errorInstallJson").text(JSON.stringify(res, null, 2));
                }
            } catch (error) {
                console.log('error', error);
            } finally {
                toggleAllInputs(false);
                btn.disabled = false;
                btnLoader.classList.add("hidden");
                btnStatusText.textContent = "Finish Installation";
            }
        };
        form.addEventListener("submit", installForm);

        const backToInstall = document.querySelector(".js-back-to-install");
        backToInstall.addEventListener("click", () => {
            $("#installationError").addClass("hidden");
            const stepper = new Stepper(document.querySelector(".bs-stepper"), {
                linear: true,
                animation: true,
            });
            stepper.to(0);

            $("#startInstall").removeClass("hidden");
        });

        const btnPreInstall = document.querySelector("#preInstall_submit");
        btnPreInstall.addEventListener("click", async () => {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const formData = new FormData();
            formData.append('preInstall_licenseKey', document.querySelector('#preInstall_licenseKey').value);
            const response = await fetch("/install/changeLicenseKey", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: formData,
            });
            const res = await response.json();
        });

        const btnUpdateKey = document.querySelector("#licenseKey_submit");
        btnUpdateKey.addEventListener("click", async () => {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const formData = new FormData();
            formData.append('preInstall_licenseKey', document.querySelector('#preInstall_licenseKey').value);
            const response = await fetch("/install/changeLicenseKey", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: formData,
            });
            const res = await response.json();

            location.reload();
        });
    </script>
    <style>
        body {
            overflow-x: hidden;
        }
    </style>
@endsection

@section('content')
    <!-- Installation Wizard -->
    <div class="row">
        <!-- Logo -->
        <div class="app-brand justify-content-center pt-5">
          <span class="app-brand-logo demo">
            <img class="app-brand-logo demo" style="height: 50px;" src="/res/img/logo-{{ $configData['style'] == 'dark' ? 'white' : 'colored' }}.png" alt="MineStoreCMS Logo">
          </span>
        </div>
    </div>
    <div class="row d-flex align-items-center justify-content-center" id="startInstall">
        <div class="col-lg-9 authentication-bg pt-3 p-4">
            <div class="bs-stepper wizard-vertical vertical wizard-vertical-icons-example mt-2">
                <div class="bs-stepper-header">
                    <div class="step" data-target="#preInstall">
                        <button type="button" class="step-trigger">
                            <span class="bs-stepper-circle">
                              <i class="bx bx-basket"></i>
                            </span>
                            <span class="bs-stepper-label mt-1">
                              <span class="bs-stepper-title">{{ __('System diagnosis') }}</span>
                              <span class="bs-stepper-subtitle">{{ __('Checking requirements') }}</span>
                            </span>
                        </button>
                    </div>
                    <div class="line"></div>
                    <div class="step" data-target="#webstoreSettings">
                        <button type="button" class="step-trigger">
                            <span class="bs-stepper-circle">
                              <i class="bx bx-basket"></i>
                            </span>
                            <span class="bs-stepper-label mt-1">
                              <span class="bs-stepper-title">{{ __('Webstore Settings') }}</span>
                              <span class="bs-stepper-subtitle">{{ __('General Webstore Details') }}</span>
                            </span>
                        </button>
                    </div>
                    <div class="line"></div>
                    <div class="step" data-target="#monitoring">
                        <button type="button" class="step-trigger">
                            <span class="bs-stepper-circle">
                              <i class="bx bx-server"></i>
                            </span>
                            <span class="bs-stepper-label mt-1">
                              <span class="bs-stepper-title">{{ __('Monitoring') }}</span>
                              <span class="bs-stepper-subtitle">{{ __('Minecraft & Discord for Monitoring') }}</span>
                            </span>
                        </button>
                    </div>
                    <div class="line"></div>
                    <div class="step" data-target="#database">
                        <button type="button" class="step-trigger">
                            <span class="bs-stepper-circle">
                              <i class="bx bx-data"></i>
                            </span>
                            <span class="bs-stepper-label mt-1">
                              <span class="bs-stepper-title">{{ __('Database') }}</span>
                              <span class="bs-stepper-subtitle">{{ __('Configure Database Credentials') }}</span>
                            </span>
                        </button>
                    </div>
                    <div class="line"></div>
                    <div class="step" data-target="#adminAccount">
                        <button type="button" class="step-trigger">
                            <span class="bs-stepper-circle">
                              <i class="bx bx-user"></i>
                            </span>
                            <span class="bs-stepper-label mt-1">
                              <span class="bs-stepper-title">{{ __('Admin User') }}</span>
                              <span class="bs-stepper-subtitle">{{ __('Set Up an Admin Account') }}</span>
                            </span>
                        </button>
                    </div>
                </div>
                <div class="bs-stepper-content">
                    <form id="installForm" method="post" enctype="multipart/form-data">
                        @csrf
                        <!-- preInstall -->
                        <div id="preInstall" class="content">
                            <div class="content-header mb-3">
                                <h6 class="mb-0">{{ __('MineStoreCMS license key') }}</h6>
                                <small>{{ __('Enter Your MineStoreCMS license key') }}.</small>
                            </div>
                            <div class="row g-3">
                                <div class="col-sm-9">
                                    <label class="form-label" for="preInstall_licenseKey">{{ __('License key') }}</label>
                                    <input type="text" id="preInstall_licenseKey" name="preInstall_licenseKey" class="form-control" placeholder="License Key" value="{{ config('app.LICENSE_KEY') }}" />
                                    @error('preInstall_licenseKey')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-sm-3" style="margin-top: auto">
                                    <button type="button" class="btn btn-primary w-100" id="licenseKey_submit">
                                        <span class="align-middle d-sm-inline-block d-none me-sm-1">{{ __('Update Key') }}</span>
                                    </button>
                                </div>
                                <div class="col-sm-12">
                                    <label class="form-label">License Key</label>
                                    @if($isLicenseValid)
                                        <span class="badge rounded-pill bg-label-success" style="margin-left: 10px;">VALID</span>
                                    @else
                                        <span class="badge rounded-pill bg-label-danger" style="margin-left: 10px;">INVALID</span><br>
                                    @endif
                                </div>
                                <div class="col-sm-12">
                                    <label class="form-label">{{ __('PHP Timezone Extension status') }}</label>
                                    @if($isTimezone)
                                        <span class="badge rounded-pill bg-label-success" style="margin-left: 10px;">OK</span>
                                    @else
                                        <span class="badge rounded-pill bg-label-danger" style="margin-left: 10px;">ERROR</span>
                                    @endif
                                </div>
                                <div class="col-sm-12">
                                    <label class="form-label">{{ __('PHP8.2-FPM status') }}</label>
                                    @if($isFPM)
                                        <span class="badge rounded-pill bg-label-success" style="margin-left: 10px;">OK</span>
                                    @else
                                        <span class="badge rounded-pill bg-label-danger" style="margin-left: 10px;">ERROR</span>
                                    @endif
                                </div>
                                <div class="col-sm-12">
                                    <label class="form-label">{{ __('PHP version 8.2 status') }}</label>
                                    @if($versionUse)
                                        <span class="badge rounded-pill bg-label-success" style="margin-left: 10px;">OK</span>
                                    @else
                                        <span class="badge rounded-pill bg-label-danger" style="margin-left: 10px;">Your webserver currently using {{ PHP_VERSION }} for this website but need using PHP-FPM-8.2!</span><br>
                                    @endif
                                </div>
                                <div class="col-12 d-flex justify-content-between">
                                    <button type="button" class="btn btn-label-secondary btn-prev" disabled>
                                        <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                        <span class="align-middle d-sm-inline-block d-none">{{ __('Previous') }}</span>
                                    </button>
                                    <button type="button" class="btn btn-primary btn-next" id="preInstall_submit" @if(!$isLicenseValid || !$versionUse || !$isFPM || !$isTimezone) disabled @endif>
                                        <span class="align-middle d-sm-inline-block d-none me-sm-1">{{ __('Next') }}</span>
                                        <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- Webstore Settings -->
                        <div id="webstoreSettings" class="content">
                                <div class="content-header mb-3">
                                    <h6 class="mb-0">{{ __('Webstore Settings') }}</h6>
                                    <small>{{ __('Configure and Customize Various Aspects of Your Webstore.') }}</small>
                                </div>
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <label class="form-label" for="webstore_name">{{ __('Webstore Name') }}</label>
                                        <input type="text" id="webstore_name" name="webstore_name" class="form-control" placeholder="{{ __('My Amazing Store') }}" />
                                        @error('webstore_name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="webstore_description">{{ __('Webstore Description') }}</label>
                                        <input type="text" id="webstore_description" name="webstore_description" class="form-control" placeholder="{{ __('Description for My Amazing Store') }}" aria-label="Description for My Amazing Store"  />
                                        @error('webstore_description')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-sm-12">
                                        <label class="form-label" for="webstore_currency">{{ __('Currency') }}</label>
                                        <select id="select2Currency" name="webstore_currency" class="select2 form-select form-select-lg" data-allow-clear="true" >
                                            @foreach($currencies as $currency)
                                                <option value="{{ $currency->name }}">{{ $currency->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('webstore_currency')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-sm-12">
                                        <label class="form-label" for="webstore_language">{{ __('Language') }}</label>
                                        <select id="select2Language" name="webstore_language" class="select2 form-select form-select-lg" data-allow-clear="true"  >
                                            @foreach($languages as $language_code => $language_name)
                                                <option value="{{ $language_code }}">{{ $language_name }}</option>
                                            @endforeach
                                        </select>
                                        @error('webstore_language')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="align-self-center text-center mx-auto">
                                            <img src="{{ asset('/res/img/question-icon.png') }}" alt="Image" id="preview-icon" class="rounded mb-2" height="150" width="145" id="uploadedAvatar" />
                                            <div class="button-wrapper">
                                                <label for="icon" class="btn btn-primary me-2 mt-2 mb-2" tabindex="0">
                                                    <span class="d-none d-sm-block">{{ __('Upload Logo') }}</span>
                                                    <i class="bx bx-upload d-block d-sm-none"></i>
                                                    <input type="file" id="icon" name="webstore_logo" onchange="loadPreview(event, 'icon')" class="account-file-input" hidden accept="image/png, image/jpeg, image/gif" />
                                                </label>

                                                <p class="text-muted mb-0">{{ __('Allowed PNG, JPG, GIF') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-between">
                                        <button type="button" class="btn btn-primary btn-prev">
                                            <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                            <span class="align-middle d-sm-inline-block d-none">{{ __('Previous') }}</span>
                                        </button>
                                        <button type="button" class="btn btn-primary btn-next">
                                            <span class="align-middle d-sm-inline-block d-none me-sm-1">{{ __('Next') }}</span>
                                            <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                                        </button>
                                    </div>
                                </div>
                        </div>
                        <!-- Monitoring -->
                        <div id="monitoring" class="content">
                                <div class="content-header mb-3">
                                    <h6 class="mb-0">{{ __('Monitoring Details') }}</h6>
                                    <small>{{ __('Enter and Configure Setting to Display Minecraft & Discord Server Monitoring.') }}</small>
                                </div>
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <label class="form-label" for="webstore_serverIP">{{ __('Minecraft Server IP') }}</label>
                                        <input type="text" id="webstore_serverIP" name="webstore_serverIP" class="form-control" placeholder="mc.amazingserver.net"  />
                                        @error('webstore_serverIP')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="webstore_serverPort">{{ __('Minecraft Server Port') }}</label>
                                        <input type="text" id="webstore_serverPort" name="webstore_serverPort" class="form-control" placeholder="25565" />
                                        @error('webstore_serverPort')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <hr class="mb-0">
                                    <div class="col-sm-6">
                                        <label class="form-label" for="webstore_discordServerID">{{ __('Discord Server ID') }}</label>
                                        <input type="text" id="webstore_discordServerID" name="webstore_discordServerID" class="form-control" placeholder="676904052855930899" />
                                        @error('webstore_discordServerID')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="webstore_discordInviteLink">{{ __('Discord Invite Link') }}</label>
                                        <input type="text" id="webstore_discordInviteLink" name="webstore_discordInviteLink" class="form-control" placeholder="https://discord.gg/amazingServer" />
                                        @error('webstore_discordInviteLink')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-sm-12">
                                        <label class="form-label" for="webstore_discordWebhookURL">{{ __('Discord Webhook URL') }}</label>
                                        <input type="text" id="webstore_discordWebhookURL" name="webstore_discordWebhookURL" class="form-control" placeholder="https://discord.com/api/webhooks/webhook_URL" />
                                        @error('webstore_discordWebhookURL')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <hr class="mb-0">
                                    <div class="form-check form-switch mb-2" style="margin-left:10px;">
                                        <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" name="webstore_shareMetrics" checked>
                                        <label class="form-check-label" for="flexSwitchCheckChecked">{{ __('Do You Want To Share Metrics with MineStoreCMS Developers?') }}</label>
                                    </div>
                                    <div class="col-12 d-flex justify-content-between">
                                        <button type="button" class="btn btn-primary btn-prev">
                                            <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                            <span class="align-middle d-sm-inline-block d-none">{{ __('Previous') }}</span>
                                        </button>
                                        <button type="button" class="btn btn-primary btn-next">
                                            <span class="align-middle d-sm-inline-block d-none me-sm-1">{{ __('Next') }}</span>
                                            <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                                        </button>
                                    </div>
                                </div>
                        </div>
                        <!-- Database -->
                        <div id="database" class="content">
                                <div class="content-header mb-3">
                                    <h6 class="mb-0">{{ __('Database') }}</h6>
                                    <small>{{ __('Set Up Database Credentials for MineStoreCMS') }}</small>
                                </div>
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <label class="form-label" for="database_host">{{ __('Database Host') }} *</label>
                                        <input type="text" id="database_host" name="DB_HOST" class="form-control" placeholder="127.0.0.1" value="{{ !empty(config('database.connections.mysql.host')) ? config('database.connections.mysql.host') : '127.0.0.1' }}" />
                                        @error('database_host')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="database_port">{{ __('Database Port') }} *</label>
                                        <input type="text" id="database_port" name="DB_PORT" class="form-control" placeholder="3306" value="{{ !empty(config('database.connections.mysql.port')) ? config('database.connections.mysql.port') : '3306' }}" />
                                        @error('database_port')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-sm-12">
                                        <label class="form-label" for="database_name">{{ __('Database Name') }} *</label>
                                        <input type="text" id="database_name" name="DB_DATABASE" class="form-control" placeholder="minestore" value="{{ !empty(config('database.connections.mysql.database')) ? config('database.connections.mysql.database') : 'minestore' }}" />
                                        @error('database_name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-sm-12">
                                        <label class="form-label" for="database_user">{{ __('Database User') }} *</label>
                                        <input type="text" id="database_user" name="DB_USERNAME" class="form-control" placeholder="minestore" value="{{ !empty(config('database.connections.mysql.username')) ? config('database.connections.mysql.username') : '' }}" />
                                        @error('database_user')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-password-toggle">
                                            <label class="form-label" for="database_password">{{ __('Database User Password') }} *</label>
                                            <div class="input-group input-group-merge">
                                                <input type="password" class="form-control" id="database_password" name="DB_PASSWORD" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="database_password" value="{{ !empty(config('database.connections.mysql.password')) ? config('database.connections.mysql.password') : '' }}" />
                                                <span class="input-group-text cursor-pointer" id="toggle_password">
                                                <i class="bx bx-hide"></i>
                                            </span>
                                            </div>
                                        </div>
                                        @error('database_password')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-12 d-flex justify-content-between">
                                        <button type="button" class="btn btn-primary btn-prev">
                                            <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                            <span class="align-middle d-sm-inline-block d-none">{{ __('Previous') }}</span>
                                        </button>
                                        <button type="button" class="btn btn-primary btn-next">
                                            <span class="align-middle d-sm-inline-block d-none me-sm-1">{{ __('Next') }}</span>
                                            <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                                        </button>
                                    </div>
                                </div>
                        </div>
                        <!-- Admin Account -->
                        <div id="adminAccount" class="content">
                                <div class="content-header mb-3">
                                    <h6 class="mb-0">{{ __('Admin User') }}</h6>
                                    <small>{{ __('Enter Your Admin Account Settings') }}.</small>
                                </div>
                                <div class="row g-3">
                                    <div class="col-sm-12">
                                        <label class="form-label" for="admin_username">{{ __('Username') }} *</label>
                                        <input type="text" id="admin_username" name="admin_username" class="form-control" placeholder="admin" />
                                        @error('admin_username')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-password-toggle">
                                            <label class="form-label" for="admin_password">{{ __('Password') }} *</label>
                                            <div class="input-group input-group-merge">
                                                <input type="password" class="form-control" id="admin_password" name="admin_password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="admin_password"   />
                                                <span class="input-group-text cursor-pointer toggle_password" data-target="admin_password">
                                                    <i class="bx bx-hide"></i>
                                                </span>
                                            </div>
                                            @error('admin_password')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-password-toggle">
                                            <label class="form-label" for="admin_passwordConfirm">{{ __('Confirm Password') }} *</label>
                                            <div class="input-group input-group-merge">
                                                <input type="password" class="form-control" id="admin_password_confirmation" name="admin_password_confirmation" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="admin_passwordConfirm"   />
                                                <span class="input-group-text cursor-pointer toggle_password" data-target="admin_password_confirmation">
                                                    <i class="bx bx-hide"></i>
                                                </span>
                                            </div>
                                            @error('admin_passwordConfirm')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-between">
                                        <button type="button" class="btn btn-primary btn-prev">
                                            <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                            <span class="align-middle d-sm-inline-block d-none">{{ __('Previous') }}</span>
                                        </button>
                                        <button class="btn btn-primary btn-submit" type="submit" id="installForm_submit" value="submit">
                                            <span class="spinner-border me-1 hidden" id="installForm_loader" role="status" aria-hidden="true"></span>
                                            <span id="installForm_status"> {{ __('Finish Installation') }} </span>
                                        </button>
                                    </div>
                                </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /Installation Wizard -->
    <!-- Installation Success -->
    <div class="row d-flex align-items-center justify-content-center hidden" id="installationSuccess">
        <div class="col-lg-6 authentication-bg pt-3 p-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-center">
                        {{ __('Installation Complete') }} 🎉
                    </h4>
                    <p class="card-text">
                        {{ __('Congratulations on successfully setting up') }} <strong>MineStoreCMS</strong> {{ __('for your Minecraft server webstore!') }} 🚀
                        <br>
                        {{ __('Thank you for choosing') }} <strong>MineStoreCMS</strong> {!! __('and welcome to our community! We\'re excited to have you on board.') !!} 😊
                        <br>
                        {!! __('Now that your installation is complete, it\'s time to unleash the full potential of your') !!} <strong>Minecraft server</strong> {{ __('with our powerful ecommerce platform.') }}
                        <br>
                        {{ __('Get ready to configure and personalize your store to create the ultimate gaming experience for your players!') }} 🎮
                        <br>
                        <strong>Restart your VPS Server again to make sure everything is working fine!</strong>
                        <br>
                        <br>
                        {!! __('Should you have any questions or need assistance, don\'t hesitate to') !!} <a href="https://minestorecms.com/discord">{{ __('reach out') }}</a>. {!! __('We\'re here to support you every step of the way!') !!} 💪
                    </p>
                </div>
                <div class="row mb-4 d-flex justify-content-center">
                    <div class="col-md-4">
                        <a href="/" class="btn btn-primary w-100">{{ __('Webstore') }}</a>
                    </div>
                    <div class="col-md-4">
                        <a href="/admin" class="btn btn-primary w-100">{{ __('Admin Dashboard') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Installation Success -->
    <!-- Installation Error -->
    <div class="row d-flex align-items-center justify-content-center hidden" id="installationError">
        <div class="col-lg-6 authentication-bg pt-3 p-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-center">
                        {{ __('Ooooooops!') }} 🛠️
                    </h4>
                    <p class="card-text">
                        {{ __('It seems like we hit a') }} <strong>{{ __('little snag') }}</strong> {{ __('during the installation process. We apologize for the inconvenience') }} 😞
                        <br>
                        <p class="text-center mb-1">{!! __('We\'ve received the following error message:') !!}</p>
                        <br>
                        <code>
                            <pre id="errorInstallJson" style="max-height: 300px;overflow-y: scroll;">
                            </pre>
                        </code>
                        <br>
                        {{ __('To help us resolve this issue quickly, could you please send this error message to our support team at') }} <a href="https://minestorecms.com/discord">Official Discord Server</a>.
                        <br>
                        <br>
                        {{ __('Your patience and cooperation are highly appreciated.') }} <strong>{!! __('Rest assured, we\'re committed to resolving this as soon as possible') !!}</strong> {{ __('and getting you back on track!') }} 🚀
                    </p>
                </div>
                <div class="row mb-4 d-flex justify-content-center">
                    <div class="col-md-4">
                        <a href="https://docs.minestorecms.com/" class="btn btn-primary w-100">{{ __('Documentation') }}</a>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-primary w-100 js-back-to-install">{{ __('Back to Installation') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Installation Error -->
@endsection
