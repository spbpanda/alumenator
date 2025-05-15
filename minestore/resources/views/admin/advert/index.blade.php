@extends('admin.layout')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('res/vendor/libs/quill/editor.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('res/vendor/libs/quill/katex.js')}}"></script>
<script src="{{asset('res/vendor/libs/quill/quill.js')}}"></script>
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
  const fullEditor = new Quill('#content-editor', {
      bounds: '#content-editor',
      placeholder: 'Type Something...',
      modules: {
          formula: true,
          toolbar: fullToolbar
      },
      theme: 'snow'
  });
  $("form").submit(function(e) {
      $("#content").text(fullEditor.root.innerHTML);
  });
</script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-1">
    <span class="text-body fw-light">{{ __('Announcement Message') }}</span>
</h4>

<form action="{{ route('advert.store') }}" method="POST" autocomplete="off">
@csrf
<div class="col-12 mb-4">
	<div class="col-12 mb-4">
		<div class="card">
			<div class="card-body">
				<div class="row d-flex w-100 align-self-center">
					<div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
						<div class="row align-self-center h-100">
							<div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
								<div class="d-flex justify-content-center mb-4">
								  <div class="settings_icon bg-label-primary">
									  <i class="bx bx-error-circle"></i>
								  </div>
								</div>
							</div>
							<div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
								<h4>
                                    {{ __('Display Announcement Message on the Index Page?') }}
                                </h4>
                                <div class="mb-3 col-md-10">
                                <p class="card-text">{{ __('Your customers will see Announcement Message on the main page of your webstore.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
                        <label class="switch switch-square" for="is_index">
                          <input type="checkbox" class="switch-input" id="is_index" name="is_index" {{ $advert->is_index == 1 ? 'checked' : ''}} />
						  <span class="switch-toggle-slider">
							<span class="switch-on"></span>
							<span class="switch-off"></span>
						  </span>
						</label>
					</div>
				</div>
			</div>
		</div>
	</div>
		<div class="card mb-4">
		  <div class="card-body">
			<div class="row">
				<div class="col-md-12 mb-3">
					<label for="title" class="form-label">
                        {{ __('Title') }}
                    </label>
                    <input class="form-control" type="text" id="title" name="title" value="{{ $advert->title }}" placeholder="Alert">
				</div>
				<div class="col-md-12 mb-3">
					<label for="content-editor" class="form-label">
                        {{ __('Content') }}
                        <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('This content will be displayed for announcement message on the index page.') }}"></i>
                    </label>
                    <textarea style="display:none" id="content" name="content"></textarea>
                    <div id="content-editor">{!! $advert->content  !!}</div>
                </div>
             </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="button_name" class="form-label">
                        {{ __('Text for Button') }}
                    </label>
                    <input class="form-control" type="text" id="button_name" name="button_name" value="{{ $advert->button_name }}" placeholder="Visit Sale">
				</div>
				<div class="col-md-6 mb-3">
					<label for="button_url" class="form-label">
                        {{ __('Link for Button') }}
                    </label>
                    <input class="form-control" type="text" id="button_url" name="button_url" value="{{ $advert->button_url }}" placeholder="/ranks">
				</div>
			</div>
		  </div>
		</div>
</div>
<div class="row">
	<div class="d-grid gap-2 col-lg-12 mx-auto">
        <button class="btn btn-primary btn-lg" id="submit"><span class="tf-icon bx bx-save bx-xs"></span> {{ __('Save Announcement') }}</button>
    </div>
</div>
    </form>
@endsection

