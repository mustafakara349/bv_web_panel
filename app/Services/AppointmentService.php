<?php

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\AppointmentService as AppointmentServiceModel;
use App\Models\AppointmentStatusLog;
use App\Repositories\Contracts\AppointmentRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AppointmentService
{
    public function __construct(
        private AppointmentRepositoryInterface $appointmentRepo
    ) {}

    public function createAppointment(array $data): Appointment
    {
        return DB::transaction(function () use ($data) {
            $data['uuid'] = (string) Str::uuid();
            $data['appointment_code'] = $this->generateCode();

            $services = $data['services'] ?? [];
            unset($data['services']);

            $totalDuration = 0;
            $subtotal = 0;

            $appointment = $this->appointmentRepo->create($data);

            foreach ($services as $service) {
                $totalPrice = $service['unit_price'] * ($service['quantity'] ?? 1);
                $totalDuration += $service['duration_minutes'];
                $subtotal += $totalPrice;

                AppointmentServiceModel::create([
                    'appointment_id' => $appointment->id,
                    'service_id' => $service['service_id'],
                    'employee_id' => $data['employee_id'],
                    'quantity' => $service['quantity'] ?? 1,
                    'duration_minutes' => $service['duration_minutes'],
                    'unit_price' => $service['unit_price'],
                    'total_price' => $totalPrice,
                ]);
            }

            $appointment->update([
                'total_duration' => $totalDuration,
                'subtotal' => $subtotal,
                'total_price' => $subtotal - ($data['discount_amount'] ?? 0) + ($data['tax_amount'] ?? 0),
                'end_at' => $appointment->start_at->addMinutes($totalDuration),
            ]);

            $this->logStatusChange($appointment, null, AppointmentStatus::Pending->value);

            return $appointment->load(['customer', 'employee.user', 'appointmentServices.service']);
        });
    }

    public function updateStatus(Appointment $appointment, AppointmentStatus $status, ?int $userId = null, ?string $note = null): Appointment
    {
        $oldStatus = $appointment->status->value;

        $updateData = ['status' => $status];

        if ($status === AppointmentStatus::Completed) {
            $updateData['completed_at'] = now();
        }

        if ($status === AppointmentStatus::Cancelled) {
            $updateData['cancelled_at'] = now();
            $updateData['cancelled_by'] = $userId;
        }

        if ($status === AppointmentStatus::NoShow) {
            $updateData['no_show'] = true;
        }

        $appointment->update($updateData);

        $this->logStatusChange($appointment, $oldStatus, $status->value, $userId, $note);

        return $appointment->fresh();
    }

    private function logStatusChange(Appointment $appointment, ?string $old, string $new, ?int $userId = null, ?string $note = null): void
    {
        AppointmentStatusLog::create([
            'appointment_id' => $appointment->id,
            'changed_by' => $userId,
            'old_status' => $old,
            'new_status' => $new,
            'note' => $note,
        ]);
    }

    private function generateCode(): string
    {
        do {
            $code = 'BV-' . strtoupper(Str::random(8));
        } while (Appointment::where('appointment_code', $code)->exists());

        return $code;
    }
}
