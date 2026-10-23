@extends('layouts.base')

@section('css')
<link rel='stylesheet' href='/css/about.css' />
@endsection

@section('content')
<div class="polrx-page-card about-contents">
    <div class="polrx-about-header">
        <img class='logo-img' src='/favicon.svg' alt='polrX Logo' style='width:96px;height:96px;border-radius:22px;box-shadow:0 8px 24px rgba(0,0,0,0.18);' />
        <div>
            <h2 style="margin: 0 0 6px;">{{env('APP_NAME')}}</h2>
            <p class="polrx-muted" style="margin: 0;">{{ __('polr.about.subtitle') }}</p>
        </div>
    </div>

    <hr class="polrx-divider">

    @if ($role == "admin")
    <div class="well polrx-subcard" style="margin-bottom: 24px;">
        <h4 style="margin-top: 0; margin-bottom: 10px;">{{ __('polr.about.sys_info') }}</h4>
        <dl style="margin-bottom: 10px;">
            <dt>{{ __('polr.about.version') }} {{env('POLR_VERSION')}}</dt>
            <dt>{{ __('polr.about.release_date') }} {{env('POLR_RELDATE')}}</dt>
            <dt>{{ __('polr.about.install_info') }} {{env('APP_NAME')}} / {{env('APP_ADDRESS')}} / {{env('POLR_GENERATED_AT')}}</dt>
        </dl>
        <p class="polrx-muted" style="margin-bottom: 0; font-size: 13px;">{{ __('polr.about.admin_note') }} <code>resources/views/about.blade.php</code></p>
    </div>
    @endif

    <p style="line-height: 1.7; margin-bottom: 0;">{!! __('polr.about.powered_by', ['app' => e(env('APP_NAME'))]) !!}
        <br />{{ __('polr.about.license') }}
    </p>
</div>
@endsection
