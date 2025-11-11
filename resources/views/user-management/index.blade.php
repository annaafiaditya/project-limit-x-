@extends('layouts.app')

@section('content')
    <div class="container py-5">
        @if (session('success'))
            <div class="alert alert-success mb-4 mx-auto" style="max-width: 1200px; border-radius: 1rem;">
                {{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger mb-4 mx-auto" style="max-width: 1200px; border-radius: 1rem;">
                {{ session('error') }}</div>
        @endif

        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <div class="card shadow border-0"
                    style="border-radius: 1.5rem; background: linear-gradient(120deg, #f8fafc 0%, #e0f2fe 100%);">
                    <div class="card-header bg-white border-0" style="border-radius: 1.5rem 1.5rem 0 0; padding: 1.5rem;">
                        <h3 class="fw-bold text-primary mb-0 d-flex align-items-center">
                            <i class="bi bi-people-fill me-3" style="font-size: 1.5rem;"></i>
                            Management Akun
                        </h3>
                        <p class="text-muted mb-0 mt-1">Kelola semua akun pengguna sistem</p>
                        <p class="text-muted mb-0 mt-1">supervisor akan mendapat notifikasi bila ada akun baru, dan
                            mendapankan highlight untuk mempermudah</p>
                        </h3>
                    </div>

                    <div class="card-body p-4">
                        <div class="search-container">
                            <form method="GET" action="{{ route('user-management.index') }}" class="row g-3">
                                <div class="col-md-4">
                                    <label for="search_name" class="form-label fw-bold text-primary">Cari Nama</label>
                                    <input type="text" class="form-control modern-input" id="search_name"
                                        name="search_name" value="{{ request('search_name') }}"
                                        placeholder="Masukkan nama...">
                                </div>
                                <div class="col-md-4">
                                    <label for="search_email" class="form-label fw-bold text-primary">Cari Email</label>
                                    <input type="text" class="form-control modern-input" id="search_email"
                                        name="search_email" value="{{ request('search_email') }}"
                                        placeholder="Masukkan email...">
                                </div>
                                <div class="col-md-4">
                                    <label for="search_role" class="form-label fw-bold text-primary">Filter Role</label>
                                    <select class="form-select modern-select" id="search_role" name="search_role">
                                        <option value="">Semua Role</option>
                                        <option value="supervisor"
                                            {{ request('search_role') == 'supervisor' ? 'selected' : '' }}>Supervisor
                                        </option>
                                        <option value="staff" {{ request('search_role') == 'staff' ? 'selected' : '' }}>
                                            Staff</option>
                                        <option value="technician"
                                            {{ request('search_role') == 'technician' ? 'selected' : '' }}>Technician
                                        </option>
                                        <option value="guest" {{ request('search_role') == 'guest' ? 'selected' : '' }}>
                                            Guest</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary me-2"
                                        style="border-radius: 10px; padding: 0.75rem 2rem; font-weight: 600;">
                                        <i class="bi bi-search me-2"></i>Cari
                                    </button>
                                    <a href="{{ route('user-management.index') }}" class="btn btn-outline-secondary"
                                        style="border-radius: 10px; padding: 0.75rem 2rem; font-weight: 600;">
                                        <i class="bi bi-x-circle me-2"></i>Reset
                                    </a>
                                </div>
                            </form>
                        </div>

                        <div class="bg-white shadow rounded-lg overflow-x-auto animate-fade-in-up"
                            style="animation-delay:.15s; animation-duration:.8s;">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-blue-200">
                                    <tr>
                                        <th class="px-4 py-2">#</th>
                                        <th class="px-4 py-2">Nama</th>
                                        <th class="px-4 py-2">Email</th>
                                        <th class="px-4 py-2">Role</th>
                                        <th class="px-4 py-2">Tanggal Dibuat</th>
                                        <th class="px-4 py-2">Waktu Dibuat</th>
                                        <th class="px-4 py-2">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $index => $user)
                                        @php
                                            $isNewUser = $user->created_at >= now()->subDay();
                                            $notificationsViewed = session('user_notifications_viewed', false);
                                            $shouldHighlight = $isNewUser && !$notificationsViewed;
                                        @endphp
                                        <tr class="{{ $shouldHighlight ? 'new-user-highlight' : '' }} hover:bg-yellow-50 cursor-pointer"
                                            data-user-id="{{ $user->id }}">
                                            <td class="px-4 py-2 fw-bold">{{ $users->firstItem() + $index }}</td>
                                            <td class="px-4 py-2 fw-semibold">{{ $user->name }}</td>
                                            <td class="px-4 py-2">{{ $user->email }}</td>
                                            <td class="px-4 py-2">
                                                <span
                                                    class="badge modern-badge
                                            @if ($user->role == 'supervisor') bg-primary
                                            @elseif($user->role == 'staff') bg-success
                                            @elseif($user->role == 'technician') bg-info
                                            @else bg-secondary @endif">
                                                    {{ $user->jabatan }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-2">{{ $user->created_at->format('d M Y') }}</td>
                                            <td class="px-4 py-2">{{ $user->created_at->diffForHumans() }}</td>
                                            <td class="px-4 py-2 flex gap-2">
                                                @if ($user->id !== Auth::id())
                                                    <form action="{{ route('user-management.destroy', $user) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $user->name }}?')"
                                                        onclick="event.stopPropagation()">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="text-red-600 hover:underline">Hapus</button>
                                                    </form>
                                                @else
                                                    <span class="text-muted small fw-bold">Akun Anda</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if ($users->hasPages())
                            <div class="d-flex justify-content-between align-items-center mt-4 animate-fade-in-up">
                                <div></div>
                                <div class="d-flex align-items-center gap-2 p-2 bg-white rounded shadow-sm border"
                                    style="min-width:260px;">
                                    <form method="GET" action="{{ route('user-management.index') }}" id="perPageForm"
                                        class="d-flex align-items-center me-2">
                                        @foreach (request()->except('perPage', 'page') as $key => $val)
                                            <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                                        @endforeach
                                        <label for="perPage" class="me-2 mb-0 fw-semibold text-primary"
                                            style="font-size:0.95em;">Tampil:</label>
                                        <select name="perPage" id="perPage"
                                            class="form-select form-select-sm w-auto border-primary text-primary fw-bold"
                                            style="background-color:#e9f2fe; font-size:0.95em;"
                                            onchange="document.getElementById('perPageForm').submit()">
                                            <option value="10" {{ request('perPage', 10) == 10 ? 'selected' : '' }}>10
                                            </option>
                                            <option value="20" {{ request('perPage', 10) == 20 ? 'selected' : '' }}>20
                                            </option>
                                            <option value="50" {{ request('perPage', 10) == 50 ? 'selected' : '' }}>50
                                            </option>
                                            <option value="100" {{ request('perPage', 10) == 100 ? 'selected' : '' }}>
                                                100</option>
                                        </select>
                                    </form>
                                    <nav>
                                        <ul class="pagination pagination-sm mb-0">
                                            @if ($users->onFirstPage())
                                                <li class="page-item disabled"><span
                                                        class="page-link bg-light border-0">&laquo;</span></li>
                                            @else
                                                <li class="page-item"><a
                                                        class="page-link bg-warning text-primary border-0 fw-bold"
                                                        href="{{ $users->previousPageUrl() }}">&laquo;</a></li>
                                            @endif
                                            @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                                                <li
                                                    class="page-item {{ $page == $users->currentPage() ? 'active' : '' }}">
                                                    <a class="page-link {{ $page == $users->currentPage() ? 'bg-primary text-white border-primary' : 'bg-light text-primary border-0' }} fw-bold"
                                                        href="{{ $url }}">{{ $page }}</a>
                                                </li>
                                            @endforeach
                                            @if ($users->hasMorePages())
                                                <li class="page-item"><a
                                                        class="page-link bg-warning text-primary border-0 fw-bold"
                                                        href="{{ $users->nextPageUrl() }}">&raquo;</a></li>
                                            @else
                                                <li class="page-item disabled"><span
                                                        class="page-link bg-light border-0">&raquo;</span></li>
                                            @endif
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        .animate-fade-in-up {
            animation: fade-in-up 0.6s ease-out both;
        }

        @keyframes highlight {
            0% {
                background-color: #fef3c7;
            }

            100% {
                background-color: transparent;
            }
        }

        .highlight-restored {
            animation: highlight 5.5s ease-out;
        }

        .new-user-highlight {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%) !important;
            animation: highlightNewUser 4.5s ease-out;
            border-left: 4px solid #ffc107;
        }

        @keyframes highlightNewUser {
            0% {
                background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%) !important;
                box-shadow: 0 0 20px rgba(255, 193, 7, 0.6);
                transform: scale(1.02);
            }

            50% {
                background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%) !important;
                box-shadow: 0 0 25px rgba(255, 193, 7, 0.8);
                transform: scale(1.02);
            }

            100% {
                background: transparent !important;
                box-shadow: none;
                transform: scale(1);
                border-left: none;
            }
        }

        .modern-badge {
            border-radius: 20px;
            padding: 0.5rem 1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.75rem;
        }

        .search-container {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .modern-input {
            border: 2px solid rgba(102, 126, 234, 0.2);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .modern-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }

        .modern-select {
            border: 2px solid rgba(102, 126, 234, 0.2);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            background: white;
            transition: all 0.3s ease;
        }

        .modern-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }
    </style>

    <script>
        // Clear notification when page loads - mark as seen
        if (window.location.pathname === '/user-management') {
            // Store in session that notifications have been viewed
            fetch('/clear-user-notifications', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }).catch(err => console.log('Notification clear failed:', err));
        }
    </script>
@endsection
