@extends('admin.layout')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('res/vendor/libs/bs-stepper/bs-stepper.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/spinkit/spinkit.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('res/vendor/libs/bs-stepper/bs-stepper.js')}}"></script>
<script src="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('res/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('res/vendor/libs/tagify/tagify.js')}}"></script>
<script src="{{asset('res/vendor/libs/jquery-repeater/jquery-repeater.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('res/js/forms-extras.js')}}"></script>
<script src="{{asset('res/js/forms-selects.js')}}"></script>
<script>
    $("#command-switcher").on('change',function(){
        if($(this).prop("checked")){
            $("#commands-block").show();
        }
        else{
            $("#commands-block").hide();
        }
    });

    $(function () {
        var formRepeater = $(".items-repeater");
        var repeaters = {'minecraft': {'main': 1, 'inner': 1}};

        function serverUpdate(){
            let subServers = $(this).parents('.row').find(".inner_commands select[data-name='servers']");
            let totalServersArray = $(this).val();
            if (totalServersArray.indexOf('0') > -1){
                totalServersArray = ['0'];
                $(this).unbind('change', serverUpdate);
                $(this).val(totalServersArray);
                $(this).trigger('change');
                $(this).bind('change', serverUpdate);
            }
            let isAllSelected = false;
            if (totalServersArray == ['0'] || totalServersArray == false || totalServersArray.length == 0){
                isAllSelected = true;
                let serverOptions = $(this).find("option");
                for (let i = 0; i < serverOptions.length; i++){
                    if (totalServersArray.indexOf(serverOptions[i].value) === -1)
                        totalServersArray.push(serverOptions[i].value);
                }
            }
            let serversHTML = "";
            for (let i = 0; i < totalServersArray.length; i++)
                serversHTML += `<option value="${totalServersArray[i]}">${$(this).find("option[value='"+totalServersArray[i]+"']:first").text()}</option>`;
            // let subServers = event.data.find("select[data-name='servers']");
            for (let i = 0; i < subServers.length; i++){
                let $subServer = $(subServers[i]);
                let currentServersArray = $subServer.val();
                $subServer.html(serversHTML);
                $subServer.val(currentServersArray.length == 0 ? (isAllSelected ? ['0'] : totalServersArray) : currentServersArray);
                $subServer.trigger('change');
            }
        }

        function repeaterUpdate(){
            $(".items-repeater .minecraftServerCommandBlock").each(function(){
                let fromControl = $(this).find('.form-control, .form-select');
                let formLabel = $(this).find('.form-label');
                $(this).find('.inner_commands').attr('data-repid', repeaters['minecraft']['main']);

                fromControl.each(function(i) {
                    if($(fromControl[i]).closest('.inner_commands').length == 0){
                        let $this = $(this);
                        let dataName = $this.attr('data-name');
                        if (!dataName) return;
                        let id = dataName + '' + repeaters['minecraft']['main'];
                        $(fromControl[i]).attr('id', id);
                        $(formLabel[i]).attr('for', id);
                        $this.attr('id', dataName + repeaters['minecraft']['main']);

                        if(this.tagName == 'SELECT'){
                            $this.attr('name', 'minecraft['+repeaters['minecraft']['main']+']['+dataName+']'+($this.hasAttr("multiple") ? '[]' : ''));
                            if($this.hasClass('select2') && !$this.data('select2'))
                                $this.select2();

                            if ($this.val() === null && $this.hasAttr("data-name")){
                                if ($this.attr('data-name') == "event" || $this.attr('data-name') == "delay_unit"){
                                    $this.val("0");
                                } else if ($this.attr('data-name') == "is_online_required" || $this.attr('data-name') == "repeat_unit"){
                                    $this.val("1");
                                }
                            }
                        } else {
                            $this.attr('name', 'minecraft['+repeaters['minecraft']['main']+']['+dataName+']');
                        }
                    }
                });

                $(this).slideDown();

                $(this).find(".inner_commands > div").each(function(){
                    let $this = $(this);
                    let fromControl = $this.find('.form-control, .form-select');
                    let formLabel = $this.find('.form-label');
                    let mainRepeaterId = $this.parent().attr('data-repid');
                    $this.attr('data-repid-inner', repeaters['minecraft']['inner']);

                    fromControl.each(function(i) {
                        let $this = $(this);
                        let dataName = $this.attr('data-name');
                        if (!dataName) return;
                        let id = dataName + '' + repeaters['minecraft']['inner'];
                        $(fromControl[i]).attr('id', id);
                        $(formLabel[i]).attr('for', id);

                        $this.attr('id', dataName + mainRepeaterId + '_' + repeaters['minecraft']['inner']);

                        if(this.tagName == 'SELECT'){
                            // $val = $this.val();
                            $this.attr('name', 'minecraft['+mainRepeaterId+'][commands]['+repeaters['minecraft']['inner']+']['+dataName+']'+($this.hasAttr("multiple") ? '[]' : ''));
                            if($this.hasClass('select2') && !$this.data('select2')){
                                $this.parent().find('.select2-container').remove();
                                $this.select2({
                                    dropdownParent: $this.parent(),
                                });
                                // $this.select2({
                                //     val: $val,
                                // });
                            }

                            if ($this.val() === null && $this.hasAttr("data-name")){
                                if ($this.attr('data-name') == "event" || $this.attr('data-name') == "delay_unit"){
                                    $this.val("0");
                                } else if ($this.attr('data-name') == "is_online_required" || $this.attr('data-name') == "repeat_unit"){
                                    $this.val("1");
                                }
                            }
                        } else {
                            $this.attr('name', 'minecraft['+mainRepeaterId+'][commands]['+repeaters['minecraft']['inner']+']['+dataName+']');
                        }
                    });

                    $this.find('.accordition-btn').attr('data-bs-target', '#accordition_command_'+mainRepeaterId+'_'+repeaters['minecraft']['inner']);
                    $this.find('.accordition-btn').attr('aria-controls', 'accordition_command_'+mainRepeaterId+'_'+repeaters['minecraft']['inner']);

                    $this.find('.accordion-item > div').attr('id', 'accordition_command_'+mainRepeaterId+'_'+repeaters['minecraft']['inner']);
                    $this.find('.accordion-item > div').attr('data-bs-parent', '#accordition_command_'+mainRepeaterId+'_'+repeaters['minecraft']['inner']);

                    repeaters['minecraft']['inner']++;

                    $this.slideDown();
                });

                $("#servers"+repeaters['minecraft']['main']).unbind('change', serverUpdate);
                $("#servers"+repeaters['minecraft']['main']).bind('change', serverUpdate);
                serverUpdate.call($("#servers"+repeaters['minecraft']['main']));

                repeaters['minecraft']['main']++;
            });
        }

        let submitFormTimer = null;
        formRepeater.repeater({
            initEmpty: false/*{{ $isExist ? 'false' : 'true' }}*/,
            repeaters: [{
                initEmpty: {{ !$isExist || empty($ref->commands) ? 'true' : 'false' }},
                selector: '.inner-repeater',
                show: function() {
                    repeaterUpdate();
                },
                hide: function(deleteElement) {
                    $(this).slideUp(deleteElement);
                    clearTimeout(submitFormTimer);
                    $('#submitFormButton').prop('disabled', true);
                    $('#submitFormButtonText').text('{{ __('Loading...') }}');
                    $('#submitFormButton .tf-icon').hide();
                    $('#submitFormLoader').show();
                    submitFormTimer = setTimeout(() => {
                        repeaterUpdate();
                        $('#submitFormButton').prop('disabled', false);
                        $('#submitFormLoader').hide();
                        $('#submitFormButton .tf-icon').show();
                        $('#submitFormButtonText').text($('#submitFormButtonText').attr('data-text'));
                    }, 6000);
                },
            }],
            show: function() {
                repeaterUpdate();
            },
            hide: function(deleteElement) {
                $(this).slideUp(deleteElement);
            },
        });

        const select2s = $('.select2');
        select2s.each(function () {
            var $this = $(this);
            $this.select2({
                dropdownParent: $this.parent()
            });
        });
        repeaterUpdate();
    });

    function repeatConfigButton(e, repeatStatus) {
        let repeatNode = e.currentTarget.parentElement.parentElement.parentElement;
        if (repeatStatus){
            $(repeatNode.querySelector(".repeatConfig-disabled")).hide();
            $(repeatNode.querySelector(".repeatConfig-enabled")).show();
        } else {
            $(repeatNode.querySelector(".repeatConfig-enabled input[data-name=\"repeat_value\"]")).val(0);

            $(repeatNode.querySelector(".repeatConfig-disabled")).show();
            $(repeatNode.querySelector(".repeatConfig-enabled")).hide();
        }
    }
</script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-1">
  <span class="text-body fw-light">{{ $isExist ? __('Edit') : __('New') }} {{ __('Creator Code') }}</span>
</h4>

<form action="{{ $isExist ? route('refs.update', $ref->id) : route('refs.store') }}" method="POST" autocomplete="off">
@csrf
@method($isExist ? 'PATCH' : 'POST')
<div class="col-12 mb-4">
	<div class="card mb-4">
	  <div class="card-body">
		<div class="row">
			<div class="col-md-12 mb-4">
                <label class="form-label" for="bs-validation-name">
                    {{ __('Creator Name') }}
                    <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('The information about referrer (inviter) user to display in the dashboard.') }}"></i>
                </label>
                <input type="text" class="form-control" id="bs-validation-name" name="referer" value="{{ old('referer', $isExist ? $ref->referer : '') }}" placeholder="MrBeast" required />
                @error('referer')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
			</div>
			<div class="col-md-6 mb-4">
				<label for="referrer_code" class="form-label">
                    {{ __('Creator Code') }}
					<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('This code will be used by client to activate it in the checkout.') }}"></i>
				</label>
				<input class="form-control" type="text" id="referrer_code" name="code" value="{{ old('code', $isExist ? $ref->code : '') }}" placeholder="MRBEST20OFF">
                @error('code')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
			</div>
			<div class="col-md-6 mb-4">
				<label for="referrer_code" class="form-label">
                    {{ __('Sharing part') }} (%)
					<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('This is shared percentage amount that will automatically calculate the profit for the creator.') }}"></i>
				</label>
				<div class="input-group mb-2">
					<input class="form-control" type="number" id="referrer_percentage" name="percent" value="{{ old('percent', $isExist ? $ref->percent : '') }}" placeholder="20">
					<span class="input-group-text">%</span>
				</div>
                @error('referrer_percentage')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
			</div>
		 </div>
		<div class="row">
			<div class="col mb-2">
				<div class="bg-lighter border rounded p-3 mb-3">
					<label class="switch switch-square">
						<input type="checkbox" name="cmd" class="switch-input" @checked(old('cmd') == "on" || ($isExist && $ref->cmd == 1)) />
						<span class="switch-toggle-slider">
							<span class="switch-on"></span>
							<span class="switch-off"></span>
						</span>
						<span class="switch-label">{{ __('Enable command execution after success transaction made by using this creator code?') }}</span>
					</label>
				</div>
			</div>
		 </div>
	  </div>
	</div>
