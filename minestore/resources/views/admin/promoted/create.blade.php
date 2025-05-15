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
@endsection

@section('content')

<h4 class="fw-bold py-3 mb-1">
  <span class="text-body fw-light">{{ __('New Promoted Package') }}</span>
</h4>

<form action="{{ route('promoted.store') }}" method="POST" autocomplete="off">
@csrf

<div class="col-12 mb-4">
	<div class="card">
		<div class="card-body">
			<div class="row">
				<div class="col-md-12 mb-2">
					<label for="featured_items" class="form-label">
                        {{ __('Select the Package') }}
						<i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="Select the package for being upselling.') }}"></i>
					</label>
					<select id="featured_items" name="item_id" class="selectpicker w-100" data-style="btn-default">
                      @foreach($items as $item)
						<option value="{{ $item->id }}">{{ $item->name }} {{ $item->price }} {{ $settings->currency }}</option>
                      @endforeach
					</select>
				</div>
				<div class="col-md-12 mb-4">
					<label for="price" class="form-label">
                        {{ __('Offer Price') }}
					</label>
					<div class="input-group">
					   <span class="input-group-text">{{ $settings->currency }}</span>
					   <input type="text" inputmode="numeric" pattern="^\d*([,.]\d{1,2})?$" class="form-control" id="price" name="price" placeholder="{{ __('Provide a discounted price to make customer buy this package as well') }}">
					</div>
                    @error('price')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
				</div>
              <div class="col-md-12 mb-4">
				<div class="bg-lighter border rounded p-3 mb-3">
					<label class="switch switch-square" for="is_featured_offer">
					  <input type="checkbox" id="is_featured_offer" name="is_featured_offer" checked class="switch-input" />
					  <span class="switch-toggle-slider">
						<span class="switch-on"></span>
						<span class="switch-off"></span>
					  </span>
					  <span class="switch-label">{{ __('Send this offer to the customer email after purchase?') }} {{ __('(Ultimate Feature)') }}</span>
					</label>
				</div>
              </div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="d-grid gap-2 col-lg-12 mx-auto">
	   <button class="btn btn-primary btn-lg" type="submit"><span class="tf-icon bx bx-plus-circle bx-xs"></span> {{ __('Create Promoted Package') }}</button>
	</div>
</div>

</form>
@endsection
