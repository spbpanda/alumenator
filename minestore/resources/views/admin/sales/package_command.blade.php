<div data-repeater-item>
    <div class="row mb-3 mb-2 mt-4">
        <div class="col-2">
            <select name="packages_commands[{{ $i }}][item_id]" data-name="item_id" class="select2 form-select">
                @foreach($items as $item)
                    <option @if($isExist && $packageCommand['item_id'] == $item->id) selected @endif value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-2">
          <div class="position-relative">
            <select data-name="servers" name="command[{{ $i }}][servers][]" class="select2 form-select form-select-lg" multiple data-allow-clear="true">
                @php($cmdServers = !$isExist ? [] : $packageCommand->servers()->pluck('id')->toArray())
                <option {{ !$isExist || empty($cmdServers) ? 'selected' : '' }} value="ALL">{{ __('All selected servers') }}</option>
                @foreach ($servers as $server)
                    <option {{ in_array($server->id, $cmdServers) ? "selected" : ""}} value="{{ $server->id }}">{{ $server->name }}</option>
                @endforeach
            </select>
          </div>
        </div>
        <div class="col-7">
            <input type="text" name="command[{{ $i }}][command]" data-name="command" value="{{ $isExist && !empty($packageCommand['command']) ? $packageCommand['command'] : '' }}" aria-label="Enter the command to execute" placeholder="{{ __('Enter the command to execute. Use {} to use variables.') }}" class="form-control">
        </div>
        <div class="col-1">
            <button type="button" class="btn btn-label-danger" data-repeater-delete>
                <i class="bx bx-x"></i>
            </button>
        </div>
    </div>
    <hr>
</div>
