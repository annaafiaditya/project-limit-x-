<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Limit-X Futami</title>
        <link rel="icon" type="image/png" href="{{ asset('assets/img/logo_2x_limit_x.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Bootstrap 5.3.2 CDN -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- CSRF Token Setup -->
        <script>
            window.Laravel = {
                csrfToken: '{{ csrf_token() }}'
            };
            
            // Setup CSRF token for all AJAX requests
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                
                // Handle 419 errors globally
                $(document).ajaxError(function(event, xhr, settings, thrownError) {
                    if (xhr.status === 419) {
                        alert('Session expired. Please refresh the page and try again.');
                        window.location.reload();
                    }
                });
                
                // Refresh CSRF token periodically
                setInterval(function() {
                    $.get('/refresh-csrf').done(function(data) {
                        $('meta[name="csrf-token"]').attr('content', data.csrf_token);
                        $('input[name="_token"]').val(data.csrf_token);
                    }).fail(function() {
                        // If refresh fails, reload the page
                        window.location.reload();
                    });
                }, 300000); // Refresh every 5 minutes
            });
        </script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                @yield('content')
            </main>
        </div>
        @stack('scripts')
    </body>
</html>
