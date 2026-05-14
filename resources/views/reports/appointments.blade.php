@extends('layouts.app')
@section('title', 'Randevu Raporları - B&V Barber')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fs-3 mb-1">Randevu Raporları</h1>
                <p class="text-muted">Tüm randevuların durum analizleri.</p>
            </div>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                <i class="ti-arrow-left me-1"></i> Geri Dön
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-6">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Randevu Durumu</th>
                                <th class="text-end pe-4">Toplam Adet</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($appointments as $apt)
                            <tr>
                                <td class="ps-4">
                                    <span class="badge bg-light text-dark border px-3 py-2 fw-medium text-uppercase">
                                        {{ $apt->status }}
                                    </span>
                                </td>
                                <td class="text-end pe-4 fs-5 fw-bold text-primary">{{ $apt->total }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="text-center py-4 text-muted">Veri bulunmuyor.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
