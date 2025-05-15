@extends('admin.layout')

@section('vendor-style')
    <style>
        button.btn-table:not(:disabled),button.btn-table:disabled,
        button.btn-table[type=button]:not(:disabled),
        button.btn-table[type=reset]:not(:disabled),
        button.btn-table[type=submit]:not(:disabled) {
            cursor: pointer;
            background: 0;
            border: 0;
        }
    </style>
<link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/animate-css/animate.css')}}">
<link rel="stylesheet" href="{{asset('res/vendor/libs/sweetalert2/sweetalert2.css')}}">
<link rel="stylesheet" href="{{asset('res/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('res/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('res/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('res/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script>
<script src="{{asset('res/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('js/modules/servers.js')}}"></script>
<script>
function checkServer(serverId){
    serverCheck(serverId).done(function(r) {
        Swal.fire({
            title: "{{ __('Success') }}",
            text: "{{ __('Check Minecraft Server Chat to Verify Connection') }}",
            icon: 'success',
            timer: 4000,
            customClass: {
                confirmButton: 'btn btn-primary'
            },
            buttonsStyling: false,
        });
    }).fail(function(r) {
        Swal.fire({
            title: "{{ __('Error') }}",
            text: "{{ __('Connection to the Minecraft Server was failed!') }}",
            icon: 'danger',
            timer: 4000,
            customClass: {
                confirmButton: 'btn btn-primary'
            },
            buttonsStyling: false,
        });
    });
}

function updateServer(event) {
    const serverId = $(event.currentTarget).data('server');
    const serverName = $('#serverName' + serverId).val();
    const serverSecret = $('#serverSecret' + serverId).val();
    const serverHost = $('#serverHost' + serverId).val();
    const serverPort = $('#serverPort' + serverId).val();
    const serverPassword = $('#serverPassword' + serverId).val();
    const serverMethod = $(event.currentTarget).data('method'); // Assuming you have a data-method attribute on the button

    let updateFunction;

    if (serverMethod === 'listener') {
        updateFunction = serverUpdatePlugin;
    } else if (serverMethod === 'rcon') {
        updateFunction = serverUpdateRCON;
    }

    if (updateFunction) {
        updateFunction(serverId, serverName, serverSecret, serverHost, serverPort, serverPassword)
            .done(function (r) {
                // Update UI elements on success
                $('#serverNameLabel' + serverId).text(serverName);
                $('#serverName_' + serverId).val(serverName);
                $('#serverSecret_' + serverId).val(serverSecret);
                $('#serverHost_' + serverId).val(serverHost);
                $('#serverPort_' + serverId).val(serverPort);
                $('#serverPassword_' + serverId).val(serverPassword);

                // Close the modal
                $('#modalServerEdit_' + serverId).modal('hide');

                Swal.fire({
                    title: "{{ __('Success') }}",
                    text: "{{ __('Saved') }}",
                    icon: 'success',
                    timer: 2000,
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false,
                });
            })
            .fail(function (r) {
                toastr.error("{{ __('Unable to update server!') }}");
            });
    }
}

function deleteServer(event,serverId){
    serverDelete(serverId).done(function(r) {
        event.target.parentElement.parentElement.parentElement.remove();
        toastr.success("{{ __('Server was Successfully Deleted!') }}");
    }).fail(function(r) {
        toastr.error("{{ __('Something went wrong, server deletion was failed!') }}");
    });
}
</script>
    <script>
        function confirmDelete(serverId, tablesRow) {
            Swal.fire({
                title: "{{ __('Are you sure?') }}",
                text: "{!! __('You won\'t be able to revert this!') !!}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ __('Yes, delete it!') }}",
                customClass: {
                    confirmButton: 'btn btn-primary me-1',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        method: "POST",
                        url: "{{ route('api.settings.server.destroy', '') }}/" + serverId,
                        data: {
                            '_method': 'DELETE',
                            'ajax': true,
                            '_token': '{{ csrf_token() }}',
                        },
                        success: function() {
                            // Update UI or do something on success
                            toastr.success("{{ __('Deleted Successfully!') }}");
                            $('#tableItem' + serverId).hide();
                        },
                        error: function() {
                            // Handle error
                            toastr.error("{{ __('Unable to Delete!') }}");
                        }
                    });
                }
            });
        }
    </script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-1">
  <span class="text-body fw-light">{{ __('Minecraft Servers') }}</span>
</h4>

