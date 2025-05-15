<div data-repeater-item data-repid-inner="{{ $c }}" @if($isEmpty) style="display: none;" @endif>
  <div class="row">
    <div class="mb-3 col-lg-6 col-xl-12 col-12 mb-2 mt-4">
        <div class="input-group">
            <select name="minecraft[{{ $i }}][commands][{{ $c }}][event]" data-name="event" class="form-select" data-style="btn-default">
              <option {{ $isEmpty || $subCommand['event'] == 0 ? "selected" : ""}} value="0">{{ __('When the package is purchased') }}</option>
              <option {{ !$isEmpty && $subCommand['event'] == 1 ? "selected" : ""}} value="1">{{ __('When the package is chargebacked') }}</option>
              <option {{ !$isEmpty && $subCommand['event'] == 2 ? "selected" : ""}} value="2">{{ __('When the package is removed') }}</option>
              <option {{ !$isEmpty && $subCommand['event'] == 3 ? "selected" : ""}} value="3">{{ __('When the subscription renews') }}</option>
            </select>
            <input type="text" name="minecraft[{{ $i }}][commands][{{ $c }}][command]" data-name="command" value="{{ !$isEmpty ? $subCommand['command'] : '' }}" aria-label="Enter the command to execute on your server" placeholder="{{ __('Enter the command to execute on your server. Use {} to use variables.') }}" class="form-control">
            <button type="button" class="btn btn-label-primary accordition-btn" data-bs-toggle="collapse" data-bs-target="#accordition_command_{{ $i }}_{{ $c }}" aria-expanded="true" aria-controls="accordition_command_{{ $i }}_{{ $c }}">
                <i class="bx bx-cog"></i>
            </button>
            <button type="button" class="btn btn-label-danger" data-repeater-delete>
                <i class="bx bx-x"></i>
            </button>
        </div>
        <div class="card accordion-item bg-lighter">
            <div id="accordition_command_{{ $i }}_{{ $c }}" class="accordion-collapse collapse" data-bs-parent="#accordition_command_{{ $i }}_{{ $c }}">
              <div class="accordion-body" style="padding: 20px;">
                <div class="row">
                  <div class="col-sm-6 mb-1">
                    <label for="servers{{ $i }}_{{ $c }}" class="form-label">
                        {{ __('Minecraft Servers') }}
                        <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Select servers assigned to delivery of this package.') }}"></i>
                    </label>
                    <select id="servers{{ $i }}_{{ $c }}" name="minecraft[{{ $i }}][commands][{{ $c }}][servers][]" data-name="servers" class="select2 form-select" multiple>
                          @php($subCommandServers = $isEmpty ? [] : \App\Models\ItemServer::where('type', \App\Models\ItemServer::TYPE_REF_COMMAND_SERVER)->where('item_id', $ref->id)->where('cmd_id', $subCommand->id)->select('server_id')->get()->pluck('server_id')->toArray())
                          <option {{ $isEmpty || empty($subCommandServers) || (count($subCommandServers) == 1 && $subCommandServers[0] == '0') ? "selected" : ""}} value="0">
                              {{ __('All selected servers') }}
                          </option>
                          @php($usingServers = $isEmpty ? [] : array_unique($subCommandServers, SORT_NUMERIC))
                          @foreach ($servers as $server)
                              <option
                                  {{ !$isEmpty && !empty($subCommandServers) && in_array($server->id, $usingServers) ? "selected": "" }}
                                  value="{{ $server->id }}">
                                  {{ $server->name }}
                              </option>
                          @endforeach
                    </select>
                  </div>
                  <div class="col-sm-6 mb-1">
                    <label for="is_online_required{{ $i }}_{{ $c }}" class="form-label">
                        {{ __('Check if the player online?') }}
                    </label>
                    <div class="input-group">
                        <select id="is_online_required{{ $i }}_{{ $c }}" name="minecraft[{{ $i }}][commands][{{ $c }}][is_online_required]" data-name="is_online_required" class="form-select" data-style="btn-default">
                          <option value="1" {{ $isEmpty || $subCommand['is_online_required'] == 1 ? "selected" : "" }}>{{ __('Only execute the command when player is online.') }}</option>
                          <option value="0" {{ !$isEmpty && $subCommand['is_online_required'] == 0 ? "selected" : "" }}>{{ __('Execute the command even if the player offline.') }}</option>
                        </select>
                    </div>
                  </div>
                  <div class="col-sm-6 mb-2">
                    <label for="minecraft_delay_value_{{ $i }}_{{ $c }}" class="form-label">
                        {{ __('Delay Before Executing') }}
                    </label>
                    <div class="input-group">
                      <input type="number" value="{{ !$isEmpty ? \App\Helpers\CommandHelper::GetOriginDelayValue($subCommand['delay_unit'], $subCommand['delay_value']) : '0' }}" id="minecraft_delay_value_{{ $i }}_{{ $c }}" name="minecraft[{{ $i }}][commands][{{ $c }}][delay_value]" data-name="delay_value" aria-label="delay_value" class="form-control">
                      <select id="delay_unit{{ $i }}_{{ $c }}" name="minecraft[{{ $i }}][commands][{{ $c }}][delay_unit]" data-name="delay_unit" class="form-select" data-style="btn-default">
                        <option value="0" {{ !$isEmpty && $subCommand['delay_unit'] == 0 ? "selected" : "" }}>{{ __('Seconds') }}</option>
                        <option value="1" {{ $isEmpty || $subCommand['delay_unit'] == 1 ? "selected" : "" }}>{{ __('Minute') }}</option>
                        <option value="2" {{ !$isEmpty && $subCommand['delay_unit'] == 2 ? "selected" : "" }}>{{ __('Hour') }}</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-6 mb-2 repeatConfig-disabled" @if(!$isEmpty && $subCommand['repeat_value'] > 0) style="display:none;" @endif>
                    <label class="form-label">
                        {{ __('Repeat') }}
                    </label>
                    <div class="input-group">
                      <input type="text" class="form-control" disabled value="{{ __('Never repeat this command.') }}" aria-label="Never remove this package">
                      <button class="btn btn-outline-primary" onclick="repeatConfigButton(event, true)" type="button">{{ __('Configure') }}</button>
                    </div>
                  </div>
                  <div class="col-sm-6 mb-2 repeatConfig-enabled" @if($isEmpty || $subCommand['repeat_value'] <= 0) style="display:none;" @endif>
                    <label class="form-label">
                        {{ __('Repeat') }}
                    </label>
                    <div class="input-group">
                      <span class="input-group-text">{{ __('Every') }}</span>
                      <input type="number" class="form-control" name="minecraft[{{ $i }}][commands][{{ $c }}][repeat_value]" data-name="repeat_value" value="{{ !$isEmpty ? \App\Helpers\CommandHelper::GetOriginDelayValue($subCommand['repeat_unit'], $subCommand['repeat_value']) : '1' }}">
                      <select id="repeat_unit" name="minecraft[{{ $i }}][commands][{{ $c }}][repeat_unit]" data-name="repeat_unit" class="form-select" data-style="btn-default">
                        <option value="0" {{ !$isEmpty && $subCommand['repeat_unit'] == 0 ? "selected" : "" }}>{{ __('Seconds') }}</option>
                        <option value="1" {{ $isEmpty || $subCommand['repeat_unit'] == 1 ? "selected" : "" }}>{{ __('Minute') }}</option>
                        <option value="2" {{ !$isEmpty && $subCommand['repeat_unit'] == 2 ? "selected" : "" }}>{{ __('Hour') }}</option>
                      </select>
                      <span class="input-group-text">{{ __('for') }}</span>
                      <input type="number" class="form-control" name="minecraft[{{ $i }}][commands][{{ $c }}][repeat_cycles]" data-name="repeat_cycles" value="{{ !$isEmpty ? $subCommand['repeat_cycles'] : '1' }}">
                      <span class="input-group-text">{{ __('cycles') }}</span>
                      <button type="button" onclick="repeatConfigButton(event, false)" class="btn btn-icon btn-danger">
                          <span class="tf-icons bx bx-x"></span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        </div>
    </div>
  </div>
  <hr>
</div>
