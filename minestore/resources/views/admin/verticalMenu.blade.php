@php
    require_once(resource_path('views/admin/menuData.blade.php'));
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

  <!-- ! Hide app brand if navbar-full -->
  @if(!isset($navbarFull))
  <div class="app-brand demo">
    <a href="{{url('/')}}" class="app-brand-link">
      <span class="app-brand-logo demo">
        <img class="app-brand-logo demo" style="height: 50px;" src="/res/img/logo-{{ $configData['style'] == 'light' ? 'colored' : 'white' }}.png">
      </span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <i class="bx bx-chevron-left bx-sm align-middle"></i>
    </a>
  </div>
  @endif

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    @foreach ($menuData->menu as $menu)
        {{-- adding active and open class if child is active --}}

        {{-- menu headers --}}
        @if (isset($menu["menuHeader"]))
            <li class="menu-header small text-uppercase">
              <span class="menu-header-text">{{ $menu["menuHeader"] }}</span>
            </li>
        {{-- check is user has rule for view menu item --}}
        @elseif (isset($menu['rule']) && \App\Http\Controllers\Admin\UsersController::hasRule($menu['rule'], 'read'))
            {{-- active menu method --}}
            @php
                $activeClass = null;
                $currentRouteName = Route::currentRouteName();

                if (gettype($menu["slug"]) === "string" && ($currentRouteName === $menu["slug"] /* || strpos(Route::current()->action['prefix'], 'admin/'.$menu["slug"]) === 0 */)) {
                   $activeClass = 'active open';
                } else if (gettype($menu["slug"]) === "array"){
                    foreach($menu["slug"] as $slug){
                      if ($currentRouteName === $slug /* || strpos(Route::current()->action['prefix'], 'admin/'.$slug) === 0 */) {
                        $activeClass = 'active open';
                      }
                    }
                }

                if (isset($menu['submenu'])) {
                    foreach ($menu['submenu'] as $submenu){
                        if (gettype($submenu["slug"]) === "string" && ($currentRouteName === $submenu["slug"] /* || strpos(Route::current()->action['prefix'], 'admin/'.$submenu["slug"]) === 0 */)) {
                           $activeClass = 'active open';
                        } else if (gettype($submenu["slug"]) === "array"){
                            foreach($submenu["slug"] as $slug){
                              if ($currentRouteName === $slug /* || strpos(Route::current()->action['prefix'], 'admin/'.$slug) === 0 */) {
                                $activeClass = 'active open';
                              }
                            }
                        }
                  }
                }
            @endphp
            {{-- main menu --}}
            <li class="menu-item {{$activeClass}}">
              <a href="{{ isset($menu["url"]) ? url($menu["url"]) : (isset($menu["submenu"]) ? 'javascript:void(0);' : route($menu["slug"])) }}" class="{{ isset($menu["submenu"]) ? 'menu-link menu-toggle' : 'menu-link' }}" @if (isset($menu["target"]) && !empty($menu["target"])) target="_blank" @endif>
                @isset($menu["icon"])
                <i class="{{ $menu["icon"] }}"></i>
                @endisset
                <div>{{ isset($menu["name"]) ? __($menu["name"]) : '' }}</div>
              </a>

              {{-- submenu --}}
              @isset($menu["submenu"])
              @include('admin.submenu', ['menu' => $menu["submenu"]])
              @endisset
            </li>
        @endif
    @endforeach
  </ul>

</aside>
