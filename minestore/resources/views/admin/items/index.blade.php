@extends('admin.layout')

@section('vendor-style')
	<link rel="stylesheet" href="{{asset('res/vendor/css/pages/items.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/sweetalert2/sweetalert2.css')}}" />
    <link rel="stylesheet" href="{{asset('res/vendor/libs/toastr/toastr.css')}}" />
@endsection

@section('vendor-script')
    <script src="{{asset('res/vendor/libs/sortablejs/sortable.js')}}"></script>
    <script src="{{asset('res/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
    <script src="{{asset('res/vendor/libs/toastr/toastr.js')}}"></script>
@endsection

@section('page-script')
    <script src="{{asset('res/js/forms-selects.js')}}"></script>
    <script src="{{asset('res/js/forms-tagify.js')}}"></script>
    <script src="{{asset('res/js/forms-extras.js')}}"></script>
    <script src="{{asset('res/js/extended-ui-sweetalert2.js')}}"></script>
    <script src="{{asset('js/requests.js')}}"></script>
    <style type="text/css">
        /* .sortable-ghost{background-color: red;}
         .sortable-chosen{background-color: blue;}
         .sortable-drag{background-color: yellow;}*/
		 .packages.nested-item{min-height: 1px;}
    </style>
    <script>
        "use strict";
        (function() {
            (function ($) {
                $.fn.deepest = function (selector) {
                    let deepestLevel = 0,
                        $deepestChild,
                        $deepestChildSet;

                    this.each(function () {
                        let $parent = $(this);
                        $parent
                            .find((selector || '*'))
                            .each(function () {
                                if (!this.firstChild || this.firstChild.nodeType !== 1) {
                                    let levelsToParent = $(this).parentsUntil($parent).length;
                                    if (levelsToParent > deepestLevel) {
                                        deepestLevel = levelsToParent;
                                        $deepestChild = $(this);
                                    } else if (levelsToParent === deepestLevel) {
                                        $deepestChild = !$deepestChild ? $(this) : $deepestChild.add(this);
                                    }
                                }
                            });
                        $deepestChildSet = !$deepestChildSet ? $deepestChild : $deepestChildSet.add($deepestChild)
                    });

                    return this.pushStack($deepestChildSet || [], 'deepest', selector || '');
                };
            }(jQuery));

            var nestedCategories = [].slice.call(document.querySelectorAll(".nested-category"));
            for (var i = 0; i < nestedCategories.length; i++) {
                Sortable.create(nestedCategories[i], {
                    group: "nested-category",
                    // ghostClass: 'sortable-ghost',//selected\dragged
                    // chosenClass: "sortable-chosen",  // Class name for the chosen item
                    // dragClass: "sortable-drag",  // Class name for the dragging item
                    // handle: '.drag-handle',
                    // dragoverBubble: true,
                    animation: 150,
                    fallbackOnBody: true,
                    swapThreshold: 0.4,

                    onMove: function (/**Event*/evt, /**Event*/originalEvent) {
                        const relatedLevel = parseInt($(evt.related).attr('data-level'));
                        const draggedDeepElement = $(evt.dragged).deepest('[data-level]');
                        const draggedDeep = draggedDeepElement.length > 1 ? ($(draggedDeepElement[0])).parents('.category[data-category]').length : $(draggedDeepElement).parents('.category[data-category]').length;

                        const exceedsCategoryLevel = (relatedLevel - 1) >= {{ $settings->categories_level }};
                        const totalDepth = (relatedLevel - 1) + (draggedDeep - 1);
                        const exceedsTotalDepth = totalDepth >= {{ $settings->categories_level }};

                        if (
                            ($(evt.related).hasClass('subcategories') && $(evt.related).prev('.packages').children().length > 0) ||
                            exceedsCategoryLevel ||
                            (($(evt.related).hasAttr("id") && $(evt.related).attr("id") == "nestedDemo") ? false : exceedsTotalDepth)
                        ) {
                            return false;
                        }

                        return true;
                    },

                    onEnd: function(/**Event*/evt) {
                        $('[data-level]').each((index, element)=>{
                            $(element).attr('data-level', $(element).parents('.category[data-category]').length + 1);
                        });

                        let fromCategoryId = evt.from.parentElement.getAttribute("data-category");
                        let toCategoryId = evt.to.parentElement.getAttribute("data-category");
                        let objectId = evt.item.getAttribute("data-category");

                        updateCategorySorting(fromCategoryId, toCategoryId, evt.oldIndex, evt.newIndex, objectId);
                    }
                });
            }
            var nestedItems = [].slice.call(document.querySelectorAll(".nested-item"));
            for (var i = 0; i < nestedItems.length; i++) {
                Sortable.create(nestedItems[i], {
                    group: "nested-item",
                    // ghostClass: 'sortable-ghost',//selected\dragged
                    // chosenClass: "sortable-chosen",  // Class name for the chosen item
                    // dragClass: "sortable-drag",  // Class name for the dragging item
                    // handle: '.drag-handle',
                    // dragoverBubble: true,
                    animation: 150,
                    fallbackOnBody: true,
                    swapThreshold: 0.4,

                    onEnd: function(/**Event*/evt) {
                        let fromCategoryId = evt.from.parentElement.getAttribute("data-category");
                        let toCategoryId = evt.to.parentElement.getAttribute("data-category");
                        let objectId = evt.item.getAttribute("data-item-id");

                        updateItemSorting(fromCategoryId, toCategoryId, evt.oldIndex, evt.newIndex, objectId);
                    }
                });
            }
        })();

        // Should be moved in separate file
        toastr.options = {
            "closeButton": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        const deleteActions = document.querySelectorAll('.deleteAction');
        for (var i = 0; i < deleteActions.length; i++) {
            deleteActions[i].addEventListener('click', deleteActionFunc);
        }
        function deleteActionFunc() {
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
                $.ajax({
                    method: "POST",
                    url:  "/admin/" + ($(this).data('del-type') === 'item' ? 'items' : 'categories') + '/delete/' + $(this).data('del-id'),
                }).done(() => {
                    $(this).parent().parent().parent().parent().parent().parent().remove();
                    Swal.fire({
                        icon: 'success',
                        title: "{{ __('Deleted Successfully!') }}",
                        text: ($(this).data('del-type') === 'item' ? 'Package' : 'Category') + ' has been deleted.',
                        customClass: {
                            confirmButton: 'btn btn-success'
                        },
                    });
                });
            }
          });
        }

        let isHideDisabled = false;
        function toggleDisabled(e){
            isHideDisabled = !isHideDisabled;
            e.currentTarget.innerText = (isHideDisabled ? 'Show' : 'Hide') + ' Disabled';
            isHideDisabled ? $('.disabled').hide() : $('.disabled').show();
        }

        $(function() {
            $('#quickSearchPackages').on('change keyup enter', function(){
                let searchText = this.value.toLowerCase();
                $('.categories .package_name h6').each(function(){
                    if(this.innerText.toLowerCase().indexOf(searchText) == -1){
                        $(this).parents('li').hide();
                    } else {
                        $(this).parents('li').show();
                    }
                });
            });
        });
    </script>
