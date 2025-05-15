@extends('admin.layout')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/sweetalert2/sweetalert2.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/toastr/toastr.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/animate-css/animate.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('res/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('res/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="{{asset('res/vendor/libs/toastr/toastr.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('res/js/forms-file-upload.js')}}"></script>
<script src="{{asset('res/js/extended-ui-sweetalert2.js')}}"></script>

<script>
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
    };

  $(function() {
      $(".installTheme").on('click', function(e){
          e.preventDefault();
          const that = this;
          Swal.fire({
              title: "{{ __('Are you sure?') }}",
              text: "{!! __('After agreeing to the upgrading theme. Do NOT close this page?') !!}",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: "{{ __('Continue action') }}",
              customClass: {
                  confirmButton: 'btn btn-primary me-1',
                  cancelButton: 'btn btn-label-secondary'
              },
              buttonsStyling: false
          }).then((result) => {
              if (result.value) {
                  $.ajax({
                      method: "POST",
                      url:  "/admin/" + ($(that).data('del-type') === 'item' ? 'items' : 'categories') + '/delete/' + $(that).data('del-id'),
                  }).done(() => {
                      var oldToastrOptions = toastr.options;
                      toastr.options = {
                          "positionClass": "toast-top-right",
                          "preventDuplicates": false,
                          "showDuration": "0",
                          "hideDuration": "0",
                          "timeOut": "0",
                          "extendedTimeOut": "0",
                      };

                      $(that).prop('disabled', true);
                      toastr.warning("{{ __('Installing theme...') }}", "{{ __('Please wait and do not refresh the page!') }}");

                      $.ajax({
                          method: "POST",
                          url: "/admin/themes/" + $(that).attr('data-id'),
                          data: {},
                      }).done(function( msg ) {
                          location.reload();
                      });
                  });
              }
          });
      });

      $(".upgradeTheme").on('click', function(e){
          e.preventDefault();
          const that = this;
          Swal.fire({
              title: "{{ __('Are you sure?') }}",
              text: "{!! __('After agreeing to the upgrading theme. Do NOT close this page') !!}",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: "{{ __('Continue action') }}",
              customClass: {
                  confirmButton: 'btn btn-primary me-1',
                  cancelButton: 'btn btn-label-secondary'
              },
              buttonsStyling: false
          }).then((result) => {
              if (result.value) {
                  var oldToastrOptions = toastr.options;
                  toastr.options = {
                      "positionClass": "toast-top-right",
                      "preventDuplicates": false,
                      "showDuration": "0",
                      "hideDuration": "0",
                      "timeOut": "0",
                      "extendedTimeOut": "0",
                  };

                  $(that).prop('disabled', true);
                  toastr.warning("{{ __('Installing theme...') }}", "{{ __('Please wait and do not refresh the page!') }}");

                  $.ajax({
                      method: "POST",
                      url: "/admin/themes/upgrade/" + $(that).attr('data-id'),
                      data: {},
                  }).done(function( msg ) {
                      location.reload();
                  });
              }
          });
      });

      $(".createTheme").on('click', function(e){
          e.preventDefault();
          $("form").css('display', 'block');
      });

      $(".buildTemplate").on('click', function(e){
          e.preventDefault();
          const that = this;
          Swal.fire({
              title: "{{ __('Are you sure?') }}",
              text: "{!! __('After agreeing to the build template. Do NOT close this page') !!}",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: "{{ __('Continue action') }}",
              customClass: {
                  confirmButton: 'btn btn-primary me-1',
                  cancelButton: 'btn btn-label-secondary'
              },
              buttonsStyling: false
          }).then((result) => {
              if (result.value) {
                  var oldToastrOptions = toastr.options;
                  toastr.options = {
                      "positionClass": "toast-top-right",
                      "preventDuplicates": false,
                      "showDuration": "0",
                      "hideDuration": "0",
                      "timeOut": "0",
                      "extendedTimeOut": "0",
                  };

                  $(that).prop('disabled', true);
                  toastr.warning("{{ __('Building theme...') }}", "{{ __('Please wait and do not refresh the page!') }}");

                  $.ajax({
                      method: "POST",
                      url: "/admin/themes/build/{{ $theme->theme }}",
                      data: {},
                  }).done(function( msg ) {
                      location.reload();
                  });
              }
          });
      });

      $(".toggleDeveloperMode").on('click', function(e){
          e.preventDefault();
          const that = this;
          Swal.fire({
              title: "{{ __('Are you sure?') }}",
              text: "{!! __('Toggle developer mode') !!}",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: "{{ __('Continue action') }}",
              customClass: {
                  confirmButton: 'btn btn-primary me-1',
                  cancelButton: 'btn btn-label-secondary'
              },
              buttonsStyling: false
          }).then((result) => {
              if (result.value) {
                  var oldToastrOptions = toastr.options;
                  toastr.options = {
                      "positionClass": "toast-top-right",
                      "preventDuplicates": false,
                      "showDuration": "0",
                      "hideDuration": "0",
                      "timeOut": "0",
                      "extendedTimeOut": "0",
                  };

                  $(that).prop('disabled', true);
                  toastr.warning("{{ __('Toggling developer mode...') }}", "{{ __('Please wait and do not refresh the page!') }}");

                  $.ajax({
                      method: "POST",
                      url: "/admin/themes/toggleDeveloperMode/{{ $theme->theme }}",
                      data: {},
                  }).done(function( msg ) {
                      if (msg === "OK") {
                          location.reload();
                      }
                  });
              }
          });
      });

      $(".delLanguageFile").on('click', function(e){
          e.preventDefault();
          const that = this;
          Swal.fire({
              title: "{{ __('Are you sure?') }}",
              text: "{!! __('Are you sure delete this file?') !!}",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: "{{ __('Continue action') }}",
              customClass: {
                  confirmButton: 'btn btn-primary me-1',
                  cancelButton: 'btn btn-label-secondary'
              },
              buttonsStyling: false
          }).then((result) => {
              if (result.value) {
                  $(that).remove();
                  $.ajax({
                      method: "POST",
                      url: "/admin/themes/files/deleteFile/{{ $theme->theme }}/" + $(that).attr('data-path'),
                      data: {},
                  }).done(function( msg ) {
                      location.reload();
                  });
              }
          });
      });

  });

    function loadPreview(elementId) {
        const fileInput = document.getElementById('input-' + elementId);
        const previewImg = document.getElementById('preview-' + elementId);
        const defaultIcon = document.getElementById('default-icon-' + elementId);

        if (fileInput.files && fileInput.files[0]) {
            const file = fileInput.files[0];
            previewImg.src = URL.createObjectURL(file);
            previewImg.style.display = 'block';
            defaultIcon.style.display = 'none';
        }
    }

    function clearImage(elementId, defaultImage) {
        const fileInput = document.getElementById('input-' + elementId);
        const previewImg = document.getElementById('preview-' + elementId);
        const defaultIcon = document.getElementById('default-icon-' + elementId);

        const dataTransfer = new DataTransfer();
        fileInput.files = dataTransfer.files;

        previewImg.style.display = 'none';
        defaultIcon.style.display = 'block';
    }
