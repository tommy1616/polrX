@extends('layouts.base')

@section('css')
<link rel='stylesheet' href='css/login.css' />
@endsection

@section('content')
<div class="center-text">
    <div class="polrx-page-card polrx-auth-card">
        <h2 style="margin-top: 0; margin-bottom: 24px;">{{ __('polr.auth.login_title') }}</h2>
        <form action="login" method="POST">
            <input type="text" placeholder="{{ __('polr.auth.username') }}" name="username" class="form-control login-field" />
            <input type="password" placeholder="{{ __('polr.auth.password') }}" name="password" class="form-control login-field" />
            <input type="hidden" name='_token' value='{{csrf_token()}}' />
            <input type="submit" value="{{ __('polr.auth.login_btn') }}" class="login-submit btn btn-primary" />

            <p class='login-prompts'>
            @if (env('POLR_ALLOW_ACCT_CREATION') == true)
                <small>{{ __('polr.auth.no_account') }} <a href='{{route('signup')}}'>{{ __('polr.auth.register_link') }}</a></small>
            @endif

            @if (env('SETTING_PASSWORD_RECOV') == true)
                <small>{{ __('polr.auth.forgot_password') }} <a href='{{route('lost_password')}}'>{{ __('polr.auth.reset_link') }}</a></small>
            @endif
            </p>
        </form>
    </div>
</div>
@endsection