</div>

@php
    $isItemExist = $isExist ?? false;
    $commands = isset($ref) && is_string($ref->commands)
        ? json_decode($ref->commands, true)
        : ($ref->commands ?? []);
    $isCommandsNotEmpty = is_array($commands) && count($commands) > 0;
@endphp
@if ($isExist && $isCommandsNotEmpty)
    <div class="items-repeater minecraftServerCommandBlock">
        <button id="itemsRepeaterCreate" type="button" data-repeater-create style="display:none"></button>
        <div data-repeater-list="command">
            @include('admin.refs.blocks.minecraftServerCommand', ['isEmpty' => false, 'i' => 0, 'servers' => $servers, 'item' => (object)['type' => 1, 'cmds' => \App\Models\Command::where('item_type', \App\Models\Command::REF_COMMAND)->where('item_id', $ref->id)->get(), 'servers' => \App\Models\ItemServer::where('type', \App\Models\ItemServer::TYPE_REF_COMMAND_SERVER)->where('item_id', $ref->id)->select('server_id')->get()->pluck('server_id')->toArray()]])
        </div>
    </div>
@else
    <div class="items-repeater minecraftServerCommandBlock">
        <button id="itemsRepeaterCreate" type="button" data-repeater-create style="display:none"></button>
        <div data-repeater-list="command">
            @include('admin.refs.blocks.minecraftServerCommand', ['isEmpty' => true, 'i' => 0, 'servers' => $servers, 'item' => (object)['type' => 1, 'cmds' => []]])
        </div>
    </div>
