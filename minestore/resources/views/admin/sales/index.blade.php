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
    <link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
@endsection

@section('vendor-script')
@endsection

@section('page-script')
@endsection

@section('content')
    <h4 class="fw-bold py-3 mb-1">
        <span class="text-body fw-light">{{ __('Sales') }}</span>
    </h4>

    @if(count($sales) == 0)
        <div class="col-12 mb-4">
            <div class="card">
                <div class="row text-center">
                    <div class="card-body mt-2 mb-3">
                        <i class="bx bxs-store p-4 bx-lg bx-border-circle d-inline-block mb-4"></i>
                        <p class="card-text mb-2">
                            {{ __('Here you can create a Global Sale for your Webstore.') }}
                        </p>
                        <a href="{{ route('sales.create') }}" class="btn btn-primary btn-lg mt-2"><span
                                class="tf-icon bx bx-plus bx-xs"></span> {{ __('Add a First Sale') }}</a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-12 mb-4">
            <div class="col-12 mb-4">
                <x-card-input type="checkbox" name="is_sale_email_notify" :checked="$is_sale_email_notify" icon="bx-mail-send">
                    <x-slot name="title">{{ __('Enable Email Customers Notifications about Sales?') }}</x-slot>
                    <x-slot name="text">{{ __('Your customers will receive notification on the email when sale started with sale announcement and promoted packages.') }}</x-slot>
                    <x-slot name="badge">{{ __('Ultimate Feature') }}</x-slot>
                </x-card-input>
            </div>
        </div>
    </div>

    @if(count($sales) > 0)
        <div class="col-12 mb-4">
            <div class="col-12 mb-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4 class="text-body fw-light mb-0">
                            {{ __('Sales') }}
                        </h4>
                    </div>
                    <div class="col-md-6 pt-4 pt-md-0 d-flex justify-content-end">
                        <a href="{{ route('sales.create') }}"
                           class="btn btn-primary btn-sm fs-6 d-flex align-items-center gap-1">
                            <span class="tf-icon bx bx-plus-circle bx-xs"></span>
                            {{ __('Create a Sale') }}
                        </a>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Discount') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Starts') }}</th>
                            <th>{{ __('Expires') }}</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                        @foreach($sales as $sale)
                            <tr>
                                <td><strong>{{ $sale->name }}</strong></td>
                                <td>{{ $sale->discount }}%</td>
                                <td>
                                    <span
                                        class="badge bg-{{ ['success','primary','danger'][\App\Helpers\SalesHelper::GetStatus($sale)] }}">
                                        {{ [__('ACTIVE'),__('NOT STARTED'),__('EXPIRED')][\App\Helpers\SalesHelper::GetStatus($sale)] }}
                                    </span>
                                </td>
                                <td>{{ $sale->start_at }}</td>
                                <td>{{ $sale->expire_at }}</td>
                                <td>
									<div class="d-flex">
										<a href="{{ route('sales.edit', $sale->id) }}">
											<span class="tf-icons bx bx-edit-alt text-primary"></span>
										</a>
										<form action="{{ route('sales.destroy', $sale->id) }}" method="POST">
											@csrf
											@method('DELETE')
											<button class="tf-icons bx bx-x text-danger"></button>
										</form>
									</div>
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
