@extends('admin.layout')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/select2/select2.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/tagify/tagify.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/quill/typography.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/quill/editor.css')}}" />
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
    <script src="{{asset('res/vendor/libs/quill/katex.js')}}"></script>
    <script src="{{asset('res/vendor/libs/quill/quill.js')}}"></script>
    <script src="{{asset('res/vendor/libs/jquery-repeater/jquery-repeater.js')}}"></script>
    <script src="{{asset('res/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
@endsection

@section('page-script')
    <script>
        document.querySelector("#start_at").flatpickr({
            enableTime: true,
            dateFormat: "Y-m-d H:i"
        });
        document.querySelector("#expire_at").flatpickr({
            enableTime: true,
            dateFormat: "Y-m-d H:i"
        });

        const select2s = $('.select2');
            select2s.each(function () {
            var $this = $(this);
            $this.select2({
                dropdownParent: $this.parent()
            });
        });

        document.querySelector("#apply_sale_type").addEventListener("change", function() {
            if (this.value == "1") {
                $("#apply_categories_block").show();
                $("#apply_items_block").hide();
            } else if (this.value == "2") {
                $("#apply_categories_block").hide();
                $("#apply_items_block").show();
            } else {
                $("#apply_categories_block").hide();
                $("#apply_items_block").hide();
            }
        });

        const fullToolbar = [
            [
                {
                    font: []
                },
                {
                    size: []
                }
            ],
            ["bold", "italic", "underline", "strike"],
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
                    script: "super"
                },
                {
                    script: "sub"
                }
            ],
            [
                {
                    header: "1"
                },
                {
                    header: "2"
                },
                "blockquote",
                "code-block"
            ],
            [
                {
                    list: "ordered"
                },
                {
                    list: "bullet"
                },
                {
                    indent: "-1"
                },
                {
                    indent: "+1"
                }
            ],
            [
                "direction",
                {
                    align: []
                }
            ],
            ["link", "image", "video", "formula"],
            ["clean"]
        ];

        const fullEditor = new Quill("#description-editor", {
            bounds: "#description-editor",
            placeholder: "Type Something...",
            name: "description",
            modules: {
                formula: true,
                toolbar: fullToolbar
            },
            theme: "snow"
        });
        $("form").on("submit",function() {
            $("#description").val($("#description-editor .ql-editor").html());
        });

        $("#announcement-check").on("change", function() {
            if ($(this).prop("checked")) {
                $("#announcement").show();
            } else {
                $("#announcement").hide();
            }
        });

        var formRepeater = $(".form-repeater");
        var repeaterRow = 1;
        formRepeater.repeater({
            initEmpty: true,
            show: function() {
                var col = 1;
                var fromControl = $(this).find('.form-control, .form-select');
                var formLabel = $(this).find('.form-label');

                fromControl.each(function(i) {
                    var id = 'form-repeater-' + repeaterRow + '-' + col;
                    $(fromControl[i]).attr('id', id);
                    $(formLabel[i]).attr('for', id);
                    var $this = $(this);

                    if(this.tagName == 'SELECT'){
                        if ($this.hasClass('select2'))
                        {
                            if($this.hasClass('select2') && !$this.data('select2'))
                                $this.select2();

                            $this.attr('name', 'packages_commands[' + repeaterRow + '][' + $this.attr('data-name') + ']' + ($this.hasAttr("multiple") ? '[]' : ''));
                            $this.parent().find('.select2-container').remove();
                            $this.select2({
                                dropdownParent: $this.parent()
                            });
                        }
                    } else {
                        $this.attr('name', 'packages_commands['+repeaterRow+']['+$this.attr('data-name')+']');
                    }

                    col++;
                });

                repeaterRow++;

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
                            text: "{{ __('Command has been deleted successfully!') }}",
                            customClass: {
                                confirmButton: 'btn btn-success'
                            },
                        });
                    }
                });
            }
        });
    </script>
@endsection

