<div data-repeater-item>
    <div class="row justify-content-center">
        <div class="mb-3 col-sm-2">
            <label class="form-label d-flex align-items-center justify-content-between" for="form-repeater-{{ $i }}-1">{{ __('Display Name') }}<i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="The name will be displayed for the URL." data-bs-original-title="The name will be displayed for the URL."></i>
            </label>
            <input type="text" id="form-repeater-{{ $i }}-1" data-name="name" name="links[{{ $i }}][name]" value="{{ $link->name }}" class="form-control" placeholder="Forum">
            @error('name')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3 col-sm-2">
            <label class="form-label d-flex align-items-center justify-content-between" for="form-repeater-{{ $i }}-2">{{ __('SVG Icon') }}<i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="The name will be displayed for the icon." data-bs-original-title="The name will be displayed for the icon."></i>
            </label>
            <input type="text" id="form-repeater-{{ $i }}-2" data-name="icon" name="links[{{ $i }}][icon]" value="{{ $link->icon }}" class="form-control" placeholder="icon">
            @error('icon')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3 col-sm-4">
            <label class="form-label d-flex align-items-center justify-content-between" for="form-repeater-{{ $i }}-3">
                {{ __('Link URL') }}
                <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Could be external URL." data-bs-original-title="Could be external URL."></i>
            </label>
            <input type="text" id="form-repeater-{{ $i }}-3" data-name="url" name="links[{{ $i }}][url]" value="{{ $link->url }}" class="form-control" placeholder="https://forum.minestorecms.com/">
            @error('url')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3 col-sm-3">
            <div class="row mt-2">
                <div class="col-md-6">
                    <label class="form-label d-flex align-items-center justify-content-between" for="form-repeater-{{ $i }}-4">
                        {{ __('Display for Footer?') }}
                    </label>
                    <div class="d-flex justify-content-center mb-2">
                        <input type="checkbox" class="form-check-input" id="form-repeater-{{ $i }}-4" data-name="footer" name="links[{{ $i }}][footer]"
                               @if(isset($link->type) && ($link->type == App\Models\Link::SHOW_FOOTER || $link->type == App\Models\Link::SHOW_ALL))
                                   checked
                               @endif
                        >
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label d-flex align-items-center justify-content-between" for="form-repeater-{{ $i }}-5">
                        {{ __('Display for Header?') }}
                    </label>
                    <div class="d-flex justify-content-center mb-2">
                        <input type="checkbox" class="form-check-input" id="form-repeater-{{ $i }}-5" data-name="header" name="links[{{ $i }}][header]"
                               @if(isset($link->type) && ($link->type == App\Models\Link::SHOW_HEADER || $link->type == App\Models\Link::SHOW_ALL))
                                   checked
                               @endif
                        >
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-3 col-sm-1" style="align-self: center;">
            <button type="button" class="btn btn-label-danger mt-3" data-repeater-delete="">
                <i class="bx bx-x"></i>
            </button>
        </div>
    </div>
    <hr class="mt-2">
</div>
