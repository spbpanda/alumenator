@extends('admin.layout')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('res/vendor/libs/select2/select2.js')}}"></script>
@endsection

@section('page-script')
<script type="text/javascript">
  const select2 = $('.select2');
  if (select2.length) {
    select2.each(function () {
      var $this = $(this);
      $this.wrap('<div class="position-relative"></div>');
      $this.select2({
        placeholder: 'Select value',
        dropdownParent: $this.parent()
      });
    });
  }
</script>
@endsection

@section('content')
<form method="POST" enctype="multipart/form-data" autocomplete="off">
@csrf

<h4 class="fw-bold py-3 mb-1">
  <span class="text-body fw-light">{{ __('Featured Items') }}</span>
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
                    <i class="bx bx-question-mark"></i>
                  </div>
                </div>
              </div>
              <div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
                <h4>
                    {{ __('Enable this module?') }}
                </h4>
                <div class="mb-3 col-md-10">
                  <p class="card-text">{{ __('You need to enable "Featured Packages" module to use it.') }}</p>
                </div>
              </div>
            </div>
          </div>
          <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
            <label class="switch switch-square">
              <input type="checkbox" class="switch-input" name="is_featured" {{ $settings->is_featured == 1 ? 'checked' : '' }} />
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
                    <i class="bx bx-star"></i>
                  </div>
                </div>
              </div>
              <div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
                <h4>
                    {{ __('Featured Packages') }}
                </h4>
                <div class="mb-3 col-md-10">
                  <p class="card-text">{{ __('These packages will be displayed on the homepage depends on your theme.') }}</p>
                </div>
              </div>
            </div>
          </div>
          <div class="action col-12 col-xl-3 col-lg-4 align-self-center mx-auto d-grid">
            @php($featured_items = empty($settings->featured_items) ? [] : explode(',',$settings->featured_items))
            <select id="featured_items" name="featured_items[]" class="select2 form-select" multiple>
              @foreach($items as $item)
                <option value="{{ $item->id }}" @if(in_array($item->id,$featured_items)) selected @endif>{{ $item->name }}</option>
              @endforeach
            </select>
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
