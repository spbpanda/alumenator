@php
$containerNav = $containerNav ?? 'container-fluid';
$navbarDetached = ($navbarDetached ?? '');

$languages = \App\Http\Controllers\SettingsController::getLanguages();
$allowedLanguages = \App\Models\Setting::select('allow_langs')->find(1);

$allowedLanguageCodes = explode(',', $allowedLanguages->allow_langs);

$languages = array_filter($languages, function ($lang, $code) use ($allowedLanguageCodes) {
    return in_array($code, $allowedLanguageCodes);
}, ARRAY_FILTER_USE_BOTH);

$languages['en'] = 'English';
@endphp

<!-- Navbar -->
@if(isset($navbarDetached) && $navbarDetached == 'navbar-detached')
<nav class="layout-navbar {{$containerNav}} navbar navbar-expand-xl {{$navbarDetached}} align-items-center bg-navbar-theme" id="layout-navbar">
  @endif
  @if(isset($navbarDetached) && $navbarDetached == '')
  <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
    <div class="{{$containerNav}}">
      @endif

      <!--  Brand demo (display only for navbar-full and hide on below xl) -->
      @if(isset($navbarFull))
      <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
        <a href="{{url('/')}}" class="app-brand-link gap-2">
          <span class="app-brand-logo demo">
            @include('_partials.macros',["width"=>25,"withbg"=>'#696cff'])
          </span>
          <span class="app-brand-text demo menu-text fw-bolder">{{config('variables.templateName')}}</span>
        </a>
      </div>
      @endif

      <!-- ! Not required for layout-without-menu -->
      @if(!isset($navbarHideToggle))
      <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0{{ isset($menuHorizontal) ? ' d-xl-none ' : '' }} {{ isset($contentNavbar) ?' d-xl-none ' : '' }}">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
          <i class="bx bx-menu bx-sm"></i>
        </a>
      </div>
      @endif

      <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

        @if(!isset($menuHorizontal))
        <!-- Search -->
        <div class="navbar-nav align-items-center">
          <div class="nav-item navbar-search-wrapper mb-0">
            <a class="nav-item nav-link search-toggler px-0" href="javascript:void(0);">
              <i class="bx bx-search bx-sm"></i>
              <span class="d-none d-md-inline-block text-muted">{{ __('Search (Ctrl+/)') }}</span>
            </a>
          </div>
        </div>
        <!-- /Search -->
        @endif

        <ul class="navbar-nav flex-row align-items-center ms-auto">
          @if(!isset($menuHorizontal))

          @endif

          @if(isset($menuHorizontal))
          <!-- Search -->
          <li class="nav-item navbar-search-wrapper me-2 me-xl-0">
            <a class="nav-item nav-link search-toggler" href="javascript:void(0);">
              <i class="bx bx-search bx-sm"></i>
            </a>
          </li>
          <!-- /Search -->
          @endif

          <!-- Language -->
          <li class="nav-item dropdown-language dropdown me-2 me-xl-0">
              <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                <span>
                    <i class='bx bx-globe bx-sm mb-1'></i>
                </span>
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                  @foreach ($languages as $code => $language)
                      <li style="cursor: pointer;">
                          <a class="dropdown-item" data-language="{{ $code }}">
                              <span class="align-middle">{{ $language }}</span>
                          </a>
                      </li>
                  @endforeach
              </ul>
          </li>
          <!--/ Language -->

          <!-- Style Switcher -->
          <li class="nav-item me-2 me-xl-0">
            <button class="nav-link style-switcher-toggle hide-arrow" style="background: none;border: none;">
              <i class='bx bx-sm bx-{{ $configData['style'] == 'light' ? 'moon' : 'sun' }}'></i>
            </button>
          </li>
          <!--/ Style Switcher -->

          <!-- Notification -->
          <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-1">
            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
              <i class="bx bx-bell bx-sm"></i>
                @if($unreadCount > 0)
              <span class="badge bg-danger rounded-pill badge-notifications" id="notifications-count">{{ $unreadCount }}</span>
                @endif
            </a>
            <ul class="dropdown-menu dropdown-menu-end py-0">
              <li class="dropdown-menu-header border-bottom">
                <div class="dropdown-header d-flex align-items-center py-3">
                  <h5 class="text-body mb-0 me-auto">{{ __('Notification') }}</h5>
                  <a href="javascript:void(0)" class="dropdown-notifications-all text-body" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Mark all as read') }}" id="mark-as-read"><i class="bx fs-4 bx-envelope-open"></i></a>
                </div>
              </li>
              <li class="dropdown-notifications-list scrollable-container">
                <ul class="list-group list-group-flush">
                  @foreach ($notifications as $notification)
                      <li class="list-group-item list-group-item-action dropdown-notifications-item @if($notification->read()) marked-as-read @endif" data-id="{{ $notification->id }}" data-link="{{ $notification->data['link'] }}">
                        <div class="d-flex">
                          <div class="flex-shrink-0 me-3">
                            <div class="avatar">
                                <span class="avatar-initial rounded-circle bg-label-{{$notification->data['color']}}"><i class="bx {{ $notification->data['icon'] }}"></i></span>
                            </div>
                          </div>
                          <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $notification->data['title'] }}</h6>
                            <p class="mb-0">{!! $notification->data['description'] !!}</p>
                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                          </div>
                          <div class="flex-shrink-0 dropdown-notifications-actions">
                            <a href="javascript:void(0)" class="dropdown-notifications-read"><span class="badge badge-dot"></span></a>
                            <a href="javascript:void(0)" class="dropdown-notifications-archive" data-id="{{ $notification->id }}"><span class="bx bx-x"></span></a>
                          </div>
                        </div>
                      </li>
                  @endforeach
                </ul>
              </li>
              <li class="dropdown-menu-footer border-top">
                <a href="javascript:void(0);" class="dropdown-item d-flex justify-content-center text-primary p-2 h-px-40">
                    {{ __('View all notifications') }}
                </a>
              </li>
            </ul>
          </li>
          <!--/ Notification -->

          <!-- User -->
          <li class="nav-item navbar-dropdown dropdown-user dropdown">
            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
              <div class="avatar">
                  <img
                      src="{{ Auth::guard('admins')->user() && !empty(Auth::guard('admins')->user()->profile_photo_url) ? Auth::guard('admins')->user()->profile_photo_url : asset('https://mc-heads.net/avatar/' . Auth::guard('admins')->user()->username . '/25') }}"
                      alt
                      class="w-px-40 h-auto rounded"
                      onerror="this.src='{{ asset('res/img/question-icon.png') }}';">
              </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li>
                <a class="dropdown-item" href="{{ Route::has('profile.show') ? route('profile.show') : url('/admin/profile') }}">
                  <div class="d-flex">
                    <div class="flex-shrink-0 me-3">
                      <div class="avatar">
                          <img
                              src="{{ Auth::guard('admins')->user() && !empty(Auth::guard('admins')->user()->profile_photo_url) ? Auth::guard('admins')->user()->profile_photo_url : 'https://mc-heads.net/avatar/' . Auth::guard('admins')->user()->username . '/25' }}"
                              alt=""
                              class="w-px-40 h-auto rounded"
                              onerror="this.src='{{ asset('res/img/question-icon.png') }}';">
                      </div>
                    </div>
                    <div class="flex-grow-1">
                      <span class="fw-semibold d-block">
                        @if (Auth::guard('admins')->check())
                        {{ Auth::guard('admins')->user()->username }}
                        @else
                        Unknown User
                        @endif
                      </span>
                      <small class="text-muted">{{ __('Admin') }}</small>
                    </div>
                  </div>
                </a>
              </li>
              <li>
                <div class="dropdown-divider"></div>
              </li>
              <li>
                <a class="dropdown-item" href="/admin/profile">
                  <i class="bx bx-user me-2"></i>
                  <span class="align-middle">{{ __('Account Settings') }}</span>
                </a>
              </li>
            @if (Auth::guard('admins')->check())
              <li>
                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                  <i class='bx bx-power-off me-2'></i>
                  <span class="align-middle">{{ __('Logout') }}</span>
                </a>
              </li>
              <form method="POST" id="logout-form" action="{{ route('logout') }}">
                @csrf
              </form>
              @else
              <li>
                <a class="dropdown-item" href="{{ Route::has('login') ? route('login') : 'javascript:void(0)' }}">
                  <i class='bx bx-log-in me-2'></i>
                  <span class="align-middle">{{ __('Login') }}</span>
                </a>
              </li>
              @endif
            </ul>
          </li>
          <!--/ User -->
        </ul>
      </div>

      <!-- Search Small Screens -->
      <div class="navbar-search-wrapper search-input-wrapper {{ isset($menuHorizontal) ? $containerNav : '' }} d-none">
        <input type="text" class="form-control search-input {{ isset($menuHorizontal) ? '' : $containerNav }} border-0" placeholder="{{ __('Search...') }}" aria-label="Search...">
        <i class="bx bx-x bx-sm search-toggler cursor-pointer"></i>
      </div>

      @if(!isset($navbarDetached))
    </div>
    @endif
  </nav>
  <!-- / Navbar -->
