<ul class="menu-sub">
  @if (isset($menu))
    @foreach ($menu as $submenu)

    {{-- active menu method --}}
    @php
      $activeClass = null;
      $active = 'active open';
      $currentRouteName =  Route::currentRouteName();

      if ($currentRouteName === $submenu["slug"]) {
          $activeClass = 'active';
      }
      elseif (isset($submenu->submenu)) {
        if (gettype($submenu["slug"]) === 'array') {
          foreach($submenu["slug"] as $slug){
            if (str_contains($currentRouteName,$slug) && strpos($currentRouteName,$slug) === 0) {
                $activeClass = $active;
            }
          }
        }
        else
        {
          if (str_contains($currentRouteName,$submenu["slug"]) && strpos($currentRouteName,$submenu["slug"]) === 0) {
            $activeClass = $active;
          }
        }
      }
    @endphp

      <li class="menu-item {{$activeClass}}">
        <a href="{{ isset($submenu["url"]) ? url($submenu["url"]) : route($submenu["slug"]) }}" class="{{ isset($submenu["submenu"]) ? 'menu-link menu-toggle' : 'menu-link' }}" @if (isset($submenu["target"]) and !empty($submenu["target"])) target="_blank" @endif>
          @if (isset($submenu["icon"]))
          <i class="{{ $submenu["icon"] }}"></i>
          @endif
          <div>{{ isset($submenu["name"]) ? $submenu["name"] : '' }}</div>
        </a>

        {{-- submenu --}}
        @if (isset($submenu["submenu"]))
          @include('admin.submenu',['menu' => $submenu["submenu"]])
        @endif
      </li>
    @endforeach
  @endif
</ul>
