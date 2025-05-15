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

@section('title', 'Diagnose')

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
        body {
            overflow-x: hidden;
        }

        .hidden {
            display: none!important;
        }
    </style>
@endsection

@section('page-script')
<script>
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
        alert('OK!');
    });
</script>
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
    <div class="row d-flex align-items-center justify-content-center">
        <div class="col-lg-6 authentication-bg pt-3 p-4">
            <div class="card">
                <div class="card-body">
                    <p class="card-text">
                        <span>Your license key: <code>{{$licenseKey}}</code></span>
                        <br>
                        <input type="text" id="preInstall_licenseKey" name="preInstall_licenseKey" class="form-control" placeholder="license key" value="{{ config('app.LICENSE_KEY') }}" />
                        <br>
                        <button type="button" class="btn btn-primary btn-next" id="preInstall_submit">
                            <span class="align-middle d-sm-inline-block d-none me-sm-1">{{ __('Change license key') }}</span>
                            <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                        </button>
                        <br>
                        <span>
                            PHP Timezone Extension
                            @if($isTimezone)
                                <span class="badge rounded-pill bg-label-success" style="margin-left: 10px;">OK</span>
                            @else
                                <span class="badge rounded-pill bg-label-danger" style="margin-left: 10px;">ERROR</span>
                            @endif
                        </span>
                        <br>
                        <span>
                            PHP8.2-FPM
                            @if($isFPM)
                                <span class="badge rounded-pill bg-label-success" style="margin-left: 10px;">OK</span>
                            @else
                                <span class="badge rounded-pill bg-label-danger" style="margin-left: 10px;">ERROR</span>
                            @endif
                        </span>
                        <br>
                        @if(!$versionUse)
                            <span>Your webserver currently using {{ PHP_VERSION }} for this website but need using PHP-FPM-8.2!</span><br>
                        @endif
                        <span>
                            License Key
                            @if($isLicenseValid)
                                <span class="badge rounded-pill bg-label-success" style="margin-left: 10px;">VALID</span>
                            @else
                                <span class="badge rounded-pill bg-label-danger" style="margin-left: 10px;">INVALID</span>
                            @endif
                        </span>
                    </p>
                </div>
                <div class="row mb-4 d-flex justify-content-center">
                    <div class="col-md-4">
                        <a href="/" class="btn btn-primary w-100">{{ __('Documentation') }}</a>
                    </div>
                    <div class="col-md-4">
                        <a href="/install" class="btn btn-primary w-100">{{ __('Install') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
