@php use Carbon\Carbon; @endphp
@extends('admin.layout')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}"/>
@endsection

@section('vendor-script')
    <script src="{{asset('res/vendor/libs/moment/moment.js')}}"></script>
@endsection

@section('page-script')
@endsection

@section('content')
    <h4 class="fw-bold py-3 mb-1">
        <span class="text-body fw-light">{{ __('Edit Staff User') }}</span>
    </h4>

    <form action="{{ route('pages.staff.store', $player->id) }}" method="POST" autocomplete="off">
        @csrf
        @method('POST')
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="username">
                                {{ __('Username') }}
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;"
                                   data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Need to be unique.') }}"></i>
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="username" name="username" value="{{ $player->username }}"
                                       placeholder="xMarkus" required/>
                            </div>
                            @error('username')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="uuid">
                                {{ __('UUID') }}
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Need to be unique.') }}"></i>
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="uuid" name="uuid" placeholder="f81d4fae-7dec-11d0-a765-00a0c91e6bf6" value="{{ $player->uuid }}" required />
                            </div>
                            @error('uuid')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="expire_at" class="form-label">
                                {{ __('Prefix') }}
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Prefix to display if this option enabled.') }}"></i>
                            </label>
                            <input type="text" class="form-control" id="prefix" name="prefix" placeholder="&c[Moderator]" value="{{ $player->prefix ?? '' }}" required />
                            @error('prefix')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="player_group" class="form-label">
                                {{ __('Group') }}
                                <i class="bx bx-help-circle text-muted" style="margin-bottom: 3px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Group to attach user.') }}"></i>
                            </label>
                            <input type="text" class="form-control" id="player_group" name="player_group" placeholder="Moderator" value="{{ $player->player_group }}" required />
                            @error('player_group')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="d-grid gap-2 col-lg-12 mx-auto">
                <button class="btn btn-primary btn-lg" type="submit"><span class="tf-icon bx bx-refresh bx-xs"></span>
                    {{ __('Update User') }}
                </button>
            </div>
        </div>
    </form>
@endsection
