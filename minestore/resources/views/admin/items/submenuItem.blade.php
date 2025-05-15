@if ($item->deleted == 0)
<li class="border-top-0 {{ $item->active == 0 ? 'disabled' : '' }}" data-item-id="{{ $item->id }}">
    <div class="row align-items-center d-flex">
        <a class="col-9 passive-link d-flex align-items-center">
            <i class="cursor-move bx bx-sm bx-menu align-text-bottom p-2 d-inline-block"></i>
            <div class="package_name ml-3 d-inline-block">
                <h6 class="mb-0">
                    {{ $item->name }}
                    @if (!$item->active)
						<span class="badge bg-label-secondary ms-2">{{ __('Disabled') }}</span>
                    @endif
                </h6>
            </div>
        </a>
        <div class="col-3 align-items-center flex-wrap justify-content-end d-flex">
            <div class="btn-group">
                <button type="button" class="btn btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown" aria-expanded="true">
                    <i class="bx bx-sm bx-dots-horizontal-rounded align-text-bottom p-2 d-inline-block"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="position: absolute; inset: auto 0px 0px auto; margin: 0px; transform: translate(0px, -41px);" data-popper-placement="top-end">
                    <li><a class="dropdown-item" href="{{ route('items.view', ['id' => $item->id]) }}">{{ __('Edit') }}</a></li>
                    <li><a class="dropdown-item" href="{{ route('items.duplicate', ['id' => $item->id]) }}">{{ __('Clone') }}</a></li>
                    <li><a class="dropdown-item deleteAction" data-del-type="item" data-del-id="{{ $item->id }}" href="javascript:void(0);">{{ __('Delete') }}</a></li>
                </ul>
            </div>
        </div>
    </div>
</li>
@endif
