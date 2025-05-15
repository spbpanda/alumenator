@extends('admin.layout')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/quill/editor.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/quill/typography.css')}}" />
<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/10.1.2/styles/github.min.css"
/>
@endsection

@section('vendor-script')
<script src="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('res/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('res/vendor/libs/quill/katex.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/10.1.2/highlight.min.js"></script>
<script
    charset="UTF-8"
    src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/10.1.2/languages/xml.min.js"
></script>
<script src="{{asset('res/vendor/libs/quill/quill.js')}}"></script>
<script src="https://unpkg.com/quill-html-edit-button@2.2.7/dist/quill.htmlEditButton.min.js"></script>
@endsection

@section('page-script')
<script>
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

    Quill.register("modules/htmlEditButton", htmlEditButton);

    const fullEditor = new Quill('#description-editor', {
        bounds: '#description-editor',
        placeholder: 'Type Something...',
        modules: {
            formula: true,
            toolbar: fullToolbar,
            htmlEditButton: {
                syntax: true,
            }
        },
        theme: 'snow'
    });

    $("form").on("submit",function() {
        $("#description").val($("#description-editor .ql-editor").html());
    });
</script>
@endsection

@section('content')

<form action="{{ route('pages.edit', ['id' => $page->id]) }}" method="POST" autocomplete="off">
@csrf
<h4 class="fw-bold py-3 mb-1">
  <span class="text-body fw-light">{{ __('Edit Page') }}</span>
</h4>

<div class="col-12 mb-4">
  <div class="card">
    <div class="card-body">
      <div class="row">
        <div class="col-md-12 mb-2">
          <div class="col-md-12">
              <div class="mb-3">
              <label class="form-label" for="name">
                  {{ __('Page Name') }}
                <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('The name will be displayed in the title and dashboard.') }}"></i>
              </label>
              <input type="text" class="form-control" id="name" name="name" placeholder="Terms Of Service" value="{{ $page->name }}" required />
                  @error('name')
                    <span class="text-danger">{{ $message }}</span>
                  @enderror
              </div>
          </div>
          <div class="col-md-12 mb-3">
            <label for="full-editor" class="form-label">
                {{ __('Page URL') }}
              <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Set the URL for this page to access it.') }}"></i>
            </label>
              <div class="input-group">
                  <span class="input-group-text">https://{{ $_SERVER['HTTP_HOST'] }}/pages/</span>
                  <input type="text" class="form-control" placeholder="tos" id="url" name="url" value="{{ $page->url }}">
              </div>
              @error('url')
                <span class="text-danger">{{ $message }}</span>
              @enderror
          </div>
          <div class="row">
              <div class="col-md-12 mb-2">
                  <label for="description-editor" class="form-label">
                      {{ __('Page Content') }}
                      <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Content of the page. You can use source mode to edit HTML and CSS code.') }}"></i>
                  </label>
                  <textarea style="display:none" id="description" name="description"></textarea>
                  <div id="description-editor">
                    {!! $page->content !!}
                  </div>
                  @error('description')
                    <span class="text-danger">{{ $message }}</span>
                  @enderror
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="d-grid gap-2 col-lg-12 mx-auto">
    <button class="btn btn-primary btn-lg"><span class="tf-icon bx bx-plus-circle bx-xs"></span> {{ __('Edit Page') }}</button>
  </div>
</div>
</form>

@endsection
