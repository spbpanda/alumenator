@extends('admin.layout')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/tagify/tagify.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/sweetalert2/sweetalert2.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('res/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('res/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('res/vendor/libs/tagify/tagify.js')}}"></script>
<script src="{{asset('res/vendor/libs/jquery-repeater/jquery-repeater.js')}}"></script>
<script src="{{asset('res/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
@endsection

@section('page-script')
<script type="text/javascript">
var formRepeater = $(".form-repeater");
var repeaterRow = {{ empty($cmds) ? 1 : (count($cmds) + 1) }};

function repeaterUpdate(){
    $(".form-repeater").each(function() {
        var formsRepeater = $(this).find('div[data-repeater-item]');

        formsRepeater.each(function (r) {
            var col = 0;
            var fromControl = $(this).find('.form-control, .form-select');
            var formLabel = $(this).find('.form-label');

            fromControl.each(function (i) {
                var id = 'form-repeater-' + repeaterRow + '-' + col;
                $(fromControl[i]).attr('id', id);
                $(formLabel[i]).attr('for', id);
                var $this = $(this);

                if (this.tagName == 'SELECT')
                {
                    $this.attr('name', 'command[' + repeaterRow + '][' + $this.attr('data-name') + ']' + ($this.hasAttr("name") ? '[]' : ''));
                    if ($this.hasClass('select2'))
                    {
                        $this.parent().find('.select2-container').remove();
                        $this.select2({
                            dropdownParent: $this.parent()
                        });
                    }
                } else {
                    $this.attr('name', 'command[' + repeaterRow + '][' + $this.attr('data-name') + ']');
                }

                col++;
            });
            repeaterRow++;
        });

        $(this).slideDown();
    });
}
repeaterUpdate();
formRepeater.repeater({
  initEmpty: {{ count($cmds) == 0 ? 'true' : 'false' }},
  show: function() {
    repeaterUpdate();
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

@if(count($cmds) > 0)
  const select2s = $('.select2');
  select2s.each(function () {
    var $this = $(this);
    $this.select2({
      dropdownParent: $this.parent()
    });
  });
@endif
</script>
@endsection

@section('content')
<h4 class="fw-bold py-3 mb-1">
  <span class="text-body fw-light">{{ __('Global Commands') }}</span>
</h4>

<form method="POST" autocomplete="off" class="form-repeater">
@csrf
<div class="row">
	<div class="col-12 mb-4">
		<div class="card">
			<div class="card-body">
				<div class="row d-flex w-100 align-self-center">
					<div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
						<div class="row align-self-center h-100">
							<div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
								<div class="d-flex justify-content-center mb-4">
								  <div class="settings_icon bg-label-primary">
									  <i class="bx bx-shape-polygon"></i>
								  </div>
								</div>
							</div>
							<div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
								<h4>
                                    {{ __('Enable Global Commands?') }}
								</h4>
								<div class="mb-3 col-md-10">
								<p class="card-text">{{ __('Do you want to use this module, which will execute commands after each purchase that meet the requirements?') }}</p>
								</div>
							</div>
						</div>
					</div>
					<div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
						<label class="switch switch-square" for="enable_globalcmd">
						  <input type="checkbox" class="switch-input" id="enable_globalcmd" name="enable_globalcmd" {{ $enable_globalcmd == 1 ? 'checked' : '' }} />
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
	<div class="col-12 mb-4">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
	  <div class="col-12 mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="text-body fw-light mb-0">
                    {{ __('Created Global Commands') }}
                </h4>
            </div>
            <div class="col-md-6 pt-4 pt-md-0 d-flex justify-content-end">
                <button type="button" class="btn btn-primary btn-sm fs-6 d-flex align-items-center gap-1" data-repeater-create>
                    <span class="tf-icon bx bx-plus-circle bx-xs"></span>
                    {{ __('Add a New Global Command') }}
                </button>
            </div>
        </div>
	  </div>
		<div class="card">
			<div class="card-body" data-repeater-list="command">
        @if(count($cmds) == 0)
            @include('admin.globalcommands.command', ['isEmpty' => true, 'servers' => $servers, 'i' => 1, 'cmd' => ['cmd'=>'','servers'=>[],'is_online'=>1,'price'=>1]])
        @else
            @for ($i = 0; $i < count($cmds); $i++)
                @include('admin.globalcommands.command', ['isEmpty' => false, 'servers' => $servers, 'i' => $i, 'cmd' => $cmds[$i]])
            @endfor
        @endif
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
