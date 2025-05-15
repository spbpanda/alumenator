@extends('admin.layout')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/tagify/tagify.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/typeahead-js/typeahead.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/jstree/jstree.css')}}" />
<link rel="stylesheet" href="{{asset('res/codemirror/codemirror.css')}}" />
<link rel="stylesheet" href="{{asset('res/codemirror/theme/dracula.css')}}" />
<style>
    .CodeMirror {resize: vertical;}
</style>
@endsection

@section('vendor-script')
<script src="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('res/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('res/vendor/libs/jstree/jstree.js')}}"></script>
<script src="{{asset('res/codemirror/codemirror.js')}}"></script>
<script src="{{asset('res/codemirror/mode/xml/xml.js')}}"></script>
<script src="{{asset('res/codemirror/mode/javascript/javascript.js')}}"></script>
<script src="{{asset('res/codemirror/mode/css/css.js')}}"></script>
<script src="{{asset('res/codemirror/mode/vbscript/vbscript.js')}}"></script>
<script src="{{asset('res/codemirror/mode/htmlmixed/htmlmixed.js')}}"></script>
<script src="{{asset('res/codemirror/addon/edit/matchbrackets.js')}}"></script>
<script src="{{asset('res/codemirror/addon/edit/closebrackets.js')}}"></script>
<script src="{{asset('res/codemirror/addon/edit/matchtags.js')}}"></script>
<script src="{{asset('res/codemirror/addon/search/searchcursor.js')}}"></script>
<script src="{{asset('res/codemirror/addon/search/search.js')}}"></script>
<script src="{{asset('res/codemirror/addon/comment/comment.js')}}"></script>
<script src="{{asset('res/codemirror/addon/fold/foldcode.js')}}"></script>
<script src="{{asset('res/codemirror/addon/fold/brace-fold.js')}}"></script>
<script src="{{asset('res/codemirror/addon/fold/xml-fold.js')}}"></script>
@endsection

@section('page-script')
<script>
  $(function() {
      let openedFile = '';
      $(".saveFile").on('click', function(e){
          e.preventDefault();

          if (!openedFile) return;

          var oldToastrOptions = toastr.options;
          toastr.options = {
              "positionClass": "toast-bottom-right",
              "preventDuplicates": false,
              "showDuration": "300",
              "hideDuration": "300",
              "timeOut": "3000",
              "extendedTimeOut": "300",
          };

          $('.saveFile').prop('disabled', true);
          toastr.warning('@lang('Saving the file...')', '@lang('Please wait!')');

          $.ajax({
              method: "POST",
              url: "/admin/themes/files/saveFile/{{$themeId}}/" + openedFile,
              processData: false,
              contentType: 'text/plain',
              data: editor.getValue(),
          }).done(function( msg ) {
              $('.saveFile').prop('disabled', false);
              toastr.success('@lang('File has been saved successfully')!', '@lang('Completed')');
          });
      });

      const editorFileTypes = {
          'js': 'javascript', 'html': 'htmlmixed', 'css': 'css',
      };

      const editor = CodeMirror.fromTextArea(document.getElementById("code"), {
          lineNumbers: true,
          mode: "htmlmixed",
          autoCloseBrackets: true,
          matchBrackets: true,
          showCursorWhenSelecting: true,
          theme: "dracula",
          matchTags: {bothTags: true},
          tabSize: 2,
      });

      function customMenu(node) {
          let items = {};

          if (node.icon.indexOf('folder') !== -1) {
              items['Upload file'] = {
                  label: 'Upload file',
                  action: jsTreeUpload,
              }
          }

          return items;
      }

      function jsTreeUpload(data) {
          let selNode = $(data.reference).hasClass('jstree-node') ? $(data.reference).attr('id') : $(data.reference).parent().attr('id');
          $('#file_path').val(contextMenu.jstree(true).get_path(selNode,"/"));
          $('#newFileForm > input').trigger('click');
      }

      $("#newFileForm > input").change(function(){
          $("#newFileForm").submit();
      });

      const contextMenu = $('#jstree-context-menu');
      if (contextMenu.length) {
          let theme = $('html').hasClass('light-style') ? 'default' : 'default-dark';
          contextMenu
              .on('select_node.jstree', function (node, selected, event) {
                  if (editorFileTypes.hasOwnProperty(selected.node.type)){
                      let fullFilePath = contextMenu.jstree(true).get_path(selected.node,"/");
                      $('#editedFileName').text(fullFilePath);
                      $.ajax({
                          method: "GET",
                          url: "/admin/themes/files/readFile/{{ $themeId }}/" + fullFilePath,
                      }).done(function(fileData) {
                          openedFile = fullFilePath;
                          editor.setValue(fileData);
                          editor.setOption("mode", editorFileTypes[selected.node.type]);
                      });
                  }
              })
              .jstree({
              core: {
                  themes: {
                      name: theme
                  },
                  check_callback: true,
                  data: {!! json_encode($files) !!}
              },
              contextmenu: {
                  show_at_node: false,
                  items: customMenu
              },
              plugins: ['types', 'contextmenu'],
              types: {
                  default: {
                      icon: 'bx bx-folder'
                  },
                  html: {
                      icon: 'bx bxl-html5 text-danger'
                  },
                  unknown: {
                      icon: 'bx bx-file text-secondary'
                  },
                  css: {
                      icon: 'bx bxl-css3 text-info'
                  },
                  img: {
                      icon: 'bx bx-image text-success'
                  },
                  js: {
                      icon: 'bx bxl-nodejs text-warning'
                  }
              }
          });
        @if(!empty($_GET['path']))
        contextMenu.on('loaded.jstree', function() {
            const list = contextMenu.jstree(true).get_json(null, {'flat': true});
            for (let i = 0; i < list.length; i++){
                if (contextMenu.jstree(true).get_path(list[i],"/") == '{{ $_GET['path'] }}'){
                    contextMenu.jstree(true).select_node(list[i]);
                    break;
                }
            }
        });
        @endif
      }
  });
</script>
    <style>
        .CodeMirror {
            height: 600px;
        }
    </style>
@endsection

@section('content')
<form id="newFileForm" method="post" enctype="multipart/form-data" style="display: none">
    @csrf
    <input id="file_path" name="path" type="text" />
    <input id="file" name="file" type="file" />
</form>

<h4 class="fw-bold py-3 mb-1">
    <span class="text-body fw-light">{{ __('Theme file manager') }}</span>
</h4>

<div class="row">
  <div class="col-md-3">
    <div class="card mb-4">
      <h5 class="card-header">{{ __('Theme Files') }}</h5>
      <div class="card-body">
        <div id="jstree-context-menu" class="overflow-auto"></div>
      </div>
    </div>
  </div>
   <div class="col-md-9">
    <div class="card mb-4">
      <h4 class="card-header">{{ __('File Editor') }} <span id="editedFileName"></span></h4>
      <div class="card-body">
        <textarea id="code"></textarea>
          <br>
          <div class="d-flex align-items-center justify-content-end">
            <button type="button" class="btn btn-primary d-flex align-items-center me-1 mt-0 saveFile"><i class="bx bxs-save me-1"></i>{{ __('Save File') }}</button>
          </div>
      </div>
    </div>
  </div>
</div>
@endsection
