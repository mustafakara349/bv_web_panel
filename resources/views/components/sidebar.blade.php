<aside id="sidebar" class="sidebar">
    <div class="logo-area">
        <a href="{{ route('dashboard') }}" class="d-inline-flex align-items-center">
            <span class="fw-bold text-primary fs-5">B&V</span>
            <span class="logo-text ms-2 fw-semibold">Barber</span>
        </a>
    </div>

    <ul class="nav flex-column">
        <li class="px-4 py-2"><small class="nav-text">Ana Menü</small></li>

        <li>
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="ti ti-dashboard"></i>
                <span class="nav-text">Dashboard</span>
            </a>
        </li>

        <li>
            <a class="nav-link {{ request()->routeIs('appointments.*') ? 'active' : '' }}" href="{{ route('appointments.index') }}">
                <i class="ti ti-calendar-event"></i>
                <span class="nav-text">Randevular</span>
            </a>
        </li>

        <li>
            <a class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}" href="{{ route('employees.index') }}">
                <i class="ti ti-users"></i>
                <span class="nav-text">Çalışanlar</span>
            </a>
        </li>

        <li>
            <a class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}">
                <i class="ti ti-user-circle"></i>
                <span class="nav-text">Müşteriler</span>
            </a>
        </li>

        <li>
            <a class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}" href="{{ route('services.index') }}">
                <i class="ti ti-cut"></i>
                <span class="nav-text">Hizmetler</span>
            </a>
        </li>

        <li class="px-4 pt-4 pb-2"><small class="nav-text">Finans</small></li>

        <li>
            <a class="nav-link {{ request()->routeIs('finance.transactions') ? 'active' : '' }}" href="{{ route('finance.transactions') }}">
                <i class="ti ti-report-money"></i>
                <span class="nav-text">İşlemler</span>
            </a>
        </li>

        <li>
            <a class="nav-link {{ request()->routeIs('finance.expenses') ? 'active' : '' }}" href="{{ route('finance.expenses') }}">
                <i class="ti ti-receipt"></i>
                <span class="nav-text">Giderler</span>
            </a>
        </li>

        <li class="px-4 pt-4 pb-2"><small class="nav-text">Pazarlama</small></li>

        <li>
            <a class="nav-link {{ request()->routeIs('campaigns.*') ? 'active' : '' }}" href="{{ route('campaigns.index') }}">
                <i class="ti ti-speakerphone"></i>
                <span class="nav-text">Kampanyalar</span>
            </a>
        </li>

        <li>
            <a class="nav-link {{ request()->routeIs('reviews.*') ? 'active' : '' }}" href="{{ route('reviews.index') }}">
                <i class="ti ti-star"></i>
                <span class="nav-text">Değerlendirmeler</span>
            </a>
        </li>

        <li class="px-4 pt-4 pb-2"><small class="nav-text">Sistem</small></li>

        <li>
            <a class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}" href="{{ route('notifications.index') }}">
                <i class="ti ti-bell"></i>
                <span class="nav-text">Bildirimler</span>
            </a>
        </li>

        <li>
            <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                <i class="ti ti-chart-bar"></i>
                <span class="nav-text">Raporlar</span>
            </a>
        </li>

        <li>
            <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}">
                <i class="ti ti-settings"></i>
                <span class="nav-text">Ayarlar</span>
            </a>
        </li>
    </ul>
</aside>
