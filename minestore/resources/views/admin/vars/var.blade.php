@extends('admin.layout')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('res/vendor/libs/bs-stepper/bs-stepper.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/quill/typography.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/quill/editor.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/sweetalert2/sweetalert2.css')}}" />
<style>.drag-handle{cursor: pointer;}</style>
@endsection

@section('vendor-script')
<script src="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('res/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('res/vendor/libs/quill/katex.js')}}"></script>
<script src="{{asset('res/vendor/libs/quill/quill.js')}}"></script>
<script src="{{asset('res/vendor/libs/jquery-repeater/jquery-repeater.js')}}"></script>
<script src="{{asset('res/vendor/libs/sortablejs/sortable.js')}}"></script>
<script src="{{asset('res/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
@endsection

@section('page-script')
<script type="text/javascript">
var formRepeater = $(".form-repeater");
var repeaterRow = 1;
formRepeater.repeater({
  initEmpty: {{ $isExist && !empty($var->variables) ? 'false' : 'true'}},
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
              $this.attr('name', 'variables['+repeaterRow+']['+$this.attr('data-name')+']');
              if($this.hasClass('select2')){
                  $this.parent().find('.select2-container').remove();
                  $this.select2({
                      dropdownParent: $this.parent()
                  });
              }
          } else {
              $this.attr('name', 'variables['+repeaterRow+']['+$this.attr('data-name')+']');
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
                    text: "{{ __('Variable has been deleted.') }}",
                    customClass: {
                        confirmButton: 'btn btn-success'
                    },
                });
            }
        });
    }
});

function updateType(){
    if ($("#type").val() == "0")
        $(".variableOptions").show();
    else
        $(".variableOptions").hide();
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
    name: 'description',
    modules: {
      formula: true,
      toolbar: fullToolbar
    },
    theme: 'snow'
  });
$("form").on("submit",function() {
    $("#description").val($("#description-editor .ql-editor").html());
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

<form action="{{ $isExist ? route('vars.update', $var->id) : route('vars.store') }}" method="POST" autocomplete="off" class="form-repeater">
@csrf
@method($isExist ? 'PATCH' : 'POST')
<h4 class="fw-bold py-3 mb-1">
  <span class="text-body fw-light">{{ $isExist ? __('Edit') : __('New') }} {{ __('Variable') }}
</span></h4>
<div class="col-12 mb-4">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 mb-2">
                    <div class="col-md-12">
                      <div class="needs-validation">
                          <div class="mb-3">
                            <label class="form-label" for="name">
                                {{ __('Name for Variable') }}
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('The name for this variable to identify it in your admin dashboard. Just for yourself.') }}"></i>
                            </label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $isExist ? $var->name : '' }}" placeholder="Prefix Color" required />
                              @error('name')
                                <span class="text-danger">{{ $message }}</span>
                              @enderror
                          </div>
                      </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="description-editor" class="form-label">
                            {{ __('Description') }}
                            <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('The description customer will see when need to enter/select required option.') }}"></i>
                        </label>
                        <textarea style="display:none" id="description" name="description"></textarea>
                        <div id="description-editor">{!! $isExist ? $var->description : ''  !!}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="identifier" class="form-label">
                                {{ __('Variable Identifier') }}
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('The identifier that would be used in package command field. For example {color}.') }}"></i>
                            </label>
                            <div class="input-group mb-2">
                              <span class="input-group-text">{</span>
                              <input type="text" class="form-control" id="identifier" name="identifier" value="{{ $isExist ? $var->identifier : '' }}" placeholder="prefix_color">
                              <span class="input-group-text">}</span>
                            </div>
                            @error('identifier')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">
                                {{ __('Variable Type') }}
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Select the type for variable option.') }}"></i>
                            </label>
                            <select class="selectpicker w-100 show-tick" id="type" name="type" data-icon-base="bx" data-tick-icon="bx-check" data-style="btn-default" onchange="updateType()">
                              <option value="1" {{ !$isExist || $var->type == 1 ? 'selected' : ''}} data-icon="bx-text">{{ __('Input Text Form') }}</option>
                              <option value="2" {{ $isExist && $var->type == 2 ? 'selected' : ''}} data-icon="bx-ruler">{{ __('Input Digit Form') }}</option>
                              <option value="0" {{ $isExist && $var->type == 0 ? 'selected' : ''}} data-icon="bx-caret-down-circle">{{ __('Dropdown') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-12 mb-4 variableOptions" @if(!$isExist || $var->type != 0) style="display:none" @endif>
    <div class="col-md-12 mb-3">
        <h5 class="card-header">{{ __('Variable Options') }}</h5>
    </div>
    <div class="card">
    <div class="row mt-3">
      <div class="col-md-12">
          <div class="d-grid gap-2 col-lg-6 mx-auto mb-3">
            <button type="button" class="btn btn-primary" data-repeater-create>
              <i class="bx bx-plus"></i>
              <span class="align-middle">{{ __('Add Variable Option') }}</span>
            </button>
          </div>
          <hr>
      </div>
    </div>
    <div class="card-body">
      <div class="sortableParent" data-repeater-list="variables">
{{--          @php($variables = $isExist && !empty($var->variables) ? json_decode($var->variables, true) : [])--}}
          @if(!$isExist || empty($var->variables))
              @include('admin.vars.variable', ['i' => 1, 'name' => '', 'value' => '', 'price' => ''])
          @endif

          @if($isExist && !empty($var->variables))
              @for ($i = 0; $i < count($var->variables); $i++)
                  @include('admin.vars.variable', ['i' => $i, 'name' => $var->variables[$i]['name'], 'value' => $var->variables[$i]['value'], 'price' => $var->variables[$i]['price']])
              @endfor
          @endif
      </div>
      </div>
    </div>
</div>

<div class="row">
    <div class="d-grid gap-2 col-lg-12 mx-auto">
       <button class="btn btn-primary btn-lg" type="submit"><span class="tf-icon bx bx-plus-circle bx-xs"></span> {{ $isExist ? __('Save') : __('Create') }} {{ __('a Variable') }}</button>
    </div>
</div>
</form>
@endsection
