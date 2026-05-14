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
                    <p class="text-primary mb-0 small">{{ $widgets['appointments']['today_completed'] ?? 0 }} tamamlandı</p>
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
                        <h3 class="fw-bold h4">₺{{ number_format($widgets['financial']['profit'] ?? 0, 0, ',', '.') }}</h3>
                        <span>Aylık Kâr</span>
                    </div>
                    <div><i class="ti ti-layers-subtract fs-1 text-primary"></i></div>
                </div>
                <div class="d-flex justify-content-between align-items-center small">
                    <div class="text-muted">Gelir - Gider</div>
                    <div><a href="{{ route('finance.transactions') }}" class="link-primary text-decoration-underline">Detay</a></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-12">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between border-bottom pb-5 mb-3">
                    <div>
                        <h3 class="fw-bold h4">₺{{ number_format($widgets['financial']['income'] ?? 0, 0, ',', '.') }}</h3>
                        <span>Aylık Gelir</span>
                    </div>
                    <div><i class="ti ti-trending-up fs-1 text-success"></i></div>
                </div>
                <div class="d-flex justify-content-between align-items-center small">
                    <div class="text-muted"><span class="text-success">Toplam gelir</span> bu ay</div>
                    <div><a href="{{ route('finance.transactions') }}" class="link-primary text-decoration-underline">Detay</a></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-12">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between border-bottom pb-5 mb-3">
                    <div>
                        <h3 class="fw-bold h4">₺{{ number_format($widgets['financial']['expense'] ?? 0, 0, ',', '.') }}</h3>
                        <span>Aylık Gider</span>
                    </div>
                    <div><i class="ti ti-trending-down fs-1 text-danger"></i></div>
                </div>
                <div class="d-flex justify-content-between align-items-center small">
                    <div class="text-muted"><span class="text-danger">Toplam gider</span> bu ay</div>
                    <div><a href="{{ route('finance.expenses') }}" class="link-primary text-decoration-underline">Detay</a></div>
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
                <div id="customerChart"></div>
                <div class="row text-center border-top mt-4 pt-4">
                    <div class="col-4 border-end">
                        <h3 class="fw-bold mb-2">{{ $widgets['customers']['total'] ?? 0 }}</h3>
                        <small class="text-secondary">Toplam</small>
                    </div>
                    <div class="col-4 border-end">
                        <h3 class="fw-bold mb-2">{{ $widgets['customers']['new_this_month'] ?? 0 }}</h3>
                        <small class="text-secondary">Yeni</small>
                    </div>
                    <div class="col-4">
                        <h3 class="fw-bold mb-2">{{ $widgets['customers']['loyal'] ?? 0 }}</h3>
                        <small class="text-secondary">Sadık</small>
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
                            <small class="fw-semibold">₺{{ number_format($service['total_revenue'], 0, ',', '.') }}</small>
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
                <span class="badge bg-primary-subtle text-primary">Bu Ay</span>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>Toplam Randevu</span>
                    <span class="fw-bold">{{ $widgets['appointments']['month_total'] ?? 0 }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>İptal Edilen</span>
                    <span class="fw-bold text-danger">{{ $widgets['appointments']['month_cancelled'] ?? 0 }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>Gelmeyen</span>
                    <span class="fw-bold text-warning">{{ $widgets['appointments']['month_no_show'] ?? 0 }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>İptal Oranı</span>
                    <span class="badge bg-{{ ($widgets['appointments']['cancellation_rate'] ?? 0) > 10 ? 'danger' : 'success' }}-subtle text-{{ ($widgets['appointments']['cancellation_rate'] ?? 0) > 10 ? 'danger' : 'success' }}">
                        %{{ $widgets['appointments']['cancellation_rate'] ?? 0 }}
                    </span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>Ort. Müşteri Harcaması</span>
                    <span class="fw-bold">₺{{ number_format($widgets['customers']['avg_spending'] ?? 0, 0, ',', '.') }}</span>
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
                <a href="{{ route('appointments.index') }}" class="small text-primary text-decoration-underline">Tümünü Gör</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Saat</th>
                                <th>Müşteri</th>
                                <th>Berber</th>
                                <th>Hizmetler</th>
                                <th>Durum</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($todayAppointments as $apt)
                            <tr class="{{ $apt->start_at < now() && $apt->status->value != 'completed' ? 'bg-light text-muted' : '' }}">
                                <td class="ps-4 fw-bold {{ $apt->start_at >= now() && $apt->start_at <= now()->addHours(2) ? 'text-primary' : '' }}">
                                    {{ $apt->start_at->format('H:i') }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm rounded-circle bg-primary-subtle text-primary me-2 d-flex align-items-center justify-content-center fw-bold">
                                            {{ mb_substr($apt->customer->first_name ?? 'M', 0, 1) }}{{ mb_substr($apt->customer->last_name ?? 'M', 0, 1) }}
                                        </div>
                                        <span class="fw-medium">{{ $apt->customer->full_name ?? 'Bilinmeyen Müşteri' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted"><i class="ti ti-cut me-1"></i>{{ $apt->employee->user->full_name ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border text-wrap" style="max-width: 200px;">
                                        {{ $apt->appointmentServices->pluck('service.name')->join(', ') }}
                                    </span>
                                </td>
                                <td>
                                    @if($apt->status->value == 'completed')
                                        <span class="badge bg-success-subtle text-success">Tamamlandı</span>
                                    @elseif($apt->status->value == 'cancelled')
                                        <span class="badge bg-danger-subtle text-danger">İptal</span>
                                    @elseif($apt->start_at < now() && $apt->status->value == 'scheduled')
                                        <span class="badge bg-warning-subtle text-warning">Gecikmiş</span>
                                    @else
                                        <span class="badge bg-primary-subtle text-primary">Bekliyor</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-4 text-muted">Bugün için randevu bulunmuyor.</td></tr>
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
<script type="module">
import ApexCharts from 'apexcharts';

document.addEventListener('DOMContentLoaded', () => {
    const chartDataAll = @json($revenueChart);

    if (document.getElementById('revenueChart')) {
        let currentPeriod = 'year';

        const options = {
            chart: { type: 'area', height: 350, toolbar: { show: false } },
            colors: ['#E66239'],
            stroke: { width: 3, curve: 'smooth' },
            markers: { size: 4 },
            series: [{ name: 'Gelir', data: chartDataAll[currentPeriod].map(d => d.revenue) }],
            fill: {
                type: 'gradient',
                gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [20, 60, 100] }
            },
            xaxis: { categories: chartDataAll[currentPeriod].map(d => d.label) },
            yaxis: {
                labels: { formatter: (val) => '₺' + val.toLocaleString('tr-TR', { maximumFractionDigits: 0 }) }
            },
            tooltip: {
                y: { formatter: (val) => '₺' + val.toLocaleString('tr-TR', { maximumFractionDigits: 0 }) }
            },
        };
        const chart = new ApexCharts(document.querySelector('#revenueChart'), options);
        chart.render();

        document.getElementById('revenueChartPeriod').addEventListener('change', function(e) {
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

    if (document.getElementById('customerChart')) {
        const customerData = @json($widgets['customers'] ?? ['new_this_month' => 0, 'loyal' => 0]);
        const total = (customerData.new_this_month || 0) + (customerData.loyal || 0);
        const newPct = total > 0 ? Math.round((customerData.new_this_month / total) * 100) : 50;
        const loyalPct = total > 0 ? Math.round((customerData.loyal / total) * 100) : 50;

        const options = {
            series: [newPct, loyalPct],
            chart: { height: 200, type: 'radialBar' },
            colors: ['#5BE49B', '#E66239'],
            plotOptions: {
                radialBar: {
                    dataLabels: {
                        name: { fontSize: '14px' },
                        value: { fontSize: '14px' },
                        total: { show: false },
                    },
                    hollow: { size: '40%' },
                    track: { background: '#f0f0f0', strokeWidth: '45%' },
                },
            },
            fill: {
                type: 'gradient',
                gradient: { shade: 'dark', type: 'vertical', gradientToColors: ['#007867', '#FFD666'], stops: [0, 100] },
            },
            stroke: { lineCap: 'round' },
            labels: ['Yeni', 'Sadık'],
        };
        new ApexCharts(document.querySelector('#customerChart'), options).render();
    }
});
</script>
@endpush
