@extends('admin.layout')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('res/vendor/libs/bs-stepper/bs-stepper.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/select2/select2.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/quill/typography.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/quill/editor.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/dropzone/dropzone.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/flatpickr/flatpickr.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/pickr/pickr-themes.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/spinkit/spinkit.css')}}" />
@endsection

@section('vendor-script')
    <script src="{{asset('res/vendor/libs/bs-stepper/bs-stepper.js')}}"></script>
    <script src="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
    <script src="{{asset('res/vendor/libs/select2/select2.js')}}"></script>
    <script src="{{asset('res/vendor/libs/quill/katex.js')}}"></script>
    <script src="{{asset('res/vendor/libs/quill/quill.js')}}"></script>
    <script src="{{asset('res/vendor/libs/dropzone/dropzone.js')}}"></script>
    <script src="{{asset('res/vendor/libs/flatpickr/flatpickr.js')}}"></script>
    <script src="{{asset('res/vendor/libs/jquery-repeater/jquery-repeater.js')}}"></script>
    <script src="{{asset('res/vendor/libs/moment/moment.js')}}"></script>
    <script src="{{asset('res/vendor/libs/pickr/pickr.js')}}"></script>
@endsection

@section('page-script')
    <script src="{{asset('res/js/forms-selects.js')}}"></script>
    <script src="{{asset('res/js/forms-extras.js')}}"></script>
    <script>
        $(function () {
            repeaterUpdate();

            $('#type').on('change', function(){
                if(this.value === "1"){ //gift
                    $('.minecraftServerCommandBlock').hide();
                    $('.giftcardBlock').show();
                } else { //package
                    $('.minecraftServerCommandBlock').show();
                    $('.giftcardBlock').hide();
                }
            });

            $('#is_subs').on('change', function(){
                if(this.value === "1"){
                    $('#chargePeriodBlock').show();
                } else {
                    $('#chargePeriodBlock').hide();
                }
            });
            $('#expireEnable').on('click', function(){
                $('#expireTitle').val('This Package Will Be Removed');
                $('#expireAfter').val(1);
                $('#expireBlock').show();
                $('#expireDisable').show();
                $(this).hide();
            });
            $('#expireDisable').on('click', function(){
                $('#expireTitle').val('Never Remove This Package');
                $('#expireAfter').val(0);
                $('#expireBlock').hide();
                $('#expireEnable').show();
                $(this).hide();
            });

            $('#quantityUserButtonConfigure').on('click', function(){
                $('#quantityUserLimitEnabled').show();
                $('#quantityUserLimitDisabled').hide();
            });
            $('#quantityUserButtonDisable').on('click', function(){
                $('#quantityUserLimitEnabled').hide();
                $('#quantityUserLimitDisabled').show();
                $('#quantityUserLimit').val('');
            });

            $('#quantityGlobalButtonConfigure').on('click', function(){
                $('#quantityGlobalLimitEnabled').show();
                $('#quantityGlobalLimitDisabled').hide();
            });
            $('#quantityGlobalButtonDisable').on('click', function(){
                $('#quantityGlobalLimitEnabled').hide();
                $('#quantityGlobalLimitDisabled').show();
                $('#quantityGlobalLimit').val('');
            });
        });

            function loadPreview(e, elementId) {
                const filePreviewElement = document.querySelector('#preview-'+elementId);
                filePreviewElement.src = URL.createObjectURL(e.currentTarget.files[0]);
            }
            function clearImage(elementId) {
                document.getElementById('preview-'+elementId).src = "";
                document.getElementById(elementId).value = null;
            }

            function repeatConfigButton(e, repeatStatus) {
                let repeatNode = e.currentTarget.parentElement.parentElement.parentElement;
                if (repeatStatus){
                    $(repeatNode.querySelector(".repeatConfig-disabled")).hide();
                    $(repeatNode.querySelector(".repeatConfig-enabled")).show();
                } else {
                    $(repeatNode.querySelector(".repeatConfig-enabled input[data-name=\"repeat_value\"]")).val(0);
                    $(repeatNode.querySelector(".repeatConfig-enabled input[data-name=\"repeat_cycles\"]")).val(0);

                    $(repeatNode.querySelector(".repeatConfig-disabled")).show();
                    $(repeatNode.querySelector(".repeatConfig-enabled")).hide();
                }
            }

            const wizardNumbered = document.querySelector('.wizard-numbered'),
                wizardNumberedBtnNextList = [].slice.call(wizardNumbered.querySelectorAll('.btn-next')),
                wizardNumberedBtnPrevList = [].slice.call(wizardNumbered.querySelectorAll('.btn-prev')),
                wizardNumberedBtnSubmit = wizardNumbered.querySelector('.btn-submit');

            if (typeof wizardNumbered !== undefined && wizardNumbered !== null) {
                const numberedStepper = new Stepper(wizardNumbered, {
                    linear: false
                });
                if (wizardNumberedBtnNextList) {
                    wizardNumberedBtnNextList.forEach(wizardNumberedBtnNext => {
                        wizardNumberedBtnNext.addEventListener('click', event => {
                            numberedStepper.next();
                        });
                    });
                }
                if (wizardNumberedBtnPrevList) {
                    wizardNumberedBtnPrevList.forEach(wizardNumberedBtnPrev => {
                        wizardNumberedBtnPrev.addEventListener('click', event => {
                            numberedStepper.previous();
                        });
                    });
                }
                if (wizardNumberedBtnSubmit) {
                    wizardNumberedBtnSubmit.addEventListener('click', event => {

                    });
                }
            }

            const fullToolbar = [
                [
                    {
                        font: []
                    },
                    {
                        size: []
                    }
                ],
                ['bold', 'italic', 'underline', 'strike'],
                [
                    {
                        color: []
                    },
                    {
                        background: []
                    }
                ],
                [
                    {
                        script: 'super'
                    },
                    {
                        script: 'sub'
                    }
                ],
                [
                    {
                        header: '1'
                    },
                    {
                        header: '2'
                    },
                    'blockquote',
                    'code-block'
                ],
                [
                    {
                        list: 'ordered'
                    },
                    {
                        list: 'bullet'
                    },
                    {
                        indent: '-1'
                    },
                    {
                        indent: '+1'
                    }
                ],
                [
                    'direction',
                    {
                        align: []
                    }
                ],
                ['link', 'image', 'video', 'formula'],
                ['clean']
            ];
            const fullEditor = new Quill('#description-editor', {
                bounds: '#description-editor',
                placeholder: 'Type Something...',
                modules: {
                    formula: true,
                    toolbar: fullToolbar
                },
                theme: 'snow'
            });
            $("form").on("submit",function() {
                $("#description").val($("#description-editor .ql-editor").html());
            });

            const flatpickrItems = document.querySelectorAll('.flatpickr-datetime');
            for(let flatpickrItem of flatpickrItems)
                flatpickrItem.flatpickr({
                    enableTime: true,
                    dateFormat: 'Y-m-d H:i'
                });

            var formRepeater = $(".items-repeater");
            var repeaters = {'minecraft': {'main': 1, 'inner': 1}, 'gift': {'main': 1, 'inner': 1}};

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
                    });

                    $('.server_details').attr('id', 'server_details'+repeaters['minecraft']['main']);
                    $('.server_details_btn').attr('data-bs-target', '#server_details'+repeaters['minecraft']['main']);
                    $('.is_server_choice').attr('for', 'is_server_choice'+repeaters['minecraft']['main']);

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
                initEmpty: false/*{{ $isItemExist ? 'false' : 'true' }}*/,
                repeaters: [{
                    initEmpty: {{ $isItemExist ? 'false' : 'true' }},
                    selector: '.inner-repeater',
                    show: function() {
                        $(this).slideDown();
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

            @if(count([]) > 0)
            // const select2s = $('.items-repeater .select2, .gifts-repeater .select2');
            // select2s.each(function () {
            //   var $this = $(this);
            //   $this.select2({
            //     dropdownParent: $this.parent()
            //   });
            // });
            @endif

            $('#addMinecraftServerCommandsButton').on('click', function(){
                $('#itemsRepeaterCreate').click();
            });
            $('#addGiftCardButton').on('click', function(){
                $('#giftsRepeaterCreate').click();
            });

            $('#category_id').on('change', function () {
                const categoriesIsComparison = {
                    @foreach($categories as $cat){!! "'" . $cat->id . "': " . ($cat->is_comparison == 1 ? 'true' : 'false') !!},@endforeach
                };
                $('.comparisonSection').hide();

                const currentCategoryId = this.value;
                if (categoriesIsComparison.hasOwnProperty(currentCategoryId) && categoriesIsComparison[currentCategoryId]){
                    $.ajax({
                        url: '/admin/categories/comparisons/'+currentCategoryId,
                        dataType: 'json',
                        async: true,
                    }).done(function(comparisons){
                        $('.comparisonSectionBlock').html('');
                        for(let i = 0; i < comparisons.length; i++)
                        {
                            $('.comparisonSectionBlock').append(`
                <div class="row justify-content-center">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Comparison Parameter (Read Only)</label>
                        <input type="text" class="form-control" value="${comparisons[i]['name']}" readonly />
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="mb-3">` +
                                ((comparisons[i]['type'] == 1) ?
                                    `<label for="comparison${i}" class="form-label">Comparison Value</label>
                        <input type="text" name="comparison[${comparisons[i]['id']}]" value="" class="form-control" id="comparison${i}" placeholder="<p style='color: #fff'>VIP+</p>">`
                                    :
                                    `<label class="switch switch-square switch-lg">
                <input type="hidden" value="0" name="comparison[${comparisons[i]['id']}]">
                <input type="checkbox" value="1" class="switch-input" name="comparison[${comparisons[i]['id']}]">
                                    <span class="switch-toggle-slider" style="margin-top:35px;">
                                      <span class="switch-on">
                                        <i class="bx bx-check"></i>
                                      </span>
                                      <span class="switch-off">
                                        <i class="bx bx-x"></i>
                                      </span>
                                    </span>
                            </label>`)
                                + `</div></div>
                </div>`);
                        }
                        $('.comparisonSection').show();
                    });
                }
            });

            @if(!$isItemExist && isset($_GET['category']))
            $('#category_id').val('{{ $_GET['category'] }}').trigger('change');
           @endif
           flatpickr("#publishAt, #showUntil", {
               dateFormat: "Y-m-d H:i",
               enableTime: true,
               minuteIncrement: 5,
           });
    </script>
@endsection

@section('content')
    <h4 class="fw-bold py-3 mb-1">
        <span class="text-body fw-light">{{ $isItemExist ? __('Edit') : __('New') }} {{ __('Package') }}</span>
    </h4>

    <form method="POST" enctype="multipart/form-data" autocomplete="off">
        @csrf
        <div class="col-12 mb-4 basicBlock">
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label" for="name">
                                    {{ __('Name') }}
                                </label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $isItemExist ? $item->name : '') }}" placeholder="100x Keys Bundle" required />
                                @error('name')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-2">
                                <label for="description" class="form-label">
                                    {{ __('Description') }}
                                    <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('The description might include review of the package that user purchase. It will be displayed when client checks it.') }}"></i>
                                </label>
                                <textarea style="display:none" id="description" name="description"></textarea>
                                <div id="description-editor">
                                    {!! old('description', $isItemExist ? $item->description : '') !!}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="category_id" class="form-label">
                                        {{ __('Category') }}
                                        <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Select the category (or subcategory) that this package will appear.') }}"></i>
                                    </label>
                                    <select id="category_id" name="category_id" class="selectpicker w-100" data-style="btn-default">
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" @selected(old('category_id', $isItemExist ? $item->category_id : null) == $cat->id)>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="type" class="form-label">
                                        {{ __('Package Type') }}
                                        <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Select the type for package delivery.') }}"></i>
                                    </label>
                                    <select class="selectpicker w-100 show-tick" id="type" name="type" data-icon-base="bx" data-tick-icon="bx-check" data-style="btn-default">
                                        <option data-icon="bx-package" value="0" @selected(old('type', $isItemExist ? $item->type : \App\Models\Item::MINECRAFT_PACKAGE) == \App\Models\Item::MINECRAFT_PACKAGE)>{{ __('Minecraft Package') }}</option>
                                        <option data-icon="bx-gift" value="1" @selected(old('type', $isItemExist ? $item->type : \App\Models\Item::MINECRAFT_PACKAGE) == \App\Models\Item::GIFTCARD)>{{ __('Giftcard') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="mb-5">
                                <label class="form-label">
                                    {{ __('Image') }}
                                    <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Recommended max height of 180px.') }}"></i>
                                </label>

                                <div class="align-self-center text-center mx-auto">
                                    <img src="{{ $isItemExist && $item->image ? asset('/img/items/' . $item->image) : asset('/res/img/question-icon.png') }}"
                                         alt="Image"
                                         id="preview-icon"
                                         class="rounded mb-2"
                                         height="185"
                                         width="180"
                                         onerror="this.src='{{ asset('/res/img/question-icon.png') }}';">
                                    <div class="button-wrapper">
                                        <label for="icon" class="btn btn-primary me-2 mt-2 mb-2" tabindex="0">
                                            <span class="d-none d-sm-block">{{ __('Upload Image') }}</span>
                                            <i class="bx bx-upload d-block d-sm-none"></i>
                                            <input type="file" id="icon" name="image" onchange="loadPreview(event, 'icon')" class="account-file-input" hidden accept="image/png, image/jpeg, image/gif" />
                                        </label>

                                        <p class="text-muted mb-0">{{ __('Allowed PNG, JPG, GIF') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2" style="margin-bottom:87px;">
                                <div class="row">
                                    <label for="item_id" class="form-label">{{ __('Minecraft GUI Item') }}</label>
                                    <div class="mb-4">
                                        <input type="text" id="item_id" name="item_id" value="{{ $isItemExist ? $item->item_id : '' }}" class="form-control" placeholder="minecraft:grass" aria-label="Minecrat In-Game Item ID">
                                        <!--<button class="btn btn-outline-primary" disabled type="button" id="button-addon2">{{ __('Browse') }}</button> --->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Default Wizard -->
        <div class="col-12 mb-4 wizardStepper">
            <div class="bs-stepper wizard-numbered mt-2">
                <div class="bs-stepper-header" style="justify-content: center;">
                    <div class="step" data-target="#pricing">
                        <button type="button" class="step-trigger">
                            <span class="bs-stepper-circle">1</span>
                            <span class="bs-stepper-label mt-1">
              <span class="bs-stepper-title">{{ __('Pricing') }}</span>
              <span class="bs-stepper-subtitle">{{ __('Setup Pricing and Type') }}</span>
            </span>
                        </button>
                    </div>
                    <div class="line">
                        <i class="bx bx-chevron-right"></i>
                    </div>
                    <div class="step" data-target="#visibility">
                        <button type="button" class="step-trigger">
                            <span class="bs-stepper-circle">2</span>
                            <span class="bs-stepper-label mt-1">
              <span class="bs-stepper-title">{{ __('Visibility') }}</span>
              <span class="bs-stepper-subtitle">{{ __('Setup Timing & Visibility') }}</span>
            </span>
                        </button>
                    </div>
                    <div class="line">
                        <i class="bx bx-chevron-right"></i>
                    </div>
                    <div class="step" data-target="#limits">
                        <button type="button" class="step-trigger">
                            <span class="bs-stepper-circle">3</span>
                            <span class="bs-stepper-label mt-1">
              <span class="bs-stepper-title">{{ __('Limits') }}</span>
              <span class="bs-stepper-subtitle">{{ __('Setup Limits for the Package') }}</span>
            </span>
                        </button>
                    </div>
                    <div class="line">
                        <i class="bx bx-chevron-right"></i>
                    </div>
                    <div class="step" data-target="#others">
                        <button type="button" class="step-trigger">
                            <span class="bs-stepper-circle">4</span>
                            <span class="bs-stepper-label mt-1">
              <span class="bs-stepper-title">{{ __('Others') }}</span>
              <span class="bs-stepper-subtitle">{{ __('Setup Other Settings for the Package') }}</span>
            </span>
                        </button>
                    </div>
                </div>
                <div class="bs-stepper-content">
                    <!-- Pricing -->
                    <div id="pricing" class="content">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label" for="price">
                                    {{ __('Price') }}
                                    <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Price amount that customer will pay for this package and currency to pay. If you select Virtual Currency, you can NOT give your customers ability to pay with real money.') }}"></i>
                                </label>
                                <div class="input-group">
                                    <input id="price" name="price" type="text" inputmode="numeric" pattern="^\d*([,.]\d{1,2})?$"
                                           @if($isItemExist && $item->price)
                                               value="{{ $isItemExist ? $item->price : '' }}"
                                           @elseif($isItemExist && $item->is_virtual_currency_only == '1')
                                               value="{{ $item->virtual_price }}"
                                           @endif
                                           class="form-control" placeholder="9,99" aria-label="Amount of money your customers will pay"
                                    />
                                    <select class="form-select" id="is_virtual_currency_only" name="is_virtual_currency_only" aria-label="Select the way to pay.">
                                        <option {{ !$isItemExist || $item->is_virtual_currency_only == 0 ? 'selected' : '' }} value="0">{{ $settings->currency }}</option>
                                        <option {{ $isItemExist && $item->is_virtual_currency_only == 1 ? 'selected' : '' }} value="1">QQ (Virtual Currency)</option>
                                    </select>
                                </div>
                                @error('price')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-sm-6" style="margin-top: auto; margin-bottom: 10px;">
                                <label class="switch switch-square">
                                    <input type="checkbox" class="switch-input" name="is_any_price" {{ $isItemExist && $item->is_any_price == 1 ? 'checked' : '' }} />
                                    <span class="switch-toggle-slider">
					<span class="switch-on"></span>
					<span class="switch-off"></span>
				  </span>
                                    <span class="switch-label">{{ __('Allow customers to pay what they want?') }}</span>
                                </label>
                            </div>
                            <div class="col-sm-6">
                                <label for="eco_server" class="form-label">
                                    {{ __('Server to charge Virtual Currency') }}
                                    <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Select the server to charge customers virtual currency by economy plugin.') }} It is not required option."></i>
                                </label>
                                <select id="eco_server" name="eco_server[]" class="selectpicker w-100" data-style="btn-default" {{ $isItemExist && $item->is_virtual_currency_only == 0 ? 'disabled' : '' }} multiple>
                                    <option {{in_array("ALL", ($isItemExist ? $item->eco_server : null) ?: []) ? "selected": ""}} value="ALL">{{ __('All servers') }}</option>
                                    @foreach ($servers as $server)
                                        <option {{in_array($server->id, ($isItemExist ? $ecoServers : null) ?: []) ? "selected": ""}} value="{{ $server->id }}">{{ $server->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="discount">
                                    {{ __('Discount') }}
                                    <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Amount in percents that will dicrease original price as a sale.') }}"></i>
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="discount" name="discount" value="{{ $isItemExist ? $item->discount : '0' }}" aria-label="Amount to discount original price">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label for="is_subs" class="form-label">
                                    {{ __('Recurring Payment Type') }}
                                    <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Recurring Payment Type: One-Time-Charge or Subscription-Based Recurring Plan.') }}"></i>
                                </label>
                                <select id="is_subs" name="is_subs" class="selectpicker w-100" data-style="btn-default">
                                    <option value="0" {{ !$isItemExist || $item->is_subs == 0 ? 'selected' : '' }}>{{ __('Charge the customer once') }}</option>
                                    <option value="1" {{ $isItemExist && $item->is_subs == 1 ? 'selected' : '' }}>{{ __('Charge the customer every X amount of time (Subscription Mode)') }}</option>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">
                                    {{ __('Remove Package From Customer After') }}
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="expireTitle" disabled value="Never remove this package">
                                    <button class="btn btn-outline-primary" type="button" id="expireEnable" @if($isItemExist && $item->expireAfter > 0) style="display: none;" @endif >{{ __('Configure') }}</button>
                                    <button class="btn btn-outline-danger" type="button" id="expireDisable" @if(!$isItemExist || $item->expireAfter == 0) style="display: none;" @endif >{{ __('Disable') }}</button>
                                </div>
                            </div>
                            <!--- EXAMPLES FOR RECURRING PAYMENT TYPE (EXPIRE TIME) -->
                            <div class="col-sm-6" id="chargePeriodBlock" @if(!$isItemExist || $item->is_subs == 0) style="display: none;" @endif>
                                <label for="chargePeriodValue" class="form-label">
                                    {{ __('Charge Period') }}
                                </label>
                                <div class="input-group">
                                    <input type="number" id="chargePeriodValue" name="chargePeriodValue" value="{{ $isItemExist ? $item->chargePeriodValue : 1}}" aria-label="expire_amount" class="form-control">
                                    <select id="chargePeriodUnit" name="chargePeriodUnit" class="form-select" data-style="btn-default">
                                        <option value="1" {{ $isItemExist && $item->chargePeriodUnit == 1 ? "selected" : "" }}>{{ __('Day') }}</option>
                                        <option value="2" {{ $isItemExist && $item->chargePeriodUnit == 2 ? "selected" : "" }}>{{ __('Week') }}</option>
                                        <option value="3" {{ !$isItemExist || $item->chargePeriodUnit == 3 ? "selected" : "" }}>{{ __('Month') }}</option>
                                        <option value="4" {{ $isItemExist && $item->chargePeriodUnit == 4 ? "selected" : "" }}>{{ __('Year') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6" id="expireBlock" @if(!$isItemExist || $item->expireAfter == 0) style="display: none;" @endif>
                                <label for="expire_time" class="form-label">
                                    {{ __('Remove Package From Customer After') }}
                                </label>
                                <div class="input-group">
                                    <input type="number" value="{{ $isItemExist ? $item->expireAfter : 0 }}" id="expireAfter" name="expireAfter" class="form-control">
                                    <select id="expireUnit" name="expireUnit" class="form-select" data-style="btn-default">
                                        <option value="0" {{ $isItemExist && $item->expireUnit == 0 ? "selected" : "" }}>{{ __('Minute') }}</option>
                                        <option value="1" {{ $isItemExist && $item->expireUnit == 1 ? "selected" : "" }}>{{ __('Hour') }}</option>
                                        <option value="2" {{ $isItemExist && $item->expireUnit == 2 ? "selected" : "" }}>{{ __('Day') }}</option>
                                        <option value="3" {{ $isItemExist && $item->expireUnit == 3 ? "selected" : "" }}>{{ __('Week') }}</option>
                                        <option value="4" {{ !$isItemExist || $item->expireUnit == 4 ? "selected" : "" }}>{{ __('Month') }}</option>
                                        <option value="5" {{ $isItemExist && $item->expireUnit == 5 ? "selected" : "" }}>{{ __('Year') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6"></div>
                            <!--- END OF EXAMPLES FOR RECURRING PAYMENT TYPE (EXPIRE TIME) -->
                            <div class="col-12 d-flex justify-content-between">
                                <button class="btn btn-label-secondary btn-prev" type="button" disabled>
                                    <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                    <span class="align-middle d-sm-inline-block d-none">{{ __('Previous') }}</span>
                                </button>
                                <button class="btn btn-primary btn-next" type="button">
                                    <span class="align-middle d-sm-inline-block d-none me-sm-1">{{ __('Next') }}</span>
                                    <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Visibility -->
                    <div id="visibility" class="content">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label for="publishAt" class="form-label">
                                    {{ __('Publish On Webstore At') }}
                                    <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Set a datetime when package will be published in your webstore.') }}"></i>
                                </label>
                                <input type="text" class="form-control flatpickr-datetime" id="publishAt" placeholder="YYYY-MM-DD HH:MM" name="publishAt" value="{{ $isItemExist ? $item->publishAt : '' }}" />
                            </div>
                            <div class="col-sm-6">
                                <label for="showUntil" class="form-label">
                                    {{ __('Remove From Webstore After') }}
                                    <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Set a datetime when package will be removed from your webstore.') }}"></i>
                                </label>
                                <input type="text" class="form-control flatpickr-datetime" id="showUntil" placeholder="YYYY-MM-DD HH:MM" name="showUntil" value="{{ $isItemExist ? $item->showUntil : '' }}" />
                            </div>
                            <div class="col-sm-12">
                                <div class="bg-lighter border rounded p-3 mb-3">
                                    <label class="switch switch-square">
                                        <input type="checkbox" name="active" {{ !$isItemExist || $item->active == 1 ? "checked" : ""}} class="switch-input" />
                                        <span class="switch-toggle-slider">
                                            <span class="switch-on"></span>
                                            <span class="switch-off"></span>
                                        </span>
                                        <span class="switch-label">{{ __('Enable this package and make it visible?') }}</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-12 d-flex justify-content-between">
                                <button class="btn btn-primary btn-prev" type="button">
                                    <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                    <span class="align-middle d-sm-inline-block d-none">{{ __('Previous') }}</span>
                                </button>
                                <button class="btn btn-primary btn-next" type="button">
                                    <span class="align-middle d-sm-inline-block d-none me-sm-1">{{ __('Next') }}</span>
                                    <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Limits -->
                    <div id="limits" class="content">
                        <div class="row g-3">
                            <div class="col-sm-12 mb-1">
                                <label class="form-label">
                                    {{ __('Quantity Limit per User') }}
                                </label>
                                <div class="input-group" id="quantityUserLimitDisabled" @if($isItemExist && $item->quantityUserLimit > 0) style="display: none;" @endif>
                                    <input type="text" class="form-control" disabled value="Do not set quantity limit per user for this package." aria-label="Do not set quantity limit per user for this package.">
                                    <button class="btn btn-outline-primary" type="button" id="quantityUserButtonConfigure">{{ __('Configure') }}</button>
                                </div>
                                <div class="input-group" id="quantityUserLimitEnabled" @if(!$isItemExist || $item->quantityUserLimit == 0) style="display: none;" @endif>
                                    <input type="number" class="form-control" id="quantityUserLimit" name="quantityUserLimit" value="{{ $isItemExist ? $item->quantityUserLimit : 0 }}">
                                    <span class="input-group-text">{{ __('purchase every') }}</span>
                                    <input type="number" class="form-control" name="quantityUserPeriodValue" value="{{ $isItemExist ? \App\Helpers\QuantityHelper::GetOriginPeriodValue($item->quantityUserPeriodUnit, $item->quantityUserPeriodValue) : 0 }}">
                                    <select id="quantityUserPeriodUnit" name="quantityUserPeriodUnit" class="form-select" data-style="btn-default">
                                        <option value="-1" {{ !$isItemExist || $item->quantityUserPeriodUnit == -1 ? "selected" : ""}}>{{ __('No Period') }}</option>
                                        <option value="0" {{ $isItemExist && $item->quantityUserPeriodUnit == 0 ? "selected" : ""}}>{{ __('Minute') }}</option>
                                        <option value="1" {{ $isItemExist && $item->quantityUserPeriodUnit == 1 ? "selected" : ""}}>{{ __('Hour') }}</option>
                                        <option value="2" {{ $isItemExist && $item->quantityUserPeriodUnit == 2 ? "selected" : ""}}>{{ __('Day') }}</option>
                                        <option value="3" {{ $isItemExist && $item->quantityUserPeriodUnit == 3 ? "selected" : ""}}>{{ __('Week') }}</option>
                                        <option value="4" {{ $isItemExist && $item->quantityUserPeriodUnit == 4 ? "selected" : ""}}>{{ __('Month') }}</option>
                                        <option value="5" {{ $isItemExist && $item->quantityUserPeriodUnit == 5 ? "selected" : ""}}>{{ __('Year') }}</option>
                                    </select>
                                    <button type="button" class="btn btn-icon btn-danger" id="quantityUserButtonDisable">
                                        <span class="tf-icons bx bx-x"></span>
                                    </button>
                                </div>
                            </div>
                            <div class="col-sm-12 mb-2">
                                <label class="form-label">
                                    {{ __('Global Quantity Limit') }}
                                </label>
                                <div class="input-group" id="quantityGlobalLimitDisabled" @if($isItemExist && $item->quantityGlobalLimit > 0) style="display: none;" @endif>
                                    <input type="text" class="form-control" disabled value="Do not set quantity limit per user for this package." aria-label="Do not set quantity limit per user for this package.">
                                    <button class="btn btn-outline-primary" type="button" id="quantityGlobalButtonConfigure">{{ __('Configure') }}</button>
                                </div>
                                <div class="input-group" id="quantityGlobalLimitEnabled" @if(!$isItemExist || $item->quantityGlobalLimit == 0) style="display: none;" @endif>
                                    <input type="number" class="form-control" id="quantityGlobalLimit" name="quantityGlobalLimit" value="{{ $isItemExist ? $item->quantityGlobalLimit : 0 }}">
                                    <span class="input-group-text">{{ __('purchase every') }}</span>
                                    <input type="number" class="form-control" name="quantityGlobalPeriodValue" value="{{ $isItemExist ? \App\Helpers\QuantityHelper::GetOriginPeriodValue($item->quantityGlobalPeriodUnit, $item->quantityGlobalPeriodValue) : 0 }}">
                                    <select id="quantityGlobalPeriodUnit" name="quantityGlobalPeriodUnit" class="form-select" data-style="btn-default">
                                        <option value="-1" {{ !$isItemExist || $item->quantityGlobalPeriodUnit == -1 ? "selected" : ""}}>{{ __('No Period') }}</option>
                                        <option value="0" {{ $isItemExist && $item->quantityGlobalPeriodUnit == 0 ? "selected" : ""}}>{{ __('Minute') }}</option>
                                        <option value="1" {{ $isItemExist && $item->quantityGlobalPeriodUnit == 1 ? "selected" : ""}}>{{ __('Hour') }}</option>
                                        <option value="2" {{ $isItemExist && $item->quantityGlobalPeriodUnit == 2 ? "selected" : ""}}>{{ __('Day') }}</option>
                                        <option value="3" {{ $isItemExist && $item->quantityGlobalPeriodUnit == 3 ? "selected" : ""}}>{{ __('Week') }}</option>
                                        <option value="4" {{ $isItemExist && $item->quantityGlobalPeriodUnit == 4 ? "selected" : ""}}>{{ __('Month') }}</option>
                                        <option value="5" {{ $isItemExist && $item->quantityGlobalPeriodUnit == 5 ? "selected" : ""}}>{{ __('Year') }}</option>
                                    </select>
                                    <button type="button" class="btn btn-icon btn-danger" id="quantityGlobalButtonDisable">
                                        <span class="tf-icons bx bx-x"></span>
                                    </button>
                                </div>
                            </div>
                            <div class="col-sm-8 mb-2">
                                <label for="reqs" class="form-label">
                                    {{ __('Required Packages') }}
                                    <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Select required packages that customer need to have to buy this item.') }}"></i>
                                </label>
                                <select id="reqs" class="select2 form-select" name="reqs[]" multiple>
                                    @foreach ($reqs as $req)
                                        @php
                                            $required_items = json_decode($isItemExist ? $item->required_items : '[]', true) ?? [];
                                        @endphp
                                        <option {{ in_array($req->id, $required_items) ? "selected" : "" }} value="{{ $req->id }}">#{{ $req->id }} | {{ $req->name }} | {{ $req->category_name }} Category</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label for="required_type" class="form-label">
                                    {{ __('Required to have') }}
                                    <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Select the type for required packages ownership to purchase this package.') }}"></i>
                                </label>
                                <select id="req_type" name="req_type" class="selectpicker w-100" data-style="btn-default">
                                    <option value="0" {{ !$isItemExist || $item->req_type == 0 ? "selected": "" }}>{{ __('All Packages') }}</option>
                                    <option value="2" {{ $isItemExist && $item->req_type == 2 ? "selected": "" }}>{{ __('One Package') }}</option>
                                </select>
                            </div>
                            <div class="col-12 d-flex justify-content-between">
                                <button class="btn btn-primary btn-prev" type="button">
                                    <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                    <span class="align-middle d-sm-inline-block d-none">{{ __('Previous') }}</span>
                                </button>
                                <button class="btn btn-primary btn-next" type="button">
                                    <span class="align-middle d-sm-inline-block d-none me-sm-1">{{ __('Next') }}</span>
                                    <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Others -->
                    <div id="others" class="content">
                        <div class="row g-3">
                            <div class="col-sm-12">
                                <label for="vars" class="form-label">
                                    {{ __('Select Variables') }}
                                    <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Select the variables that would allow customer option to select specific settings for executable commands.') }}"></i>
                                </label>
                                <select id="vars" name="vars[]" class="selectpicker w-100" data-style="btn-default" multiple data-icon-base="bx" data-tick-icon="bx-check text-primary">
                                    @foreach ($vars as $var)
                                        <option {{in_array($var->id, ($isItemExist ? $itemVars : null) ?: []) ? "selected": ""}} value="{{ $var->id }}">{{ $var->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-12">
                                <label for="discord_roles" class="form-label">
                                    {{ __('Select Discord Roles') }}
                                    <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Select the Discord roles that would be assigned to the customer after purchase. Keep in mind that you need to setup Discord Bot for this feature.') }}"></i>
                                </label>
                                <select id="discord_roles" name="discord_roles[]" class="selectpicker w-100" data-style="btn-default" multiple data-icon-base="bx" data-tick-icon="bx-check text-primary" @if($discordRoles->isEmpty()) disabled @endif>
                                    @foreach ($discordRoles as $role)
                                        <option
                                            {{ in_array($role->id, $isItemExist ? $itemRoles ?? [] : []) ? "selected" : "" }}
                                            value="{{ $role->id }}">
                                            #{{ $role->id }} | {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-12">
                                <div class="bg-lighter border rounded p-3 mb-3">
                                    <label class="switch switch-square">
                                        <input type="checkbox" class="switch-input" id="featured" name="featured" {{ $isItemExist && $item->featured == 1 ? "checked" : "" }} />
                                        <span class="switch-toggle-slider">
                                            <span class="switch-on"></span>
                                            <span class="switch-off"></span>
                                        </span>
                                        <span class="switch-label">{{ __('Mark this package as a featured package for category?') }}</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-12 d-flex justify-content-between">
                                <button class="btn btn-primary btn-prev" type="button">
                                    <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                                    <span class="align-middle d-sm-inline-block d-none">{{ __('Previous') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 comparisonSection"
             @if(!$isItemExist || is_null($categories->first(function ($c, $i) use ($item) {return $c->is_comparison == 1 && $c->id == $item->category_id;})))
                 style="display: none"
            @endif
        >
            @php($comparisonList = $isItemExist ? $item->parentCategory()->first()->comparison()->get() : [])
            <div class="card text-center mb-3">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Comparison Package Values') }}</h5>
                    <i class="bx bx-table bx-lg bx-border-circle mb-2"></i>
                    <p class="card-text">{{ __('Setup values for each comparison parameter.') }}</p>
                    <div class="divider divider-dashed">
                        <div class="divider-text">{{ __('Available Values') }}</div>
                    </div>
                    @php($comparisonValues = $isItemExist ? $comparisonItemValues : [''])
                    @for($i = 0; $i < count($comparisonList); $i++)
                        <div class="row justify-content-center comparisonSectionBlock">
                            <div class="col-sm-3">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Comparison Parameter (Read Only)') }}</label>
                                    <input type="text" class="form-control" value="{{ $comparisonList[$i]->name }}" readonly />
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <div class="mb-3">
                                    @if($comparisonList[$i]->type == 1)
                                        <label for="comparison{{ $i }}" class="form-label">{{ __('Comparison Value') }}</label>
                                        <input type="text" name="comparison[{{ $comparisonList[$i]->id }}]" value="{{ !empty($comparisonValues[$comparisonList[$i]->id]) ? $comparisonValues[$comparisonList[$i]->id] : '' }}" class="form-control" id="comparison{{ $comparisonList[$i]->id }}" required placeholder="<p style='color: #fff'>VIP+</p>">
                                    @else
                                        <label class="switch switch-square switch-lg">
                                            <input type="hidden" value="0" name="comparison[{{ $comparisonList[$i]->id }}]">
                                            <input type="checkbox" class="switch-input" value="1" name="comparison[{{ $comparisonList[$i]->id }}]" {{ !empty($comparisonValues[$comparisonList[$i]->id]) && $comparisonValues[$comparisonList[$i]->id] == '1' ? 'checked' : '' }}>
                                            <span class="switch-toggle-slider" style="margin-top:35px;">
                                  <span class="switch-on">
                                    <i class="bx bx-check"></i>
                                  </span>
                                  <span class="switch-off">
                                    <i class="bx bx-x"></i>
                                  </span>
                                </span>
                                        </label>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endfor
                    @if(count($comparisonList) == 0)
                        <div class="row justify-content-center comparisonSectionBlock"></div>
                    @endif
                </div>
            </div>
        </div>


        <div class="col-md-12">
            <div class="card text-center mb-3">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Package Delivery') }}</h5>
                    <i class="bx bx-package bx-lg bx-border-circle mb-2"></i>
                    <p class="card-text">{{ __('Setup in which way customers should receive upon purchasing this package.') }}</p>
                    <!-- <button style="margin-right: 5px;@if($isItemExist && $item->type != \App\Models\Item::MINECRAFT_PACKAGE) display: none; @endif" type="button" id="addMinecraftServerCommandsButton" class="btn btn-sm btn-primary mb-2"><span class="tf-icon bx bx-plus-circle bx-xs"></span> Minecraft Server Commands</button> -->
                    <!-- <button type="button" @if(!$isItemExist || $item->type != \App\Models\Item::GIFTCARD) style="display: none;" @endif id="addGiftCardButton" class="btn btn-sm btn-primary mb-2"><span class="tf-icon bx bx-plus-circle bx-xs"></span> Gift Card</button> -->
                </div>
            </div>
        </div>
        @if($isItemExist && $item->type == \App\Models\Item::MINECRAFT_PACKAGE && count($item->cmds) > 0)
            @php($command = \App\Models\Command::where('item_type', \App\Models\Command::ITEM_COMMAND)->where('item_id', $item->id)->get())
            <div class="items-repeater minecraftServerCommandBlock" @if($item->type != \App\Models\Item::MINECRAFT_PACKAGE) style="display: none;" @endif>
                <button id="itemsRepeaterCreate" type="button" data-repeater-create style="display:none"></button>
                <div data-repeater-list="command">
                    @php($i = 0)
                    @php($isEmpty = false)
                    @include('admin.items.blocks.minecraftServerCommand')
                </div>
            </div>
        @else
            <div class="items-repeater minecraftServerCommandBlock" @if($isItemExist && $item->type != \App\Models\Item::MINECRAFT_PACKAGE) style="display: none;" @endif>
                <button id="itemsRepeaterCreate" type="button" data-repeater-create style="display:none"></button>
                <div data-repeater-list="command">
                    @include('admin.items.blocks.minecraftServerCommand', ['isEmpty' => true, 'i' => 0, 'servers' => $servers, 'item' => (object)['type' => 1], 'server_command' => ['server'=>0]])
                </div>
            </div>
        @endif

        @if($isItemExist)
            <div class="gifts-repeater giftcardBlock" @if($item->type != \App\Models\Item::GIFTCARD) style="display: none;" @endif>
                <button id="giftsRepeaterCreate" type="button" data-repeater-create style="display:none"></button>
                <div data-repeater-list="gift">
                    @include('admin.items.blocks.giftcard')
                </div>
            </div>
        @else
            <div class="gifts-repeater giftcardBlock" style="display: none;">
                <button id="giftsRepeaterCreate" type="button" data-repeater-create style="display:none"></button>
                <div data-repeater-list="gift">
                    @include('admin.items.blocks.giftcard')
                </div>
            </div>
        @endif

        <div class="row">
            <div class="d-grid gap-2 col-lg-12 mx-auto">
                <button id="submitFormButton" class="btn btn-primary btn-lg" type="submit">
                    <span id="submitFormLoader" class="spinner-border me-1" role="status" aria-hidden="true" style="display: none;"></span>
                    <span class="tf-icon bx bx-{{ $isItemExist ? 'save' : 'plus-circle' }} bx-xs"></span>
                    <span id="submitFormButtonText" data-text="{{ $isItemExist ? 'Save' : 'Create' }} {{ __('the Package') }}">{{ $isItemExist ? 'Save' : 'Create' }} {{ __('the Package') }}</span>
                </button>
            </div>
        </div>


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
                                        <td>{username}</td>
                                        <td>{{ __('User nickname') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{package_name}</td>
                                        <td>{{ __('Package name') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{price}</td>
                                        <td>{{ __('Package price') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{currency}</td>
                                        <td>{{ __('Currency') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{coupon}</td>
                                        <td>{{ __('Used coupon') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{uuid}</td>
                                        <td>UUID (Universally Unique Identifier)</td>
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
                                        <td>{customerIP}</td>
                                        <td>{{ __('Customer') }} IP</td>
                                    </tr>
                                    <tr>
                                        <td>{server}</td>
                                        <td>{{ __('Server name') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{id}</td>
                                        <td>{{ __('Cart ID') }}</td>
                                    </tr>
                                    @foreach($vars as $var)
                                        <tr>
                                            <td>&#123;{{ $var->identifier }}&#125;</td>
                                            <td>{{ $var->name }}{{ empty($var->description) ? '' : ': '.$var->description }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Examples Modal -->
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
        <!-- Modals Ending -->

    </form>
@endsection