@if(count($servers) == 0)
<div class="col-12 mb-4">
	<div class="card">
		<div class="row text-center">
		  <div class="card-body mt-2 mb-3">
			<i class="bx bx-server p-4 bx-lg bx-border-circle d-inline-block mb-4"></i>
			<p class="card-text mb-2" style="font-size: 17px;text-align: center; padding-left: 50px;padding-right: 50px;">
                {{ __('Here you can connect your Minecraft server (or servers) to your webstore.') }}
			</p>
			<a href="{{ route('settings.servers.create') }}" class="btn btn-primary btn-lg mt-2" type="button"><span class="tf-icon bx bx-plus bx-xs"></span> {{ __('Connect your first Minecraft Server') }}</a>
		  </div>
		</div>
	</div>
</div>
@else
<div class="col-12 mb-4">
		  <div class="col-12 mb-3">
            <div class="row align-items-right">
                <div class="col-12 pt-4 pt-md-0 d-flex justify-content-end">
                    <a href="{{ route('settings.servers.create') }}" class="btn btn-primary btn-sm fs-6 d-flex align-items-center gap-1">
                        <span class="tf-icon bx bx-plus-circle bx-xs"></span>
                        {{ __('Add a New Server') }}
                    </a>
                </div>
            </div>
		  </div>
			<div class="card">
			  <div class="table-responsive text-nowrap">
				<table class="table table-striped">
				  <thead>
					<tr>
					  <th>{{ __('Name') }}</th>
					  <th>{{ __('Secret Key') }}</th>
					  <th>{{ __('Actions') }}</th>
					</tr>
				  </thead>
				  <tbody class="table-border-bottom-0">
                    @foreach($servers as $server)
					<tr id="tableItem{{ $server->id }}">
					  <td><strong id="serverNameLabel{{ $server->id }}">{{ $server->name }}</strong></td>
					  <td>
					  	<div class="d-grid gap-2 col-lg-3">
							@if($server->method == 'listener')
							<button type="button" data-bs-toggle="modal" data-bs-target="#modalServer_{{ $server->id }}" class="btn rounded-pill btn-label-secondary">
								<span class="tf-icons bx bx-expand-alt me-1"></span>
                                {{ __('Reveal Secret Key') }}
							</button>
							@elseif($server->method == 'rcon')
							<button type="button" data-bs-toggle="modal" data-bs-target="#modalServer_{{ $server->id }}" class="btn rounded-pill btn-label-secondary">
								<span class="tf-icons bx bx-expand-alt me-1"></span>
                                {{ __('Reveal RCON Settings') }}
							</button>
							@endif
						</div>
					  </td>
					  <td>
						<a onclick="checkServer({{ $server->id }})" href="javascript:void(0);" title="{{ __('Check connection') }}">
							<span class="tf-icons bx bx-refresh text-success"></span>
						</a>
						<a data-bs-toggle="modal" data-bs-target="#modalServerEdit_{{ $server->id }}" href="javascript:void(0);">
							<span class="tf-icons bx bx-edit-alt text-primary"></span>
						</a>
                          <button onclick="confirmDelete({{ $server->id }})" class="tf-icons bx bx-x text-danger btn-table deleteButton" title="{{ __('Delete the server') }}">
                          </button>
					  </td>
					</tr>
                    @endforeach
				  </tbody>
				</table>
			  </div>
			</div>

      @foreach($servers as $server)
      <div class="modal fade" id="modalServer_{{ $server->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalCenterTitle">{{ __('Secret Token') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
			@if($server->method == 'listener')
            <div class="row">
            <div class="col mb-3">
              <label for="serverName_{{ $server->id }}" class="form-label">{{ __('Minecraft Server Name') }}</label>
              <input type="text" readonly id="serverName_{{ $server->id }}" class="form-control" value="{{ $server->name }}" autocomplete="false">
            </div>
            </div>
            <div class="row g-2">
              <div class="col-sm-12">
              <label class="form-label" for="serverSecret_{{ $server->id }}">{{ __('Server Secret Token') }}</label>
              <div class="input-group">
                <input type="text" readonly class="form-control" id="serverSecret_{{ $server->id }}" value="{{ $server->secret_key }}" autocomplete="false">
              </div>
              </div>
            </div>
			@elseif($server->method == 'rcon')
            <div class="row">
            <div class="col mb-3">
              <label for="serverName_{{ $server->id }}" class="form-label">{{ __('Minecraft Server Name') }}</label>
              <input type="text" readonly id="serverName_{{ $server->id }}" class="form-control" value="{{ $server->name }}" autocomplete="false">
            </div>
            </div>
            <div class="row g-2">
              <div class="col-sm-6">
              <label class="form-label" for="serverHost_{{ $server->id }}">{{ __('RCON Host IP') }}</label>
              <div class="input-group">
                <input type="text" readonly class="form-control" id="serverHost_{{ $server->id }}" value="{{ $server->host }}" autocomplete="false">
              </div>
              </div>
              <div class="col-sm-6">
              <label class="form-label" for="serverPort_{{ $server->id }}">{{ __('RCON Port') }}</label>
              <div class="input-group">
                <input type="number" readonly class="form-control" id="serverPort_{{ $server->id }}" value="{{ $server->port }}" autocomplete="false">
              </div>
              </div>
            </div>
			<div class="row">
				<div class="col-sm-12">
					<label class="form-label" for="serverPassword_{{ $server->id }}">{{ __('RCON Password') }}</label>
					<div class="input-group">
					  <input type="text" readonly class="form-control" id="serverPassword_{{ $server->id }}" value="{{ $server->password }}" autocomplete="false">
					</div>
				</div>
			</div>
			@endif
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-label-primary" data-bs-dismiss="modal">{{ __('Close') }}</button>
          </div>
          </div>
        </div>
      </div>

        <div class="modal fade" id="modalServerEdit_{{ $server->id }}" tabindex="-1" aria-hidden="true">
	       	<div class="modal-dialog modal-dialog-centered" role="document">
			  <div class="modal-content">
				<div class="modal-header">
				  <h5 class="modal-title" id="modalCenterTitle">{{ __('Minecraft Server Settings') }}</h5>
				  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
				  @if($server->method == 'listener')
				  <div class="row">
					<div class="col mb-3">
					  <label for="serverName{{ $server->id }}" class="form-label">{{ __('Minecraft Server Name') }}</label>
					  <input type="text" id="serverName{{ $server->id }}" class="form-control" value="{{ $server->name }}" autocomplete="false">
					</div>
				  </div>
				  <div class="row g-2">
					  <div class="col-sm-12 mb-4">
						<label class="form-label" for="serverSecret{{ $server->id }}">{{ __('Server Secret Token') }}</label>
						<div class="input-group">
							<input type="text" class="form-control" id="serverSecret{{ $server->id }}" value="{{ $server->secret_key }}" autocomplete="false">
						</div>
					  </div>
				  </div>
				  @elseif($server->method == 'rcon')
				  <div class="row">
					<div class="col mb-3">
					  <label for="serverName{{ $server->id }}" class="form-label">{{ __('Minecraft Server Name') }}</label>
					  <input type="text" id="serverName{{ $server->id }}" class="form-control" value="{{ $server->name }}" autocomplete="false">
					</div>
				  </div>
				  <div class="row g-2">
					  <div class="col-sm-6 mb-3">
						<label class="form-label" for="serverHost{{ $server->id }}">{{ __('RCON Host IP') }}</label>
						<div class="input-group">
							<input type="text" class="form-control" id="serverSecret{{ $server->id }}" value="{{ $server->host }}" autocomplete="false">
						</div>
					  </div>
					  <div class="col-sm-6 mb-3">
						<label class="form-label" for="serverPort{{ $server->id }}">{{ __('RCON Port') }}</label>
						<div class="input-group">
							<input type="number" class="form-control" id="serverPort{{ $server->id }}" value="{{ $server->port }}" autocomplete="false">
						</div>
					  </div>
				  </div>
				  <div class="row">
					<div class="col-sm-12">
						<label class="form-label" for="serverPassword{{ $server->id }}">{{ __('RCON Password') }}</label>
						<div class="input-group">
							<input type="text" class="form-control" id="serverPassword{{ $server->id }}" value="{{ $server->password }}" autocomplete="false">
						</div>
					</div>
				  </div>
				  @endif
				</div>
				<div class="modal-footer">
                    <button type="button" class="btn btn-primary serverSave" data-server="{{ $server->id }}" data-method="{{ $server->method }}" onclick="updateServer(event)">{{ __('Save') }}</button>
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
				</div>
			  </div>
			</div>
		  </div>
      @endforeach
</div>

@endif

<div class="row">
  <div class="col-12 mb-4">
      <x-card-input type="link" name="Download" value="https://minestorecms.com/plugin" icon="bxs-plug">
          <x-slot name="title">{{ __('Plugins') }}</x-slot>
          <x-slot name="tooltip">{{ __('Official plugins that used to deliver and manage packages on your Minecraft Server.') }}</x-slot>
          <x-slot name="text">{{ __('Download Official MineStore Plugins on your Minecraft Server.') }}</x-slot>
      </x-card-input>
  </div>
</div>
@endsection
