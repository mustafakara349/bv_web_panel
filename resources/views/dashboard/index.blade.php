@extends('layouts.app')

@section('title', 'Dashboard - B&V Barber')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="mb-6">
                <h1 class="fs-3 mb-1">Dashboard</h1>
                <p>Merhaba {{ auth()->user()->first_name }}, işte bugünkü özet.</p>
            </div>
        </div>
    </div>

    {{-- Pending Appointments Row --}}
    @if($pendingAppointments->isNotEmpty())
        <div class="row g-3 mt-1 mb-3">
            <div class="col-12">
                <div class="card border-warning border-opacity-50 shadow-sm">
                    <div
                        class="card-header bg-warning bg-opacity-10 d-flex justify-content-between align-items-center px-4 py-3 border-bottom border-warning border-opacity-25">
                        <h4 class="mb-0 h5 text-warning-emphasis"><i class="ti ti-clock-exclamation me-2"></i>Onay Bekleyen
                            Randevular</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 border-top-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 text-uppercase text-muted fs-7 fw-semibold py-3 border-0">Tarih / Saat
                                        </th>
                                        <th class="text-uppercase text-muted fs-7 fw-semibold py-3 border-0">Müşteri</th>
                                        <th class="text-uppercase text-muted fs-7 fw-semibold py-3 border-0">Berber</th>
                                        <th class="text-uppercase text-muted fs-7 fw-semibold py-3 border-0">Hizmet</th>
                                        <th class="text-end pe-4 text-uppercase text-muted fs-7 fw-semibold py-3 border-0">İşlem
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="border-top-0">
                                    @foreach($pendingAppointments as $apt)
                                        <tr>
                                            <td class="ps-4">
                                                <div
                                                    class="d-inline-flex align-items-center justify-content-center px-3 py-2 rounded-2 bg-dark text-white fw-bold shadow-sm">
                                                    {{ $apt->start_at->format('d.m.Y') }} <i
                                                        class="ti ti-clock ms-2 me-1 opacity-75"></i>
                                                    {{ $apt->start_at->format('H:i') }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm rounded-circle bg-warning-subtle text-warning me-3 d-flex align-items-center justify-content-center fw-bold shadow-sm"
                                                        style="width: 40px; height: 40px;">
                                                        {{ mb_substr($apt->customer->first_name ?? 'M', 0, 1) }}{{ mb_substr($apt->customer->last_name ?? 'M', 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="d-block fw-bold text-dark">{{ $apt->customer->full_name ?? 'Bilinmeyen Müşteri' }}</span>
                                                        <small class="text-muted"><i
                                                                class="ti ti-phone me-1"></i>{{ $apt->customer->phone ?? 'Belirtilmemiş' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div
                                                        class="icon-shape icon-sm bg-primary-subtle text-primary rounded-circle me-2">
                                                        <i class="ti ti-cut fs-5"></i>
                                                    </div>
                                                    <span
                                                        class="fw-medium text-dark">{{ $apt->employee->user->full_name ?? '-' }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-1" style="max-width: 250px;">
                                                    @foreach($apt->appointmentServices as $aps)
                                                        <span class="badge bg-light text-secondary border px-2 py-1"><i
                                                                class="ti ti-check me-1"></i>{{ $aps->service->name }}</span>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="btn-group shadow-sm">
                                                    <form action="{{ route('appointments.update-status', $apt) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="confirmed">
                                                        <button type="submit" class="btn btn-sm btn-success" title="Onayla"
                                                            style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                                                            <i class="ti ti-check me-1"></i>Onayla
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('appointments.update-status', $apt) }}" method="POST"
                                                        class="d-inline"
                                                        onsubmit="return confirm('Bu randevuyu reddetmek istediğinize emin misiniz?');">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="rejected">
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Reddet"
                                                            style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                                            <i class="ti ti-x me-1"></i>Reddet
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Revenue Widget Cards --}}
    <div class="row g-3 mb-3">
        <div class="col-lg-3 col-12">
            <div class="card p-4 bg-primary bg-opacity-10 border border-primary border-opacity-25 rounded-2">
                <div class="d-flex gap-3">
                    <div class="icon-shape icon-md bg-primary text-white rounded-2">
                        <i class="ti ti-calendar-event fs-4"></i>
                    </div>
                    <div>
                        <h2 class="mb-3 fs-6">Bugünkü Randevular</h2>
                        <h3 class="fw-bold mb-0">{{ $widgets['appointments']['today_total'] ?? 0 }}</h3>
                        <p class="text-primary mb-0 small">{{ $widgets['appointments']['today_completed'] ?? 0 }} tamamlandı
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-12">
            <div class="card p-4 bg-success bg-opacity-10 border border-success border-opacity-25 rounded-2">
                <div class="d-flex gap-3">
                    <div class="icon-shape icon-md bg-success text-white rounded-2">
                        <i class="ti ti-currency-lira fs-4"></i>
                    </div>
                    <div>
                        <h2 class="mb-3 fs-6">Günlük Gelir</h2>
                        <h3 class="fw-bold mb-0">₺{{ number_format($widgets['revenue']['daily'] ?? 0, 0, ',', '.') }}</h3>
                        <p class="text-success mb-0 small">Bugün</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-12">
            <div class="card p-4 bg-info bg-opacity-10 border border-info border-opacity-25 rounded-2">
                <div class="d-flex gap-3">
                    <div class="icon-shape icon-md bg-info text-white rounded-2">
                        <i class="ti ti-report-money fs-4"></i>
                    </div>
                    <div>
                        <h2 class="mb-3 fs-6">Aylık Gelir</h2>
                        <h3 class="fw-bold mb-0">₺{{ number_format($widgets['revenue']['monthly'] ?? 0, 0, ',', '.') }}</h3>
                        <p class="text-info mb-0 small">Bu ay</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-12">
            <div class="card p-4 bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-2">
                <div class="d-flex gap-3">
                    <div class="icon-shape icon-md bg-warning text-white rounded-2">
                        <i class="ti ti-users fs-4"></i>
                    </div>
                    <div>
                        <h2 class="mb-3 fs-6">Yeni Müşteriler</h2>
                        <h3 class="fw-bold mb-0">{{ $widgets['customers']['new_this_month'] ?? 0 }}</h3>
                        <p class="text-warning mb-0 small">Bu ay</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Financial Summary Cards --}}
    <div class="row g-3 mb-3">
        <div class="col-lg-4 col-12">
            <div class="card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between border-bottom pb-5 mb-3">
                        <div>
                            <h3 class="fw-bold h4">₺{{ number_format($widgets['financial']['profit'] ?? 0, 0, ',', '.') }}
                            </h3>
                            <span>Aylık Kâr</span>
                        </div>
                        <div><i class="ti ti-layers-subtract fs-1 text-primary"></i></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center small">
                        <div class="text-muted">Gelir - Gider</div>
                        <div><a href="{{ route('finance.transactions') }}"
                                class="link-primary text-decoration-underline">Detay</a></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-12">
            <div class="card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between border-bottom pb-5 mb-3">
                        <div>
                            <h3 class="fw-bold h4">₺{{ number_format($widgets['financial']['income'] ?? 0, 0, ',', '.') }}
                            </h3>
                            <span>Aylık Gelir</span>
                        </div>
                        <div><i class="ti ti-trending-up fs-1 text-success"></i></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center small">
                        <div class="text-muted"><span class="text-success">Toplam gelir</span> bu ay</div>
                        <div><a href="{{ route('finance.transactions') }}"
                                class="link-primary text-decoration-underline">Detay</a></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-12">
            <div class="card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between border-bottom pb-5 mb-3">
                        <div>
                            <h3 class="fw-bold h4">₺{{ number_format($widgets['financial']['expense'] ?? 0, 0, ',', '.') }}
                            </h3>
                            <span>Aylık Gider</span>
                        </div>
                        <div><i class="ti ti-trending-down fs-1 text-danger"></i></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center small">
                        <div class="text-muted"><span class="text-danger">Toplam gider</span> bu ay</div>
                        <div><a href="{{ route('finance.expenses') }}"
                                class="link-primary text-decoration-underline">Detay</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="row g-3 mb-3">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center bg-transparent px-4 py-3">
                    <h3 class="h5 mb-0">Gelir Grafiği</h3>
                    <div>
                        <select class="form-select form-select-sm" id="revenueChartPeriod">
                            <option value="year" selected>Bu Yıl</option>
                            <option value="month">Bu Ay</option>
                            <option value="day">Bugün</option>
                        </select>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div id="revenueChart"></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center bg-transparent px-4 py-3">
                    <h3 class="h5 mb-0">Müşteri Dağılımı</h3>
                </div>
                <div class="card-body p-4">
                    <div class="customer-distribution-container">
                        <!-- Beautiful Modern Horizontal Stacked Bar -->
                        <div class="gender-bar-wrapper position-relative overflow-hidden rounded-pill bg-light d-flex" style="height: 38px; box-shadow: inset 0 2px 4px rgba(0,0,0,0.06);">
                            @php
                                $maleCount = $widgets['customers']['genders']['male'] ?? 0;
                                $femaleCount = $widgets['customers']['genders']['female'] ?? 0;
                                $otherCount = $widgets['customers']['genders']['other'] ?? 0;
                                $totalGenders = $maleCount + $femaleCount + $otherCount;
                                
                                $malePct = $totalGenders > 0 ? ($maleCount / $totalGenders) * 100 : 50;
                                $femalePct = $totalGenders > 0 ? ($femaleCount / $totalGenders) * 100 : 50;
                                $otherPct = $totalGenders > 0 ? ($otherCount / $totalGenders) * 100 : 0;
                            @endphp
                            
                            @if($maleCount > 0 || $totalGenders == 0)
                                <div class="gender-segment male-segment d-flex align-items-center justify-content-center text-white fw-bold transition-all" 
                                     style="width: {{ $malePct }}%; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); transition: width 0.6s ease; text-shadow: 0 1px 2px rgba(0,0,0,0.2);">
                                    <span class="d-flex align-items-center gap-1">
                                        <i class="ti ti-man fs-5"></i>
                                        {{ $maleCount }}
                                    </span>
                                </div>
                            @endif
                            
                            @if($otherCount > 0)
                                <div class="gender-segment other-segment d-flex align-items-center justify-content-center text-white fw-bold transition-all" 
                                     style="width: {{ $otherPct }}%; background: linear-gradient(135deg, #10b981 0%, #047857 100%); transition: width 0.6s ease; text-shadow: 0 1px 2px rgba(0,0,0,0.2);">
                                    <span class="d-flex align-items-center gap-1">
                                        {{ $otherCount }}
                                    </span>
                                </div>
                            @endif

                            @if($femaleCount > 0 || $totalGenders == 0)
                                <div class="gender-segment female-segment d-flex align-items-center justify-content-center text-white fw-bold transition-all" 
                                     style="width: {{ $femalePct }}%; background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); transition: width 0.6s ease; text-shadow: 0 1px 2px rgba(0,0,0,0.2);">
                                    <span class="d-flex align-items-center gap-1">
                                        <i class="ti ti-woman fs-5"></i>
                                        {{ $femaleCount }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Modern Info Cards Below the Bar -->
                        <div class="row g-2 mt-4">
                            <div class="col-4">
                                <div class="p-3 rounded-3 bg-light border border-white text-center h-100" style="box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                                    <span class="text-secondary small d-block mb-1">Toplam</span>
                                    <span class="fs-5 fw-bold text-dark">{{ $widgets['customers']['total'] ?? 0 }}</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 rounded-3 bg-light border border-white text-center h-100" style="box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                                    <span class="text-primary small d-block mb-1">Erkek</span>
                                    <span class="fs-6 fw-bold text-primary">{{ $maleCount }} <span class="small fw-normal text-muted" style="font-size: 11px;">({{ round($malePct) }}%)</span></span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 rounded-3 bg-light border border-white text-center h-100" style="box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                                    <span class="text-warning small d-block mb-1" style="color: #ea580c !important;">Kadın</span>
                                    <span class="fs-6 fw-bold" style="color: #ea580c;">{{ $femaleCount }} <span class="small fw-normal text-muted" style="font-size: 11px;">({{ round($femalePct) }}%)</span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom Row --}}
    <div class="row g-3">
        {{-- Top Barbers --}}
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center px-4 py-3">
                    <h4 class="mb-0 h5">En İyi Berberler</h4>
                    <a href="{{ route('employees.index') }}" class="small text-primary text-decoration-underline">Tümü</a>
                </div>
                <ul class="list-group list-group-flush">
                    @forelse($barbers as $barber)
                        <li class="list-group-item d-flex align-items-center gap-3">
                            @if($barber['photo'])
                                <img src="{{ $barber['photo'] }}" class="avatar avatar-sm rounded-circle" alt="">
                            @else
                                <div class="avatar avatar-sm rounded-circle avatar-primary">
                                    <span class="avatar-initials">{{ substr($barber['name'], 0, 1) }}</span>
                                </div>
                            @endif
                            <div class="flex-grow-1">
                                <p class="mb-1">{{ $barber['name'] }}</p>
                                <div class="d-flex align-items-center gap-2 text-muted">
                                    <small class="fw-semibold">₺{{ number_format($barber['revenue'], 0, ',', '.') }}</small>
                                    <small>•</small>
                                    <small>{{ $barber['completed_appointments'] }} randevu</small>
                                </div>
                            </div>
                            <span class="badge bg-warning-subtle text-warning border border-warning">
                                <i class="ti ti-star-filled me-1"></i>{{ $barber['rating'] }}
                            </span>
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted py-4">Henüz veri yok</li>
                    @endforelse
                </ul>
            </div>
        </div>

        {{-- Top Services --}}
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center px-4 py-3">
                    <h4 class="mb-0 h5">Popüler Hizmetler</h4>
                    <a href="{{ route('services.index') }}" class="small text-primary text-decoration-underline">Tümü</a>
                </div>
                <ul class="list-group list-group-flush">
                    @forelse($topServices as $service)
                        <li class="list-group-item d-flex align-items-center gap-3">
                            <div class="icon-shape icon-sm bg-primary bg-opacity-10 text-primary rounded-2">
                                <i class="ti ti-cut"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-1">{{ $service['name'] }}</p>
                                <div class="d-flex align-items-center gap-2 text-muted">
                                    <small
                                        class="fw-semibold">₺{{ number_format($service['total_revenue'], 0, ',', '.') }}</small>
                                    <small>•</small>
                                    <small>{{ $service['usage_count'] }} kullanım</small>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted py-4">Henüz veri yok</li>
                    @endforelse
                </ul>
            </div>
        </div>

        {{-- Appointment Stats --}}
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center px-4 py-3">
                    <h4 class="mb-0 h5">Randevu İstatistikleri</h4>
                    <select class="form-select form-select-sm w-auto" id="appointmentStatsPeriod">
                        <option value="today">Bugün</option>
                        <option value="month" selected>Bu Ay</option>
                        <option value="year">Bu Yıl</option>
                        <option value="custom">Özel Tarih</option>
                    </select>
                </div>
                <div id="customDateInputs" class="d-none border-bottom p-3 bg-light">
                    <div class="row g-2 align-items-end">
                        <div class="col-5">
                            <label class="form-label small text-secondary mb-1 fw-semibold">Başlangıç</label>
                            <input type="date" id="statsStartDate" class="form-control form-control-sm rounded-2">
                        </div>
                        <div class="col-5">
                            <label class="form-label small text-secondary mb-1 fw-semibold">Bitiş</label>
                            <input type="date" id="statsEndDate" class="form-control form-control-sm rounded-2">
                        </div>
                        <div class="col-2">
                            <button type="button" id="btnApplyStatsFilter" class="btn btn-sm btn-primary w-100 rounded-2" style="padding: 0.35rem 0;" title="Filtrele">
                                <i class="ti ti-filter"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Toplam Randevu</span>
                        <span class="fw-bold" id="stat_total">{{ $widgets['appointments']['month_total'] ?? 0 }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>İptal Edilen</span>
                        <span class="fw-bold text-danger" id="stat_cancelled">{{ $widgets['appointments']['month_cancelled'] ?? 0 }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Gelmeyen</span>
                        <span class="fw-bold text-warning" id="stat_no_show">{{ $widgets['appointments']['month_no_show'] ?? 0 }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>İptal Oranı</span>
                        <span id="stat_cancellation_rate"
                            class="badge bg-{{ ($widgets['appointments']['cancellation_rate'] ?? 0) > 10 ? 'danger' : 'success' }}-subtle text-{{ ($widgets['appointments']['cancellation_rate'] ?? 0) > 10 ? 'danger' : 'success' }}">
                            %{{ $widgets['appointments']['cancellation_rate'] ?? 0 }}
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Ort. Müşteri Harcaması</span>
                        <span class="fw-bold" id="stat_avg_spending">₺{{ number_format($widgets['customers']['avg_spending'] ?? 0, 0, ',', '.') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Today's Appointments Row --}}
    <div class="row g-3 mt-1">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center px-4 py-3 border-bottom">
                    <h4 class="mb-0 h5"><i class="ti ti-calendar-clock text-primary me-2"></i>Günün Randevuları</h4>
                    <a href="{{ route('appointments.index') }}" class="small text-primary text-decoration-underline">Tümünü
                        Gör</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 border-top-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 text-uppercase text-muted fs-7 fw-semibold py-3 border-0">Saat</th>
                                    <th class="text-uppercase text-muted fs-7 fw-semibold py-3 border-0">Müşteri</th>
                                    <th class="text-uppercase text-muted fs-7 fw-semibold py-3 border-0">Berber</th>
                                    <th class="text-uppercase text-muted fs-7 fw-semibold py-3 border-0">Hizmet</th>
                                    <th class="text-uppercase text-muted fs-7 fw-semibold py-3 border-0">Durum</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                @php
                                    $barberNextCount = [];
                                @endphp
                                @forelse($todayAppointments as $apt)
                                    @php
                                        $isPast = $apt->start_at < now();
                                        $isNext = false;
                                        if (!$isPast && !isset($barberNextCount[$apt->employee_id])) {
                                            $isNext = true;
                                            $barberNextCount[$apt->employee_id] = true;
                                        }
                                        $bgClass = $isNext ? 'bg-primary bg-opacity-10' : ($isPast ? 'bg-light opacity-75' : '');

                                        $remainingTime = '';
                                        if (!$isPast && !$isNext) {
                                            $diff = now()->diff($apt->start_at);
                                            if ($diff->h > 0) {
                                                $remainingTime = $diff->h . ' sa ' . $diff->i . ' dk';
                                            } else {
                                                $remainingTime = $diff->i . ' dk';
                                            }
                                        }
                                    @endphp
                                    <tr class="{{ $bgClass }}" style="transition: all 0.2s ease;">
                                        <td class="ps-4">
                                            <div
                                                class="d-inline-flex align-items-center justify-content-center px-3 py-2 rounded-2 {{ $isPast ? 'bg-secondary text-white' : 'bg-dark text-white' }} fw-bold shadow-sm">
                                                <i class="ti ti-clock me-1 opacity-75"></i>
                                                {{ $apt->start_at->format('H:i') }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm rounded-circle {{ $isPast ? 'bg-secondary-subtle text-secondary' : 'bg-primary-subtle text-primary' }} me-3 d-flex align-items-center justify-content-center fw-bold shadow-sm"
                                                    style="width: 40px; height: 40px;">
                                                    {{ mb_substr($apt->customer->first_name ?? 'M', 0, 1) }}{{ mb_substr($apt->customer->last_name ?? 'M', 0, 1) }}
                                                </div>
                                                <div>
                                                    <span
                                                        class="d-block fw-bold text-dark">{{ $apt->customer->full_name ?? 'Bilinmeyen Müşteri' }}</span>
                                                    <small class="text-muted"><i
                                                            class="ti ti-phone me-1"></i>{{ $apt->customer->phone ?? 'Belirtilmemiş' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div
                                                    class="icon-shape icon-sm bg-warning-subtle text-warning rounded-circle me-2">
                                                    <i class="ti ti-cut fs-5"></i>
                                                </div>
                                                <span
                                                    class="fw-medium text-dark">{{ $apt->employee->user->full_name ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1" style="max-width: 250px;">
                                                @foreach($apt->appointmentServices as $aps)
                                                    <span class="badge bg-light text-secondary border px-2 py-1"><i
                                                            class="ti ti-check me-1"></i>{{ $aps->service->name }}</span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>
                                            @if($apt->status->value == 'completed')
                                                <span
                                                    class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill"><i
                                                        class="ti ti-circle-check me-1"></i>Tamamlandı</span>
                                            @elseif($apt->status->value == 'cancelled')
                                                <span
                                                    class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill"><i
                                                        class="ti ti-x me-1"></i>İptal</span>
                                            @else
                                                @if($isNext)
                                                    <span class="badge bg-primary px-3 py-2 rounded-pill shadow-sm"><i
                                                            class="ti ti-player-play-filled me-1"></i>Sıradaki</span>
                                                @elseif($isPast)
                                                    <span
                                                        class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2 rounded-pill"><i
                                                            class="ti ti-clock-check me-1"></i>Süresi Geçti</span>
                                                @else
                                                    <span
                                                        class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2 rounded-pill"><i
                                                            class="ti ti-hourglass-empty me-1"></i>{{ $remainingTime }} kaldı</span>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Bugün için randevu bulunmuyor.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const chartDataAll = @json($revenueChart);

            // Render Revenue Chart
            try {
                if (document.getElementById('revenueChart') && typeof ApexCharts !== 'undefined') {
                    let currentPeriod = 'year';

                    const options = {
                        chart: { 
                            type: 'area', 
                            height: 350, 
                            toolbar: { show: false },
                            fontFamily: 'Outfit, system-ui, -apple-system, sans-serif',
                            dropShadow: {
                                enabled: true,
                                color: '#E66239',
                                top: 8,
                                left: 0,
                                blur: 10,
                                opacity: 0.12
                            }
                        },
                        colors: ['#E66239'],
                        stroke: { width: 3.5, curve: 'smooth', lineCap: 'round' },
                        markers: { 
                            size: 0, 
                            strokeColors: '#fff',
                            strokeWidth: 2,
                            hover: { size: 6 } 
                        },
                        grid: {
                            borderColor: '#f1f5f9',
                            strokeDashArray: 4,
                            xaxis: { lines: { show: false } },
                            yaxis: { lines: { show: true } },
                            padding: { top: 0, right: 10, bottom: 0, left: 10 }
                        },
                        series: [{ name: 'Gelir', data: chartDataAll[currentPeriod].map(d => d.revenue) }],
                        fill: {
                            type: 'gradient',
                            gradient: { 
                                shadeIntensity: 1, 
                                opacityFrom: 0.35, 
                                opacityTo: 0.02, 
                                stops: [0, 90, 100] 
                            }
                        },
                        xaxis: { 
                            categories: chartDataAll[currentPeriod].map(d => d.label),
                            axisBorder: { show: false },
                            axisTicks: { show: false },
                            labels: {
                                style: {
                                    colors: '#64748b',
                                    fontSize: '12px',
                                    fontWeight: 500
                                }
                            }
                        },
                        yaxis: {
                            tickAmount: 5,
                            labels: { 
                                style: {
                                    colors: '#64748b',
                                    fontSize: '12px',
                                    fontWeight: 500
                                },
                                formatter: (val) => '₺' + val.toLocaleString('tr-TR', { maximumFractionDigits: 0 }) 
                            }
                        },
                        tooltip: {
                            theme: 'dark',
                            y: { 
                                formatter: (val) => '₺' + val.toLocaleString('tr-TR', { maximumFractionDigits: 0 }) 
                            }
                        },
                    };
                    const chart = new ApexCharts(document.querySelector('#revenueChart'), options);
                    chart.render();

                    document.getElementById('revenueChartPeriod').addEventListener('change', function (e) {
                        currentPeriod = e.target.value;
                        chart.updateOptions({
                            xaxis: { categories: chartDataAll[currentPeriod].map(d => d.label) }
                        });
                        chart.updateSeries([{
                            name: 'Gelir',
                            data: chartDataAll[currentPeriod].map(d => d.revenue)
                        }]);
                    });
                }
            } catch (e) {
                console.error("Revenue Chart render error:", e);
            }


            // Appointment Stats Filter and AJAX Fetch
            const appointmentPeriodSelect = document.getElementById('appointmentStatsPeriod');
            const customDateInputs = document.getElementById('customDateInputs');
            const startDateInput = document.getElementById('statsStartDate');
            const endDateInput = document.getElementById('statsEndDate');
            const btnApplyStatsFilter = document.getElementById('btnApplyStatsFilter');

            function fetchAppointmentStats(period, startDate = '', endDate = '') {
                fetch(`{{ route('dashboard.appointment-stats') }}?period=${period}&start_date=${startDate}&end_date=${endDate}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('stat_total').textContent = data.total;
                        document.getElementById('stat_cancelled').textContent = data.cancelled;
                        document.getElementById('stat_no_show').textContent = data.no_show;
                        
                        const rateEl = document.getElementById('stat_cancellation_rate');
                        rateEl.textContent = '%' + data.cancellation_rate;
                        rateEl.className = `badge bg-${data.cancellation_rate > 10 ? 'danger' : 'success'}-subtle text-${data.cancellation_rate > 10 ? 'danger' : 'success'}`;
                        
                        document.getElementById('stat_avg_spending').textContent = '₺' + Number(data.avg_spending).toLocaleString('tr-TR', { maximumFractionDigits: 0 });
                    })
                    .catch(err => {
                        console.error("Error fetching stats:", err);
                    });
            }

            if (appointmentPeriodSelect) {
                appointmentPeriodSelect.addEventListener('change', function(e) {
                    const period = e.target.value;
                    if (period === 'custom') {
                        customDateInputs.classList.remove('d-none');
                    } else {
                        customDateInputs.classList.add('d-none');
                        fetchAppointmentStats(period);
                    }
                });

                if (btnApplyStatsFilter) {
                    btnApplyStatsFilter.addEventListener('click', function() {
                        if (startDateInput.value && endDateInput.value) {
                            fetchAppointmentStats('custom', startDateInput.value, endDateInput.value);
                        } else {
                            alert('Lütfen filtreleme için hem başlangıç hem de bitiş tarihini seçiniz.');
                        }
                    });
                }
            }
        });
    </script>
@endpush