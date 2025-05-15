@extends('admin.layout')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
@endsection

@section('vendor-script')
@endsection

@section('page-script')
<script type="text/javascript">
  function switchSocial(inputId){
    var $inputEl = $('#' + inputId);
    $inputEl.toggle();
    if(!$inputEl.is(':visible'))
      $inputEl.val('');
  }
</script>
@endsection

@section('content')
<form method="POST" enctype="multipart/form-data" novalidate="novalidate" autocomplete="off">
@csrf

<h4 class="fw-bold py-3 mb-1">
  <span class="text-body fw-light">{{ __('Social Media') }}</span>
</h4>
<div class="row">
  <div class="col-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="row d-flex w-100 align-self-center">
          <div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
            <div class="row align-self-center h-100">
              <div class="col-12 col-xl-4 col-lg-3 align-self-center text-center">
                <div class="d-flex justify-content-center mb-4">
                  <div class="settings_icon bg-label-primary">
                    <i class="bx bxl-facebook"></i>
                  </div>
                </div>
              </div>
              <div class="col-12 col-xl-8 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
                <h4>
                  Facebook
                  <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('This link can be used for your theme to redirect your customers on your official social media.') }}"></i>
                </h4>
                <div class="mb-3 col-md-12">
                  <input class="form-control" type="text" id="facebook_link" name="facebook_link" value="{{ $settings->facebook_link }}" placeholder="{{ __('Enter your Facebook URL here...') }}" {!! empty($settings->facebook_link) ? 'style="display:none"' : '' !!}>
                </div>
              </div>
            </div>
          </div>
          <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
            <label class="switch switch-square">
              <input type="checkbox" class="switch-input" onchange="switchSocial('facebook_link')" {{ !empty($settings->facebook_link) ? 'checked' : '' }} />
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
  <div class="col-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="row d-flex w-100 align-self-center">
          <div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
            <div class="row align-self-center h-100">
              <div class="col-12 col-xl-4 col-lg-3 align-self-center text-center">
                <div class="d-flex justify-content-center mb-4">
                  <div class="settings_icon bg-label-primary">
                    <i class="bx bxl-tiktok"></i>
                  </div>
                </div>
              </div>
              <div class="col-12 col-xl-8 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
                <h4>
                  TikTok
                  <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('This link can be used for your theme to redirect your customers on your official social media.') }}"></i>
                </h4>
                <div class="mb-3 col-md-12">
                  <input class="form-control" type="text" id="tiktok_link" name="tiktok_link" value="{{ $settings->tiktok_link }}" placeholder="{{ __('Enter your TikTok URL here...') }}" {!! empty($settings->tiktok_link) ? 'style="display:none"' : '' !!}>
                </div>
              </div>
            </div>
          </div>
          <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
            <label class="switch switch-square">
              <input type="checkbox" class="switch-input" onchange="switchSocial('tiktok_link')" {{ !empty($settings->tiktok_link) ? 'checked' : '' }} />
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
  <div class="col-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="row d-flex w-100 align-self-center">
          <div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
            <div class="row align-self-center h-100">
              <div class="col-12 col-xl-4 col-lg-3 align-self-center text-center">
                <div class="d-flex justify-content-center mb-4">
                  <div class="settings_icon bg-label-primary">
                    <i class="bx bxl-instagram"></i>
                  </div>
                </div>
              </div>
              <div class="col-12 col-xl-8 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
                <h4>
                  Instagram
                  <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('This link can be used for your theme to redirect your customers on your official social media.') }}"></i>
                </h4>
                <div class="mb-3 col-md-12">
                  <input class="form-control" type="text" id="instagram_link" name="instagram_link" value="{{ $settings->instagram_link }}" placeholder="{{ __('Enter your Instagram URL here...') }}" {!! empty($settings->instagram_link) ? 'style="display:none"' : '' !!}>
                </div>
              </div>
            </div>
          </div>
          <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
            <label class="switch switch-square">
              <input type="checkbox" class="switch-input" onchange="switchSocial('instagram_link')" {{ !empty($settings->instagram_link) ? 'checked' : '' }} />
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
  <div class="col-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="row d-flex w-100 align-self-center">
          <div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
            <div class="row align-self-center h-100">
              <div class="col-12 col-xl-4 col-lg-3 align-self-center text-center">
                <div class="d-flex justify-content-center mb-4">
                  <div class="settings_icon bg-label-primary">
                    <i class="bx bxl-discord"></i>
                  </div>
                </div>
              </div>
              <div class="col-12 col-xl-8 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
                <h4>
                  Discord
                  <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('This link can be used for your theme to redirect your customers on your official social media.') }}"></i>
                </h4>
                <div class="mb-3 col-md-12">
                  <input class="form-control" type="text" id="discord_link" name="discord_link" value="{{ $settings->discord_link }}" placeholder="{{ __('Enter your Discord URL here...') }}" {!! empty($settings->discord_link) ? 'style="display:none"' : '' !!}>
                </div>
              </div>
            </div>
          </div>
          <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
            <label class="switch switch-square">
              <input type="checkbox" class="switch-input" onchange="switchSocial('discord_link')" {{ !empty($settings->discord_link) ? 'checked' : '' }} />
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
  <div class="col-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="row d-flex w-100 align-self-center">
          <div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
            <div class="row align-self-center h-100">
              <div class="col-12 col-xl-4 col-lg-3 align-self-center text-center">
                <div class="d-flex justify-content-center mb-4">
                  <div class="settings_icon bg-label-primary">
                    <i class="bx bxl-twitter"></i>
                  </div>
                </div>
              </div>
              <div class="col-12 col-xl-8 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
                <h4>
                  Twitter
                  <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('This link can be used for your theme to redirect your customers on your official social media.') }}"></i>
                </h4>
                <div class="mb-3 col-md-12">
                  <input class="form-control" type="text" id="twitter_link" name="twitter_link" value="{{ $settings->twitter_link }}" placeholder="{{ __('Enter your Twitter URL here...') }}" {!! empty($settings->twitter_link) ? 'style="display:none"' : '' !!}>
                </div>
              </div>
            </div>
          </div>
          <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
            <label class="switch switch-square">
              <input type="checkbox" class="switch-input" onchange="switchSocial('twitter_link')" {{ !empty($settings->twitter_link) ? 'checked' : '' }} />
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
  <div class="col-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="row d-flex w-100 align-self-center">
          <div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
            <div class="row align-self-center h-100">
              <div class="col-12 col-xl-4 col-lg-3 align-self-center text-center">
                <div class="d-flex justify-content-center mb-4">
                  <div class="settings_icon bg-label-primary">
                    <i class="bx bxl-steam"></i>
                  </div>
                </div>
              </div>
              <div class="col-12 col-xl-8 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
                <h4>
                  Steam
                  <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('This link can be used for your theme to redirect your customers on your official social media.') }}"></i>
                </h4>
                <div class="mb-3 col-md-12">
                  <input class="form-control" type="text" id="steam_link" name="steam_link" value="{{ $settings->steam_link }}" placeholder="{{ __('Enter your Steam URL here...') }}" {!! empty($settings->steam_link) ? 'style="display:none"' : '' !!}>
                </div>
              </div>
            </div>
          </div>
          <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
            <label class="switch switch-square">
              <input type="checkbox" class="switch-input" onchange="switchSocial('steam_link')" {{ !empty($settings->steam_link) ? 'checked' : '' }} />
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
  <div class="row justify-content-center">
      <div class="col-6 mb-4">
          <div class="card">
              <div class="card-body">
                  <div class="row d-flex w-100 align-self-center">
                      <div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
                          <div class="row align-self-center h-100">
                              <div class="col-12 col-xl-4 col-lg-3 align-self-center text-center">
                                  <div class="d-flex justify-content-center mb-4">
                                      <div class="settings_icon bg-label-primary">
                                          <i class="bx bxl-youtube"></i>
                                      </div>
                                  </div>
                              </div>
                              <div class="col-12 col-xl-8 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
                                  <h4>
                                      YouTube
                                      <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('This link can be used for your theme to redirect your customers on your official social media.') }}"></i>
                                  </h4>
                                  <div class="mb-3 col-md-12">
                                      <input class="form-control" type="text" id="youtube_link" name="youtube_link" value="{{ $settings->youtube_link }}" placeholder="{{ __('Enter your Youtube URL here...') }}" {!! empty($settings->youtube_link) ? 'style="display:none"' : '' !!}>
                                  </div>
                              </div>
                          </div>
                      </div>
                      <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
                          <label class="switch switch-square">
                              <input type="checkbox" class="switch-input" onchange="switchSocial('youtube_link')" {{ !empty($settings->youtube_link) ? 'checked' : '' }} />
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
  </div>
  <div class="row mb-4">
    <div class="d-grid gap-2 col-lg-12 mx-auto">
       <button class="btn btn-primary btn-lg" type="submit"><span class="tf-icon bx bx-save bx-xs"></span> {{ __('Save Changes') }}</button>
    </div>
  </div>
</div>
</form>

@endsection
