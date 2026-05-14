<?php

namespace App\Repositories\Eloquent;

use App\Enums\AppointmentStatus;
use App\Enums\TransactionType;
use App\Models\Appointment;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\Review;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\User;
use App\Repositories\Contracts\DashboardRepositoryInterface;
use Illuminate\Support\Facades\DB;

class DashboardRepository implements DashboardRepositoryInterface
{
    public function getRevenueStats(int $branchId, string $period = 'month'): array
    {
        $base = Appointment::forBranch($branchId)->completed();

        return [
            'daily' => (clone $base)->whereDate('completed_at', today())->sum('total_price'),
            'weekly' => (clone $base)->whereBetween('completed_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('total_price'),
            'monthly' => (clone $base)->whereMonth('completed_at', now()->month)->whereYear('completed_at', now()->year)->sum('total_price'),
            'yearly' => (clone $base)->whereYear('completed_at', now()->year)->sum('total_price'),
        ];
    }

    public function getAppointmentStats(int $branchId, string $period = 'month'): array
    {
        $base = Appointment::forBranch($branchId);
        $today = (clone $base)->today();

        $monthBase = (clone $base)->whereMonth('start_at', now()->month)->whereYear('start_at', now()->year);
        $totalMonth = (clone $monthBase)->count();

        return [
            'today_total' => (clone $today)->count(),
            'today_completed' => (clone $today)->where('status', AppointmentStatus::Completed)->count(),
            'today_pending' => (clone $today)->whereIn('status', [AppointmentStatus::Pending, AppointmentStatus::Confirmed])->count(),
            'month_total' => $totalMonth,
            'month_cancelled' => (clone $monthBase)->where('status', AppointmentStatus::Cancelled)->count(),
            'month_no_show' => (clone $monthBase)->where('status', AppointmentStatus::NoShow)->count(),
            'cancellation_rate' => $totalMonth > 0
                ? round((clone $monthBase)->where('status', AppointmentStatus::Cancelled)->count() / $totalMonth * 100, 1)
                : 0,
        ];
    }

    public function getBarberPerformance(int $branchId): array
    {
        return Employee::with('user')
            ->forBranch($branchId)
            ->active()
            ->withCount(['appointments as completed_appointments_count' => function ($q) {
                $q->where('status', AppointmentStatus::Completed)
                    ->whereMonth('start_at', now()->month);
            }])
            ->withSum(['appointments as monthly_revenue' => function ($q) {
                $q->where('status', AppointmentStatus::Completed)
                    ->whereMonth('start_at', now()->month);
            }], 'total_price')
            ->withAvg('reviews', 'rating')
            ->orderByDesc('monthly_revenue')
            ->get()
            ->map(fn ($e) => [
                'id' => $e->id,
                'name' => $e->full_name,
                'title' => $e->title,
                'photo' => $e->user?->profile_photo,
                'completed_appointments' => $e->completed_appointments_count ?? 0,
                'revenue' => round($e->monthly_revenue ?? 0, 2),
                'rating' => round($e->reviews_avg_rating ?? 0, 1),
                'commission_rate' => $e->commission_rate,
            ])
            ->toArray();
    }

    public function getTopServices(int $branchId, int $limit = 5): array
    {
        return DB::table('appointment_services')
            ->join('services', 'appointment_services.service_id', '=', 'services.id')
            ->join('appointments', 'appointment_services.appointment_id', '=', 'appointments.id')
            ->where('appointments.branch_id', $branchId)
            ->where('appointments.status', AppointmentStatus::Completed->value)
            ->whereMonth('appointments.start_at', now()->month)
            ->select(
                'services.id',
                'services.name',
                DB::raw('COUNT(*) as usage_count'),
                DB::raw('SUM(appointment_services.total_price) as total_revenue')
            )
            ->groupBy('services.id', 'services.name')
            ->orderByDesc('usage_count')
            ->limit($limit)
            ->get()
            ->map(fn ($item) => (array) $item)
            ->toArray();
    }

    public function getCustomerStats(int $branchId): array
    {
        $thisMonth = now()->startOfMonth();

        $newCustomers = User::customers()
            ->where('created_at', '>=', $thisMonth)
            ->count();

        $totalCustomers = User::customers()->count();

        $avgSpending = Appointment::forBranch($branchId)
            ->completed()
            ->whereMonth('completed_at', now()->month)
            ->avg('total_price');

        $loyalCustomers = DB::table('appointments')
            ->where('branch_id', $branchId)
            ->where('status', AppointmentStatus::Completed->value)
            ->select('customer_id', DB::raw('COUNT(*) as visit_count'))
            ->groupBy('customer_id')
            ->having('visit_count', '>=', 3)
            ->count();

        return [
            'total' => $totalCustomers,
            'new_this_month' => $newCustomers,
            'loyal' => $loyalCustomers,
            'avg_spending' => round($avgSpending ?? 0, 2),
        ];
    }

    public function getFinancialOverview(int $branchId): array
    {
        $month = now()->month;
        $year = now()->year;

        $income = Transaction::forBranch($branchId)
            ->income()
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', $year)
            ->sum('amount');

        $expense = Expense::forBranch($branchId)
            ->whereMonth('expense_date', $month)
            ->whereYear('expense_date', $year)
            ->sum('amount');

        return [
            'income' => round($income, 2),
            'expense' => round($expense, 2),
            'profit' => round($income - $expense, 2),
        ];
    }

    public function getRevenueChart(int $branchId, string $period = 'year'): array
    {
        if ($period === 'day') {
            return collect(range(8, 22))->map(function ($hour) use ($branchId) {
                $revenue = Appointment::forBranch($branchId)
                    ->completed()
                    ->whereDate('completed_at', today())
                    ->whereRaw('HOUR(completed_at) = ?', [$hour])
                    ->sum('total_price');

                return [
                    'month' => null,
                    'label' => sprintf('%02d:00', $hour),
                    'revenue' => round($revenue, 2),
                ];
            })->toArray();
        }

        if ($period === 'month') {
            return collect(range(1, now()->daysInMonth))->map(function ($day) use ($branchId) {
                $revenue = Appointment::forBranch($branchId)
                    ->completed()
                    ->whereDate('completed_at', now()->setDay($day)->toDateString())
                    ->sum('total_price');

                return [
                    'month' => now()->month,
                    'label' => $day . ' ' . now()->translatedFormat('M'),
                    'revenue' => round($revenue, 2),
                ];
            })->toArray();
        }

        $months = collect(range(1, 12))->map(function ($month) use ($branchId) {
            $revenue = Appointment::forBranch($branchId)
                ->completed()
                ->whereMonth('completed_at', $month)
                ->whereYear('completed_at', now()->year)
                ->sum('total_price');

            return [
                'month' => $month,
                'label' => now()->month($month)->translatedFormat('M'),
                'revenue' => round($revenue, 2),
            ];
        });

        return $months->toArray();
    }

    public function getHourlyDensity(int $branchId, string $date): array
    {
        return Appointment::forBranch($branchId)
            ->forDate($date)
            ->select(DB::raw('HOUR(start_at) as hour'), DB::raw('COUNT(*) as count'))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->toArray();
    }
}
