@extends('layouts.base')

@section('css')
<link rel='stylesheet' href='/css/lost_password.css' />
@endsection

@section('content')
<div class="polrx-page-card polrx-auth-card">
    <h2 style="margin-top:0; margin-bottom:20px;">{{ __('polr.auth.lost_password_title') }}</h2>
    <form action='/lost_password' method='POST'>
        <input type='email' name='email' placeholder='{{ __('polr.auth.email') }}' class='form-control email-input-pd' style="margin-bottom:14px;">
        <input type="hidden" name='_token' value='{{csrf_token()}}' />
        <input type='submit' value='{{ __('polr.auth.send_reset_email') }}' class='btn btn-primary' style="width:100%;">
    </form>
</div>
@endsection