</script>
@endsection

@section('content')
<form action="{{ route('themes.saveSettings', ['themeId' => $theme->theme]) }}" method="POST" enctype="multipart/form-data" novalidate="novalidate" autocomplete="off">
@csrf
    <h4 class="fw-bold py-3 mb-1">
        <span class="text-body fw-light">{{ __('Theme Editing Tools') }}</span>
    </h4>
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="row d-flex w-100 align-self-center">
                    <div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
                        <div class="row align-self-center h-100">
                            <div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
                                <div class="d-flex justify-content-center mb-4">
                                    <div class="settings_icon bg-label-primary">
                                        <i class="bx bx-paint-roll"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
                                <h4>
                                    {{ __('Theme File Manager') }}
                                </h4>
                                <div class="mb-3 col-md-10">
                                    <p class="card-text">{!! __('Customise theme template\'s files (HTML, CSS, JS)') !!}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
                        <a class="btn btn-primary btn-lg" href="{{ route('themes.files', ['themeId' => $theme->theme]) }}">{{ __('Edit Theme') }}</a>
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
                                            <i class="bx bx-brush"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
                                    <h4>
                                        {{ __('Build Template') }}
                                    </h4>
                                    <div class="mb-3 col-md-10">
                                        <p class="card-text">{{ __('Apply all changes you did when editing template by using theme file editor. Keep in mind it WILL TAKE TIME!') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
                            <a class="btn btn-primary btn-lg buildTemplate" href="#">{{ __('Apply Changes') }}</a>
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
                                        <i class="bx bx-history"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
                                <h4>
                                    {{ __('Theme Service Logs') }}
                                </h4>
                                <div class="mb-3 col-md-10">
                                    <p class="card-text">If your webstore frontend is not running after building, you can check logs to find errors.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
                        <a class="btn btn-primary btn-lg" href="{{ route('themes.logs') }}">{{ __('View Logs') }}</a>
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
                                        <i class="bx bx-terminal"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
                                <h4>
                                    {{ __('Enable Developer Mode') }}
                                </h4>
                                <div class="mb-3 col-md-10">
                                    <p class="card-text">{{ __('Enabling developer mode activates hot-refresh function that will regenerate template everytime when you change something. Do NOT forget to disable this!') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
                        <a class="btn btn-primary btn-lg toggleDeveloperMode" href="#">{{ $settings->developer_mode == 1 ? __('Disable') : __('Enable') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(is_null($schema))
        <h4 class="fw-bold py-3 mb-1">
            <span class="text-body fw-light">{{ __('Theme configuration missing (SCHEMA.json not found)') }}</span>
        </h4>
    @elseif(empty($schema) || empty($schema['config']))
        <h4 class="fw-bold py-3 mb-1">
            <span class="text-body fw-light">{{ __('Theme configuration has errors (SCHEMA.json filled in incorrectly)') }}</span>
        </h4>
    @else
        @foreach($schema['config'] as $config)
            <h4 class="fw-bold py-3 mb-1">
                <span class="text-body fw-light">{{$config['header']}}</span>
            </h4>
            @foreach($config['options'] as $option)
                @if($option['type'] == 'checkbox')
                    <x-card-input type="checkbox" :id="$option['id']" :name="'schema['.$option['id'].']'" :checked="$option['value'] ?? $option['default']" icon="bxs-magic-wand">
                        <x-slot name="title">{{ $option['name'] }}</x-slot>
                        <x-slot name="text">{{ $option['description'] }}</x-slot>
                    </x-card-input>
                @elseif($option['type'] == 'textarea')
                    <x-card-input type="textarea" :value="$option['value'] ?? $option['default']" :id="$option['id']" :name="'schema['.$option['id'].']'" :checked="$option['default']" icon="bxs-magic-wand">
                        <x-slot name="title">{{ $option['name'] }}</x-slot>
                        <x-slot name="text">{{ $option['description'] }}</x-slot>
                    </x-card-input>
                @elseif($option['type'] == 'text')
                    <x-card-input type="text" :value="$option['value'] ?? $option['default']" :id="$option['id']" :name="'schema['.$option['id'].']'" :checked="$option['default']" icon="bxs-magic-wand">
                        <x-slot name="title">{{ $option['name'] }}</x-slot>
                        <x-slot name="text">{{ $option['description'] }}</x-slot>
                    </x-card-input>
                @elseif($option['type'] == 'select')
                    <x-card-input type="select" :list="$option['values']" :value="$option['value'] ?? $option['default']" :id="$option['id']" :name="'schema['.$option['id'].']'" :checked="$option['default']" icon="bxs-magic-wand">
                        <x-slot name="title">{{ $option['name'] }}</x-slot>
                        <x-slot name="text">{{ $option['description'] }}</x-slot>
                    </x-card-input>
                @elseif($option['type'] == 'image-uploader')
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="description col-8 col-xl-8 col-lg-8 text-center text-lg-left">
                                        <div class="row align-self-center h-100">
                                            <div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
                                                <div class="d-flex justify-content-center mb-4">
                                                    <div class="settings_icon bg-label-primary" id="preview-container-{{ $option['id'] }}">
                                                        <img id="preview-{{ $option['id'] }}" src="" alt="{{ $option['name'] }}" class="img-fluid rounded" style="max-height: 100px; display: none;" />
                                                        <i class="bx bx-image" id="default-icon-{{ $option['id'] }}" style="font-size: 2rem;"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
                                                <h4>
                                                    {{ $option['name'] }}
                                                </h4>
                                                <div class="mb-1 col-md-10">
                                                    <p class="card-text">{{ $option['description'] }}</p>
                                                </div>
                                                <code class="text-primary mb-0">Path: {{ $option['value'] }}</code>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-12 d-flex align-items-start align-items-sm-center gap-4">
                                        <div class="button-wrapper">
                                            <label for="input-{{ $option['id'] }}" class="btn btn-primary me-2 mb-4" tabindex="0">
                                                <span class="d-none d-sm-block">{{ __('Upload') }} {{ $option['name'] }}</span>
                                                <i class="bx bx-upload d-block d-sm-none"></i>
                                                <input type="file" id="input-{{ $option['id'] }}" name="{{ $option['id'] }}" onchange="loadPreview('{{ $option['id'] }}')" class="account-file-input" hidden accept="image/png, image/jpeg, image/gif, image/svg+xml" />
                                            </label>
                                            <button type="button" onclick="clearImage('{{ $option['id'] }}', '{{ $option['default'] }}')" class="btn btn-label-secondary account-image-reset mb-4">
                                                <i class="bx bx-reset d-block d-sm-none"></i>
                                                <span class="d-none d-sm-block">{{ __('Reset') }}</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif($option['type'] == 'color')
                    <x-card-input type="color" :id="$option['id']" :name="'schema['.$option['id'].']'" :value="$option['value'] ?? $option['default']" icon="bxs-magic-wand">
                        <x-slot name="title">{{ $option['name'] }}</x-slot>
                        <x-slot name="text">{{ $option['description'] }}</x-slot>
                    </x-card-input>
                @endif
            @endforeach
        @endforeach
    @endif

<h4 class="fw-bold py-3 mb-1">
  <span class="text-body fw-light">{{ __('Languages') }}</span>
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
									  <i class="bx bx-world"></i>
								  </div>
								</div>
							</div>
							<div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
								<h4>
                                    {{ __('Default Webstore Language') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('The language will be used for your webstore by default for new visitors.') }}"></i>
								</h4>
								<div class="mb-3 col-md-6">
									<select id="lang" name="lang" class="selectpicker w-100" data-style="btn-default">
                                        @foreach($languages as $language_code => $language_name)
                                          <option @if($language_code == $settings->lang) selected @endif value="{{ $language_code }}">
                                              {{ $language_name }}
                                          </option>
                                        @endforeach
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
						<button class="btn btn-primary btn-lg" type="submit">{{ __('Save') }}</button>
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
									  <i class="bx bx-hive"></i>
								  </div>
								</div>
							</div>
							<div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
								<h4>
                                    {{ __('Selectable Languages for Customers') }}
									<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Configure lists of allowed to choose languages for your webstore.') }}"></i>
								</h4>
								<select id="allow_langs" name="allow_langs[]" class="selectpicker w-100" data-style="btn-default" multiple data-icon-base="bx" data-tick-icon="bx-check text-primary">
                                    @foreach($languages as $language_code => $language_name)
                                        <option @if(in_array($language_code, $allow_langs)) selected @endif value="{{ $language_code }}">{{ $language_name }}</option>
								    @endforeach
								</select>
							</div>
						</div>
					</div>
					<div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
						<button class="btn btn-primary btn-lg" type="submit">{{ __('Update') }}</button>
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

	<div class="col-12 mb-4">
	  <div class="col-12 mb-3">
		<div class="row align-items-center">
			<div class="col-md-6">
				<h4 class="text-body fw-light mb-0">
                    {{ __('Available Languages') }}
				</h4>
			</div>
		</div>
	  </div>
		<div class="card mb-4">
		  <div class="table-responsive text-nowrap">
			<table class="table table-striped">
			  <thead>
				<tr>
				  <th>{{ __('Name') }}</th>
				  <th>{{ __('Language Identifier') }}</th>
				  <th>{{ __('Actions') }}</th>
				</tr>
			  </thead>
			  <tbody class="table-border-bottom-0">
              @foreach($languages as $language_code => $language_name)
				<tr>
				  <td><strong>{{ $language_name }}</strong></td>
				  <td>{{ $language_code }}.json</td>
				  <td>
					<a href="{{ route('themes.files', ['themeId' => $theme->theme]) . '?path=frontend/src/locales/' . $language_code }}.json">
						<span class="tf-icons bx bx-edit-alt text-primary"></span>
					</a>
					<a href="#" class="delLanguageFile" data-path="frontend/src/locales/{{ $language_code }}.json">
						<span class="tf-icons bx bx-x text-danger"></span>
					</a>
				  </td>
				</tr>
              @endforeach
				<tr>
				  <td><strong>Spanish</strong></td>
				  <td>es.json</td>
				  <td>
					<a href="">
						<span class="tf-icons bx bx-edit-alt text-primary"></span>
					</a>
					<a href="">
						<span class="tf-icons bx bx-x text-danger"></span>
					</a>
				  </td>
				</tr>
			  </tbody>
			</table>
		  </div>
		</div>
	</div>
</div>
<form>
@endsection
