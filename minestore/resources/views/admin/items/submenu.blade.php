<div class="list-group-item">
<span class="d-flex justify-content-between align-items-center">
	<i class="cursor-move bx bx-menu align-text-bottom me-2"></i>
	<span>{{ $menu->name }} {{ __('category') }}</span>
</span>


    <div class="list-group nested-sortable">
        @if(count($menu->children) > 0)
        <div class="list-group nested-sortable">
            @foreach($menu->children->sortBy('sorting') as $menu)
            @include('admin.items.submenuCategory', $menu)
            @endforeach
        </div>
        @endif

        @if(count($menu->childrenItems) > 0)
        <div class="list-group nested-sortable">
            @foreach($menu->childrenItems->sortBy('sorting') as $item)
            @include('admin.items.submenuItem', $item)
            @endforeach
        </div>
        @endif
    </div>
</div>
