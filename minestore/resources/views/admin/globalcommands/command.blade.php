<div data-repeater-item {!! $isEmpty ? 'style="display:none;"' : '' !!}>
    <input type="hidden" class="form-control" id="form-repeater-{{ $i }}-0" data-name="id" name="command[{{ $i }}][id]" value="{{ $isEmpty ? 0 : $cmd->id }}">
    <div class="row">
        <div class="mb-1 col-lg-6 col-xl-3 col-12">
          <label class="form-label d-flex align-items-center justify-content-between" for="form-repeater-{{ $i }}-1">
            {{ __('Command to Run') }}
          </label>
          <input type="text" id="form-repeater-{{ $i }}-1" data-name="cmd" name="command[{{ $i }}][cmd]" value="{{ $cmd['cmd'] }}" required class="form-control" placeholder="tm say {user} just purchased a {package}">
        </div>
        <div class="mb-1 col-lg-6 col-xl-3 col-12">
          <label class="form-label d-flex align-items-center justify-content-between" for="form-repeater-{{ $i }}-2">
              {{ __('Select Servers to Run the Command') }}
          </label>
          <div class="position-relative">
            <select id="form-repeater-{{ $i }}-2" data-name="servers" name="command[{{ $i }}][servers][]" class="select2 form-select form-select-lg" multiple data-allow-clear="true">
                @php($cmdServers = $isEmpty ? [] : $cmd->servers()->pluck('id')->toArray())
                <option {{ empty($cmdServers) ? 'selected' : '' }} value="ALL">{{ __('All selected servers') }}</option>
                @foreach ($servers as $server)
                    <option {{ in_array($server->id, $cmdServers) ? "selected" : ""}} value="{{ $server->id }}">{{ $server->name }}</option>
                @endforeach
            </select>
          </div>
        </div>
        <div class="mb-1 col-lg-6 col-xl-2 col-12">
          <label class="form-label d-flex align-items-center justify-content-between" for="form-repeater-{{ $i }}-3">
              {{ __('Check if the User is Online?') }}
          </label>
          <div class="text-center">
            <label class="switch switch-square">
              <input type="checkbox" class="form-control switch-input ruleCheckbox" id="form-repeater-{{ $i }}-3" data-name="is_online" name="command[{{ $i }}][is_online]" {{ $cmd['is_online'] == 1 ? 'checked' : '' }}>
               <span class="switch-toggle-slider">
                    <span class="switch-on"></span>
                    <span class="switch-off"></span>
                </span>
            </label>
          </div>
        </div>
        <div class="mb-1 col-lg-6 col-xl-3 col-12">
          <label class="form-label d-flex align-items-center justify-content-between" for="form-repeater-{{ $i }}-4">
              {{ __('Minimal Price to Run') }}
          </label>
            <div class="input-group mb-2">
              <input type="text" inputmode="numeric" pattern="^\d*([,.]\d{1,2})?$" class="form-control" id="form-repeater-{{ $i }}-4" required data-name="price" name="command[{{ $i }}][price]" value="{{ $cmd['price'] }}">
              <span class="input-group-text">{{ $settings->currency }}</span>
            </div>
        </div>
        <div class="mb-1 col-lg-12 col-xl-1 col-12">
          <button type="button" class="btn btn-label-danger mt-4" data-repeater-delete="">
            <i class="bx bx-x"></i>
          </button>
        </div>
    </div>
    <hr class="mt-2">
</div>
