<nav id="topbar" class="navbar bg-white border-bottom fixed-top topbar px-3">
    <button id="toggleBtn" class="d-none d-lg-inline-flex btn btn-light btn-icon btn-sm">
        <i class="ti ti-layout-sidebar-left-expand"></i>
    </button>

    <button id="mobileBtn" class="btn btn-light btn-icon btn-sm d-lg-none me-2">
        <i class="ti ti-layout-sidebar-left-expand"></i>
    </button>

    <div>
        <ul class="list-unstyled d-flex align-items-center mb-0 gap-1">
            {{-- Notifications --}}
            <li>
                <a class="position-relative btn-icon btn-sm btn-light btn rounded-circle" data-bs-toggle="dropdown" aria-expanded="false" href="#" role="button">
                    <i class="ti ti-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger mt-2 ms-n2" id="notificationBadge">
                        0
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-md p-0">
                    <ul class="list-unstyled p-0 m-0" id="notificationList">
                        <li class="px-4 py-3 text-center text-muted small">
                            Bildirim bulunmuyor
                        </li>
                    </ul>
                </div>
            </li>

            {{-- User Dropdown --}}
            <li class="ms-3 dropdown">
                <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    @if(auth()->user()?->profile_photo)
                        <img src="{{ auth()->user()->profile_photo }}" alt="" class="avatar avatar-sm rounded-circle">
                    @else
                        <div class="avatar avatar-sm rounded-circle avatar-primary">
                            <span class="avatar-initials">{{ substr(auth()->user()?->first_name ?? 'A', 0, 1) }}</span>
                        </div>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-end p-0" style="min-width: 200px;">
                    <div>
                        <div class="d-flex gap-3 align-items-center border-dashed border-bottom px-3 py-3">
                            @if(auth()->user()?->profile_photo)
                                <img src="{{ auth()->user()->profile_photo }}" alt="" class="avatar avatar-md rounded-circle">
                            @else
                                <div class="avatar avatar-md rounded-circle avatar-primary">
                                    <span class="avatar-initials">{{ substr(auth()->user()?->first_name ?? 'A', 0, 1) }}</span>
                                </div>
                            @endif
                            <div>
                                <h4 class="mb-0 small">{{ auth()->user()?->full_name ?? 'Admin' }}</h4>
                                <p class="mb-0 small">{{ auth()->user()?->role?->name ?? '' }}</p>
                            </div>
                        </div>
                        <div class="p-3 d-flex flex-column gap-1 small lh-lg">
                            <a href="{{ route('dashboard') }}"><span>Dashboard</span></a>
                            <a href="{{ route('settings.index') }}"><span>Ayarlar</span></a>
                        </div>
                        <div class="border-top p-3">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                    <i class="ti ti-logout me-1"></i>Çıkış Yap
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</nav>
