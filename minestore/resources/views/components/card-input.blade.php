<div class="card mb-4">
    <div class="card-body">
        <div class="row d-flex w-100 align-self-center">
            <div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
                <div class="row align-self-center h-100">
                    <div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
                        <div class="d-flex justify-content-center mb-4">
                          <div class="settings_icon bg-label-primary">
                              <i class="{{ ($icon[0] == 'b' && $icon[1] == 'x') ? 'bx' : 'fas' }} {{ $icon }}"></i>
                          </div>
                        </div>
                    </div>
                    <div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
                        <h4>
                            {{ $title }}
                            @if($tooltip ?? false)<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $tooltip }}"></i>@endif
                        </h4>
                        <div class="mb-3 col-md-10">
                            <p class="card-text">{!! $text !!}</p>
                        </div>
                    </div>
                </div>
            </div>
            @if($type == 'checkbox')
            <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
                @if($badge ?? false)<span class="badge bg-primary" style="position: absolute; right: 15px; top: 10px;">{{ $badge }}</span>@endif
                <label class="switch switch-square">
                  <input class="switch-input" type="checkbox" id="{{ $attributes['id'] ?? $name }}" name="{{ $name }}" {{ $checked ? "checked" : "" }} {{ $attributes }}>
                  <span class="switch-toggle-slider">
                    <span class="switch-on"></span>
                    <span class="switch-off"></span>
                  </span>
                </label>
            </div>
            @elseif($type == 'text')
                <div class="action col-12 col-xl-3 col-lg-4 align-self-center mx-auto d-grid">
                    <input class="form-control" type="text" id="{{ $attributes['id'] ?? $name }}" name="{{ $name }}" value="{{ $value ?? '' }}" {{ $attributes }}>
                </div>
            @elseif($type == 'textarea')
                <div class="action col-12 col-xl-3 col-lg-4 align-self-center mx-auto d-grid">
                    <textarea class="form-control" id="{{ $attributes['id'] ?? $name }}" name="{{ $name }}" {{ $attributes }}>{{ $value ?? '' }}</textarea>
                </div>
            @elseif($type == 'select')
            <div class="action col-12 col-xl-3 col-lg-4 align-self-center mx-auto d-grid">
                <select id="{{ $attributes['id'] ?? $name }}" name="{{ $name }}" class="select2 form-select" {{ $attributes }}>
                    @foreach($list as $option)
                        <option value="{{$option}}" {{ $value == $option ? 'selected' : '' }}>{{$option}}</option>
                    @endforeach()
                </select>
            </div>
            @elseif($type == 'number')
            <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
                <input class="form-control" type="number" id="{{ $attributes['id'] ?? $name }}" name="{{ $name }}" value="{{ $value ?? '' }}" {{ $attributes }}>
            </div>
            @elseif($type == 'link')
            <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
                <a href="{{ $value }}" class="btn btn-primary btn-lg" type="button" {{ $attributes }}>{{ $name }}</a>
            </div>
            @elseif($type == 'range')
            <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
               <div class="col-md-10">
                    <input class="form-range mt-3" type="range" id="{{ $attributes['id'] ?? $name }}" name="{{ $name }}" value="{{ $value ?? '' }}" {{ $attributes }}>
                </div>
            </div>
            @elseif($type == 'button')
            <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
                <button onclick="{{ $value }}" class="btn btn-primary btn-lg" type="button" {{ $attributes }}>{{ $name }}</button>
            </div>
            @elseif($type == 'color')
                <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
                    <input class="form-control" type="color" name="{{ $name }}" value="{{ $value ?? '' }}" id="{{ $attributes['id'] ?? $name }}" />
                </div>
            @endif
        </div>
    </div>
</div>
