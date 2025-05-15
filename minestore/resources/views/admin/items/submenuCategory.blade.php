@if ($menu->deleted == 0)
<div class="category {{ $menu->is_enable == 0 ? 'disabled' : '' }}" data-category="{{ $menu->id }}" data-level="{{ $submenuLevel }}">
    <div class="header">
        <div class="row align-items-center d-flex">
            <a class="col-7 passive-link d-flex align-items-center">
                <i class="cursor-move bx bx-sm bx-menu align-text-bottom p-2 d-inline-block"></i>
                <div class="category_name ml-3 d-inline-block">
                    <h6 class="mb-0">
                        {{ $menu->name }} {{ __('category') }}
                    </h6>
                </div>
            </a>
            <div class="col-5 align-items-center flex-wrap justify-content-end d-flex">
                <div class="btn-group">
                    <button type="button" class="btn btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown" aria-expanded="true">
                        <i class="bx bx-sm bx-dots-horizontal-rounded align-text-bottom p-2 d-inline-block"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" style="position: absolute; inset: auto 0px 0px auto; margin: 0px; transform: translate(0px, -41px);" data-popper-placement="top-end">
                        <li><a class="dropdown-item" href="{{ route('categories.view', ['id' => $menu->id]) }}">{{ __('Edit') }}</a></li>
                        @if($submenuLevel < $settings->categories_level)
                            <li><a class="dropdown-item" href="{{ route('categories.new') }}?topcategory={{ $menu->id }}">{{ __('Create subcategory') }}</a></li>
                        @endif
                        <li><a class="dropdown-item" href="{{ route('items.new') }}?category={{ $menu->id }}">{{ __('Create package') }}</a></li>
                        <li><a class="dropdown-item deleteAction" data-del-type="category" data-del-id="{{ $menu->id }}" href="javascript:void(0);">{{ __('Delete') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <ul class="packages collapse show mb-0 nested-item">
        @if(count($menu->childrenItems) > 0)
            @foreach($menu->childrenItems->sortBy('sorting') as $item)
            @include('admin.items.submenuItem', $item)
            @endforeach
        @endif
    </ul>

    <div class="subcategories nested-category" data-level="{{ $submenuLevel }}">
        @if(count($menu->children) > 0)
            @php($submenuLevel++)
            @foreach($menu->children->sortBy('sorting') as $menu)
                @include('admin.items.submenuCategory', $menu)
            @endforeach
        @endif
    </div>
</div>
@endif
