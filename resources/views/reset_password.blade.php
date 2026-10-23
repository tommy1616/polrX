@extends('layouts.base')

@section('css')
<link rel='stylesheet' href='/css/reset_password.css' />
@endsection

@section('content')
<div class="polrx-page-card polrx-auth-card">
    <h2 style="margin-top:0; margin-bottom:20px;">{{ __('polr.auth.reset_password_title') }}</h2>
    <form action="#" method='POST'>
        <input type='password' id='passwordFirst' placeholder='{{ __('polr.auth.new_password') }}' class='form-control password-input-pd' style="margin-bottom:14px;">
        <input type='password' id='passwordConfirm' placeholder='{{ __('polr.auth.confirm_new_password') }}' class='form-control password-input-pd' name='new_password' style="margin-bottom:14px;">

        <input type="hidden" name='_token' value='{{csrf_token()}}' />
        <input type='submit' id='submitForm' value='{{ __('polr.auth.reset_password_btn') }}' class='btn btn-primary' style="width:100%;">
    </form>
</div>
@endsection

@section('js')
<script src='/js/reset_password.js'></script>
@endsection
