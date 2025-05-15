@extends('admin.layout')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('res/vendor/libs/bs-stepper/bs-stepper.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/select2/select2.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/flatpickr/flatpickr.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/pickr/pickr-themes.css')}}" />
@endsection

@section('vendor-script')
    <script src="{{asset('res/vendor/libs/bs-stepper/bs-stepper.js')}}"></script>
    <script src="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
    <script src="{{asset('res/vendor/libs/select2/select2.js')}}"></script>
    <script src="{{asset('res/vendor/libs/flatpickr/flatpickr.js')}}"></script>
    <script src="{{asset('res/vendor/libs/pickr/pickr.js')}}"></script>
    <script src="{{asset('res/vendor/libs/sortablejs/sortable.js')}}"></script>
@endsection

@section('page-script')
    <script src="{{asset('js/modules/staff_page.js')}}"></script>
    <script src="{{asset('res/js/forms-selects.js')}}"></script>

    <script>
        const cardEl = document.getElementById("sortable-cards");
        Sortable.create(cardEl,{
            onEnd: function(/**Event*/evt) {
                updateStaffGroupSorting(evt.oldIndex, evt.newIndex);
            }
        });

        let groupCount = {{ count($groupPlayers) }};
        for (let i = 1; i < groupCount + 1; i++) {
            Sortable.create(document.getElementById(`image-list-${i}`), {
                animation: 150,
                group: "imgList",
                onEnd: function(/**Event*/evt) {
                    let group = evt.from.parentElement.getAttribute("data-group");
                    updateStaffItemSorting(group, evt.oldIndex, evt.newIndex);
                }
            });
        }


    </script>
    <script src="{{asset('js/modules/staff_page.js')}}"></script>
    <script>
        // Should be moved in separate file
        toastr.options = {
            "closeButton": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        $("#is-staff-page-enabled").on("change", function() {
            let value = $(this).prop("checked") ? 1 : 0;
            updateIsStaffPageEnabledSetting(value);
        });

        $("#enabled-ranks").on("change", function() {
            let value = $(this).val();
            updateEnabledRanksSetting(value);
            setTimeout(function() {
                location.reload();
            }, 500);
        });

        $("#is-prefix-enabled").on("change", function() {
            let value = $(this).prop("checked") ? 1 : 0;
            updateIsPrefixEnabledSetting(value);
        });
    </script>

@endsection

@section('content')
    <h4 class="fw-bold py-3 mb-1">
        <span class="text-body fw-light">{{ __('Staff Page Module') }}</span>
    </h4>

    <form method="POST" autocomplete="off">
        @csrf

        <div class="row">
            <div class="col-12 mb-4">
                <x-card-input type="checkbox" id="is-staff-page-enabled" name="is_staff_page_enabled"
                              :checked="$is_staff_page_enabled" icon="bx-question-mark">
                    <x-slot name="title">{{ __('Enable this module?') }}</x-slot>
                    <x-slot name="text">{{ __('You need to enable "Staff Page" module to display it at your Webstore.') }}</x-slot>
                </x-card-input>
            </div>

            <div class="col-12 mb-4">
				<div class="card">
					<div class="card-body">
						<div class="row d-flex w-100 align-self-center">
							<div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
								<div class="row align-self-center h-100">
									<div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
										<div class="d-flex justify-content-center mb-4">
										  <div class="settings_icon bg-label-primary">
											  <i class="bx bxs-analyse"></i>
										  </div>
										</div>
									</div>
									<div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
										<h4>
                                            {{ __('Groups to Display at the Staff Page') }}
										</h4>
										<div class="mb-3 col-md-10">
											<p class="card-text">{{ __('Groups will be displayed at the Staff Page.') }}</p>
										</div>
									</div>
								</div>
							</div>
							<div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
								<div class="select2-primary">
								  <select id="enabled-ranks" name="g_stuff" class="select2 form-select" multiple>
									@foreach($ranks as $rank)
										<option @if(in_array($rank, $enabled_ranks)) selected @endif value="{{ $rank }}">{{ $rank }}</option>
									@endforeach
								  </select>
								</div>
							</div>
						</div>
					</div>
				</div>
            </div>

            <div class="col-12 mb-4">
                <x-card-input type="checkbox" id="is-prefix-enabled" name="is_prefix_enabled" :checked="$is_prefix_enabled"
                              icon="bxs-color">
                    <x-slot name="title">{{ __('Display Prefix Before Username?') }}</x-slot>
                    <x-slot name="text">{!! __('This option will display user\'s group prefix before the username.') !!}</x-slot>
                </x-card-input>
            </div>
        </div>

        <h4 class="fw-bold py-3 mb-1">
            <span class="text-body fw-light">{{ __('Groups & Users Sorting Settings') }}</span>
        </h4>
        <div class="col-12 mb-3">
            <div class="row align-items-right">
                <div class="col-12 pt-4 pt-md-0 d-flex justify-content-end">
                    <a href="{{ route('pages.staff.create') }}" class="btn btn-primary btn-sm fs-6 d-flex align-items-center gap-1">
                        <span class="tf-icon bx bx-plus-circle bx-xs"></span>
                        {{ __('Add a New User') }}
                    </a>
                </div>
            </div>
        </div>
        <!-- Cards Draggable -->
        <div class="row mb-4" id="sortable-cards">
            @foreach ($groupPlayers as $group => $players)
                <div class="col-lg-12 col-md-6 col-sm-12 mb-4">
                    <div class="card drag-item cursor-move mb-lg-0 mb-4">
                        <div class="card-body text-center" data-group="{{ $group }}">
                            <h4>{{ $group }}</h4>
                            <hr>
                            <div class="d-flex flex-wrap gap-3 justify-content-center"
                                 id="image-list-{{$loop->iteration}}">
                                @foreach ($players as $player)
                                    <div class="position-relative">
                                        <img class="rounded drag-item cursor-move"
                                             src="https://mc-heads.net/avatar/{{ $player->username }}/50"
                                             data-bs-toggle="tooltip"
                                             data-bs-placement="top"
                                             title="{{$player->username}}"
                                             alt="avatar" height="50" width="50" />
                                        <a href="{{ route('pages.staff.edit', $player->id) }}" class="position-absolute top-0 start-100 translate-middle d-flex align-items-center justify-content-center"
                                           style="width: 20px; height: 20px; background-color: #fe6c00; border-radius: 50%;">
                                            <i class="fas fa-edit text-white" style="font-size: 12px;"></i>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </form>
    <!-- /Cards Draggable ends -->
@endsection
