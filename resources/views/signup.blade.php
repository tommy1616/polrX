@extends('layouts.base')

@section('css')
<link rel='stylesheet' href='/css/signup.css' />
@endsection

@section('content')
<div class="polrx-page-card">
    <div class="row">
        <div class='col-md-6'>
            <h2 class='title' style="margin-top:0; margin-bottom:20px;">{{ __('polr.auth.register_title') }}</h2>

            <form action='/signup' method='POST'>
                <label class="polrx-field-label">{{ __('polr.auth.username') }}</label>
                <input type='text' name='username' class='form-control form-field' placeholder='{{ __('polr.auth.username') }}' />
                <label class="polrx-field-label">{{ __('polr.auth.password') }}</label>
                <input type='password' name='password' class='form-control form-field' placeholder='{{ __('polr.auth.password') }}' />
                <label class="polrx-field-label">{{ __('polr.auth.email') }}</label>
                <input type='email' name='email' class='form-control form-field' placeholder='{{ __('polr.auth.email') }}' />

                @if (env('POLR_ACCT_CREATION_RECAPTCHA'))
                <div class="g-recaptcha" data-sitekey="{{env('POLR_RECAPTCHA_SITE_KEY')}}"></div>
                @endif

                <input type="hidden" name='_token' value='{{csrf_token()}}' />
                <input type="submit" class="btn btn-primary" value="{{ __('polr.auth.register_btn') }}"/>
                <p class='login-prompt' style="margin-top: 14px;">
                    <small>{{ __('polr.auth.already_have_account') }} <a href='{{route('login')}}'>{{ __('polr.auth.login_btn') }}</a></small>
                </p>
            </form>
        </div>
        <div class='col-md-6 hidden-xs hidden-sm'>
            <div class="well polrx-subcard">
                <h4 style="margin-top:0;">{{ __('polr.auth.username_help_title') }}</h4>
                <p class="polrx-muted">{{ __('polr.auth.username_help_desc', ['app' => env('APP_NAME')]) }}</p>
                <h4>{{ __('polr.auth.password_help_title') }}</h4>
                <p class="polrx-muted">{{ __('polr.auth.password_help_desc', ['app' => env('APP_NAME')]) }}</p>
                <h4>{{ __('polr.auth.email_help_title') }}</h4>
                <p class="polrx-muted" style="margin-bottom:0;">{{ __('polr.auth.email_help_desc') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
    @if (env('POLR_ACCT_CREATION_RECAPTCHA'))
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif
@endsection
