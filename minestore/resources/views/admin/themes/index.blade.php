@extends('admin.layout')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/tagify/tagify.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/typeahead-js/typeahead.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/jstree/jstree.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/sweetalert2/sweetalert2.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('res/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('res/vendor/libs/jstree/jstree.js')}}"></script>
<script src="{{asset('res/vendor/libs/highlight/highlight.js')}}"></script>
<script src="{{asset('res/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<!-- <script src="{{asset('res/vendor/libs/highlight/highlight-github.css')}}"></script> -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/default.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/languages/css.min.js"></script>
@endsection

@section('page-script')
<script src="{{asset('res/js/forms-file-upload.js')}}"></script>
<script src="{{asset('res/js/forms-selects.js')}}"></script>
<script src="{{asset('res/js/extended-ui-treeview.js')}}"></script>
<script>hljs.highlightAll();</script>
<script>
  $(function() {
      $(".installTheme").on('click', function(e) {
          e.preventDefault();

          const themeId = $(this).attr('data-id');

          Swal.fire({
              title: "{{ __('Are you sure?') }}",
              text: "{!! __('After agreeing to the installing theme, in no case can you close the current page. \nContinue action?') !!}",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: "{{ __('Yes') }}",
              customClass: {
                  confirmButton: 'btn btn-primary me-1',
                  cancelButton: 'btn btn-label-secondary'
              },
              buttonsStyling: false
          }).then((result) => {
              if (result.isConfirmed) {
                  Swal.fire({
                      title: "{{ __('Installing theme...') }}",
                      text: "{{ __('Please wait and do not refresh the page!') }}",
                      icon: 'info',
                      toast: true,
                      position: 'top-end',
                      showConfirmButton: false,
                      timer: 20000,
                      timerProgressBar: true,
                      background: '#252525',
                      iconColor: '#fe6c00',
                      didOpen: () => {
                          Swal.showLoading();
                      }
                  });

                  $("#installThemeForm").attr('action', '/admin/themes/install/' + themeId);
                  $("#installThemeForm").submit();
              }
          });
      });

      $(".upgradeTheme").on('click', function(e) {
          e.preventDefault();

          const themeId = $(this).attr('data-id');

          Swal.fire({
              title: "{{ __('Are you sure?') }}",
              text: "{!! __('After agreeing to upgrade the theme, you must not close or refresh the page. \nContinue action?') !!}",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: "{{ __('Yes') }}",
              customClass: {
                  confirmButton: 'btn btn-primary me-1',
                  cancelButton: 'btn btn-label-secondary'
              },
              buttonsStyling: false
          }).then((result) => {
              if (result.isConfirmed) {
                  Swal.fire({
                      title: "{{ __('Upgrading theme...') }}",
                      text: "{{ __('Please wait and do not refresh the page!') }}",
                      icon: 'info',
                      toast: true,
                      position: 'top-end',
                      showConfirmButton: false,
                      timer: 20000,
                      timerProgressBar: true,
                      background: '#252525',
                      iconColor: '#fe6c00',
                      didOpen: () => {
                          Swal.showLoading();
                      }
                  });

                  $("#upgradeThemeForm").attr('action', '/admin/themes/upgrade/' + themeId);
                  $("#upgradeThemeForm").submit();
              }
          });
      });

      $(".deleteTheme").on('click', function(e){
          e.preventDefault();

          Swal.fire({
              title: "{{ __('Are you sure?') }}",
              text: "{!! __('After agreeing to the deleting theme. Do NOT close this page. \nContinue action?') !!}",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: "{{ __('Yes') }}",
              customClass: {
                  confirmButton: 'btn btn-primary me-1',
                  cancelButton: 'btn btn-label-secondary'
              },
              buttonsStyling: false
          }).then((result) => {
              if (result.value) {
                  var oldToastrOptions = toastr.options;
                  toastr.options = {
                      "positionClass": "toast-top-center",
                      "preventDuplicates": false,
                      "showDuration": "0",
                      "hideDuration": "0",
                      "timeOut": "0",
                      "extendedTimeOut": "0",
                  };

                  $(this).prop('disabled', true);
                  toastr.info("{{ __('Deleting theme...') }}", "{{ __('Please wait and do not refresh the page!') }}");

                  $.ajax({
                      method: "POST",
                      url: "/admin/themes/delete/" + $(this).attr('data-id'),
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
          $(this).remove();
      });
  });
</script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-1">
    <span class="text-body fw-light">{{ __('Themes') }}</span>
</h4>

@if (session('success'))
    <div class="alert alert-primary alert-dismissible fade show" role="alert">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        {{ session('success') }}
    </div>
@elseif(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        {{ session('error') }}
    </div>
@endif

<div class="col-12 mb-4">
	<div class="card">
		<div class="row text-center">
			<div class="card-body mt-2 mb-3">
				<i class="bx bx-unite p-4 bx-lg bx-border-circle d-inline-block mb-4"></i>
				<p class="card-text mb-2">
                    {{ __('Here you can install, select and edit a theme for your webstore.') }}
				</p>
				<button class="btn btn-primary btn-lg mt-2 createTheme" type="button"><span class="tf-icon bx bx-plus bx-xs"></span> {{ __('Create a Theme') }}</button>
				<form class="card" action="/admin/themes/create" method="POST" enctype="multipart/form-data" novalidate="novalidate" autocomplete="off" style="display: none;">
					@csrf
					<div class="card-content">
						  <div class="form-group label-floating">
							<label for="name" class="control-label">{{ __('Custom theme name') }}</label>
							<input type="text" id="name" name="name" class="form-control">
							<span class="material-input"></span>
						  </div>
						  <button class="btn btn-primary btn-lg mt-2" type="submit"><span class="tf-icon bx bx-plus bx-xs"></span> {{ __('Create a Theme') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-12 mb-3">
		<div class="row align-items-center">
			<div class="col-md-6">
				<h4 class="text-body fw-light mb-0">
                    {{ __('Available Themes') }}
				</h4>
			</div>
		</div>
	</div>
	<div class="row">
	@foreach($myThemes as $theme)
			<div class="col-md-4 mb-3">
				<div class="card">
					<div class="card-body" style="height: 430px;">
						<div class="mx-auto mb-3">
							<img src="{{$theme->img}}" style="height: 200px; border-radius: 8px;" alt="Theme Image" class="w-100">
						</div>
						<h5 class="card-title text-center"><a href="{{ empty($theme->url) ? 'https://minestorecms.com/dashboard/marketplace' : $theme->url }}">{{$theme->name}} {{$theme->version}}</a></h5>
						<div class="d-flex align-items-center justify-content-center mb-3 gap-2">
                            {{ __('Author:') }} <a href="https://minestorecms.com/dashboard/marketplace"><span class="badge bg-label-primary">{{$theme->author}}</span></a>
						</div>
						<div class="mb-3 text-center">
							<span class="card-text">{{$theme->description}}</span>
						</div>
						<div class="d-flex align-items-center justify-content-center">
						@if($settings->theme == $theme->theme)
							<button type="button" class="btn btn-primary d-flex align-items-center me-1 mt-0" disabled><i class="bx bxs-download me-1"></i>{{ __('Installed') }}</button>
							<a href="{{ route('themes.settings', ['themeId' => $theme->theme]) }}" class="btn btn-icon btn-outline-primary me-1"><span class="tf-icons bx bx-edit-alt"></span></a>
							@if($theme->is_custom == 0 && count(array_filter($allThemes, function($obj) use($theme) {return $theme->theme == $obj->id && $theme->version != $obj->version;})) > 0)
                               <button type="button" data-id="{{$theme->theme}}" class="btn btn-primary d-flex align-items-center me-1 mt-0 upgradeTheme">
                                   <i class="bx bxs-download me-1"></i>{{ __('Update') }}
                               </button>
                            @endif
						@else
							<button type="button" data-id="{{$theme->theme}}" class="btn btn-primary d-flex align-items-center me-1 mt-0 installTheme"><i class="bx bxs-download me-1"></i>{{ __('Install') }}</button>
						@endif

                        @if($theme->is_custom == 1)
                            <button type="button" data-id="{{$theme->id}}" class="btn btn-primary d-flex align-items-center me-1 mt-0 installTheme">
                                <i class="bx bxs-download me-1"></i>{{ __('Install') }}
                            </button>
                        @endif
						</div>
					</div>
				</div>
			</div>
	@endforeach

	@foreach($officialThemes as $theme)
			<div class="col-md-4 mb-3">
				<div class="card">
					<div class="card-body" style="height: 430px;">
						<div class="mx-auto mb-3">
							<img src="{{$theme->img}}" style="height: 200px; border-radius: 8px;" alt="Theme Image" class="w-100">
						</div>
						<h5 class="card-title text-center"><a href="{{ empty($theme->url) ? 'https://minestorecms.com/dashboard/marketplace' : $theme->url }}">{{$theme->name}} {{$theme->version}}</a></h5>
						<div class="d-flex align-items-center justify-content-center mb-3 gap-2">
                            {{ __('Author:') }} <a href="https://minestorecms.com/dashboard/marketplace"><span class="badge bg-label-primary">{{$theme->author}}</span></a>
						</div>
						<div class="mb-3 text-center">
							<span class="card-text">{{$theme->description}}</span>
						</div>
						<div class="d-flex align-items-center justify-content-center">
							@if($settings->theme === $theme->id)
								<button type="button" class="btn btn-primary d-flex align-items-center me-1 mt-0" disabled><i class="bx bxs-download me-1"></i>{{ __('Installed') }}</button>
                                <a href="{{ route('themes.settings', ['themeId' => $theme->id]) }}" class="btn btn-icon btn-outline-primary me-1"><span class="tf-icons bx bx-edit-alt"></span></a>
							@elseif(empty($theme->user_id) && $theme->id !== 1)
								<a href="https://minestorecms.com/dashboard/marketplace" target="_blank" class="btn btn-primary d-flex align-items-center me-1 mt-0"><i class="bx bxs-purchase-tag me-1"></i>{{ __('Purchase') }} ({{$theme->price}} USD)</a>
							@else
                                <button type="button" data-id="{{$theme->id}}" class="btn btn-primary d-flex align-items-center me-1 mt-0 installTheme">
                                    <i class="bx bxs-download me-1"></i>{{ __('Install') }}
                                </button>
                            @endif
						</div>
					</div>
				</div>
			</div>
	@endforeach
	</div>
</div>

<form id="installThemeForm" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="theme_id" id="theme_id">
</form>

<form id="upgradeThemeForm" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="theme_id" id="upgrade_theme_id">
</form>

@endsection
