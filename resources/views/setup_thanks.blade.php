@extends('layouts.minimal')

@section('title')
{{ __('polr.setup.thanks.title') }}
@endsection

@section('content')
<div style="max-width: 640px; margin: 72px auto;">
    <div style="background: var(--polrx-surface); border: 1px solid var(--polrx-border); border-radius: 16px; padding: 40px 32px; text-align: center; box-shadow: var(--polrx-shadow);">
        <img src="/favicon.svg" alt="polrX Logo" style="width: 72px; height: 72px; border-radius: 16px; margin-bottom: 18px;" />
        <h2 style="margin-top: 0; font-weight: 700; color: var(--polrx-text) !important;">
            {{ __('polr.setup.thanks.title') }}
        </h2>
        <p style="color: var(--polrx-text-muted) !important; font-size: 15px; margin-bottom: 28px;">
            {{ __('polr.setup.thanks.subtitle') }}
        </p>

        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; padding: 18px; border-radius: 12px; background: var(--polrx-surface-alt); border: 1px solid var(--polrx-border); text-align: left; margin-bottom: 28px;">
            <div>
                <span style="display: block; font-size: 12px; color: var(--polrx-text-muted);">{{ __('polr.setup.thanks.admin_user') }}</span>
                <strong style="font-size: 15px; color: var(--polrx-text);">{{ $admin_username ?? 'admin' }}</strong>
            </div>
            <div>
                <span style="display: block; font-size: 12px; color: var(--polrx-text-muted);">{{ __('polr.setup.thanks.site_url') }}</span>
                <strong style="font-size: 15px; color: var(--polrx-text);">{{ $site_url ?? '/' }}</strong>
            </div>
        </div>

        <div style="display: flex; justify-content: center; gap: 12px; flex-wrap: wrap;">
            <a href="{{ route('index') }}" class="btn btn-primary btn-lg">
                <i class="fa fa-link"></i> {{ __('polr.setup.thanks.go_home') }}
            </a>
            <a href="{{ route('admin') }}" class="btn btn-default btn-lg">
                <i class="fa fa-cog"></i> {{ __('polr.setup.thanks.go_admin') }}
            </a>
        </div>
    </div>
</div>
@endsection
