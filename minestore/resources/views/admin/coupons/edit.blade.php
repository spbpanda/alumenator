@extends('admin.layout')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/tagify/tagify.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/pickr/pickr-themes.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('res/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('res/vendor/libs/moment/moment.js')}}"></script>
<script src="{{asset('res/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('res/vendor/libs/pickr/pickr.js')}}"></script>
@endsection

@section('page-script')
<script>
    document.querySelector('#start_at').flatpickr({
      enableTime: true,
      dateFormat: 'Y-m-d H:i'
    });
    document.querySelector('#expire_at').flatpickr({
      enableTime: true,
      dateFormat: 'Y-m-d H:i'
    });

	const select2 = $('.select2');
	if (select2.length) {
		select2.each(function () {
		  var $this = $(this);
		  $this.wrap('<div class="position-relative"></div>').select2({
		    placeholder: 'Select value',
		    dropdownParent: $this.parent(),
		  });
		});
	}

    document.querySelector('#apply_type').addEventListener('change', function(){
    	if(this.value == '1'){
    		$('#apply_categories_block').show();
    		$('#apply_items_block').hide();
    	} else if(this.value == '2'){
    		$('#apply_categories_block').hide();
    		$('#apply_items_block').show();
    	} else {
    		$('#apply_categories_block').hide();
    		$('#apply_items_block').hide();
    	}
    });

    $('#type').on('change',function(){
        console.log()
        if($(this).val() == 1){
            $("#discount_money_block").show();
            $("#discount_percent_block").hide();
        }else{
            $("#discount_money_block").hide();
            $("#discount_percent_block").show();
        }
    });

</script>
<script>
    function refreshKey(el) {
        var password = $('#name');
        var chars = "ABCDEFGHIJKLMNOP1234567890";
        var pass = "";
        const length = 10;
        for (var x = 0; x < length; x++) {
            var i = Math.floor(Math.random() * chars.length);
            pass += chars.charAt(i);
        }
        password.val(pass);
        password[0].setAttribute("type", "text");
    }
</script>
@endsection

@section('content')

<h4 class="fw-bold py-3 mb-1">
  <span class="text-body fw-light">{{ __('Edit Coupon') }}
</span></h4>

<form action="{{ route('coupons.update',$coupon->id) }}" method="POST" autocomplete="off">
@csrf
@method('PATCH')
<div class="col-12 mb-4">
	<div class="card">
		<div class="card-body">
			<div class="row">
					<div class="col-md-12 mb-3">
						<label class="form-label" for="name">
							{{ __('Coupon Name') }}*
							<i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Need to be unique.') }}"></i>
						</label>
						<div class="input-group">
							<input type="text" class="form-control" id="name" name="name" value="{{ $coupon->name }}" placeholder="JD78X87HDK" required />
							<span onclick="refreshKey(event)" class="input-group-text cursor-pointer"><i class="bx bx-refresh"></i></span>
					  </div>
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
					</div>
					<div class="col-md-4 mb-3">
						<label for="type" class="form-label">
                            {{ __('Discount Type') }}*
							<i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Select the discount type for a coupon.') }}"></i>
						</label>
						<select class="selectpicker w-100 show-tick" id="type" name="type" data-icon-base="bx" data-tick-icon="bx-check" data-style="btn-default">
						  <option data-icon="bxs-offer" value="0" @if($coupon->type == 0) selected @endif>{{ __('Percentage') }}</option>
						  <option data-icon="bx-money" value="1" @if($coupon->type == 1) selected @endif>{{ __('Amount') }}</option>
						</select>
					</div>
					<div class="col-sm-4 mb-3" id="discount_percent_block" @if($coupon->type == 1) style="display:none" @endif>
						<label class="form-label" for="discount_percent">
                            {{ __('Discount') }}*
							<i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Amount in percents that will decrease original price as a sale.') }}"></i>
						</label>
						<div class="input-group">
						  <input type="number" class="form-control" id="discount_percent" name="discount_percent" value="{{ $coupon->discount }}" aria-label="Amount to discount original price">
						  <span class="input-group-text">%</span>
						</div>
					</div>
					<div class="col-sm-4 mb-3" id="discount_money_block" @if($coupon->type == 0) style="display:none" @endif>
						<label class="form-label" for="discount_money">
                            {{ __('Discount') }}*
							<i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Amount of money that will decrease original price as a sale.') }}"></i>
						</label>
						<div class="input-group">
						  <input type="text" inputmode="numeric" pattern="^\d*([,.]\d{1,2})?$" class="form-control" id="discount_money" name="discount_money" value="{{ $coupon->discount }}" aria-label="Amount to discount original price">
						  <span class="input-group-text">{{ $settings->currency }}</span>
						</div>
					</div>
					<div class="col-sm-4 mb-3">
						<label class="form-label" for="available">
                            {{ __('Max Uses') }}
							<i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Leave empty for unlimited.') }}"></i>
						</label>
						<div class="input-group">
						  <input type="number" id="available" name="available" class="form-control" value="{{ $coupon->available }}" aria-label="Max uses amount.">
						</div>
					</div>
					<div class="col-sm-4 mb-3">
						<label class="form-label" for="limit_per_user">
                            {{ __('Redeem Limit Per Customer/IP') }}
							<i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Set a limit to use this coupon for each Customer/IP.') }}"></i>
						</label>
						<div class="input-group">
						  <input type="number" class="form-control" id="limit_per_user" name="limit_per_user" value="{{ $coupon->limit_per_user }}" aria-label="Redeem Limit Per Customer/IP.">
						</div>
					</div>
					<div class="col-sm-4 mb-3">
						<label class="form-label" for="min_basket">
                            {{ __('Minimum Basket Value') }}
							<i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Set the minimal basket price to apply this coupon for a basket.') }}"></i>
						</label>
						<div class="input-group">
						  <input type="number" class="form-control" id="min_basket" name="min_basket" value="{{ $coupon->min_basket }}" aria-label="Minimum Basket Value.">
						  <span class="input-group-text">{{ $settings->currency }}</span>
						</div>
					</div>
					<div class="col-sm-4 mb-3">
						<label for="apply_type" class="form-label">
                            {{ __('Apply coupon to') }}*
							<i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Select the option that you want to apply your coupon for.') }}"></i>
						</label>
						<select id="apply_type" name="apply_type" class="selectpicker w-100" data-style="btn-default">
							<option value="0" @if($coupon->apply_type == 0) selected @endif>{{ __('Whole Webstore') }}</option>
							<option value="1" @if($coupon->apply_type == 1) selected @endif>{{ __('Categories') }}</option>
							<option value="2" @if($coupon->apply_type == 2) selected @endif>{{ __('Packages') }}</option>
						</select>
					</div>
					<div class="col-sm-8 mb-3" id="apply_categories_block" @if($coupon->apply_type != 1) style="display:none" @endif>
						<label for="apply_categories" class="form-label">
							<span>{{ __('Categories') }}*</span>
							<i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Select the option that you want to apply your coupon for.') }}"></i>
						</label>
						<select id="apply_categories" name="apply_categories[]" class="select2 form-select" multiple>
							@foreach($categories as $cat)
							    <option @if($coupon->apply_type == 1 && $applies->contains($cat->id)) selected @endif value="{{ $cat->id }}">{{ $cat->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="col-sm-8 mb-3" id="apply_items_block" @if($coupon->apply_type != 2) style="display:none" @endif>
						<label for="apply_items" class="form-label">
							<span>{{ __('Packages') }}*</span>
							<i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Select the option that you want to apply your coupon for.') }}"></i>
						</label>
						<select id="apply_items" name="apply_items[]" class="select2 form-select" multiple>
							@foreach($items as $item)
							<option @if($coupon->apply_type == 2 && $applies->contains($item->id)) selected @endif value="{{ $item->id }}">{{ $item->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="col-sm-12 mb-3">
						<label for="note" class="form-label">
                            {{ __('Note') }}
							<i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Make a note for yourself.') }}"></i>
						</label>
						<textarea class="form-control" id="note" name="note" rows="2">{{ $coupon->note }}</textarea>
					</div>
                    <hr>
                    <div class="col-md-12 mb-3">
                        <label class="form-label" for="username">
                            {{ __('Linked Username') }}
                            <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Allow only specific user to use this coupon.') }}"></i>
                        </label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="username" name="username" value="{{ $coupon->username }}" />
                        </div>
                        @error('username')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <hr>
					<div class="col-sm-6 mb-3">
						<label for="start_at" class="form-label">
                            {{ __('Publish On Webstore At') }}
							<i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Set a datetime when coupon will be available to use in your webstore.') }}"></i>
						</label>
                        <input type="text" class="form-control" id="start_at" name="start_at" value="{{ $coupon->start_at ? \Carbon\Carbon::parse($coupon->start_at)->format('Y-m-d H:i') : '' }}" placeholder="YYYY-MM-DD HH:MM" />
                        @error('start_at')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
					</div>
					<div class="col-sm-6 mb-3">
						<label for="expire_at" class="form-label">
                            {{ __('Remove From Webstore After') }}
							<i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Set a datetime when coupon will be inaccessible from your webstore.') }}"></i>
						</label>
                        <input type="text" class="form-control" id="expire_at" name="expire_at" value="{{ $coupon->expire_at ? \Carbon\Carbon::parse($coupon->expire_at)->format('Y-m-d H:i') : '' }}" placeholder="YYYY-MM-DD HH:MM" />
                        @error('expire_at')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
			</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="d-grid gap-2 col-lg-12 mx-auto">
			<button class="btn btn-primary btn-lg" type="submit"><span class="tf-icon bx bx-refresh bx-xs"></span> {{ __('Update a Coupon') }}</button>
		</div>
	</div>

</form>
@endsection
