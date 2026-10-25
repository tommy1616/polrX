<div class="container-fluid">
    <nav role="navigation" class="navbar navbar-default navbar-fixed-top">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">{{ __('polr.nav.toggle_menu') }}</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>

        <div class="navbar-header">
            <a class="navbar-brand" href="{{ route('index') }}">{{env('APP_NAME')}}</a>
        </div>

        <ul id="navbar" class="nav navbar-collapse collapse navbar-nav">
            <li><a href="{{ route('about') }}">{{ __('polr.nav.about') }}</a></li>
            <li><a href="{{ route('qrcode') }}">{{ __('polr.nav.qrcode') }}</a></li>

            @if (empty(session('username')))
                <li class="visible-xs"><a href="{{ route('login') }}">{{ __('polr.nav.login') }}</a></li>
                @if (env('POLR_ALLOW_ACCT_CREATION'))
                    <li class="visible-xs"><a href="{{ route('signup') }}">{{ __('polr.nav.signup') }}</a></li>
                @endif
            @else
                <li class="visible-xs"><a href="{{ route('admin') }}">{{ __('polr.nav.dashboard') }}</a></li>
                <li class="visible-xs"><a href="{{ route('admin') }}#settings">{{ __('polr.nav.settings') }}</a></li>
                <li class="visible-xs"><a href="{{ route('admin') }}#theme">{{ __('polr.admin.nav.theme') }}</a></li>
                <li class="visible-xs"><a href="{{ route('admin') }}#totp">{{ __('polr.admin.nav.totp') }}</a></li>
                <li class="visible-xs"><a href="{{ route('admin') }}#language">{{ __('polr.admin.nav.language') }}</a></li>
                <li class="visible-xs"><a href="{{ route('logout') }}">{{ __('polr.nav.logout') }}</a></li>
            @endif
        </ul>

        <ul class="nav pull-right navbar-nav hidden-xs">
            @if (isset($available_locales) && isset($current_locale_meta))
            <li class="dropdown">
                <a class="dropdown-toggle" href="#" data-toggle="dropdown" title="{{ __('polr.nav.language') }}">
                    <i class="fa fa-globe"></i> {{ $current_locale_meta['name'] }} <strong class="caret"></strong>
                </a>
                <ul class="dropdown-menu pull-right" role="menu">
                    @foreach ($available_locales as $locCode => $locMeta)
                    <li>
                        <a href="#" class="polrx-nav-locale-item {{ ($user_locale === $locCode) ? 'is-active' : '' }}" data-locale="{{ $locCode }}">
                            <span>{{ $locMeta['name'] }}</span>
                            @if ($user_locale === $locCode)
                            <i class="fa fa-check" style="margin-left: 8px; color: var(--polrx-primary);"></i>
                            @endif
                        </a>
                    </li>
                    @endforeach
                </ul>
            </li>
            @endif

            <li class="divider-vertical"></li>

            @if (empty(session('username')))
                @if (env('POLR_ALLOW_ACCT_CREATION'))
                    <li><a href="{{route('signup')}}">{{ __('polr.nav.signup') }}</a></li>
                @endif

                <li class="dropdown">
                    <a class="dropdown-toggle" href="#" data-toggle="dropdown">{{ __('polr.nav.login') }} <strong class="caret"></strong></a>
                    <div class="dropdown-menu pull-right login-dropdown-menu" id="dropdown">
                        <h2>{{ __('polr.nav.login') }}</h2>
                        <form action="login" method="POST" accept-charset="UTF-8">
                            <input type="text" name="username" placeholder="{{ __('polr.nav.username_placeholder') }}" size="30" class="form-control login-form-field" />
                            <input type="password" name="password" placeholder="{{ __('polr.nav.password_placeholder') }}" size="30" class="form-control login-form-field" />
                            <input type="hidden" name='_token' value='{{csrf_token()}}' />
                            <input class="btn btn-primary form-control login-form-submit" type="submit" name="login" value="{{ __('polr.nav.login') }}" />
                        </form>
                    </div>
                </li>
            @else
                <div class='nav pull-right navbar-nav'>
                    <li class='dropdown'>
                    <a class="dropdown-toggle login-name" href="#" data-toggle="dropdown">{{session('username')}} <strong class="caret"></strong></a>
                        <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dropdownMenu">
                            <li><a tabindex="-1" href="{{ route('admin') }}">{{ __('polr.nav.dashboard') }}</a></li>
                            <li><a tabindex="-1" href="{{ route('admin') }}#settings">{{ __('polr.nav.settings') }}</a></li>
                            <li><a tabindex="-1" href="{{ route('admin') }}#theme">{{ __('polr.admin.nav.theme') }}</a></li>
                            <li><a tabindex="-1" href="{{ route('admin') }}#totp">{{ __('polr.admin.nav.totp') }}</a></li>
                            <li><a tabindex="-1" href="{{ route('admin') }}#language">{{ __('polr.admin.nav.language') }}</a></li>
                            <li><a tabindex="-1" href="{{ route('logout') }}">{{ __('polr.nav.logout') }}</a></li>
                        </ul>
                    </li>
                </div>
            @endif
        </ul>
    </nav>
</div>
