<nav x-data="{ open: false }" id="main-navbar"
    style="background: rgba(24,24,40,0.96); border-radius: 0.7rem; box-shadow: 0 8px 32px #0005; padding: 0.6rem 2.2rem; margin: 1.2rem auto 2.2rem auto; max-width: 1100px; position: sticky; top: 18px; z-index: 50; transition: box-shadow .2s, background .2s;">
    <style>
        .modern-dropdown {
            background: #232334;
            border-radius: 1.1rem;
            box-shadow: 0 8px 32px #0005;
            padding: 0.7rem 0.5rem;
            min-width: 180px;
            animation: fadeInDrop .25s cubic-bezier(.39, .575, .565, 1) both;
        }

        @keyframes fadeInDrop {
            0% {
                opacity: 0;
                transform: translateY(-10px);
            }

            100% {
                opacity: 1;
                transform: none;
            }
        }

        .modern-dropdown a,
        .modern-dropdown button,
        .modern-dropdown form {
            color: #fff !important;
            font-weight: 500;
            border-radius: 0.7rem;
            padding: 0.5rem 1rem;
            margin-bottom: 0.2rem;
            display: block;
            transition: background .18s;
        }

        .modern-dropdown a:hover,
        .modern-dropdown button:hover {
            background: #2d2d44 !important;
            color: #ff2d2d !important;
        }

        .modern-logout {
            background: #ff2d2d !important;
            color: #fff !important;
            font-weight: 600;
            border-radius: 0.7rem;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            justify-content: center;
            transition: background .18s;
        }

        .modern-logout:hover {
            background: #d60000 !important;
            color: #fff !important;
        }

        @keyframes navDropDown {
            from {
                opacity: 0;
                transform: translateY(-60px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        .nav-animate-drop {
            animation: navDropDown 0.7s cubic-bezier(.39, .575, .565, 1) both;
        }

        nav {
            transition: transform 0.7s cubic-bezier(.22, 1, .36, 1) !important;
            transition-property: transform !important;
            transition-duration: 0.7s !important;
            transition-timing-function: cubic-bezier(.22, 1, .36, 1) !important;
            will-change: transform;
        }

        .nav-hide {
            transform: translateY(-120%) !important;
        }
    </style>

    <div class="flex justify-between h-14 items-center">
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
                <img src="{{ asset('assets/img/logo_futami.png') }}" alt="Futami Logo"
                    style="height:32px; background:rgba(255,255,255,0.10); border-radius:0.7rem; padding:0.2rem 0.7rem;">
                <img src="{{ asset('assets/img/logo_limit_x.png') }}" alt="Limit X Logo"
                    style="height:32px; background:rgba(255,255,255,0.10); border-radius:0.7rem; padding:0.2rem 0.7rem;">
            </div>

            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex items-center">
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" style="color: #fff; font-weight: 600;">
                    {{ __('Dashboard') }}
                </x-nav-link>

                <div x-data="{ open: false }" class="relative h-14 flex items-center">
                    <button @click="open = !open" type="button"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-green-900 bg-yellow-300 hover:bg-green-400 focus:outline-none transition ease-in-out duration-150">
                        Mikrobiologi
                        <svg class="ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false" class="absolute left-0 z-10 modern-dropdown mt-2"
                        x-transition>
                        <a href="{{ route('mikrobiologi-forms.index') }}">Data Form Mikrobiologi</a>

                        @if (!Auth::user()->isGuest())
                            <a href="{{ route('mikrobiologi-forms.create') }}">Buat Form Baru</a>
                        @endif
                    </div>
                </div>

                <div x-data="{ open: false }" class="relative h-14 flex items-center">
                    <button @click="open = !open" type="button"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-500 hover:bg-purple-600 focus:outline-none transition ease-in-out duration-150">
                        Kimia
                        <svg class="ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false" class="absolute left-0 z-10 modern-dropdown mt-2"
                        x-transition>
                        <a href="{{ route('kimia.index') }}">Data Form Kimia</a>

                        @if (!Auth::user()->isGuest())
                            <a href="{{ route('kimia.create') }}">Buat Form Baru</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="hidden sm:flex sm:items-center sm:ms-6">
            @php
                $newUsersCount = 0;
                if (Auth::user()->hasRole('supervisor')) {
                    $newUsersCount = \App\Models\User::where('created_at', '>=', now()->subDay())->count();
                    $notificationsViewed = session('user_notifications_viewed', false);
                    if ($notificationsViewed) {
                        $newUsersCount = 0;
                    }
                }
            @endphp



            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150 relative">
                        <div>{{ Auth::user()->name }}</div>
                        <div class="ms-1">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        @if ($newUsersCount > 0)
                            <span
                                class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold animate-pulse">
                                {{ $newUsersCount }}
                            </span>
                        @endif
                    </button>
                </x-slot>

                <x-slot name="content">
                    <div class="modern-dropdown">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        @if (Auth::user()->hasRole('supervisor'))
                            <x-dropdown-link :href="route('user-management.index')">
                                <i class="bi bi-people-fill"></i> Management Akun
                                @if ($newUsersCount > 0)
                                    <span class="badge bg-danger ms-2">{{ $newUsersCount }}</span>
                                @endif
                            </x-dropdown-link>
                        @endif

                        <x-dropdown-link :href="route('trash.index')">
                            <i class="bi bi-trash"></i> Sampah
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                            @csrf
                            <button type="submit" class="modern-logout"><i class="bi bi-box-arrow-right"></i>
                                Logout</button>
                        </form>
                    </div>
                </x-slot>
            </x-dropdown>
        </div>

        <div class="-me-2 flex items-center sm:hidden">
            <button @click="open = ! open"
                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round"
                        stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                        stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>


    <script>
        (function() {
            let lastScroll = window.scrollY;
            let nav = document.getElementById('main-navbar');
            let navVisible = true;

            window.addEventListener('scroll', function() {
                const current = window.scrollY;
                if (current <= 0) {
                    nav.classList.remove('nav-hide');
                    navVisible = true;
                } else if (current > lastScroll && navVisible) {
                    nav.classList.add('nav-hide');
                    navVisible = false;
                } else if (current < lastScroll && !navVisible) {
                    nav.classList.remove('nav-hide');
                    navVisible = true;
                }
                lastScroll = current;
            });
        })();
    </script>
</nav>
