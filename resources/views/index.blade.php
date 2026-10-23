@extends('layouts.base')

@section('css')
<link rel='stylesheet' href='css/index.css' />
@endsection

@section('content')
<h1 class='title'>{{env('APP_NAME')}}</h1>

<form method='POST' action='/shorten' role='form'>
    <input type='url' autocomplete='off'
        class='form-control long-link-input' placeholder='{{ __('polr.index.long_url_placeholder') }}' name='link-url' />

    <div class='row' id='options' ng-cloak>
        <p>{{ __('polr.index.custom_url_title') }}</p>

        @if (!env('SETTING_PSEUDORANDOM_ENDING'))
        <div class='btn-group btn-toggle visibility-toggler' data-toggle='buttons'>
            <label class='btn btn-primary btn-sm active'>
                <input type='radio' name='options' value='p' checked /> {{ __('polr.index.public') }}
            </label>
            <label class='btn btn-sm btn-default'>
                <input type='radio' name='options' value='s' /> {{ __('polr.index.secret') }}
            </label>
        </div>
        @endif

        <div>
            <div class='custom-link-text'>
                <h2 class='site-url-field'>{{env('APP_ADDRESS')}}/</h2>
                <input type='text' autocomplete="off" class='form-control custom-url-field' name='custom-ending' />
            </div>
            <div>
                <a href='#' class='btn btn-success btn-xs check-btn' id='check-link-availability'>{{ __('polr.index.check_availability') }}</a>
                <div id='link-availability-status'></div>
            </div>
        </div>
    </div>
    <input type='submit' class='btn btn-info' id='shorten' value='{{ __('polr.index.shorten') }}' />
    <a href='#' class='btn btn-warning' id='show-link-options'>{{ __('polr.index.link_options') }}</a>
    <input type="hidden" name='_token' value='{{csrf_token()}}' />
</form>

<div id='tips' class='text-muted tips'>
    <i class='fa fa-spinner'></i> {{ __('polr.index.loading_tips') }}
</div>
@endsection

@section('js')
<script src='js/index.js'></script>
@endsection
