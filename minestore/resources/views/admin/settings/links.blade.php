@extends('admin.layout')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/sweetalert2/sweetalert2.css')}}" />
    <style>.drag-handle{cursor: pointer;}</style>
@endsection

@section('vendor-script')
    <script src="{{asset('res/vendor/libs/sortablejs/sortable.js')}}"></script>
    <script src="{{asset('res/vendor/libs/jquery-repeater/jquery-repeater.js')}}"></script>
    <script src="{{asset('res/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>

@endsection

@section('page-script')
<script>
    var formRepeater = $(".form-repeater");
    var repeaterRow = {{ $links->isEmpty() ? 1 : count($links) }};
    function repeaterUpdate(){
        $(".sortableParent > div").each(function() {
            var col = 1;
            var fromControl = $(this).find('.form-control, .form-select, .form-check-input');
            var formLabel = $(this).find('.form-label');

            fromControl.each(function (i) {
                var id = 'form-repeater-' + repeaterRow + '-' + col;
                $(fromControl[i]).attr('id', id);
                $(formLabel[i]).attr('for', id);
                var $this = $(this);
                $this.attr('name', 'links[' + repeaterRow + '][' + $this.attr('data-name') + ']');
                col++;
            });

            repeaterRow++;

            $(this).slideDown();
        });
    }
    repeaterUpdate();
    formRepeater.repeater({
        initEmpty: {{ $links->isEmpty() ? 'true' : 'false' }},
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
                        title: " {{ __('Deleted!') }}",
                        text: "{{ __('Link has been deleted successfully.') }}",
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
<form action="{{ route('settings.linksSave') }}" method="POST" autocomplete="off" class="form-repeater">
    @csrf
    <div class="col-12 mb-4">
        <div class="col-md-12 mb-3">
            <h5 class="card-header">{{ __('Link settings') }}</h5>
        </div>
        <div class="card">
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="d-grid gap-2 col-lg-6 mx-auto mb-3">
                        <button type="button" class="btn btn-primary" data-repeater-create>
                            <i class="bx bx-plus"></i>
                            <span class="align-middle">{{ __('Add link') }}</span>
                        </button>
                    </div>
                    <hr>
                </div>
            </div>
            <div class="card-body">
                <div class="sortableParent" data-repeater-list="links">
                    @if($links->isEmpty())
                        @include('admin.settings.blocks.link', ['i' => 0, 'link' => (object)['name' => '', 'icon' => '', 'url' => '', 'footer' => 0, 'header' => 0]])
                    @else
                        @for ($i = 0; $i < count($links); $i++)
                            @include('admin.settings.blocks.link', ['i' => $i, 'link' => $links[$i]])
                        @endfor
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="d-grid gap-2 col-lg-12 mx-auto">
            <button class="btn btn-primary btn-lg" type="submit"><span class="tf-icon bx bx-plus-circle bx-xs"></span> {{ !empty($links) ? __('Save') : __('Create') }} {{ __('a link') }}</button>
        </div>
    </div>
</form>
@endsection
