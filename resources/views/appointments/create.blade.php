@extends('layouts.app')

@section('title', 'Yeni Randevu - B&V Barber')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fs-3 mb-1">Yeni Randevu</h1>
                <p class="mb-0">Yeni randevu oluşturun</p>
            </div>
            <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i>Geri
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('appointments.store') }}">
                    @csrf
                    <input type="hidden" name="branch_id" value="{{ session('active_branch_id', 1) }}">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Müşteri <span class="text-danger">*</span></label>
                            <select name="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
                                <option value="">Müşteri seçin</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->full_name }}</option>
                                @endforeach
                            </select>
                            @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Berber <span class="text-danger">*</span></label>
                            <select name="employee_id" class="form-select @error('employee_id') is-invalid @enderror" required>
                                <option value="">Berber seçin</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->user->full_name }}</option>
                                @endforeach
                            </select>
                            @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tarih & Saat <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="start_at" class="form-control @error('start_at') is-invalid @enderror" required>
                            @error('start_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Kaynak</label>
                            <select name="source" class="form-select">
                                <option value="admin_panel">Admin Paneli</option>
                                <option value="phone">Telefon</option>
                                <option value="walk_in">Walk-in</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Hizmetler <span class="text-danger">*</span></label>
                            <div id="servicesContainer">
                                @foreach($services as $service)
                                <div class="form-check">
                                    <input class="form-check-input service-check" type="checkbox"
                                           data-service-id="{{ $service->id }}"
                                           data-price="{{ $service->effective_price }}"
                                           data-duration="{{ $service->duration_minutes }}"
                                           id="service_{{ $service->id }}">
                                    <label class="form-check-label" for="service_{{ $service->id }}">
                                        {{ $service->name }} — ₺{{ number_format($service->effective_price, 0, ',', '.') }} ({{ $service->duration_minutes }} dk)
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            <div id="selectedServices"></div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Müşteri Notu</label>
                            <textarea name="customer_note" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Dahili Not</label>
                            <textarea name="internal_note" class="form-control" rows="2"></textarea>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-check me-1"></i>Randevu Oluştur
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('selectedServices');
    const checkboxes = document.querySelectorAll('.service-check');

    checkboxes.forEach(cb => {
        cb.addEventListener('change', () => {
            container.innerHTML = '';
            let idx = 0;
            checkboxes.forEach(c => {
                if (c.checked) {
                    container.innerHTML += `
                        <input type="hidden" name="services[${idx}][service_id]" value="${c.dataset.serviceId}">
                        <input type="hidden" name="services[${idx}][unit_price]" value="${c.dataset.price}">
                        <input type="hidden" name="services[${idx}][duration_minutes]" value="${c.dataset.duration}">
                        <input type="hidden" name="services[${idx}][quantity]" value="1">
                    `;
                    idx++;
                }
            });
        });
    });
});
</script>
@endpush
