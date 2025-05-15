@extends('admin.layout')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('res/vendor/libs/quill/typography.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/quill/editor.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/dropzone/dropzone.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/10.1.2/styles/github.min.css"
/>
@endsection

@section('vendor-script')
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
function loadPreview(elementId) {
  const filePreviewElement = document.querySelector('#preview-'+elementId);
    filePreviewElement.src = URL.createObjectURL(event.target.files[0]);
}
function clearImage(elementId) {
    document.getElementById('preview-'+elementId).src = "";
    document.getElementById(elementId).value = null;
}

// const fullToolbar = [
//     [
//       {
//         font: []
//       },
//       {
//         size: []
//       }
//     ],
//     ['bold', 'italic', 'underline', 'strike'],
//     [
//       {
//         color: []
//       },
//       {
//         background: []
//       }
//     ],
//     [
//       {
//         script: 'super'
//       },
//       {
//         script: 'sub'
//       }
//     ],
//     [
//       {
//         header: '1'
//       },
//       {
//         header: '2'
//       },
//       'blockquote',
//       'code-block'
//     ],
//     [
//       {
//         list: 'ordered'
//       },
//       {
//         list: 'bullet'
//       },
//       {
//         indent: '-1'
//       },
//       {
//         indent: '+1'
//       }
//     ],
//     [
//       'direction',
//       {
//         align: []
//       }
//     ],
//     ['link', 'image', 'video', 'formula'],
//     ['clean']
//   ];
//
//   Quill.register("modules/htmlEditButton", htmlEditButton);
//
//   new Quill('#block_1_quill', {
//     bounds: '#block_1_quill',
//     placeholder: 'Type Something...',
//     name: 'block_1',
//     modules: {
//       formula: true,
//       toolbar: fullToolbar,
//       htmlEditButton: {
//           syntax: true,
//       }
//     },
//     theme: 'snow'
//   });
//
//   new Quill('#block_2_quill', {
//     bounds: '#block_2_quill',
//     placeholder: 'Type Something...',
//     name: 'block_2',
//     modules: {
//       formula: true,
//       htmlEditButton: {
//             syntax: true,
//       },
//       toolbar: fullToolbar
//     },
//     theme: 'snow'
//   });
//
// $("form").on("submit", function() {
//     $("#block_1_textarea").val($("#block_1_quill .ql-editor").html());
//     $("#block_2_textarea").val($("#block_2_quill .ql-editor").html());
// });
</script>
@endsection

@section('content')
<form method="POST" enctype="multipart/form-data" autocomplete="off">
@csrf

<h4 class="fw-bold py-3 mb-1">
  <span class="text-body fw-light">{{ __('Homepage Content') }}</span>
</h4>
<div class="row">
{{--	<div class="col-12 mb-4">--}}
{{--		<div class="card">--}}
{{--			<div class="card-body">--}}
{{--				<label for="block_1" class="form-label">--}}
{{--                    {{ __('Content Text Block #1') }}--}}
{{--					<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="This text could be used anywhere. It is all depends on your theme.') }}"></i>--}}
{{--				</label>--}}
{{--				<textarea style="display:none" id="block_1_textarea" name="block_1"></textarea>--}}
{{--				<div id="block_1_quill">{!! str_replace("\n", "", $settings->block_1) !!}</div>--}}
{{--			</div>--}}
{{--		</div>--}}
{{--	</div>--}}
{{--	<div class="col-12 mb-4">--}}
{{--		<div class="card">--}}
{{--			<div class="card-body">--}}
{{--				<label for="block_2" class="form-label">--}}
{{--                    {{ __('Content Text Block #2') }}--}}
{{--					<i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="This text could be used anywhere. It is all depends on your theme.') }}"></i>--}}
{{--				</label>--}}
{{--				<textarea style="display:none" id="block_2_textarea" name="block_2"></textarea>--}}
{{--				<div id="block_2_quill">{!! str_replace("\n", "", $settings->block_2) !!}</div>--}}
{{--			</div>--}}
{{--		</div>--}}
{{--	</div>--}}
	<div class="col-12 mb-4">
		<div class="card">
		  <div class="card-body">
			<div class="d-flex align-items-start align-items-sm-center gap-4">
			  <img src="{{ asset('img/index-banner.png') }}" alt="Sale banner" id="preview-sale_banner" class="d-block rounded" height="300" width="850" />
			  <div class="button-wrapper">
				<label for="sale_banner" class="btn btn-primary me-2 mb-4" tabindex="0">
				  <span class="d-none d-sm-block">{{ __('Upload Sale Banner') }}</span>
				  <i class="bx bx-upload d-block d-sm-none"></i>
				  <input type="file" id="sale_banner" name="sale_banner" onchange="loadPreview('sale_banner')" class="account-file-input" hidden accept="image/png, image/jpeg, image/gif" />
				</label>
				<button type="button" onclick="clearImage('sale_banner')" class="btn btn-label-secondary account-image-reset mb-4">
				  <i class="bx bx-reset d-block d-sm-none"></i>
				  <span class="d-none d-sm-block">{{ __('Reset') }}</span>
				</button>
				<p class="text-muted mb-0">{{ __('Recommended PNG') }} & Takes up to 2 minutes to update.</p>
			  </div>
			</div>
		  </div>
		</div>
	</div>
</div>
<div class="row mb-4">
	<div class="d-grid gap-2 col-lg-12 mx-auto">
       <button class="btn btn-primary btn-lg" type="submit"><span class="tf-icon bx bx-save bx-xs"></span> {{ __('Save Changes') }}</button>
    </div>
</div>
</form>

@endsection
