@extends('admin.layout')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('res/vendor/libs/bs-stepper/bs-stepper.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/quill/typography.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/quill/editor.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/tagify/tagify.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/typeahead-js/typeahead.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/sweetalert2/sweetalert2.css')}}" />
<style>.drag-handle{cursor: pointer;}</style>
@endsection

@section('vendor-script')
<script src="{{asset('res/vendor/libs/bs-stepper/bs-stepper.js')}}"></script>
<script src="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('res/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('res/vendor/libs/quill/katex.js')}}"></script>
<script src="{{asset('res/vendor/libs/quill/quill.js')}}"></script>
<script src="{{asset('res/vendor/libs/tagify/tagify.js')}}"></script>
<script src="{{asset('res/vendor/libs/typeahead-js/typeahead.js')}}"></script>
<script src="{{asset('res/vendor/libs/bloodhound/bloodhound.js')}}"></script>
<script src="{{asset('res/vendor/libs/jquery-repeater/jquery-repeater.js')}}"></script>
<script src="{{asset('res/vendor/libs/moment/moment.js')}}"></script>
<script src="{{asset('res/vendor/libs/sortablejs/sortable.js')}}"></script>
<script src="{{asset('res/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
@endsection

@section('page-script')
<script type="text/javascript">
function loadPreview(elementId) {
  const filePreviewElement = document.querySelector('#preview-'+elementId);
    filePreviewElement.src = URL.createObjectURL(event.target.files[0]);
}
function clearImage(elementId) {
    document.getElementById('preview-'+elementId).src = "";
    document.getElementById(elementId).value = null;
}

const select2s = $('.select2');
select2s.each(function () {
    var $this = $(this);
    $this.select2({
      dropdownParent: $this.parent()
    });
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

var repeaterRow = {{ empty($comparison) ? 1 : count($comparison) + 1 }};
var formRepeater = $(".comparison");
formRepeater.repeater({
    initEmpty: {{ $isCategoryExist && (!empty($comparison) && count($comparison) > 0) ? 'false' : 'true' }},
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
                $this.attr('name', 'comparison['+repeaterRow+']['+$this.attr('data-name')+']');
                if($this.hasClass('select2')){
                    $this.parent().find('.select2-container').remove();
                    $this.select2({
                        dropdownParent: $this.parent()
                    });
                }
            } else {
                $this.attr('name', 'comparison['+repeaterRow+']['+$this.attr('data-name')+']');
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
                    text: "{{ __('Command has been deleted successfully.') }}",
                    customClass: {
                        confirmButton: 'btn btn-success'
                    },
                });
            }
        });
    }
});

$('input[type=radio][name=type]').change(function() {
    if (this.value == 'comparison') {
        $('.comparison').show();
    } else {
        $('.comparison').hide();
    }
    $('.form-check-label').removeClass('checked');
    $(this).parent().addClass('checked');
});

Sortable.create(document.querySelector(".sortableParent"), {
    group: "sortableParent",
    handle: '.drag-handle',
    animation: 150,
    fallbackOnBody: true,
    swapThreshold: 0.4,
});

</script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-1">
    <span class="text-body fw-light">{{ $isCategoryExist ? __('Edit') : __('New') }} {{ __('Category') }}</span>
</h4>

