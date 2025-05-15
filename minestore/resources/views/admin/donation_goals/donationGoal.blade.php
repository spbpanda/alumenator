@extends('admin.layout')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/tagify/tagify.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/pickr/pickr-themes.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/sweetalert2/sweetalert2.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('res/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('res/vendor/libs/moment/moment.js')}}"></script>
<script src="{{asset('res/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('res/vendor/libs/pickr/pickr.js')}}"></script>
<script src="{{asset('res/vendor/libs/jquery-repeater/jquery-repeater.js')}}"></script>
<script src="{{asset('res/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('res/js/extended-ui-sweetalert2.js')}}"></script>
<script>
    var formRepeater = $(".form-repeater");
    var repeaterRow = 1;
    function repeaterUpdate(){
        $(".form-repeater").each(function() {
            var fromControl = $(this).find('.form-control, .form-select');
            var formLabel = $(this).find('.form-label');

            fromControl.each(function(i) {
                var id = 'form-repeater-' + repeaterRow;
                $(fromControl[i]).attr('id', id);
                $(formLabel[i]).attr('for', id);
                var $this = $(this);
                $this.attr('name', 'commands['+repeaterRow+'][cmd]');
                repeaterRow++;
            });

            $(this).slideDown();
        });
    }
    repeaterUpdate();
    formRepeater.repeater({
        initEmpty: {{ $isExist && !empty(json_decode($donationGoal->servers)) ? 'false' : 'true' }},
        show: function() {
            repeaterUpdate();
            $(this).slideDown();
        },
        hide: function(e) {
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
                    $(this).slideUp(e)
                    Swal.fire({
                        icon: 'success',
                        title: "{{ __('Deleted!') }}",
                        text: "{{ __('Command has been deleted successfully.') }}",
                        customClass: {
                            confirmButton: 'btn btn-success'
                        },
                    });
                }
            });
        }
    });

    $('input[name=\'cmdExecute\']').on('change',function(){
        if ($(this).is(':checked')){
            $('.donationGoalCommands').show();
        } else {
            $('.donationGoalCommands').hide();
        }
    });

    const flatpickrItems = document.querySelectorAll('.flatpickr-datetime');
    for(let flatpickrItem of flatpickrItems)
        flatpickrItem.flatpickr({
            enableTime: true,
            dateFormat: 'Y-m-d H:i'
        });

    flatpickr("#startAt, #disableAt", {
        dateFormat: "Y-m-d H:i",
        enableTime: true,
        minuteIncrement: 5,
    });

</script>
@endsection

@section('content')

<h4 class="fw-bold py-3 mb-1"><span class="text-body fw-light">{{ $isExist ? __('Edit') : __('New') }} {{ __('Donation Goal') }}</span></h4>

