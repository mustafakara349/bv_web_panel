@extends('layouts.app')
@section('title', 'Raporlar - B&V Barber')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="mb-4">
            <h1 class="fs-3 mb-1">Raporlar</h1>
            <p class="text-muted">İşletmenizin genel durumunu ve istatistiklerini buradan takip edebilirsiniz.</p>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm border-0 rounded-3 bg-primary bg-opacity-10 h-100">
            <div class="card-body text-center p-4">
                <i class="ti-calendar-check fs-1 text-primary mb-3"></i>
                <h3 class="fw-bold mb-1">{{ $generalStats['total_appointments'] }}</h3>
                <span class="text-muted">Toplam Randevu</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 rounded-3 bg-success bg-opacity-10 h-100">
            <div class="card-body text-center p-4">
                <i class="ti-users fs-1 text-success mb-3"></i>
                <h3 class="fw-bold mb-1">{{ $generalStats['total_customers'] }}</h3>
                <span class="text-muted">Toplam Müşteri</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 rounded-3 bg-info bg-opacity-10 h-100">
            <div class="card-body text-center p-4">
                <i class="ti-trending-up fs-1 text-info mb-3"></i>
                <h3 class="fw-bold mb-1">₺{{ number_format($generalStats['total_income'], 2, ',', '.') }}</h3>
                <span class="text-muted">Toplam Gelir</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 rounded-3 bg-danger bg-opacity-10 h-100">
            <div class="card-body text-center p-4">
                <i class="ti-trending-down fs-1 text-danger mb-3"></i>
                <h3 class="fw-bold mb-1">₺{{ number_format($generalStats['total_expense'], 2, ',', '.') }}</h3>
                <span class="text-muted">Toplam Gider</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 rounded-3 h-100">
            <div class="card-body text-center p-5">
                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                    <i class="ti-cash fs-1 text-primary"></i>
                </div>
                <h4 class="fw-bold mb-3">Finansal Raporlar</h4>
                <p class="text-muted mb-4">Gelir ve gider işlemlerinizin detaylı dökümünü ve analizlerini görüntüleyin.</p>
                <a href="{{ route('reports.show', 'finance') }}" class="btn btn-outline-primary rounded-pill px-4">Raporu İncele</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow-sm border-0 rounded-3 h-100">
            <div class="card-body text-center p-5">
                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                    <i class="ti-calendar-stats fs-1 text-success"></i>
                </div>
                <h4 class="fw-bold mb-3">Randevu Analizleri</h4>
                <p class="text-muted mb-4">Gerçekleşen, iptal olan veya gelinemeyen randevuların dağılımı ve analizleri.</p>
                <a href="{{ route('reports.show', 'appointments') }}" class="btn btn-outline-success rounded-pill px-4">Raporu İncele</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow-sm border-0 rounded-3 h-100">
            <div class="card-body text-center p-5">
                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                    <i class="ti-users fs-1 text-info"></i>
                </div>
                <h4 class="fw-bold mb-3">Müşteri Raporları</h4>
                <p class="text-muted mb-4">En sadık müşterileriniz, sık tercih edilen hizmetleri ve ziyaret istatistikleri.</p>
                <a href="{{ route('reports.show', 'customers') }}" class="btn btn-outline-info rounded-pill px-4">Raporu İncele</a>
            </div>
        </div>
    </div>
</div>
@endsection
