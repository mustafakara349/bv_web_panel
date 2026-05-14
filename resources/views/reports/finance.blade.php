@extends('layouts.app')
@section('title', 'Finansal Raporlar - B&V Barber')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fs-3 mb-1">Finansal Raporlar</h1>
                <p class="text-muted">Son gelir ve gider işlemlerinin detaylı dökümü.</p>
            </div>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i> Geri Dön
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 rounded-3 h-100">
            <div class="card-header bg-transparent border-bottom py-3">
                <h5 class="mb-0 text-success"><i class="ti ti-trending-up me-2"></i>Son Gelirler</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Tarih</th>
                                <th>Açıklama</th>
                                <th class="text-end pe-4">Tutar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($incomes as $inc)
                            <tr>
                                <td class="ps-4">{{ \Carbon\Carbon::parse($inc->transaction_date)->format('d.m.Y') }}</td>
                                <td>{{ $inc->description ?? 'Randevu Geliri' }}</td>
                                <td class="text-end text-success fw-medium pe-4">+₺{{ number_format($inc->amount, 2, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center py-4 text-muted">Sonuç bulunamadı.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm border-0 rounded-3 h-100">
            <div class="card-header bg-transparent border-bottom py-3">
                <h5 class="mb-0 text-danger"><i class="ti ti-trending-down me-2"></i>Son Giderler</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Tarih</th>
                                <th>Açıklama</th>
                                <th class="text-end pe-4">Tutar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($expenses as $exp)
                            <tr>
                                <td class="ps-4">{{ \Carbon\Carbon::parse($exp->expense_date)->format('d.m.Y') }}</td>
                                <td>{{ $exp->description ?? 'Belirtilmemiş' }}</td>
                                <td class="text-end text-danger fw-medium pe-4">-₺{{ number_format($exp->amount, 2, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center py-4 text-muted">Sonuç bulunamadı.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