@endsection

@section('content')
    <h4 class="fw-bold py-3 mb-1">
        <span class="text-muted fw-light">{{ __('Packages & Categories') }}</span>
    </h4>

    <!-- Handles Example -->
    <div class="col-md-12 mb-4">
        <div class="row">
            <div class="col-12 mb-md-0 mb-4">
                <div class="card package-page">
                    <div class="card-header flex-column flex-md-row">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text" id="basic-addon-search31"><i
                                            class="bx bx-search"></i></span>
                                    <input type="text" id="quickSearchPackages" class="form-control form-control-lg"
                                           placeholder="{{ __('Quick Search Packages...') }}" aria-label="Quick Search Packages..."
                                           aria-describedby="basic-addon-search31">
                                </div>
                            </div>
                            <div class="col-md-2">
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex justify-content-end gap-2 dt-action-buttons text-end pt-3 pt-md-0">
                                    <button type="button" class="btn btn-lg btn-secondary" onclick="toggleDisabled(event)">{{ __('Hide Disabled') }}</button>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-primary btn-lg dropdown-toggle"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="tf-icon bx bx-plus bx-xs"></span> {{ __('Add New') }}
                                        </button>
                                        <ul class="dropdown-menu" style="">
                                            <li>
                                                <a class="dropdown-item"
                                                   href="{{ route('categories.new') }}">{{ __('Category') }}</a>
                                            </li>
                                            <li>
                                                @if ($categories->where('deleted', 0)->count() > 0)
                                                    <a class="dropdown-item"
                                                       href="{{ route('items.new') }}">{{ __('Package') }}</a>
                                                @endif
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="packages-sorting-body">
                        <div class="categories nested-category" id="nestedDemo">
                            @foreach($categories->sortBy('sorting') as $category)
                                @php($submenuLevel = 1)
                                @include('admin.items.submenuCategory', ['menu' => $category, 'submenuLevel' => $submenuLevel])
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
