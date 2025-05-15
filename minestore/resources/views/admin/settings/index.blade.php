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
<script src="{{asset('res/js/forms-selects.js')}}"></script>
<script>
const tagifyBasicEl = document.querySelector('#maintenance_ips');
const TagifyBasic = new Tagify(tagifyBasicEl);

function loadPreview(elementId) {
	const filePreviewElement = document.querySelector('#preview-'+elementId);
    filePreviewElement.src = URL.createObjectURL(event.target.files[0]);
}
function clearImage(elementId) {
    document.getElementById('preview-'+elementId).src = "";
    document.getElementById(elementId).value = null;
}
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
<h4 class="fw-bold py-3 mb-1">
  <span class="text-body fw-light">{{ __('Appearance') }}</span>
</h4>

<div class="row">
    <form method="POST" action="{{ route('settings.updateWebstoreName') }}" enctype="multipart/form-data" autocomplete="off">
        @csrf
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
                                        {{ __('Webstore Name') }}
                                        <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('The name that will be used for webstore and CEO settings.') }}"></i>
                                    </h4>
                                    <div class="mb-3 col-md-6">
                                        <input class="form-control" type="text" id="site_name" name="site_name" value="{{ $settings->site_name }}">
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
    </form>
    <form method="POST" action="{{ route('settings.updateWebstoreDescription') }}" enctype="multipart/form-data" autocomplete="off">
        @csrf
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="row d-flex w-100 align-self-center">
                        <div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
                            <div class="row align-self-center h-100">
                                <div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
                                    <div class="d-flex justify-content-center mb-4">
                                      <div class="settings_icon bg-label-primary">
                                          <i class="bx bx-book-bookmark"></i>
                                      </div>
                                    </div>
                                </div>
                                <div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
                                    <h4>
                                        {{ __('Webstore Meta Description') }}
                                        <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('This description will be used for CEO and Meta Settings.') }}"></i>
                                    </h4>
                                    <div class="mb-3 col-md-10">
                                        <input class="form-control" type="text" id="site_desc" name="site_desc" value="{{ $settings->site_desc }}">
                                    </div>
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
    </form>
        <div class="col-6 mb-4">
            <form method="POST" action="{{ route('settings.updateServerIP') }}" enctype="multipart/form-data" autocomplete="off">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="row d-flex w-100 align-self-center">
                            <div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
                                <div class="row align-self-center h-100">
                                    <div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
                                        <h4>
                                            {{ __('Minecraft Server IP') }}
                                            <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Minecraft Server IP to Display at the Header.') }}"></i>
                                        </h4>
                                        <div class="mb-3 col-md-12">
                                            <input class="form-control" type="text" id="server_ip" name="server_ip" value="{{ $settings->serverIP }}">
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
            </form>
        </div>
        <div class="col-6 mb-4">
            <form method="POST" action="{{ route('settings.updateServerPort') }}" enctype="multipart/form-data" autocomplete="off">
                @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row d-flex w-100 align-self-center">
                                <div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
                                    <div class="row align-self-center h-100">
                                        <div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
                                            <h4>
                                                {{ __('Minecraft Server Port') }}
                                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Minecraft Server Port to add at the Clipboard.') }}"></i>
                                            </h4>
                                            <div class="mb-3 col-md-12">
                                                <input class="form-control" type="text" id="server_port" name="server_port" value="{{ $settings->serverPort }}">
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
            </form>
        </div>
    <div class="col-6 mb-4">
            <form method="POST" class="form-inline" action="{{ route('settings.updateLogo') }}" enctype="multipart/form-data" autocomplete="off">
                @csrf
                <div class="card">
                  <div class="card-body">
                    <div class="d-flex align-items-start align-items-sm-center gap-4">
                      <img src="{{asset('/img/logo.png')}}" alt="Logotype" id="preview-logo" class="d-block rounded" height="100" width="100" />
                      <div class="button-wrapper">
                        <label for="logo" class="btn btn-primary me-2 mb-4" tabindex="0">
                          <span class="d-none d-sm-block">{{ __('Upload Logo') }}</span>
                          <i class="bx bx-upload d-block d-sm-none"></i>
                          <input type="file" id="logo" name="logo" onchange="loadPreview('logo')" class="account-file-input" hidden accept="image/png, image/jpeg, image/gif" />
                        </label>
                        <button type="button" onclick="clearImage('logo')" class="btn btn-label-secondary account-image-reset me-2 mb-4">
                          <i class="bx bx-reset d-block d-sm-none"></i>
                          <span class="d-none d-sm-block">{{ __('Reset') }}</span>
                        </button>
                        <button class="btn btn-primary me-2 mb-4" type="submit">{{ __('Save') }}</button>
                        <p class="text-muted mb-0">{{ __('Allowed PNG') }} & Takes up to ~2 minutes to update.</p>
                          @error('logo')
                          <p class="text-danger">{{ $message }}</p>
                          @enderror
                      </div>
                    </div>
                  </div>
                </div>
            </form>
    </div>
    <div class="col-6 mb-4">
        <form method="POST" class="form-inline" action="{{ route('settings.updateFavicon') }}" enctype="multipart/form-data" autocomplete="off">
            @csrf
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-start align-items-sm-center gap-4">
                  <img src="{{asset('/img/favicon.png')}}" alt="Favicon" id="preview-favicon" class="d-block rounded" height="100" width="100" />
                  <div class="button-wrapper">
                    <label for="favicon" class="btn btn-primary me-2 mb-4" tabindex="0">
                      <span class="d-none d-sm-block">{{ __('Upload Favicon') }}</span>
                      <i class="bx bx-upload d-block d-sm-none"></i>
                      <input type="file" id="favicon" name="favicon" onchange="loadPreview('favicon')" class="account-file-input" hidden accept="image/png, image/jpeg" />
                    </label>
                    <button type="button" onclick="clearImage('favicon')" class="btn btn-label-secondary account-image-reset me-2 mb-4">
                      <i class="bx bx-reset d-block d-sm-none"></i>
                      <span class="d-none d-sm-block">{{ __('Reset') }}</span>
                    </button>
                    <button class="btn btn-primary me-2 mb-4" type="submit">{{ __('Save') }}</button>
                    <p class="text-muted mb-0">{{ __('Recommended PNG (128x128px)') }}</p>
                      @error('favicon')
                        <p class="text-danger">{{ $message }}</p>
                      @enderror
                  </div>
                </div>
              </div>
            </div>
        </form>
    </div>
    <div class="col-12 mb-4">
        <form method="POST" action="{{ route('settings.updateBanner') }}" enctype="multipart/form-data" autocomplete="off">
        @csrf
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-start align-items-sm-center gap-4">
                  <img src="{{asset('/img/banner.png')}}" alt="Banner" id="preview-banner" class="d-block rounded" height="100" width="250" />
                  <div class="button-wrapper">
                    <label for="banner" class="btn btn-primary me-2 mb-4" tabindex="0">
                      <span class="d-none d-sm-block">{{ __('Upload Banner') }}</span>
                      <i class="bx bx-upload d-block d-sm-none"></i>
                      <input type="file" id="banner" name="banner" onchange="loadPreview('banner')" class="account-file-input" hidden accept="image/png, image/jpeg, image/gif" />
                    </label>
                    <button type="button" onclick="clearImage('banner')" class="btn btn-label-secondary account-image-reset me-2 mb-4">
                      <i class="bx bx-reset d-block d-sm-none"></i>
                      <span class="d-none d-sm-block">{{ __('Reset') }}</span>
                    </button>
                    <button class="btn btn-primary me-2 mb-4" type="submit">{{ __('Save') }}</button>
                    <p class="text-muted mb-0">{{ __('Recommended PNG, JPG, GIF') }}</p>
                        @error('banner')
                        <p class="text-danger">{{ $message }}</p>
                        @enderror
                  </div>
                </div>
              </div>
            </div>
        </form>
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
									  <i class="bx bx-paint-roll"></i>
								  </div>
								</div>
							</div>
							<div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
								<h4>
                                    {{ __('Theme') }}
								</h4>
								<div class="mb-3 col-md-10">
									<p class="card-text">{!! __('Customise your template\'s colour scheme via pre-built themes or create your own.') !!}</p>
								</div>
							</div>
						</div>
					</div>
					<div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
						<a class="btn btn-primary btn-lg" href="{{ route('themes.index') }}">{{ __('Edit Theme') }}</a>
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
									  <i class="bx bx-block"></i>
								  </div>
								</div>
							</div>
							<div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
								<h4>
                                    {{ __('Maintenance Mode') }}
								</h4>
								<div class="mb-3 col-md-10">
									<p class="card-text">{{ __('Prevent your store from being accessible by the public.') }}</p>
								</div>
							</div>
						</div>
					</div>
					<div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
						<button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#maintenanceMode">{{ __('Configure') }}</button>
					</div>
				  <div class="modal modal-lg fade" id="maintenanceMode" tabindex="-1" aria-hidden="true">
					<div class="modal-dialog modal-dialog-centered" role="document">
                    <form action="{{ route('settings.maintenanceSave') }}" method="POST" autocomplete="off">
                      @csrf
					  <div class="modal-content">
						<div class="modal-header">
						  <h5 class="modal-title" id="maintenanceModeTitle">{{ __('Maintenance Mode') }}</h5>
						  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
						  <div class="row">
							  <div class="col-sm-12">
								<div class="bg-lighter border rounded p-3 mb-3">
									<label class="switch switch-square" for="is_maintenance">
									  <input type="checkbox" class="switch-input" id="is_maintenance" name="is_maintenance" {{ $settings->is_maintenance == 1 ? 'checked' : '' }} />
									  <span class="switch-toggle-slider">
										<span class="switch-on"></span>
										<span class="switch-off"></span>
									  </span>
									  <span class="switch-label">{{ __('Enable Maintenance Mode and make your webstore inaccessible for public view?') }}</span>
									</label>
								</div>
							  </div>
						  </div>
						  <div class="row g-2">
							<div class="col-sm-12 mb-0">
							  <label for="maintenance_ips" class="form-label">
                                  {{ __('Allowed IP Addresses') }}
								<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Your webstore will be accessible only with these IP Addresses.') }}"></i>
							  </label>
							  <input id="maintenance_ips" class="form-control" name="maintenance_ips" value="{{ $settings->maintenance_ips }}" />
							</div>
						  </div>
						</div>
						<div class="modal-footer">
						  <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
						  <button type="submit" class="btn btn-primary">OK</button>
						</div>
					  </div>
                    </form>
					</div>
				  </div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
