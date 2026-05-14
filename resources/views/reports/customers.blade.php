@extends('layouts.app')
@section('title', 'Müşteri Raporları - B&V Barber')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fs-3 mb-1">Müşteri Raporları</h1>
                <p class="text-muted">En sadık 20 müşteriniz ve geliş istatistikleri.</p>
            </div>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i> Geri Dön
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-8">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Müşteri</th>
                                <th>Kayıt Tarihi</th>
                                <th>İletişim</th>
                                <th class="text-end pe-4">Tamamlanan Randevu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topCustomers as $customer)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        @if($customer->profile_photo)
                                            <img src="{{ asset($customer->profile_photo) }}" class="rounded-circle me-3 object-fit-cover shadow-sm" width="40" height="40" alt="{{ $customer->full_name }}">
                                        @else
                                            <div class="avatar bg-primary-subtle text-primary me-3 rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 40px; height: 40px;">
                                                {{ mb_substr($customer->first_name, 0, 1) }}{{ mb_substr($customer->last_name, 0, 1) }}
                                            </div>
                                        @endif
                                        <a href="{{ route('customers.show', $customer->id) }}" class="fw-semibold text-dark text-decoration-none">{{ $customer->full_name }}</a>
                                    </div>
                                </td>
                                <td class="text-muted">{{ $customer->created_at->format('d.m.Y') }}</td>
                                <td class="text-muted">{{ $customer->phone ?? $customer->email }}</td>
                                <td class="text-end pe-4">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fs-6">
                                        {{ $customer->appointments_count }} Kez Geldi
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-4 text-muted">Müşteri verisi bulunamadı.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
