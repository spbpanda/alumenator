@extends('admin.layout')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/pickr/pickr-themes.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('res/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('res/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('res/vendor/libs/pickr/pickr.js')}}"></script>
@endsection

@section('page-script')
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-1">
    <span class="text-body fw-light">{{ __('Profiles Module') }}</span>
</h4>

<form method="POST" enctype="multipart/form-data" autocomplete="off">
@csrf

<div class="row">
	<div class="col-12 mb-4">
		<x-card-input type="checkbox" name="is_profile_enable" :checked="$is_profile_enable" icon="bx-question-mark">
			<x-slot name="title">{{ __('Enable this module?') }}</x-slot>
			<x-slot name="text">{{ __('You need to enable "Profiles" module to make it available at your Webstore.') }}</x-slot>
		</x-card-input>
	</div>

	<div class="col-12 mb-4">
		<x-card-input type="checkbox" name="is_profile_sync" :checked="$is_profile_sync" icon="bxl-graphql">
			<x-slot name="title">{{ __('Enable Synchronization between Webstore and Minecraft Plugin?') }}</x-slot>
			<x-slot name="text">{{ __('You could enable it, if you want to display information about user group & prefix.') }}</x-slot>
		</x-card-input>
	</div>

	<div class="col-12 mb-4">
		<x-card-input type="text" name="profile_display_format" icon="bx-face" :value="$profile_display_format">
			<x-slot name="title">{{ __('Username Display Format') }}</x-slot>
			<x-slot name="tooltip">{{ __('Example: {prefix} {username}.') }}</x-slot>
			<x-slot name="text">{{ __('Available variables:') }} <code>{prefix}</code>, <code>{group}</code>, <code>{username}</code> {{ __('and Minecraft Color Codes') }} (<code>&5, &2</code>).</x-slot>
		</x-card-input>
	</div>

	<div class="col-12 mb-4">
		<x-card-input type="checkbox" name="is_group_display" :checked="$is_group_display" icon="bxs-group">
			<x-slot name="title">{!! __('Display User\'s Group Under the Username?') !!}</x-slot>
			<x-slot name="text">{!! __('You could enable it to display the user\'s group under the username.') !!}</x-slot>
		</x-card-input>
	</div>

	<div class="col-12 mb-4">
		<x-card-input type="text" name="group_display_format" icon="bxs-user-rectangle" :value="$group_display_format">
			<x-slot name="title">{{ __('Group Display Format') }}</x-slot>
			<x-slot name="tooltip">{{ __('Example:') }} {prefix} {username}.</x-slot>
			<x-slot name="text">{{ __('Available variables:') }} <code>{prefix}</code>, <code>{group}</code>, <code>{username}</code> {{ __('and Minecraft Color Codes') }} (<code>&5, &2</code>).</x-slot>
		</x-card-input>
	</div>
</div>

<div class="row mb-4">
	<div class="d-grid gap-2 col-lg-12 mx-auto">
       <button class="btn btn-primary btn-lg" type="submit"><span class="tf-icon bx bx-save bx-xs"></span> {{ __('Save Settings') }}</button>
    </div>
</div>

</form>
@endsection