<form action="{{ $isExist ? route('donation_goals.update', $donationGoal->id) : route('donation_goals.store') }}" method="POST" autocomplete="off">
@csrf
@method($isExist ? 'PATCH' : 'POST')
<div class="col-12 mb-4">
	<div class="card">
		<div class="card-body">
			<div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label" for="name">
                        {{ __('Donation Goal Name') }}
                        <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Need to be unique.') }}"></i>
                    </label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="name" name="name" placeholder="Black Friday Sale" value="{{ $isExist ? $donationGoal->name : '' }}" required />
                    </div>
                    @error('name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-sm-6 mb-3" id="current_amount">
                    <label class="form-label" for="current_amount">
                        {{ __('Current Amount') }}
                        <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Amount of money that will be displayed as collected already.') }}"></i>
                    </label>
                    <div class="input-group">
                      <input type="text" inputmode="decimal" pattern="^\d*([,.]\d{1,2})?$" class="form-control" id="current_amount" name="current_amount" value="{{ $isExist ? $donationGoal->current_amount : '0' }}" aria-label="Amount of money to display as collected">
                      <span class="input-group-text">{{ $settings->currency }}</span>
                    </div>
                    @error('current_goal')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-sm-6 mb-3" id="goal_amount">
                    <label class="form-label" for="goal_amount">
                        {{ __('Goal Amount') }}
                        <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Amount of money that will be set as a goal.') }}"></i>
                    </label>
                    <div class="input-group">
                        <input type="text" inputmode="decimal" pattern="^\d*([,.]\d{1,2})?$" class="form-control" id="goal_amount" name="goal_amount" placeholder="99,99" value="{{ $isExist ? $donationGoal->goal_amount : '' }}" aria-label="Amount of money to display as the goal.">
                        <span class="input-group-text">{{ $settings->currency }}</span>
                    </div>
                    @error('goal_amount')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-sm-6 mb-3">
                    <label for="start_at" class="form-label">
                        {{ __('Start At') }}
                        <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Set a datetime when donation goal will be started.') }}"></i>
                    </label>
                    <input type="text" class="form-control flatpickr-datetime" id="start_at" placeholder="YYYY-MM-DD HH:MM" name="start_at" value="{{ $isExist ? $donationGoal->start_at : '' }}" />
                </div>
                <div class="col-sm-6 mb-3">
                    <label for="disable_at" class="form-label">
                        {{ __('Disable At') }}
                        <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Set a datetime when donation goal will be disabled.') }}"></i>
                    </label>
                    <input type="text" class="form-control flatpickr-datetime" id="disable_at" placeholder="YYYY-MM-DD HH:MM" name="disable_at" value="{{ $isExist ? $donationGoal->disable_at : '' }}" />
                </div>

                <div class="col-sm-12">
                    <div class="bg-lighter border rounded p-3 mb-3">
                        <label class="switch switch-square mb-2">
                            <input type="checkbox" name="status" {{ $isExist && $donationGoal->status ? 'checked' : '' }} class="switch-input" />
                            <span class="switch-toggle-slider">
                                <span class="switch-on"></span>
                                <span class="switch-off"></span>
                            </span>
                            <span class="switch-label">{{ __('Enable this Donation Goal and mark as Active?') }}</span>
                        </label>
                        <br>
                        @error('status')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <label class="switch switch-square mb-2">
                            <input type="checkbox" name="automatic_disabling" {{ $isExist && $donationGoal->automatic_disabling ? 'checked' : '' }} class="switch-input" />
                            <span class="switch-toggle-slider">
                                <span class="switch-on"></span>
                                <span class="switch-off"></span>
                            </span>
                            <span class="switch-label">{{ __('Automatically disable when hits 100%?') }}</span>
                        </label>
                        <br>
                        @error('automatic_disabling')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <label class="switch switch-square mb-2">
                            <input type="checkbox" name="cmdExecute" {{ $isExist && $donationGoal->cmdExecute ? 'checked' : '' }} class="switch-input" />
                            <span class="switch-toggle-slider">
                                <span class="switch-on"></span>
                                <span class="switch-off"></span>
                            </span>
                            <span class="switch-label">{{ __('Execute Global Commands when donation goal hits 100%?') }}</span>
                        </label>
                        @error('cmdExecute')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
			</div>
			</div>
		</div>
	</div>

    <div class="card donationGoalCommands" {!! $isExist && $donationGoal->cmdExecute ? '' : 'style="display:none"' !!}>
        <div class="card-body form-repeater">
            <div class="row">
                <div class="col-sm-8">
                    <h5 class="card-title">{{ __('Commands List') }}</h5>
                </div>
                <div class="col-sm-4 d-flex justify-content-end">
                    <button style="margin-right: 5px;" type="button" data-bs-toggle="modal" data-bs-target="#variablesModal" class="btn btn-sm btn-info mb-2"><span class="tf-icon bx bx-code-curly bx-xs"></span> {{ __('Available Variables') }}</button>
                    <button data-repeater-create type="button" class="btn btn-sm btn-primary mb-2" data-repeater-create=""><span class="tf-icon bx bx-plus-circle bx-xs"></span> {{ __('Add Goal Command') }}</button>
                </div>
            </div>

            <label for="servers" class="form-label">
                {{ __('Servers to execute commands') }}
                <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Select the server.') }}"></i>
            </label>
            @php($donationGoalServers = $isExist ? json_decode($donationGoal->servers) : [])
            <select id="servers" name="servers[]" class="selectpicker w-100" data-style="btn-default" multiple>
                <option {{ in_array("ALL", $donationGoalServers) ? "selected" : "" }} value="ALL">{{ __('All servers') }}</option>
                @foreach ($servers as $server)
                    <option {{in_array($server->id, $donationGoalServers ?: []) ? "selected": ""}} value="{{ $server->id }}">{{ $server->name }}</option>
                @endforeach
            </select>

            <div data-repeater-list="command">
                @if($isExist && !empty($donationGoalServers))
                    @php($cmds = json_decode($donationGoal->commands_to_execute, true))
                    @if($cmds)
                        @for($i = 0; $i < count($cmds); $i++)
                            @include('admin.donation_goals.command', ['i' => $i+1, 'command' => $cmds[$i]])
                        @endfor
                    @endif
                @else
                    @include('admin.donation_goals.command', ['i' => 1, 'command' => ''])
                @endif
            </div>
        </div>
    </div>

	<div class="row mt-3">
		<div class="d-grid gap-2 col-lg-12 mx-auto">
			<button class="btn btn-primary btn-lg" type="submit"><span class="tf-icon bx bx-plus-circle bx-xs"></span> {{ $isExist ? __('Save') : __('Create a Donation Goal') }} </button>
		</div>
	</div>
</form>


<!-- Variables Modal -->
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
                                <td>{goal}</td>
                                <td>{{ __('Donation goal name') }}</td>
                            </tr>
                            <tr>
                                <td>{current_amount}</td>
                                <td>{{ __('Current amount') }}</td>
                            </tr>
                            <tr>
                                <td>{goal_amount}</td>
                                <td>{{ __('Total goal amount') }}</td>
                            </tr>
                            <tr>
                                <td>{reached_at}</td>
                                <td>{{ __('Goal reached date and time (YYYY-MM-DD H:i:s)') }}</td>
                            </tr>
                            <tr>
                                <td>{automatic_disabling}</td>
                                <td>{{ __('Is goal automatic disabling? (0 - NO, 1 - YES)') }}</td>
                            </tr>
                            <tr>
                                <td>{time}</td>
                                <td>{{ __('Command execution time') }}</td>
                            </tr>
                            <tr>
                                <td>{date}</td>
                                <td>{{ __('Command execution date') }}</td>
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
