<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Limit-X Futami</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/img/logo_2x_limit_x.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('{{ asset('assets/img/bg_home.png') }}') center center / cover no-repeat fixed;
            min-height: 100vh;
        }
        .bg-blur {
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            min-height: 100vh;
            width: 100vw;
            position: fixed;
            top: 0; left: 0; z-index: 0;
        }
        .home-card {
            background: rgba(24,24,40,0.72);
            border-radius: 1.5rem;
            box-shadow: 0 2px 16px #0004;
            padding: 2.5rem 2rem;
            max-width: 400px;
            width: 100%;
            margin: 0 auto;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            z-index: 1;
        }
        .logo-row {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .logo-row img {
            max-height: 56px;
            background: rgba(255,255,255,0.12);
            border-radius: 1rem;
            padding: 0.3rem 1.2rem;
        }
        .home-title {
            color: #fff;
            font-weight: 700;
            font-size: 2.1rem;
            margin-bottom: 1.2rem;
        }
        .home-desc {
            color: #e0e6f6;
            font-size: 1.08rem;
            margin-bottom: 2rem;
        }
        .btn-home {
            background: #ff2d2d;
            color: #fff;
            border: none;
            border-radius: 2rem;
            font-weight: 600;
            font-size: 1.13rem;
            letter-spacing: 1px;
            box-shadow: 0 2px 8px #0002;
            padding: 0.9rem 0;
            width: 48%;
            min-width: 120px;
            transition: all .2s;
        }
        .btn-home:hover, .btn-home:focus {
            background: #d60000;
            color: #fff;
            box-shadow: 0 4px 16px #0003;
        }
        @media (max-width: 600px) {
            .home-card { padding: 1.5rem 0.5rem; }
            .logo-row img { max-height: 38px; padding: 0.2rem 0.7rem; }
            .btn-home { font-size: 1rem; padding: 0.7rem 0; }
        }
    </style>
</head>
<body>
    <div class="bg-blur"></div>
    <div class="container d-flex flex-column justify-content-center align-items-center min-vh-100" style="position:relative; z-index:1;">
        <div class="home-card text-center">
            <div class="logo-row">
                <img src="{{ asset('assets/img/logo_futami.png') }}" alt="Futami Logo">
                <img src="{{ asset('assets/img/logo_limit_x.png') }}" alt="Limit X Logo">
            </div>
            <div class="home-title">Limit-X Futami</div>
            <div class="home-desc">Sistem pencatatan, monitoring, dan pelaporan data mikrobiologi laboratorium dengan fitur modern, mudah digunakan, dan aman.</div>
            <div class="d-flex gap-3 justify-content-center">
                <a href="{{ route('login') }}" class="btn btn-home">Login</a>
                <a href="{{ route('register') }}" class="btn btn-home">Register</a>
            </div>
        </div>
    </div>
</body>
</html> 