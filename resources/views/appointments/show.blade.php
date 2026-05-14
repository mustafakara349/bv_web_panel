@extends('layouts.app')

@section('title', 'Randevu Detay - B&V Barber')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fs-3 mb-1">Randevu #{{ $appointment->appointment_code }}</h1>
                <p class="mb-0">Randevu detayları</p>
            </div>
            <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i>Geri
            </a>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Main Info --}}
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header bg-transparent px-4 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 h5">Randevu Bilgileri</h4>
                    <span class="badge bg-{{ $appointment->status->color() }}-subtle text-{{ $appointment->status->color() }} border border-{{ $appointment->status->color() }} fs-6">
                        {{ $appointment->status->label() }}
                    </span>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <small class="text-muted d-block">Tarih</small>
                        <span class="fw-semibold">{{ $appointment->start_at->format('d.m.Y') }}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Saat</small>
                        <span class="fw-semibold">{{ $appointment->start_at->format('H:i') }} - {{ $appointment->end_at->format('H:i') }}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Süre</small>
                        <span>{{ $appointment->total_duration }} dakika</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Kaynak</small>
                        <span>{{ $appointment->source?->label() }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Services --}}
        <div class="card mb-3">
            <div class="card-header bg-transparent px-4 py-3">
                <h4 class="mb-0 h5">Hizmetler</h4>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr><th>Hizmet</th><th>Süre</th><th>Birim Fiyat</th><th>Adet</th><th>Toplam</th></tr>
                    </thead>
                    <tbody>
                        @foreach($appointment->appointmentServices as $as)
                        <tr>
                            <td>{{ $as->service?->name }}</td>
                            <td>{{ $as->duration_minutes }} dk</td>
                            <td>₺{{ number_format($as->unit_price, 0, ',', '.') }}</td>
                            <td>{{ $as->quantity }}</td>
                            <td class="fw-semibold">₺{{ number_format($as->total_price, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end fw-semibold">Toplam:</td>
                            <td class="fw-bold fs-5 text-primary">₺{{ number_format($appointment->total_price, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Status Logs --}}
        <div class="card">
            <div class="card-header bg-transparent px-4 py-3">
                <h4 class="mb-0 h5">Durum Geçmişi</h4>
            </div>
            <ul class="list-group list-group-flush">
                @foreach($appointment->statusLogs->sortByDesc('created_at') as $log)
                <li class="list-group-item">
                    <div class="d-flex justify-content-between">
                        <div>
                            @if($log->old_status)
                                <span class="badge bg-secondary-subtle text-secondary">{{ $log->old_status }}</span>
                                <i class="ti ti-arrow-right mx-1"></i>
                            @endif
                            <span class="badge bg-primary-subtle text-primary">{{ $log->new_status }}</span>
                            @if($log->note)
                                <small class="text-muted ms-2">{{ $log->note }}</small>
                            @endif
                        </div>
                        <small class="text-muted">
                            {{ $log->changedBy?->full_name ?? 'Sistem' }} •
                            {{ $log->created_at->format('d.m.Y H:i') }}
                        </small>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="col-lg-4">
        {{-- Customer --}}
        <div class="card mb-3">
            <div class="card-header bg-transparent px-4 py-3">
                <h4 class="mb-0 h5">Müşteri</h4>
            </div>
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    @if($appointment->customer?->profile_photo)
                        <img src="{{ $appointment->customer->profile_photo }}" class="avatar avatar-lg rounded-circle" alt="">
                    @else
                        <div class="avatar avatar-lg rounded-circle avatar-primary">
                            <span class="avatar-initials">{{ substr($appointment->customer?->first_name ?? '?', 0, 1) }}</span>
                        </div>
                    @endif
                    <div>
                        <h5 class="mb-0">{{ $appointment->customer?->full_name }}</h5>
                        <small class="text-muted">{{ $appointment->customer?->phone }}</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Barber --}}
        <div class="card mb-3">
            <div class="card-header bg-transparent px-4 py-3">
                <h4 class="mb-0 h5">Berber</h4>
            </div>
            <div class="card-body p-4">
                <h5 class="mb-1">{{ $appointment->employee?->user?->full_name }}</h5>
                <small class="text-muted">{{ $appointment->employee?->title }}</small>
            </div>
        </div>

        {{-- Status Update --}}
        <div class="card">
            <div class="card-header bg-transparent px-4 py-3">
                <h4 class="mb-0 h5">Durumu Güncelle</h4>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('appointments.update-status', $appointment) }}">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <select name="status" class="form-select">
                            @foreach(\App\Enums\AppointmentStatus::cases() as $s)
                                <option value="{{ $s->value }}" {{ $appointment->status === $s ? 'selected' : '' }}>{{ $s->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <textarea name="note" class="form-control" rows="2" placeholder="Not (opsiyonel)"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">Güncelle</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
