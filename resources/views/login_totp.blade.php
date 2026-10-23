@extends('layouts.base')

@section('css')
<link rel='stylesheet' href='/css/login.css' />
@endsection

@section('content')
<div class="center-text">
    <div class="polrx-page-card polrx-auth-card">
        <h2 style="margin-top: 0; margin-bottom: 14px;">{{ __('polr.auth.totp_title') }}</h2>
        <p class="polrx-muted" style="margin-bottom: 20px;">{!! __('polr.auth.totp_prompt', ['username' => e($pending_username)]) !!}</p>
        <form action="/login/totp" method="POST">
            <input type="text" inputmode="numeric" autocomplete="one-time-code" maxlength="6" placeholder="{{ __('polr.auth.totp_placeholder') }}" name="totp_code" class="form-control login-field" />
            <input type="hidden" name='_token' value='{{csrf_token()}}' />
            <input type="submit" value="{{ __('polr.auth.totp_submit') }}" class="login-submit btn btn-primary" />
        </form>
    </div>
</div>
@endsection