<form method="POST" enctype="multipart/form-data" autocomplete="off">
@csrf
<input type="hidden" name="parent_id" value="{{ empty($topcategory) ? 0 : $topcategory->id }}">
<div class="col-12 mb-4">
  <div class="row">
   <div class="col-md-8">
    <div class="card mb-4">
      <div class="card-body">
        <div class="mb-3">
        <label class="form-label" for="bs-validation-name">
          {{ __('Name') }}
        </label>
        <input type="text" class="form-control" id="bs-validation-name" name="name" value="{{ $isCategoryExist ? $category->name : '' }}" placeholder="Ranks" required />
            @error('name')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
      <div class="col-md-12 mb-2">
        <label for="description-editor" class="form-label">
          {{ __('Description') }}
          <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('The description might include review of the package that user purchase. It will be displayed when client checks it.') }}"></i>
        </label>
        <textarea style="display:none" id="description" name="description"></textarea>
        <div id="description-editor">
            {!! $isCategoryExist ? $category->description : '' !!}
        </div>
          @error('description')
          <span class="text-danger">{{ $message }}</span>
          @enderror
      </div>
      </div>
    </div>
   </div>
   <div class="col-md-4">
    <div class="card mb-4">
      <div class="card-body">
        <div class="mb-4">
          <label class="form-label">
            {{ __('Image') }}
            <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Recommended max height of 180px.') }}"></i>
          </label>

          <div class="align-self-center text-center mx-auto">
              <img src="{{ $isCategoryExist && $category->img ? asset('/img/categories/' . $category->img) : asset('/res/img/question-icon.png') }}"
                   alt="{{ __('Image is not uploaded yet') }}"
                   id="preview-icon"
                   class="rounded mb-2"
                   height="185"
                   width="180"
                   onerror="this.src='{{ asset('/res/img/question-icon.png') }}';">
              <div class="button-wrapper">
              <label for="icon" class="btn btn-primary me-2 mb-2" tabindex="0">
                <span class="d-none d-sm-block">{{ __('Upload Image') }}</span>
                <i class="bx bx-upload d-block d-sm-none"></i>
                <input type="file" id="icon" name="icon" onchange="loadPreview('icon')" class="account-file-input" hidden accept="image/png, image/jpeg, image/gif" />
              </label>

              <p class="text-muted mb-0">Allowed PNG, JPG, GIF</p>
            </div>
          </div>
        </div>
        <div class="mb-4 mt-4">
          <div class="row">
              <label for="gui_item_id" class="form-label">{{ __('Minecraft GUI Item') }}</label>
              <div class="input-group mb-4">
                <input type="text" id="gui_item_id" class="form-control" name="gui_item_id" value="{{ $isCategoryExist ? $category->gui_item_id : '' }}" placeholder="minecraft:grass" aria-label="Recipient's username" aria-describedby="button-addon2">
                <button class="btn btn-outline-primary" type="button" id="button-addon2">Browse</button>
              </div>
          </div>
        </div>
      </div>
    </div>
    </div>
  </div>
