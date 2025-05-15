<div data-repeater-item class="col-md-12 minecraftServerCommandBlock" data-repid="{{ $i }}">
<!-- @if($isEmpty || $item->type != 0) style="display: none;" @endif -->
    <div class="card mb-3">
      <div class="card-header border-bottom mb-3">
        <h5 class="card-title">{{ __('Minecraft Server Commands') }}</h5>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-sm-10 mb-4">
            <label for="servers{{ $i }}" class="form-label">
                {{ __('Minecraft Servers') }}
                <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Select servers assigned to delivery of this package.') }}"></i>
            </label>
              <select id="servers{{ $i }}" name="minecraft[{{ $i }}][servers][]" data-name="servers" class="select2 form-select form-select-lg" multiple data-allow-clear="true">
                <option {{ $isEmpty || \App\Models\ItemServer::where('type', \App\Models\ItemServer::TYPE_CMD_SERVER)->where('item_id', $item->id)->count() == 0 ? "selected" : ""}} value="0">{{ __('All selected servers') }}</option>
                @php($usingServers = $isEmpty ? [] : array_unique(\App\Models\ItemServer::where('type', \App\Models\ItemServer::TYPE_CMD_SERVER)->where('item_id', $item->id)->select('server_id')->get()->pluck('server_id')->toArray(), SORT_NUMERIC))
                @foreach ($servers as $server)
                    <option {{ !$isEmpty && in_array($server->id, $usingServers) ? "selected": ""}} value="{{ $server->id }}">{{ $server->name }}</option>
                @endforeach
              </select>
          </div>
          <div class="col-sm-2 mb-4 mt-4">
            <button type="button" class="btn btn-primary pr-4 pl-4 server_details_btn" data-bs-toggle="modal" data-bs-target="#server_details{{ $i }}">{{ __('CONFIGURE') }}</button>

            <!-- Modal -->
            <div class="modal fade server_details" id="server_details{{ $i }}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">{{ __('Minecraft Servers Configurations') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="row">
                      <div class="col mb-2">
                        <div class="bg-lighter border rounded p-3 mb-3">
                          <label class="switch switch-square is_server_choice" for="is_server_choice{{ $i }}">
                            <input type="checkbox" id="is_server_choice{{ $i }}" name="minecraft[{{ $i }}][is_server_choice]" data-name="is_server_choice" {{ !$isEmpty && $item->is_server_choice == 1 ? 'checked' : '' }} class="form-control switch-input" />
                            <span class="switch-toggle-slider">
                              <span class="switch-on"></span>
                              <span class="switch-off"></span>
                            </span>
                            <span class="switch-label">{{ __('Allow customers to select the server they want to receive purchased package on.') }}</span>
                          </label>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">{{ __('Save changes') }}</button>
                  </div>
                </div>
              </div>
            </div>

          </div>
          <div class="col-sm-12 mb-2 inner-repeater">
            <div class="card mb-3">
              <div class="card-header border background-darker mb-3">
                <div class="row">
                    <div class="col-sm-8">
                        <h5 class="card-title">{{ __('Commands List') }}</h5>
                    </div>
                    <div class="col-sm-4 d-flex justify-content-end">
                        <button style="margin-right: 5px;" type="button" data-bs-toggle="modal" data-bs-target="#examplesModal" class="btn btn-sm btn-info mb-2"><span class="tf-icon bx bx-chalkboard bx-xs"></span> {{ __('Examples') }}</button>
                        <button style="margin-right: 5px;" type="button" data-bs-toggle="modal" data-bs-target="#variablesModal" class="btn btn-sm btn-info mb-2"><span class="tf-icon bx bx-code-curly bx-xs"></span> {{ __('Variables') }}</button>
                        <button data-repeater-create type="button" class="btn btn-sm btn-primary mb-2"><span class="tf-icon bx bx-plus-circle bx-xs"></span> {{ __('Add Command') }}</button>
                    </div>
                </div>
              </div>
              <div class="card-body">
                <p>{{ __('Add commands here to execute on your Minecraft: Java Edition game server to reward customers with in-game products.') }}</p>

                <div class="">
                  <div class="inner_commands" data-repeater-list="commands_{{ $i }}" data-repid="{{ $i }}">
                    @if($isItemExist && $item->type == 0)
                      @php($commands = \App\Models\Command::where('item_type', \App\Models\Command::ITEM_COMMAND)->where('item_id', $item->id)->get())
                      @php($c = 0)
                      @foreach($commands as $key => $command)
                        @include('admin.items.blocks.minecraftServerSubCommand')
                        @php($c++)
                      @endforeach
                    @else
                      @php($c = 0)
                      @php($command = [['event'=>0,'command'=>'','servers'=>[],'is_online_required'=>1,'delay_value'=>0,'delay_unit'=>1,'repeat_unit_value'=>1,'repeat_cycles'=>1, 'execute_once_on_any_server'=> 0]])
                      @include('admin.items.blocks.minecraftServerSubCommand')
                    @endif
                  </div>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