@section('content')
    <h4 class="fw-bold py-3 mb-1">
        <span class="text-body fw-light">{{ __('Create a Sale') }}</span>
    </h4>

    <form action="{{ route('sales.store') }}" method="POST" autocomplete="off">
        @csrf
        @method('POST')
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-body pb-0">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label" for="name">
                                {{ __('Name') }}
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Release Sale" required />
                            </div>
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-sm-12 mb-3">
                            <label class="form-label" for="discount">
                                {{ __('Discount') }}
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;"
                                   data-bs-toggle="tooltip" data-bs-placement="top"
                                   title="{{ __('Amount in percents that will decrease original price as a sale.') }}"></i>
                            </label>
                            <div class="input-group">
                                <input type="number" id="discount" name="discount" value="{{ old('discount') }}" class="form-control"
                                       aria-label="Amount to discount original price" required>
                                <span class="input-group-text">%</span>
                            </div>
                            @error('discount')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-sm-5 mb-3">
                            <label for="apply_sale_type" class="form-label">
                                {{ __('Apply sale to') }}
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;"
                                   data-bs-toggle="tooltip" data-bs-placement="top"
                                   title="{{ __('Select the option that you want to apply your sale for.') }}"></i>
                            </label>
                            <select id="apply_sale_type" name="apply_type" class="selectpicker w-100"
                                    data-style="btn-default">
                                <option value="0" {{ is_null(old('apply_type')) || old('apply_type') == 0 ? 'selected' : '' }}>{{ __('Whole Webstore') }}</option>
                                <option value="1" {{ old('apply_type') == 1 ? 'selected' : '' }}>{{ __('Categories') }}</option>
                                <option value="2" {{ old('apply_type') == 2 ? 'selected' : '' }}>{{ __('Packages') }}</option>
                            </select>
                            @error('apply_type')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-sm-7 mb-3" id="apply_categories_block" {!! is_null(old('apply_type')) || old('apply_type') != 1 ? 'style="display:none"' : '' !!}>
                            <label for="apply_categories" class="form-label">
                                <span>{{ __('Categories') }}</span>
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;"
                                   data-bs-toggle="tooltip" data-bs-placement="top"
                                   title="{{ __('Select the option that you want to apply your sale for.') }}"></i>
                            </label>
                            <select id="apply_categories" name="apply_categories[]" class="first-select2 select2 form-select"
                                    multiple>
                                @foreach($categories as $cat)
                                    <option @selected(old('apply_categories') == $cat->id) value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-7 mb-3" id="apply_items_block" {!! is_null(old('apply_type')) || old('apply_type') != 2 ? 'style="display:none"' : '' !!}>
                            <label for="apply_items" class="form-label">
                                <span>{{ __('Packages') }}</span>
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;"
                                   data-bs-toggle="tooltip" data-bs-placement="top"
                                   title="{{ __('Select the option that you want to apply your coupon for.') }}"></i>
                            </label>
                            <select id="apply_items" name="apply_items[]" class="first-select2 select2 form-select" multiple>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="form-label" for="min_basket">
                                {{ __('Minimum Basket Value') }}
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;"
                                   data-bs-toggle="tooltip" data-bs-placement="top"
                                   title="{{ __('Set the minimal basket price to apply this sale for a basket.') }}"></i>
                            </label>
                            <div class="input-group">
                                <input type="text" inputmode="numeric" pattern="^\d*([,.]\d{1,2})?$" id="min_basket" name="min_basket" value="0" class="form-control"
                                       aria-label="Minimum Basket Value">
                                <span class="input-group-text">{{ $settings->currency }}</span>
                            </div>
                            @error('min_basket')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label for="start_at" class="form-label">
                                {{ __('Starts the Sale at') }}
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;"
                                   data-bs-toggle="tooltip" data-bs-placement="top"
                                   title="{{ __('Set a datetime when coupon will be started in your webstore.') }}"></i>
                            </label>
                            <input type="text" class="form-control" id="start_at" name="start_at" value="{{ old('start_at') }}"
                                   placeholder="YYYY-MM-DD HH:MM" />
                            @error('start_at')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label for="expire_at" class="form-label">
                                {{ __('Expires the Sale after') }}
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;"
                                   data-bs-toggle="tooltip" data-bs-placement="top"
                                   title="{{ __('Set a datetime when sale will be expired in your webstore.') }}"></i>
                            </label>
                            <input type="text" class="form-control" id="expire_at" name="expire_at" value="{{ old('expire_at') }}"
                                   placeholder="YYYY-MM-DD HH:MM" />
                            @error('expire_at')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-2">
                            <div class="bg-lighter border rounded p-3 mb-3">
                                <label class="switch switch-square">
                                    <input type="checkbox" id="announcement-check" name="is_advert"
                                           class="switch-input" />
                                    <span class="switch-toggle-slider">
                                        <span class="switch-on"></span>
                                        <span class="switch-off"></span>
                                    </span>
                                    <span class="switch-label">{{ __('Make an announcement of the sale?') }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0" id="announcement" style="display:none">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="title" class="form-label">
                                {{ __('Title') }}
                            </label>
                            <input class="form-control" type="text" id="title" name="advert_title" placeholder="Alert" value="{{ old('advert_title') }}">
                            @error('advert_title')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="content-editor" class="form-label">
                                {{ __('Content') }}
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;"
                                   data-bs-toggle="tooltip" data-bs-placement="top"
                                   title="{{ __('This content will be displayed for announcement message on the index page.') }}"></i>
                            </label>
                            <textarea id="description" name="advert_description" style="display:none" value="{{ old('advert_description') }}"></textarea>
                            <div id="description-editor"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="button_name" class="form-label">
                                {{ __('Text for Button') }}
                            </label>
                            <input class="form-control" type="text" id="button_name" name="button_name"
                                   placeholder="Visit Sale" value="{{ old('button_name') }}">
                            @error('button_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="button_url" class="form-label">
                                {{ __('Link for Button') }}
                            </label>
                            <input class="form-control" type="text" id="button_url" name="button_url" value="{{ old('button_url') }}"
                                   placeholder="/ranks">
                            @error('button_url')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-repeater">
            <div class="row mt-3 mb-2">
                <div class="col-sm-9">
                    <h5 class="card-title">{{ __('Custom Commands for Sale Packages') }}</h5>
                </div>
                <div class="col-sm-3 d-flex justify-content-end">
                    <button style="margin-right: 5px;" type="button" data-bs-toggle="modal" data-bs-target="#variablesModal" class="btn btn-sm btn-info mb-2"><span class="tf-icon bx bx-code-curly bx-xs"></span> {{ __('Variables') }}</button>
                    <button data-repeater-create="" type="button" class="btn btn-sm btn-primary mb-2"><span class="tf-icon bx bx-plus-circle bx-xs"></span> {{ __('Add a Custom Command') }}</button>
                </div>
            </div>
            <div class="card donationGoalCommands">
                <div class="card-body">
                    <div data-repeater-list="packages_commands">
                        @include('admin.sales.package_command', ['i' => 1, 'servers' => $servers, 'isExist' => false])
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="d-grid gap-2 col-lg-12 mx-auto">
                <button class="btn btn-primary btn-lg" type="submit"><span
                        class="tf-icon bx bx-plus-circle bx-xs"></span> {{ __('Create a Sale') }}
                </button>
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
                                    <td>{user}</td>
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
                                    <td>{{ __('Customer IP') }}</td>
                                </tr>
                                <tr>
                                    <td>{server}</td>
                                    <td>{{ __('Server name') }}</td>
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
@endsection
