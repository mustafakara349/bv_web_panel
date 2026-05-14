@extends('layouts.app')

@section('title', 'Randevular - B&V Barber')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fs-3 mb-1">Randevular</h1>
                <p class="mb-0">Tüm randevuları yönetin</p>
            </div>
            <a href="{{ route('appointments.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i>Yeni Randevu
            </a>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="row mb-3">
    <div class="col-12">
        <div class="card p-3">
            <form method="GET" action="{{ route('appointments.index') }}" class="row g-2 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label small">Ara</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Kod, isim, telefon..." value="{{ request('search') }}">
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label small">Durum</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Tümü</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label small">Berber</label>
                    <select name="employee_id" class="form-select form-select-sm">
                        <option value="">Tümü</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->user->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label small">Başlangıç</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label small">Bitiş</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                </div>
                <div class="col-lg-1 col-md-6">
                    <button type="submit" class="btn btn-primary btn-sm w-100"><i class="ti ti-filter me-1"></i> Filtrele</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Table --}}
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Kod</th>
                            <th>Müşteri</th>
                            <th>Berber</th>
                            <th>Tarih / Saat</th>
                            <th>Hizmetler</th>
                            <th>Tutar</th>
                            <th>Durum</th>
                            <th>Ödeme</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appointments as $apt)
                        <tr>
                            <td><span class="fw-semibold">{{ $apt->appointment_code }}</span></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($apt->customer?->profile_photo)
                                        <img src="{{ $apt->customer->profile_photo }}" class="avatar avatar-xs rounded-circle" alt="">
                                    @else
                                        <div class="avatar avatar-xs rounded-circle avatar-primary">
                                            <span class="avatar-initials small">{{ substr($apt->customer?->first_name ?? '?', 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="mb-0 small">{{ $apt->customer?->full_name }}</p>
                                        <small class="text-muted">{{ $apt->customer?->phone }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $apt->employee?->user?->full_name }}</td>
                            <td>
                                <div>{{ $apt->start_at->format('d.m.Y') }}</div>
                                <small class="text-muted">{{ $apt->start_at->format('H:i') }} - {{ $apt->end_at->format('H:i') }}</small>
                            </td>
                            <td>
                                @foreach($apt->appointmentServices as $as)
                                    <span class="badge bg-light text-dark border mb-1">{{ $as->service?->name }}</span>
                                @endforeach
                            </td>
                            <td class="fw-semibold">₺{{ number_format($apt->total_price, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge bg-{{ $apt->status->color() }}-subtle text-{{ $apt->status->color() }} border border-{{ $apt->status->color() }}">
                                    {{ $apt->status->label() }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $apt->payment_status->color() }}-subtle text-{{ $apt->payment_status->color() }}">
                                    {{ $apt->payment_status->label() }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group shadow-sm">
                                    <a href="{{ route('appointments.show', $apt) }}" class="btn btn-sm btn-light text-primary" title="Görüntüle">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    @if($apt->status->value != 'cancelled' && $apt->status->value != 'completed')
                                    <form action="{{ route('appointments.update-status', $apt) }}" method="POST" class="d-inline" onsubmit="return confirm('Bu randevuyu iptal etmek istediğinize emin misiniz?');">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" class="btn btn-sm btn-light text-danger" style="border-top-left-radius: 0; border-bottom-left-radius: 0;" title="İptal Et">
                                            <i class="ti ti-x"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="ti ti-calendar-off fs-1 d-block mb-2"></i>
                                Randevu bulunamadı
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($appointments->hasPages())
            <div class="card-footer d-flex justify-content-center">
                {{ $appointments->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
