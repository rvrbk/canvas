<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Collective Canvas') }}</title>
        <link rel="icon" type="image/svg+xml" href="/logo.svg">

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @if(env('GA_MEASUREMENT_ID'))
        <!-- Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ env('GA_MEASUREMENT_ID') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ env('GA_MEASUREMENT_ID') }}', { send_page_view: false });
        </script>
        @endif
    </head>
    <body class="antialiased">
        <div id="app"></div>
    </body>
</html>
