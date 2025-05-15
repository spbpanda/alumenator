@extends('admin.layout')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/nouislider/nouislider.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('res/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('res/vendor/libs/nouislider/nouislider.js')}}"></script>
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
    <span class="text-body fw-light">{{ __('Chargeback Prevention Settings') }}</span>
</h4>

<form action="{{ route('chargeback.settings') }}" method="POST" autocomplete="off">
@csrf

<div class="row">
  <div class="col-12 mb-4">
      <x-card-input type="range" name="cb_threshold" value="{{ $settings->cb_threshold ?? 1  }}" step="1" min="0" max="100" icon="fa-calculator">
          <x-slot name="title">{{ __('Chargeback Threshold') }}</x-slot>
          <x-slot name="tooltip">{{ __('The trigger that being used to prevent the customer from making purchase if the Chargeback Score is bigger than threshold value.') }}</x-slot>
          <x-slot name="text">{{ __('Customers who pass this threshold (across all MineStore Network) will be blocked from purchasing on your store.') }}</x-slot>
      </x-card-input>
  </div>
</div>

<div class="row">
  <div class="col-12 mb-4">
      <x-card-input type="select" name="cb_period" :list="['All Time', '6 months', '1 year', '2 years']" icon="fa-clock">
          <x-slot name="title">{{ __('Chargeback Time Period') }}</x-slot>
          <x-slot name="text">{{ __('The time frame in which we will check for chargebacks from customers.') }}</x-slot>
      </x-card-input>
  </div>
</div>

<div class="row">
	<div class="col-12 mb-4">
		<div class="card">
			<div class="card-body">
				<div class="row d-flex w-100 align-self-center">
				  <div class="col-md mb-md-0 mb-2">
					<div class="form-check custom-option custom-option-icon">
					  <label class="form-check-label custom-option-content" for="cb_username">
						<span class="custom-option-body">
						  <i class="bx bxs-no-entry"></i>
						  <span class="custom-option-title"> {{ __('Username Threshold Blocker') }}</span>
						  <small> {{ __('Deny customers from purchasing which go over your chargeback threshold.') }}</small>
						</span>
						<input class="form-check-input" type="checkbox" id="cb_username" name="cb_username" {{ $settings->cb_username == 1 ? 'checked' : '' }} />
					  </label>
					</div>
				  </div>
				  <div class="col-md">
					<div class="form-check custom-option custom-option-icon">
					  <label class="form-check-label custom-option-content" for="cb_ip">
						<span class="custom-option-body">
						  <i class="bx bx-wifi-off"></i>
						  <span class="custom-option-title"> {{ __('IP Address Threshold Blocker') }}</span>
						  <small> {{ __('Deny IP addresses from purchasing which go over your chargeback threshold.') }}</small>
						</span>
						<input class="form-check-input" type="checkbox" name="cb_ip" id="cb_ip" {{ $settings->cb_ip == 1 ? 'checked' : '' }} />
					  </label>
					</div>
				  </div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
  <div class="col-12 mb-4">
      <x-card-input type="range" name="cb_bypass" value="{{ $settings->cb_bypass ?? 1  }}" icon="bxs-basket">
          <x-slot name="title">{{ __('Chargeback Value Bypass') }}</x-slot>
          <x-slot name="text">{{ __('Allow customers to checkout if the total value of their chargebacks is less than X% of the value of their total purchases.') }}</x-slot>
      </x-card-input>
  </div>
</div>

<div class="row">
  <div class="col-12 mb-4">
      <x-card-input type="number" name="cb_local" min="0" value="{{ $settings->cb_local ?? 1  }}" icon="bx-task-x">
          <x-slot name="title">{{ __('Local Ban Chargeback Threshold') }}</x-slot>
          <x-slot name="text">{{ __('Autoban customers who make X or more chargebacks on your store.') }}</x-slot>
      </x-card-input>
  </div>
</div>

<div class="row mb-4">
	<div class="d-grid gap-2 col-lg-12 mx-auto">
       <button class="btn btn-primary btn-lg" type="submit"><span class="tf-icon bx bx-save bx-xs"></span> {{ __('Save Changes') }}</button>
    </div>
</div>
</form>
@endsection
