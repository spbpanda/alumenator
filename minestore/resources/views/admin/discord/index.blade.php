@extends('admin.layout')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('res/vendor/fonts/fontawesome.css')}}" />
@endsection

@section('vendor-script')
@endsection

@section('page-script')
@endsection

@section('content')
    <h4 class="fw-bold py-3 mb-1">
        <span class="text-body fw-light">{{ __('Configure your Discord Settings') }}</span>
    </h4>

    @if (session('success'))
        <div class="alert alert-primary alert-dismissible" role="alert">
            <h6 class="alert-heading d-flex align-items-center mb-1">{{ __('Well done') }} 👍</h6>
            <p class="mb-0">{{ session('success') }}</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            </button>
        </div>
    @endif

    <form method="POST" autocomplete="off">
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
                                                <i class="bx bx-git-repo-forked"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
                                        <h4>
                                            {{ __('Webhook URL') }}
                                        </h4>
                                        <div class="mb-3 col-md-10">
                                            <p class="card-text">{{ __('You can find by following: Discord Server Settings -> Integrations -> Create Webhook.') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
                                <input class="form-control" type="text" id="webhook_url" name="webhook_url" value="{{ $settings->webhook_url }}" placeholder="{{ __('Enter your Discord Webhook URL...') }}">
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
                                                <i class="fas fa-heart"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
                                        <h4>
                                            {{ __('Discord Invite Link') }}
                                        </h4>
                                        <div class="mb-3 col-md-10">
                                            <p class="card-text">{{ __('You can fill it with any URL that will redirect user to your Discord server.') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
                                <input class="form-control" type="text" id="discord_url" name="discord_url" value="{{ $settings->discord_url }}" placeholder="{{ __('Enter your Discord Invite URL...') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
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
                                        {{ __('Enable Discord Bot') }}
                                    </h4>
                                    <div class="mb-3 col-md-10">
                                        <p class="card-text">{{ __('You can enable Discord Bot to give roles to users who have purchased a product.') }} Check out <a href="https://docs.minestorecms.com/features/discord-bot" target="_blank">official documentation guide</a>.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
                            <label class="switch switch-square" for="discord_bot_enabled">
                                <input type="hidden" name="discord_bot_enabled" value="0">
                                <input type="checkbox" class="switch-input" id="discord_bot_enabled" name="discord_bot_enabled" value="1" {{ $settings->discord_bot_enabled == 1 ? 'checked' : ''}} />
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
            <div class="card">
                <div class="card-body">
                    <div class="row d-flex w-100 align-self-center">
                        <div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
                            <div class="row align-self-center h-100">
                                <div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
                                    <div class="d-flex justify-content-center mb-4">
                                        <div class="settings_icon bg-label-primary">
                                            <i class="bx bxl-discord-alt"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
                                    <h4>
                                        {{ __('Server ID') }}
                                    </h4>
                                    <div class="mb-3 col-md-10">
                                        <p class="card-text">{{ __('You can find by following: Discord Server Settings -> Widget -> Server ID.') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
                            <input class="form-control" type="text" id="discord_guild_id" name="discord_guild_id" value="{{ $settings->discord_guild_id }}" placeholder="{{ __('Enter your Discord Server ID...') }}">
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
                                            <i class="bx bxs-bot"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
                                    <h4>
                                        {{ __('Discord Bot Token') }}
                                    </h4>
                                    <div class="mb-3 col-md-10">
                                        <p class="card-text">{{ __('You can find by following:') }} <a href="https://discord.com/developers/applications" target="_blank">{{ __('Discord Developer Portal') }}</a> <code>{{ __('-> Your Application -> Bot -> Token.') }}</code></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
                            <div class="form-password-toggle">
                                <div class="input-group">
                                    <input type="password" class="form-control" id="discord_bot_token" name="discord_bot_token" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" value="{{ $settings->discord_bot_token }}" />
                                    <span id="discord_bot_token_btn" class="input-group-text cursor-pointer"><i class="bx bx-hide password-toggle-button"></i></span>
                                </div>
                            </div>
                            @error('discord_bot_token')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="row d-flex w-100 align-self-center">
                        <div class="description col-12 col-xl-8 col-lg-8 text-center text-lg-left">
                            <div class="row align-self-center h-100">
                                <div class="col-12 col-xl-2 col-lg-3 align-self-center text-center">
                                    <div class="d-flex justify-content-center mb-4">
                                        <div class="settings_icon bg-label-primary">
                                            <i class="bx bxs-user"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
                                    <h4>
                                        {{ __('Client ID') }}
                                    </h4>
                                    <div class="mb-3 col-md-10">
                                        <p class="card-text">{{ __('You can find by following:') }} <a href="https://discord.com/developers/applications" target="_blank">{{ __('Discord Developer Portal') }}</a> <code>{{ __('-> Your Application -> General Information -> Client ID.') }}</code></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
                            <input class="form-control" type="text" id="discord_client_id" name="discord_client_id" value="{{ $settings->discord_client_id }}" placeholder="{{ __('Enter your Discord Client ID...') }}">
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
                                            <i class="bx bxs-lock"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-xl-10 col-lg-9 align-self-center my-3 my-lg-0" style="text-align: left;">
                                    <h4>
                                        {{ __('Client Secret') }}
                                    </h4>
                                    <div class="mb-3 col-md-10">
                                        <p class="card-text">{{ __('You can find by following:') }} <a href="https://discord.com/developers/applications" target="_blank">{{ __('Discord Developer Portal') }}</a> <code>{{ __('-> Your Application -> General Information -> Client Secret.') }}</code></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="action col-12 col-xl-3 col-lg-4 align-self-center text-center mx-auto d-grid">
                            <div class="form-password-toggle">
                                <div class="input-group">
                                    <input type="password" class="form-control" id="discord_client_secret" name="discord_client_secret" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" value="{{ $settings->discord_client_secret }}" />
                                    <span id="discord_client_secret_btn" class="input-group-text cursor-pointer"><i class="bx bx-hide password-toggle-button"></i></span>
                                </div>
                            </div>
                            @error('discord_client_secret')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
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
