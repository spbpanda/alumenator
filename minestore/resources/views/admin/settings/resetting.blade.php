@extends('admin.layout')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('res/vendor/libs/sweetalert2/sweetalert2.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('res/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('res/js/extended-ui-sweetalert2.js')}}"></script>
<script>
'use strict';

(function () {
  const resetButtons = document.querySelectorAll('.resetAction');

  for(let i = 0; i < resetButtons.length; i++){
      resetButtons[i].onclick = function () {
          const dataAction = $(this).attr('data-action');
          Swal.fire({
            title: "{{ __('Do you confirm this action?') }}",
              text: "{!! __('You won\'t be able to revert this!') !!}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: "{{ __('Yes, I confirm!') }}",
            customClass: {
              confirmButton: 'btn btn-primary me-3',
              cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
          }).then(function (result) {
            if (result.value) {
                $.ajax({
                    method: "POST",
                    url: "/admin/settings/"+dataAction,
                }).done(function(r) {
                    Swal.fire({
                        icon: 'success',
                        title: "{{ __('Done!' ) }}",
                        text: "{{ __('Action is completed!') }}",
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    });
                }).fail(function(r) {
                    Swal.fire({
                        title: "{{ __('Error') }}",
                        text: "{{ __('Action was failed!') }}",
                        icon: 'danger',
                        timer: 4000,
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false,
                    });
                });
            }
          });
    };
  }
})();
</script>
@endsection

@section('content')
<form method="POST" enctype="multipart/form-data" autocomplete="off">
@csrf

<h4 class="fw-bold py-3 mb-1">
  <span class="text-body fw-light">{{ __('Resetting Tool') }}</span>
</h4>
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
                    <i class="bx bx-wallet"></i>
                  </div>
                </div>
              </div>
              <div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
                <h4>
                    {{ __('Remove All Payments from the Webstore?') }}
                  <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('This option allows you to remove all records related to payments from MineStoreCMS database.') }}"></i>
                </h4>
                <div class="mb-3 col-md-10">
                <p class="card-text">{{ __('All your webstore payments, checkout baskets records will be permanently removed.') }}</p>
                </div>
              </div>
            </div>
          </div>
          <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
            <button class="btn btn-primary btn-lg resetAction" data-action="removeAllPayments" type="button">{{ __('Remove Payments') }}</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="row d-flex w-100 align-self-center">
          <div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
            <div class="row align-self-center h-100">
              <div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
                <div class="d-flex justify-content-center mb-4">
                  <div class="settings_icon bg-label-primary">
                    <i class="bx bx-window-close"></i>
                  </div>
                </div>
              </div>
              <div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
                <h4>
                    {{ __('Remove All Users from the Webstore Ban list?') }}
                  <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('This option allows you to remove all records related to ban list from MineStoreCMS database.') }}"></i>
                </h4>
                <div class="mb-3 col-md-10">
                <p class="card-text">{{ __('All records related to users ban list of your webstore will be removed.') }}</p>
                </div>
              </div>
            </div>
          </div>
          <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
            <button class="btn btn-primary btn-lg resetAction" data-action="resetBanlist" type="button">{{ __('Clean Up Ban list') }}</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 mb-2">
    <div class="card">
      <div class="card-body">
        <div class="row d-flex w-100 align-self-center">
          <div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
            <div class="row align-self-center h-100">
              <div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
                <div class="d-flex justify-content-center mb-4">
                  <div class="settings_icon bg-label-primary">
                    <i class="bx bx-group"></i>
                  </div>
                </div>
              </div>
              <div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
                <h4>
                    {{ __('Remove All Data About Players?') }}
                  <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('This option allows you to clean up player data received by plugin (prefixes, virtual currencies, groups and etc).') }}"></i>
                </h4>
                <div class="mb-3 col-md-10">
                <p class="card-text">{{ __('All records received by Minecraft plugin synchronization will be removed.') }}</p>
                </div>
              </div>
            </div>
          </div>
          <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
            <button class="btn btn-primary btn-lg resetAction" data-action="removeAllPlayerdata" type="button">{{ __('Remove Players Data') }}</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<hr>
<div class="row">
  <div class="col-12 mb-4 mt-2">
    <div class="card">
      <div class="card-body">
        <div class="row d-flex w-100 align-self-center">
          <div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
            <div class="row align-self-center h-100">
              <div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
                <div class="d-flex justify-content-center mb-4">
                  <div class="settings_icon bg-label-primary">
                    <i class="bx bxs-store"></i>
                  </div>
                </div>
              </div>
              <div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
                <h4>
                    {{ __('Make a Full Wipe of the Webstore?') }}
                  <i class="bx bx-help-circle text-muted" style="margin-bottom: 1px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('This option allows you to fully clean up all your MineStoreCMS database.') }}"></i>
                </h4>
                <div class="mb-3 col-md-10">
                <p class="card-text">{{ __('This option will remove all users, payments, carts, subscriptions, chargebacks.') }}</p>
                </div>
              </div>
            </div>
          </div>
          <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
            <button data-type="fullWipe" class="btn btn-primary btn-lg resetAction" data-action="fullWipe" type="button">{{ __('Wipe Webstore') }}</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</form>

@endsection
