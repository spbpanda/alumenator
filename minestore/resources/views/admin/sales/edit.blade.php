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
    document.querySelector('#start_at').flatpickr({
      enableTime: true,
      dateFormat: 'Y-m-d H:i'
    });
    document.querySelector('#expire_at').flatpickr({
      enableTime: true,
      dateFormat: 'Y-m-d H:i'
    });

    const select2s = $('.select2');
        select2s.each(function () {
        var $this = $(this);
        $this.select2({
            dropdownParent: $this.parent()
        });
    });

    document.querySelector('#apply_sale_type').addEventListener('change', function(){
    	if(this.value == '1'){
    		$('#apply_categories_block').show();
    		$('#apply_items_block').hide();
    	} else if(this.value == '2'){
    		$('#apply_categories_block').hide();
    		$('#apply_items_block').show();
    	} else {
    		$('#apply_categories_block').hide();
    		$('#apply_items_block').hide();
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
        console.log(13123);
        if ($(this).prop("checked")) {
            $("#announcement").show();
        } else {
            $("#announcement").hide();
        }
    });

    var formRepeater = $(".form-repeater");
    var repeaterRow = 1;
    formRepeater.repeater({
        initEmpty: false,
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
  <span class="text-body fw-light">{{ __('Edit Sale') }}</span>
</h4>

<form action="{{ route('sales.update',$sale->id) }}" method="POST" autocomplete="off">
@csrf
@method('PATCH')
<div class="col-12 mb-4">
	<div class="card">
		<div class="card-body">
			<div class="row">
					<div class="col-md-12 mb-3">
						<label class="form-label" for="name">
                            {{ __('Name') }}
                        </label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="name" name="name" value="{{ $sale->name }}" placeholder="Release Sale" required />
					    </div>
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
					</div>
					<div class="col-sm-12 mb-3">
						<label class="form-label" for="percent">
                            {{ __('Discount') }}
							<i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Amount in percents that will decrease original price as a sale.') }}"></i>
						</label>
						<div class="input-group">
						  <input type="number" id="percent" name="discount" class="form-control" value="{{ $sale->discount }}" aria-label="Amount to discount original price" required>
						  <span class="input-group-text">%</span>
						</div>
                        @error('discount')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
					</div>
					<div class="col-sm-5 mb-3">
						<label for="apply_sale_type" class="form-label">
                            {{ __('Apply sale to') }}
							<i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Select the option that you want to apply your sale for.') }}"></i>
						</label>
						<select id="apply_sale_type" name="apply_type" class="selectpicker w-100" data-style="btn-default">
							<option value="0" @if($sale->apply_type == 0) selected @endif>{{ __('Whole Webstore') }}</option>
							<option value="1" @if($sale->apply_type == 1) selected @endif>{{ __('Categories') }}</option>
							<option value="2" @if($sale->apply_type == 2) selected @endif>{{ __('Packages') }}</option>
						</select>
                        @error('apply_type')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
					</div>
					<div class="col-sm-7 mb-3" id="apply_categories_block" @if($sale->apply_type != 1) style="display:none" @endif>
						<label for="apply_categories" class="form-label">
							<span>{{ __('Categories') }}</span>
							<i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Select the option that you want to apply your sale for.') }}"></i>
						</label>
						<select id="apply_categories" name="apply_categories[]" class="first-select2 select2 form-select" multiple>
							@foreach($categories as $cat)
							<option @if($sale->apply_type == 1 && $sale->applies->pluck('apply_id')->contains($cat->id)) selected @endif value="{{ $cat->id }}">{{ $cat->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="col-sm-7 mb-3" id="apply_items_block" @if($sale->apply_type != 2) style="display:none" @endif>
						<label for="apply_items" class="form-label">
							<span>{{ __('Packages') }}</span>
							<i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Select the option that you want to apply your coupon for.') }}"></i>
						</label>
						<select id="apply_items" name="apply_items[]" class="first-select2 select2 form-select" multiple>
							@foreach($items as $item)
							<option @if($sale->apply_type == 2 && $sale->applies->pluck('apply_id')->contains($item->id)) selected @endif value="{{ $item->id }}">{{ $item->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="col-sm-6 mb-3">
						<label class="form-label" for="min_basket">
                            {{ __('Minimum Basket Value') }}
							<i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Set the minimal basket price to apply this sale for a basket.') }}"></i>
						</label>
						<div class="input-group">
						  <input type="text" inputmode="numeric" pattern="^\d*([,.]\d{1,2})?$" id="min_basket" name="min_basket" class="form-control" value="{{ $sale->min_basket }}" aria-label="Minimum Basket Value.">
						  <span class="input-group-text">{{ $settings->currency }}</span>
						</div>
                        @error('min_basket')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
					</div>
					<div class="col-sm-6 mb-3">
						<label for="start_at" class="form-label">
                            {{ __('Starts the Sale at') }}
							<i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Set a datetime when coupon will be started in your webstore.') }}"></i>
						</label>
						<input type="text" class="form-control" id="start_at" name="start_at" value="{{ $sale->start_at }}" placeholder="YYYY-MM-DD HH:MM" />
                        @error('start_at')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
					</div>
					<div class="col-sm-6 mb-3">
						<label for="expire_at" class="form-label">
                            {{ __('Expires the Sale after') }}
							<i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Set a datetime when sale will be expired in your webstore.') }}"></i>
						</label>
						<input type="text" class="form-control" id="expire_at" name="expire_at" value="{{ $sale->expire_at }}" placeholder="YYYY-MM-DD HH:MM" />
                        @error('expire_at')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
					</div>
			</div>
            <div class="row">
                <div class="col mb-2">
                    <div class="bg-lighter border rounded p-3 mb-3">
                        <label class="switch switch-square">
                            <input type="checkbox" id="announcement-check" name="is_advert" {{ $sale->is_advert == 1 ? 'checked' : '' }}
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
            <div class="card-body pt-0" id="announcement" style="@if($sale->is_advert == 0) display:none @endif">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="title" class="form-label">
                            {{ __('Title') }}
                        </label>
                        <input class="form-control" type="text" id="title" name="advert_title" value="{{ $sale->advert_title }}" placeholder="Alert">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="content-editor" class="form-label">
                            {{ __('Content') }}
                            <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;"
                               data-bs-toggle="tooltip" data-bs-placement="top"
                               title="{{ __('This content will be displayed for announcement message on the index page.') }}"></i>
                        </label>
                        <textarea id="description" name="advert_description" value="{{ $sale->advert_description }}" style="display:none"></textarea>
                        <div id="description-editor">
                            {!! $sale->advert_description !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="button_name" class="form-label">
                            {{ __('Text for Button') }}
                        </label>
                        <input class="form-control" type="text" id="button_name" name="button_name"
                               placeholder="Visit Sale" value="{{ $sale->button_name }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="button_url" class="form-label">
                            {{ __('Link for Button') }}
                        </label>
                        <input class="form-control" type="text" id="button_url" name="button_url"
                               placeholder="/ranks" value="{{ $sale->button_url }}">
                    </div>
                </div>
            </div>
		</div>
	</div>

    <div class="form-repeater mb-3">
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
            <div class="card-body form-repeater">
                <div data-repeater-list="packages_commands">
                    @php($packagesCommands = $sale->saleCommands)

                    @if ($packagesCommands)
                        @for($i = 0; $i < count($packagesCommands); $i++)
                            @include('admin.sales.package_command', ['i' => $i+1, 'servers' => $servers, 'isExist' => true, 'packageCommand' => $packagesCommands[$i]])
                        @endfor
                    @endif
                </div>
            </div>
        </div>
    </div>

	<div class="row">
		<div class="d-grid gap-2 col-lg-12 mx-auto">
			<button class="btn btn-primary btn-lg" type="submit"><span class="tf-icon bx bx-refresh bx-xs"></span> {{ __('Update a Sale') }}</button>
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
