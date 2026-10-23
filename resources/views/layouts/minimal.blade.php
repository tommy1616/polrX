<!DOCTYPE html>
<html lang="{{ isset($current_locale_meta) ? $current_locale_meta['html_lang'] : 'zh-TW' }}" ng-app="polr" data-theme="{{ isset($user_theme) && $user_theme !== 'default' ? $user_theme : 'midnight' }}">
    <head>
        <title>{{ env('APP_NAME', 'polrX') }} — @yield('title')</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        <link rel="shortcut icon" href="/favicon.svg">
        <link rel="stylesheet" href="/css/default-bootstrap.min.css">
        <link rel="stylesheet" href="/css/font-awesome.min.css">
        <link rel="stylesheet" href="/css/themes.css">
        @yield('css')
    </head>
    <body>
        <div class="container">
            @yield('content')
        </div>

        <script src="/js/jquery-1.11.3.min.js"></script>
        <script src="/js/bootstrap.min.js"></script>
        @yield('js')
    </body>
</html>
