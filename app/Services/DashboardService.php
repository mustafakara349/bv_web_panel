<?php

namespace App\Services;

use App\Repositories\Contracts\DashboardRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    public function __construct(
        private DashboardRepositoryInterface $dashboardRepo
    ) {}

    private function getCacheTtl(int $seconds): int
    {
        return app()->environment('local') ? 1 : $seconds;
    }

    public function getWidgetData(int $branchId): array
    {
        return Cache::remember("dashboard.widgets.{$branchId}", $this->getCacheTtl(300), fn () => [
            'revenue' => $this->dashboardRepo->getRevenueStats($branchId),
            'appointments' => $this->dashboardRepo->getAppointmentStats($branchId),
            'financial' => $this->dashboardRepo->getFinancialOverview($branchId),
            'customers' => $this->dashboardRepo->getCustomerStats($branchId),
        ]);
    }

    public function getFilteredAppointmentStats(int $branchId, string $period, ?string $startDate = null, ?string $endDate = null): array
    {
        $cacheKey = "dashboard.appointments.{$branchId}.{$period}";
        if ($period === 'custom') {
            $cacheKey .= ".{$startDate}.{$endDate}";
        }

        return Cache::remember($cacheKey, $this->getCacheTtl(300), fn () =>
            $this->dashboardRepo->getFilteredAppointmentStats($branchId, $period, $startDate, $endDate)
        );
    }

    public function getBarberPerformance(int $branchId): array
    {
        return Cache::remember("dashboard.barbers.{$branchId}", $this->getCacheTtl(600), fn () =>
            $this->dashboardRepo->getBarberPerformance($branchId)
        );
    }

    public function getTopServices(int $branchId, int $limit = 5): array
    {
        return Cache::remember("dashboard.services.{$branchId}", $this->getCacheTtl(600), fn () =>
            $this->dashboardRepo->getTopServices($branchId, $limit)
        );
    }

    public function getRevenueChart(int $branchId): array
    {
        return Cache::remember("dashboard.chart.{$branchId}.all_periods", $this->getCacheTtl(600), fn () => [
            'year' => $this->dashboardRepo->getRevenueChart($branchId, 'year'),
            'month' => $this->dashboardRepo->getRevenueChart($branchId, 'month'),
            'day' => $this->dashboardRepo->getRevenueChart($branchId, 'day'),
        ]);
    }

    public function getHourlyDensity(int $branchId, string $date): array
    {
        return Cache::remember("dashboard.hourly.{$branchId}.{$date}", $this->getCacheTtl(300), fn () =>
            $this->dashboardRepo->getHourlyDensity($branchId, $date)
        );
    }

    public function getTodayAppointments(int $branchId)
    {
        return \App\Models\Appointment::forBranch($branchId)
            ->whereDate('start_at', today())
            ->where('start_at', '>=', now())
            ->whereIn('status', ['confirmed', 'in_progress'])
            ->with(['customer', 'employee.user', 'appointmentServices.service'])
            ->orderBy('start_at', 'asc')
            ->get();
    }

    public function getPendingAppointments(int $branchId)
    {
        return \App\Models\Appointment::forBranch($branchId)
            ->where('status', \App\Enums\AppointmentStatus::Pending)
            ->where('start_at', '>=', now())
            ->with(['customer', 'employee.user', 'appointmentServices.service'])
            ->orderBy('start_at', 'asc')
            ->get();
    }
}
