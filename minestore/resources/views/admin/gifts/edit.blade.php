@php use Carbon\Carbon; @endphp
@extends('admin.layout')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}"/>
    <link rel="stylesheet" href="{{asset('res/vendor/libs/flatpickr/flatpickr.css')}}"/>
    <link rel="stylesheet" href="{{asset('res/vendor/libs/pickr/pickr-themes.css')}}"/>
@endsection

@section('vendor-script')
    <script src="{{asset('res/vendor/libs/moment/moment.js')}}"></script>
    <script src="{{asset('res/vendor/libs/flatpickr/flatpickr.js')}}"></script>
    <script src="{{asset('res/vendor/libs/pickr/pickr.js')}}"></script>
@endsection

@section('page-script')
    <script>
        document.querySelector('#expire_at').flatpickr({
            enableTime: true,
            dateFormat: 'Y-m-d H:i'
        });
    </script>
    <script>
        function refreshKey(el) {
            var password = $('#name');
            var chars = "ABCDEFGHIJKLMNOP1234567890";
            var pass = "";
            const length = 10;
            for (var x = 0; x < length; x++) {
                var i = Math.floor(Math.random() * chars.length);
                pass += chars.charAt(i);
            }
            password.val(pass);
            password[0].setAttribute("type", "text");
        }
    </script>
@endsection

@section('content')
    <h4 class="fw-bold py-3 mb-1">
        <span class="text-body fw-light">{{ __('Gift Card') }}</span>
    </h4>

    <form action="{{ route('gifts.update', $gift->id) }}" method="POST" autocomplete="off">
        @csrf
        @method('PATCH')
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label" for="name">
                                {{ __('Gift Card Name') }}
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;"
                                   data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Need to be unique.') }}"></i>
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="name" name="name" value="{{ $gift->name }}"
                                       placeholder="JD78X87HDK" required/>
                                <span onclick="refreshKey(event)" class="input-group-text cursor-pointer"><i
                                        class="bx bx-refresh"></i></span>
                            </div>
                            @error('name')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="form-label" for="start_balance">
                                {{ __('Gift Card Value') }}
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;"
                                   data-bs-toggle="tooltip" data-bs-placement="top"
                                   title="{{ __('Enter how much money will be included in this gift card.') }}"></i>
                            </label>
                            <div class="input-group">
                                <input type="text" inputmode="numeric" pattern="^\d*([,.]\d{1,2})?$"
                                       class="form-control" id="start_balance" name="start_balance"
                                       value="{{ $gift->start_balance }}" aria-label="Amount for gift card">
                                <span class="input-group-text">{{ $settings->currency }}</span>
                            </div>
                            @error('start_balance')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-sm-6 mb-3">
                            <label class="form-label" for="current_balance">
                                {{ __('Gift Card Current Value') }}
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;"
                                   data-bs-toggle="tooltip" data-bs-placement="top"
                                   title="{{ __('Enter how much money the gift card currently has.') }}"></i>
                            </label>
                            <div class="input-group">
                                <input type="text" inputmode="numeric" pattern="^\d*([,.]\d{1,2})?$"
                                       class="form-control" id="current_balance" name="end_balance"
                                       value="{{ $gift->end_balance }}" aria-label="Current amount for gift card">
                                <span class="input-group-text">{{ $settings->currency }}</span>
                            </div>
                            @error('end_balance')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-sm-12 mb-3">
                            <label for="expire_at" class="form-label">
                                {{ __('Expire date') }}
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;"
                                   data-bs-toggle="tooltip" data-bs-placement="top"
                                   title="{{ __('Set a datetime when gift card will be expired.') }}"></i>
                            </label>
                            <input type="text" class="form-control" id="expire_at" name="expire_at"
                                   value="{{ $gift->expire_at ? Carbon::createFromFormat('Y-m-d H:i:00', $gift->expire_at)->format('Y-m-d H:i') : '' }}"
                                   placeholder="YYYY-MM-DD HH:MM"/>
                        </div>
                        <div class="col-sm-12 mb-3">
                            <label for="note" class="form-label">
                                {{ __('Note') }}
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;"
                                   data-bs-toggle="tooltip" data-bs-placement="top"
                                   title="{{ __('Make a note for yourself.') }}"></i>
                            </label>
                            <textarea class="form-control" id="note" name="note" rows="2">{{ $gift->note }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="d-grid gap-2 col-lg-12 mx-auto">
                <button class="btn btn-primary btn-lg" type="submit"><span class="tf-icon bx bx-refresh bx-xs"></span>
                    {{ __('Update a Gift Card') }}
                </button>
            </div>
        </div>
    </form>
@endsection
