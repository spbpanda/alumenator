@extends('admin.layout')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
<link rel="stylesheet" href="{{ asset('/res/flag-icon.min.css?v7') }}">
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

@if($payNowEnabled)
        <h4 class="fw-bold py-3 mb-1">
            <span class="text-body fw-light">{{ __('Taxes') }}</span>
        </h4>

        <div class="col-12 mb-4">
            <div class="card">
                <div class="row text-center">
                    <div class="card-body mt-2 mb-3">
                        <i class="bx bxs-badge-dollar p-4 bx-lg bx-border-circle d-inline-block mb-4"></i>
                        <p class="card-text mb-2">
                            {{ __('Taxes fully controlled by PayNow. You can not add or edit taxes here. All taxes are managed by PayNow.') }}
                        </p>
                        <a href="{{ route('paynow.index') }}" class="btn btn-primary btn-lg mt-2">
                            <span class="tf-icon bx bx-cog bx-xs"></span>
                            {{ __('Manage PayNow Configuration') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
@else
<form action="{{ route('taxes.store') }}" method="POST" autocomplete="off">
@csrf
<h4 class="fw-bold py-3 mb-1">
  <span class="text-body fw-light">{{ __('New Tax') }}</span>
</h4>

<div class="col-12 mb-4">
  <div class="card">
    <div class="card-body">
      <div class="row">
        <div class="col-md-12 mb-2">
          <div class="col-md-12">
              <div class="mb-3">
              <label class="form-label" for="name">
                  {{ __('Name for Tax') }}
                <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('The name for this tax to identify it in your admin dashboard. Just for yourself.') }}"></i>
              </label>
              <input type="text" class="form-control" id="name" name="name" placeholder="Spain's VAT" required />
                  @error('name')
                    <span class="text-danger">{{ $message }}</span>
                  @enderror
              </div>
          </div>
          <div class="row">
              <div class="col-md-6 mb-3">
                <label for="full-editor" class="form-label">
                    {{ __('Select a Country') }}
                  <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Select the country that tax will be charged by using address verification.') }}"></i>
                </label>
                <select id="country" name="country" class="select2 form-select">
                    @foreach($countries as $language_code => $language_name)
                    <option value="{{ $language_code }}">@if(__($language_name) != $language_name) @lang($language_name) / @endif {{ $language_name }}</option>
                    @endforeach
                </select>
                  @error('country')
                    <span class="text-danger">{{ $message }}</span>
                  @enderror
              </div>
              <div class="col-md-6 mb-3">
                  <label for="percent" class="form-label">
                      {{ __('Percentage') }}
                    <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Percentage will be charged by using this tax.') }}"></i>
                  </label>
                  <div class="input-group mb-2">
                    <input type="text" inputmode="decimal" pattern="^\d*([,.]\d{1,2})?$" id="percent" name="percent" class="form-control" placeholder="10">
                    <span class="input-group-text">%</span>
                  </div>
                    @error('percent')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
              </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="bg-lighter border rounded p-3 mb-3">
                <label class="switch switch-square" for="is_included">
                  <input type="checkbox" id="is_included" name="is_included" class="switch-input" />
                  <span class="switch-toggle-slider">
                  <span class="switch-on"></span>
                  <span class="switch-off"></span>
                  </span>
                  <span class="switch-label">{{ __('Include the tax to the final transaction price?') }}</span>
                </label>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="d-grid gap-2 col-lg-12 mx-auto">
    <button class="btn btn-primary btn-lg"><span class="tf-icon bx bx-plus-circle bx-xs"></span> {{ __('Create a Tax') }}</button>
  </div>
</div>
</form>
@endif
@endsection