@endif

<div class="row">
	<div class="d-grid gap-2 col-lg-12 mx-auto">
       <button id="submitFormButton" class="btn btn-primary btn-lg">
           <span id="submitFormLoader" class="spinner-border me-1" role="status" aria-hidden="true" style="display: none;"></span>
           <span class="tf-icon bx bx-plus-circle bx-xs"></span>
           <span id="submitFormButtonText" data-text="{{ $isExist ? __('Save') : __('Create') }} {{ __('the Referral Code') }}">{{ $isExist ? __('Save') : __('Create') }} {{ __('the Referral Code') }}</span>
       </button>
    </div>
</div>
</form>


<div class="modal fade" id="variablesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCenterTitle">{{ __('List of Variables') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ __('Variable') }}</th>
                                    <th>{{ __('Meaning of the variable / Replaces with') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{inviter}</td>
                                    <td>{{ __('Inviter nickname') }}</td>
                                </tr>
                                <tr>
                                    <td>{inviter_uuid}</td>
                                    <td>{{ __('Inviter') }} UUID</td>
                                </tr>
                                <tr>
                                    <td>{inviterIP}</td>
                                    <td>{{ __('Inviter IP address') }}</td>
                                </tr>
                                <tr>
                                    <td>{referral}</td>
                                    <td>{{ __('Referral username') }}</td>
                                </tr>
                                <tr>
                                    <td>{referral_uuid}</td>
                                    <td>{{ __('Referral') }} UUID</td>
                                </tr>
                                <tr>
                                    <td>{referralIP}</td>
                                    <td>{{ __('Referral') }} IP</td>
                                </tr>
                                <tr>
                                    <td>{time}</td>
                                    <td>{{ __('Command execution time') }}</td>
                                </tr>
                                <tr>
                                    <td>{date}</td>
                                    <td>{{ __('Command execution date') }}</td>
                                </tr>
                                <tr>
                                    <td>{currency}</td>
                                    <td>{{ __('Currency') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="examplesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCenterTitle">{{ __('Examples for Minecraft Commands') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>{{ __('Command') }}</th>
                                <th>{{ __('Meaning of the Command') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>lp user {username} parent add vip</td>
                                <td>{{ __('Example of LuckyPerms command that add the role VIP to<br>the user who bought the package.') }}</td>
                            </tr>
                            <tr>
                                <td>say {username} bought the package {package_name} for {price} {currency}</td>
                                <td>{{ __('Announces that user bought the specific package for specific price.') }}</td>
                            </tr>
                            <tr>
                                <td>unban {username}</td>
                                <td>{{ __('Unbans user after the purchasing the package.') }}</td>
                            </tr>
                            <tr>
                                <td>unban {customerIP}</td>
                                <td>{{ __('Unbans user IP after the purchase.') }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
