@php
/* Display elements */
$contentNavbar = false;
$containerNav = 'container-fluid';
$isNavbar = false;
$isMenu = false;
$isFlex = !true;
$isFooter = false;

/* HTML Classes */
$navbarDetached = 'navbar-detached';

/* Content classes */
$container = 'container-xxl';

$configData = [
  'layout' => 'vertical',
  'theme' => 'theme-default',
  'style' => Cookie::get('style', 'dark') == 'dark' ? 'dark' : 'light',
  'rtlSupport' => true,
  'rtlMode' => true,
  'textDirection' => true,
  'menuCollapsed' => false,
  'hasCustomizer' => false,
  'colors' => '#fb6604',
];
@endphp


<!DOCTYPE html>

<html lang="{{ session()->get('locale') ?? app()->getLocale() }}" class="{{ $configData['style'] }}-style {{ $navbarFixed ?? '' }} {{ $menuFixed ?? '' }} {{ $menuCollapsed ?? '' }} {{ $footerFixed ?? '' }} {{ $customizerHidden ?? '' }}" dir="{{ $configData['textDirection'] }}" data-theme="{{ $configData['theme'] }}" data-assets-path="{{ asset('/res') . '/' }}" data-base-url="{{url('/')}}" data-framework="laravel" data-template="{{ $configData['layout'] . '-menu-' . $configData['theme'] . '-' . $configData['style'] }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>{{ !empty($title) ? $title : __('Dashboard') }}</title>
    <!-- <meta name="description" content="{{ config('variables.templateDescription') ? config('variables.templateDescription') : '' }}" />
    <meta name="keywords" content="{{ config('variables.templateKeyword') ? config('variables.templateKeyword') : '' }}"> -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('img/favicon.png') }}" type="image/png">

    <!-- BEGIN: Theme CSS-->
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('res/vendor/fonts/boxicons.css') }}" />
    <link rel="stylesheet" href="{{ asset('res/vendor/fonts/fontawesome.css') }}" />
    <link rel="stylesheet" href="{{ asset('res/vendor/fonts/flag-icons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('res/vendor/css/core' .($configData['style'] !== 'light' ? '-' . $configData['style'] : '') .'.css') }}" class="{{ $configData['hasCustomizer'] ? 'template-customizer-core-css' : '' }}" />
    <link rel="stylesheet" href="{{ asset('res/vendor/css/' .$configData['theme'] .($configData['style'] !== 'light' ? '-' . $configData['style'] : '') .'.css') }}" class="{{ $configData['hasCustomizer'] ? 'template-customizer-theme-css' : '' }}" />
    <link rel="stylesheet" href="{{ asset('res/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('res/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('res/vendor/libs/typeahead-js/typeahead.css') }}" />

    <!-- Vendor Styles -->
    <link rel="stylesheet" href="{{asset('res/vendor/libs/toastr/toastr.css')}}" />
    @yield('vendor-style')
    <!-- Page Styles -->
    @yield('page-style')

    <script src="{{ asset('res/vendor/libs/jquery/jquery.js') }}"></script>

    <!-- laravel style -->
    <script src="{{ asset('res/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('res/vendor/js/template-customizer.js') }}"></script>

    <script src="{{ asset('res/js/config.js') }}"></script>

</head>

<body>
   @yield('content')

    <script src="{{ asset('res/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('res/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('res/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('res/vendor/libs/hammer/hammer.js') }}"></script>
    <script src="{{ asset('res/vendor/libs/i18n/i18n.js') }}"></script>
    <script src="{{ asset('res/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script>
        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
    </script>
    <script src="{{ asset('res/vendor/js/menu.js') }}"></script>
    <script src="{{asset('res/vendor/libs/toastr/toastr.js')}}"></script>
    @yield('vendor-script')
    <script src="{{ asset('res/js/main.js') }}"></script>
    <script src="{{ asset('js/search.js') }}"></script>
    <script src="{{ asset('js/modules/notifications.js') }}"></script>
	<script src="{{ asset('res/js/theme.js') }}"></script>
    @yield('page-script')
</body>

</html>