</div>
  <div class="col-xl-12 mb-4">
  <h4 class="fw-bold py-3 mb-1">
      <span class="text-body fw-light">{{ __('General Settings') }}</span>
  </h4>
    <div class="card">
      <div class="card-body">
        <div class="tab-content p-0">
          <div class="tab-pane fade show active" id="general_settings" role="tabpanel">
            <div class="row g-3">
              <div class="col-sm-12">
                <label for="url" class="form-label">
                  {{ __('Category URL') }}
                  <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Setup the category URL to access your category.') }}"></i>
                </label>
                <div class="input-group">
                  <span class="input-group-text">https://{{ $_SERVER['HTTP_HOST'] }}/category/{{ empty($topcategory) ? '' : $topcategory->url . '/' }}</span>
                  <input type="text" class="form-control" placeholder="ranks" id="url" name="url" value="{{ $isCategoryExist ? $category->url : '' }}">
                </div>
                  @error('url')
                    <span class="text-danger">{{ $message }}</span>
                  @enderror
              </div>
                <div class="col-xl-12">
                  <h5 class="card-header" style="text-align: center;">{{ __('Category Display Type') }}</h5>
                  <div class="row">
                    <div class="col-md mb-md-0 mb-2">
                    <div class="form-check custom-option-icon">
                      <label class="form-check-label custom-option custom-option-content {{ (!$isCategoryExist || ($isCategoryExist && ($category->is_listing == 0 && $category->is_comparison == 0))) ? 'checked' : '' }}" for="type1">
                      <span class="custom-option-body">
                        <i class="bx bx-grid-alt"></i>
                        <span class="custom-option-title"> {{ __('Grid Mode') }} </span>
                        <small> {{ __('This is a default way how store owners display their packages as cards inside of grids.') }}</small>
                      </span>
                      <input name="type" class="form-check-input" type="radio" value="grid" id="type1" {{ (!$isCategoryExist || ($isCategoryExist && ($category->is_listing == 0 && $category->is_comparison == 0))) ? 'checked' : '' }} />
                      </label>
                    </div>
                    </div>
                    <div class="col-md mb-md-0 mb-2">
                    <div class="form-check custom-option-icon">
                      <label class="form-check-label custom-option custom-option-content {{ ($isCategoryExist && $category->is_comparison == 1) ? 'checked' : '' }}" for="type2">
                      <span class="custom-option-body">
                        <i class="bx bx-table"></i>
                        <span class="custom-option-title"> {{ __('Comparison Mode') }} </span>
                        <small> {{ __('This mode displays category in table mode to compare each packages features.') }} </small>
                      </span>
                      <input name="type" class="form-check-input" type="radio" value="comparison" id="type2" {{ ($isCategoryExist && $category->is_comparison == 1) ? 'checked' : '' }} />
                      </label>
                    </div>
                    </div>
                    <div class="col-md">
                    <div class="form-check custom-option-icon">
                      <label class="form-check-label custom-option custom-option-content {{ ($isCategoryExist && $category->is_listing == 1) ? 'checked' : '' }}" for="type3">
                      <span class="custom-option-body">
                        <i class="bx bx-list-ul"></i>
                        <span class="custom-option-title"> {{ __('Listing Mode') }} </span>
                        <small>{{ __('This mode displays category in the listing way. Good choice for small packages.') }}</small>
                      </span>
                      <input name="type" class="form-check-input" type="radio" value="listing" id="type3" {{ ($isCategoryExist && $category->is_listing == 1) ? 'checked' : '' }} />
                      </label>
                    </div>
                    </div>
                  </div>
                </div>

                <div class="comparison col-sm-12" @if(!$isCategoryExist || $category->is_comparison == 0)style="display: none"@endif>
                    <div class="row mt-3 mb-2">
                        <div class="col-sm-10">
                            <h5 class="card-title">{{ __('Comparison Category Options') }}</h5>
                        </div>
                        <div class="col-sm-2 d-flex justify-content-end">
                            <button data-repeater-create type="button" class="btn btn-sm btn-primary mb-2"><span class="tf-icon bx bx-plus-circle bx-xs"></span> {{ __('Add Comparison Field') }}</button>
                        </div>
                    </div>
                    <div class="table-responsive text-nowrap">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center" style="max-width: 10px;">#</th>
                                    <th>{{ __('Comparison Name') }}</th>
                                    <th>{{ __('Comparison Description') }}</th>
                                    <th>{{ __('Comparison Type') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0 sortableParent" data-repeater-list="comparison">
                                @if(!$isCategoryExist || empty($comparison) || count($comparison) == 0)
                                @include('admin.categories.comparisonField', ['i' => 1, 'name' => '', 'description' => '', 'type' => 0, 'id' => 0])
                                @endif

                                @if($isCategoryExist)
                                @for ($i = 0; $i < count($comparison); $i++)
                                    @include('admin.categories.comparisonField', ['i' => $i, 'name' => $comparison[$i]['name'], 'description' => $comparison[$i]['description'], 'type' => $comparison[$i]['type'], 'id' => $comparison[$i]['id']])
                                @endfor
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-sm-12">
                  <h5 class="card-header" style="text-align: center;">{{ __('Advanced Settings') }}</h5>
                  <div class="bg-lighter border rounded p-3 mb-1">
                    <label class="switch switch-square">
                      <input type="checkbox" class="switch-input" name="is_cumulative" {{ ($isCategoryExist && $category->is_cumulative == 1) ? 'checked' : '' }}>
                      <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                      </span>
                      <span class="switch-label">{{ __('Cumulate the purchases inside of this category so customers only pay the difference when purchasing a higher priced package.') }}</span>
                    </label>
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="bg-lighter border rounded p-3 mb-3">
                    <label class="switch switch-square">
                      <input type="checkbox" class="switch-input" name="is_enable" {{ (!$isCategoryExist || ($isCategoryExist && $category->is_enable == 1)) ? 'checked' : '' }}>
                      <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                      </span>
                      <span class="switch-label">{{ __('Enable this category and make it visible?') }}</span>
                    </label>
                  </div>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="d-grid gap-2 col-lg-12 mx-auto">
      <button class="btn btn-primary btn-lg" type="submit"><span class="tf-icon bx bx-plus-circle bx-xs"></span> {{ $isCategoryExist ? __('Save') : __('Create') }} {{ __('the Category') }}</button>
    </div>
  </div>
</form>
@endsection
