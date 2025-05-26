@php
/* Display elements */
$contentNavbar = true;
$containerNav = 'container-fluid';
$isNavbar = true;
$isMenu = true;
$isFlex = !true;
$isFooter = true;

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
  'hasCustomizer' => true,
  'colors' => '#fb6604',
];
$notifications = [];
$unreadCount = 0;
if(auth('admins')->check()){
    $notifications = auth('admins')->user()->notifications->take(10);
    $unreadCount = $notifications->filter(function($notification){
        return $notification->unread();
    })->count();
}
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const notificationItems = document.querySelectorAll('.dropdown-notifications-item');

            notificationItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    if (e.target.closest('.dropdown-notifications-archive')) {
                        return;
                    }

                    const link = this.getAttribute('data-link');
                    if (link) {
                        window.location.href = link;
                    }
                });
            });
        });
    </script>

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
</head>

<body>
    <div class="layout-wrapper layout-content-navbar {{ $isMenu ? '' : 'layout-without-menu' }}">
        <div class="layout-container">
        @if ($isMenu)
        @include('admin/verticalMenu')
        @endif

        <div class="layout-page">
          @if ($isNavbar)
          @include('admin/navbar',[$notifications, $unreadCount])
          @endif

          <div class="content-wrapper">
            @if ($isFlex)
            <div class="{{$container}} d-flex align-items-stretch flex-grow-1 p-0">
              @else
              <div class="{{$container}} flex-grow-1 container-p-y">
                @endif

                @yield('content')
              </div>

<!-- Footer-->
<footer class="content-footer footer bg-footer-theme">
  <div class="{{ (!empty($containerNav) ? $containerNav : 'container-fluid') }} d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
    <div class="mb-2 mb-md-0">
      © <script>
        document.write(new Date().getFullYear())

      </script>.
        {{ __('Proudly powered by') }} <a href="https://minestorecms.com/" target="_blank" class="footer-link fw-bolder">MineStoreCMS (v{{ config('app.version') }}) ❤️</a>
    </div>
    <div>
      <a href="https://minestorecms.com/" class="footer-link-white me-4" target="_blank">{{ __('Official Website') }}</a>
      <a href="https://minestorecms.com/dashboard/marketplace" target="_blank" class="footer-link-white me-4">{{ __('Marketplace') }}</a>
      <a href="https://docs.minestorecms.com/" target="_blank" class="footer-link-white me-4">{{ __('Documentation') }}</a>
      <a href="https://minestorecms.com/discord" target="_blank" class="footer-link-white d-none d-sm-inline-block">{{ __('Support') }}</a>
    </div>
  </div>
</footer>
<!--/ Footer-->

              <div class="content-backdrop fade"></div>
            </div>
          </div>
        </div>

        @if ($isMenu)
        <div class="layout-overlay layout-menu-toggle"></div>
        @endif
        <div class="drag-target"></div>
    </div>

    <script src="{{ asset('res/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('res/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('res/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('res/vendor/libs/hammer/hammer.js') }}"></script>
    <script src="{{ asset('res/vendor/libs/i18n/i18n.js') }}"></script>
    <script src="{{ asset('res/vendor/libs/bloodhound/bloodhound.js') }}"></script>
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
    <script>
        let count = {{ $unreadCount }};
        $(".dropdown-notifications-item").click(function(){
            //window.location.href = $(this).data('link');
        })
        $(".dropdown-notifications-item").hover(function(){
            if(!$(this).hasClass("marked-as-read")){
                readNotification($(this).data('id'));
                count--;
                $("#notifications-count").html(count);
            }
            $(this).addClass("marked-as-read");
            if(count == 0){
                $("#notifications-count").remove();
            }
        });
        $("#mark-as-read").click(function(){
            readAllNotifications();
            count = 0;
            $("#notifications-count").remove();
        })

        $(".dropdown-notifications-archive").click(function(){
            deleteNotification($(this).data('id'));
        })
    </script>
        <script>
            function setCookie(name, value, days) {
                let expires = "";
                if (days) {
                    const date = new Date();
                    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                    expires = "; expires=" + date.toUTCString();
                }
                document.cookie = name + "=" + (value || "") + expires + "; path=/";
            }

            document.querySelectorAll('.dropdown-language .dropdown-item').forEach(function(element) {
                element.addEventListener('click', function() {
                    const language = this.getAttribute('data-language');

                    setCookie('lang', language, 7); // Cookie will expire in 7 days

                    location.reload();
                });
            });
        </script>
    @yield('page-script')
</body>

</html>
