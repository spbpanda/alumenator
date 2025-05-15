@extends('admin.layout')

@section('vendor-style')
<style>
	button:not(:disabled),
	[type=button]:not(:disabled),
	[type=reset]:not(:disabled),
	[type=submit]:not(:disabled) {
	  cursor: pointer;
	  background: 0;
	  border: 0;
	}
</style>
<link rel="stylesheet" href="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/tagify/tagify.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('res/vendor/libs/select2/select2.js')}}"></script>
@endsection

@section('page-script')
    <script src="{{asset('js/requests.js')}}"></script>
    <script>
        // Should be moved in separate file
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
        }

        $("#is-featured").on('change',function(){
            let value = $(this).prop('checked') ? 1 : 0;
            updateIsFeaturedSetting(value);
        })

        $("#is-featured-offer").on('change',function(){
            let value = $(this).prop('checked') ? 1 : 0;
            updateIsFeaturedOfferSetting(value);
        })
    </script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-1">
  <span class="text-body fw-light">{{ __('Upselling Packages') }}</span>
</h4>

<div class="col-12 mb-4">
    <x-card-input type="checkbox" name="is_featured" id="is-featured" :checked="$settings->is_featured == 1" icon="bx-trending-up">
        <x-slot name="title">{{ __('Upsell Top Packages') }}</x-slot>
        <x-slot name="text">{{ __('Display the most popular products to customers during the checkout process.') }}</x-slot>
    </x-card-input>
</div>

<div class="col-12 mb-4">
    <x-card-input type="checkbox" name="is_featured_offer" id="is-featured-offer" :checked="$settings->is_featured_offer == 1" icon="bx-mail-send">
        <x-slot name="title">{{ __('Send Upselling Packages Offers by Email') }}</x-slot>
        <x-slot name="text">{!! __('Send upselling offers by using SMTP after purchase to the customer\'s email.') !!}</x-slot>
        <x-slot name="badge">{{ __('Ultimate Feature') }}</x-slot>
    </x-card-input>
</div>

@if(count($promotedItems) == 0)
<div class="col-12 mb-4">
	<div class="card">
		<div class="row text-center">
		  <div class="card-body mt-2 mb-3">
			<i class="bx bx-mail-send p-4 bx-lg bx-border-circle d-inline-block mb-4"></i>
			<p class="card-text mb-2">
                {{ __('Recommended packages are displayed during the checkout flow, giving customers the option to add them to their basket.') }}
			</p>
			<a href="{{ route('promoted.create') }}" class="btn btn-primary btn-lg mt-2"><span class="tf-icon bx bx-plus bx-xs"></span> {{ __('Add Promoted Package') }}</a>
		  </div>
		</div>
	</div>
</div>
@else
<div class="col-12 mb-4">
    <div class="col-12 mb-3">
        <div class="row align-items-center">
        	<div class="col-md-6">
                <h4 class="text-body fw-light mb-0">
                    {{ __('Promoted Packages') }}
                </h4>
        	</div>
            <div class="col-md-6 pt-4 pt-md-0 d-flex justify-content-end">
                <a href="{{ route('promoted.create') }}" class="btn btn-primary btn-sm fs-6 d-flex align-items-center gap-1">
                    <span class="tf-icon bx bx-plus-circle bx-xs"></span>
                    {{ __('Create a Promoted Package') }}
                </a>
            </div>
        </div>
    </div>
	<div class="card">
	  <div class="table-responsive text-nowrap">
		<table class="table table-striped">
		  <thead>
			<tr>
			  <th>{{ __('Package') }}</th>
			  <th>{{ __('Price') }}</th>
			  <th>{{ __('Actions') }}</th>
			</tr>
		  </thead>
		  <tbody class="table-border-bottom-0">
            @foreach($promotedItems as $item)
			<tr>
			  <td><strong>{{ $item->name }}</strong></td>
			  <td><s class="text-muted">{{ $item->old_price }}</s> <span>{{ $item->price }} <small>{{ $settings->currency }}</small></span></td>
			  <td>
				<form action="{{ route('promoted.destroy', $item->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
					<button class="tf-icons bx bx-x text-danger"></button>
				</form>
			  </td>
			</tr>
            @endforeach
		  </tbody>
		</table>
	  </div>
	</div>
</div>
@endif
@endsection
