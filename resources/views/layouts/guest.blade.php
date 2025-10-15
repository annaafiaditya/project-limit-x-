<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Limit-X Futami</title>
        <link rel="icon" type="image/png" href="{{ asset('assets/img/logo_2x_limit_x.png') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased" style="background: url('{{ asset('assets/img/bg_login.jpeg') }}') center center / cover no-repeat fixed; min-height: 100vh;">
        <div style="backdrop-filter: blur(8px); min-height: 100vh; width: 100vw; position: fixed; top: 0; left: 0; z-index: 0;"></div>
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-transparent" style="position: relative; z-index: 1;">
            @yield('content')
        </div>
    </body>
</html>
