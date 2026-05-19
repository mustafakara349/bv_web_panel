<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    private function parseDateRange(Request $request): array
    {
        $period = $request->get('period', 'this_month');
        $startDateInput = $request->get('start_date');
        $endDateInput = $request->get('end_date');

        if ($period === 'custom' && $startDateInput && $endDateInput) {
            $startDate = Carbon::parse($startDateInput)->startOfDay();
            $endDate = Carbon::parse($endDateInput)->endOfDay();
        } else {
            switch ($period) {
                case 'today':
                    $startDate = Carbon::today()->startOfDay();
                    $endDate = Carbon::today()->endOfDay();
                    break;
                case 'this_week':
                    $startDate = Carbon::now()->startOfWeek();
                    $endDate = Carbon::now()->endOfWeek();
                    break;
                case 'last_30_days':
                    $startDate = Carbon::now()->subDays(30)->startOfDay();
                    $endDate = Carbon::now()->endOfDay();
                    break;
                case 'last_month':
                    $startDate = Carbon::now()->subMonth()->startOfMonth();
                    $endDate = Carbon::now()->subMonth()->endOfMonth();
                    break;
                case 'this_year':
                    $startDate = Carbon::now()->startOfYear();
                    $endDate = Carbon::now()->endOfYear();
                    break;
                case 'this_month':
                default:
                    $startDate = Carbon::now()->startOfMonth();
                    $endDate = Carbon::now()->endOfMonth();
                    $period = 'this_month';
                    break;
            }
        }

        return [$startDate, $endDate, $period];
    }

    public function index()
    {
        $branchId = session('active_branch_id', 1);

        $generalStats = [
            'total_appointments' => Appointment::forBranch($branchId)->count(),
            'total_customers' => User::customers()->count(),
            'total_income' => Transaction::forBranch($branchId)->income()->sum('amount'),
            'total_expense' => Expense::forBranch($branchId)->sum('amount'),
        ];

        return view('reports.index', compact('generalStats'));
    }

    public function show($type, Request $request)
    {
        $branchId = session('active_branch_id', 1);
        [$startDate, $endDate, $period] = $this->parseDateRange($request);

        // Date labels and string format for request inputs
        $filters = [
            'period' => $period,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
        ];

        switch ($type) {
            case 'finance':
                return $this->handleFinanceReport($branchId, $startDate, $endDate, $filters);
                
            case 'appointments':
                return $this->handleAppointmentReport($branchId, $startDate, $endDate, $filters);

            case 'customers':
                return $this->handleCustomerReport($branchId, $startDate, $endDate, $filters);

            default:
                abort(404);
        }
    }

    private function handleFinanceReport($branchId, Carbon $startDate, Carbon $endDate, array $filters)
    {
        $totalIncome = Transaction::forBranch($branchId)
            ->income()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        $totalExpense = Expense::forBranch($branchId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount');

        $netProfit = $totalIncome - $totalExpense;

        // Group by day or month based on timeframe length
        $diffInDays = $startDate->diffInDays($endDate);
        $groupByMonth = $diffInDays > 90;

        if ($groupByMonth) {
            $incomeHistory = Transaction::forBranch($branchId)->income()
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->select(DB::raw("DATE_FORMAT(transaction_date, '%Y-%m') as month_key"), DB::raw('SUM(amount) as total'))
                ->groupBy('month_key')
                ->pluck('total', 'month_key')
                ->toArray();
                
            $expenseHistory = Expense::forBranch($branchId)
                ->whereBetween('expense_date', [$startDate, $endDate])
                ->select(DB::raw("DATE_FORMAT(expense_date, '%Y-%m') as month_key"), DB::raw('SUM(amount) as total'))
                ->groupBy('month_key')
                ->pluck('total', 'month_key')
                ->toArray();
                
            $chartTimeline = [];
            $chartIncomeData = [];
            $chartExpenseData = [];
            $current = clone $startDate;
            while ($current->lte($endDate)) {
                $monthStr = $current->format('Y-m');
                $chartTimeline[] = $current->translatedFormat('F Y');
                $chartIncomeData[] = round($incomeHistory[$monthStr] ?? 0, 2);
                $chartExpenseData[] = round($expenseHistory[$monthStr] ?? 0, 2);
                $current->addMonth()->startOfMonth();
            }
        } else {
            $incomeHistory = Transaction::forBranch($branchId)->income()
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->select(DB::raw('DATE(transaction_date) as date'), DB::raw('SUM(amount) as total'))
                ->groupBy('date')
                ->pluck('total', 'date')
                ->toArray();

            $expenseHistory = Expense::forBranch($branchId)
                ->whereBetween('expense_date', [$startDate, $endDate])
                ->select(DB::raw('DATE(expense_date) as date'), DB::raw('SUM(amount) as total'))
                ->groupBy('date')
                ->pluck('total', 'date')
                ->toArray();

            $chartTimeline = [];
            $chartIncomeData = [];
            $chartExpenseData = [];
            $current = clone $startDate;
            while ($current->lte($endDate)) {
                $dateStr = $current->format('Y-m-d');
                $chartTimeline[] = $current->format('d.m.Y');
                $chartIncomeData[] = round($incomeHistory[$dateStr] ?? 0, 2);
                $chartExpenseData[] = round($expenseHistory[$dateStr] ?? 0, 2);
                $current->addDay();
            }
        }

        // Income by Method
        $incomeByMethod = Transaction::forBranch($branchId)->income()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->select('payment_method', DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->get()
            ->map(fn($t) => [
                'label' => $t->payment_method ? $t->payment_method->label() : 'Belirtilmemiş',
                'total' => round($t->total, 2),
            ])->toArray();

        // Expense by Category
        $expenseByCategory = Expense::where('expenses.branch_id', $branchId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->join('expense_categories', 'expenses.category_id', '=', 'expense_categories.id')
            ->select('expense_categories.name as category_name', DB::raw('SUM(expenses.amount) as total'))
            ->groupBy('expense_categories.name')
            ->orderByDesc('total')
            ->get()
            ->map(fn($e) => [
                'label' => $e->category_name,
                'total' => round($e->total, 2),
            ])->toArray();

        // Recent listings
        $incomes = Transaction::forBranch($branchId)->income()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->latest('transaction_date')
            ->take(50)
            ->get();

        $expenses = Expense::forBranch($branchId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->latest('expense_date')
            ->take(50)
            ->get();

        return view('reports.finance', compact(
            'totalIncome', 'totalExpense', 'netProfit',
            'chartTimeline', 'chartIncomeData', 'chartExpenseData',
            'incomeByMethod', 'expenseByCategory',
            'incomes', 'expenses', 'filters'
        ));
    }

    private function handleAppointmentReport($branchId, Carbon $startDate, Carbon $endDate, array $filters)
    {
        $statusCounts = Appointment::forBranch($branchId)
            ->whereBetween('start_at', [$startDate, $endDate])
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $totalAppointments = array_sum($statusCounts);
        $completedCount = $statusCounts['completed'] ?? 0;
        $cancelledCount = $statusCounts['cancelled'] ?? 0;
        $rejectedCount = $statusCounts['rejected'] ?? 0;
        $noShowCount = $statusCounts['no_show'] ?? 0;

        // Barber stats
        $barberStats = DB::table('employees')
            ->join('users', 'employees.user_id', '=', 'users.id')
            ->leftJoin('appointments', function($join) use ($startDate, $endDate) {
                $join->on('employees.id', '=', 'appointments.employee_id')
                     ->whereBetween('appointments.start_at', [$startDate, $endDate]);
            })
            ->where('employees.branch_id', $branchId)
            ->where('employees.is_active', true)
            ->select(
                'employees.id',
                'users.first_name',
                'users.last_name',
                'employees.title',
                DB::raw("COUNT(CASE WHEN appointments.status = 'completed' THEN 1 END) as completed_count"),
                DB::raw("COUNT(CASE WHEN appointments.status IN ('cancelled', 'rejected', 'no_show') THEN 1 END) as cancelled_count"),
                DB::raw("COALESCE(SUM(CASE WHEN appointments.status = 'completed' THEN appointments.total_price ELSE 0 END), 0) as total_revenue")
            )
            ->groupBy('employees.id', 'users.first_name', 'users.last_name', 'employees.title')
            ->orderByDesc('total_revenue')
            ->get();

        // Service stats
        $serviceStats = DB::table('services')
            ->leftJoin('appointment_services', 'services.id', '=', 'appointment_services.service_id')
            ->leftJoin('appointments', function($join) use ($startDate, $endDate) {
                $join->on('appointment_services.appointment_id', '=', 'appointments.id')
                     ->where('appointments.status', '=', 'completed')
                     ->whereBetween('appointments.start_at', [$startDate, $endDate]);
            })
            ->where('services.branch_id', $branchId)
            ->where('services.is_active', true)
            ->select(
                'services.id',
                'services.name',
                DB::raw('COUNT(appointments.id) as completed_count'),
                DB::raw('COALESCE(SUM(appointment_services.total_price), 0) as total_revenue')
            )
            ->groupBy('services.id', 'services.name')
            ->orderByDesc('completed_count')
            ->get();

        // Cancellation reason stats
        $cancellationReasons = Appointment::forBranch($branchId)
            ->whereBetween('start_at', [$startDate, $endDate])
            ->whereIn('status', ['cancelled', 'rejected'])
            ->whereNotNull('cancellation_reason')
            ->where('cancellation_reason', '!=', '')
            ->select('cancellation_reason', DB::raw('count(*) as total'))
            ->groupBy('cancellation_reason')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        // Timeline of appointments over period (completed vs cancelled)
        $diffInDays = $startDate->diffInDays($endDate);
        $groupByMonth = $diffInDays > 90;

        if ($groupByMonth) {
            $completedHistory = Appointment::forBranch($branchId)
                ->whereBetween('start_at', [$startDate, $endDate])
                ->where('status', 'completed')
                ->select(DB::raw("DATE_FORMAT(start_at, '%Y-%m') as month_key"), DB::raw('count(*) as total'))
                ->groupBy('month_key')
                ->pluck('total', 'month_key')
                ->toArray();

            $cancelledHistory = Appointment::forBranch($branchId)
                ->whereBetween('start_at', [$startDate, $endDate])
                ->whereIn('status', ['cancelled', 'rejected'])
                ->select(DB::raw("DATE_FORMAT(start_at, '%Y-%m') as month_key"), DB::raw('count(*) as total'))
                ->groupBy('month_key')
                ->pluck('total', 'month_key')
                ->toArray();

            $chartTimeline = [];
            $chartCompletedData = [];
            $chartCancelledData = [];
            $current = clone $startDate;
            while ($current->lte($endDate)) {
                $monthStr = $current->format('Y-m');
                $chartTimeline[] = $current->translatedFormat('F Y');
                $chartCompletedData[] = $completedHistory[$monthStr] ?? 0;
                $chartCancelledData[] = $cancelledHistory[$monthStr] ?? 0;
                $current->addMonth()->startOfMonth();
            }
        } else {
            $completedHistory = Appointment::forBranch($branchId)
                ->whereBetween('start_at', [$startDate, $endDate])
                ->where('status', 'completed')
                ->select(DB::raw('DATE(start_at) as date'), DB::raw('count(*) as total'))
                ->groupBy('date')
                ->pluck('total', 'date')
                ->toArray();

            $cancelledHistory = Appointment::forBranch($branchId)
                ->whereBetween('start_at', [$startDate, $endDate])
                ->whereIn('status', ['cancelled', 'rejected'])
                ->select(DB::raw('DATE(start_at) as date'), DB::raw('count(*) as total'))
                ->groupBy('date')
                ->pluck('total', 'date')
                ->toArray();

            $chartTimeline = [];
            $chartCompletedData = [];
            $chartCancelledData = [];
            $current = clone $startDate;
            while ($current->lte($endDate)) {
                $dateStr = $current->format('Y-m-d');
                $chartTimeline[] = $current->format('d.m.Y');
                $chartCompletedData[] = $completedHistory[$dateStr] ?? 0;
                $chartCancelledData[] = $cancelledHistory[$dateStr] ?? 0;
                $current->addDay();
            }
        }

        return view('reports.appointments', compact(
            'totalAppointments', 'completedCount', 'cancelledCount', 'rejectedCount', 'noShowCount',
            'barberStats', 'serviceStats', 'cancellationReasons',
            'chartTimeline', 'chartCompletedData', 'chartCancelledData', 'filters'
        ));
    }

    private function handleCustomerReport($branchId, Carbon $startDate, Carbon $endDate, array $filters)
    {
        $newCustomers = User::customers()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $totalCustomers = User::customers()->count();

        // Loyal customers definition: visited branch 3+ times completed
        $loyalCustomers = DB::table('appointments')
            ->where('branch_id', $branchId)
            ->where('status', 'completed')
            ->select('customer_id', DB::raw('COUNT(*) as visit_count'))
            ->groupBy('customer_id')
            ->having('visit_count', '>=', 3)
            ->count();

        // Gender stats
        $genders = User::customers()
            ->select('gender', DB::raw('count(*) as count'))
            ->groupBy('gender')
            ->pluck('count', 'gender')
            ->toArray();

        $genderDistribution = [
            ['label' => 'Erkek', 'total' => $genders['male'] ?? 0],
            ['label' => 'Kadın', 'total' => $genders['female'] ?? 0],
            ['label' => 'Diğer/Belirtilmemiş', 'total' => ($genders['other'] ?? 0) + ($genders[''] ?? 0)],
        ];

        // VIP Customers
        $topCustomers = User::customers()
            ->join('appointments', 'users.id', '=', 'appointments.customer_id')
            ->where('appointments.branch_id', $branchId)
            ->where('appointments.status', 'completed')
            ->whereBetween('appointments.start_at', [$startDate, $endDate])
            ->select(
                'users.id',
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.phone',
                'users.profile_photo',
                'users.created_at',
                DB::raw('COUNT(appointments.id) as visits_count'),
                DB::raw('SUM(appointments.total_price) as total_spent'),
                DB::raw('MAX(appointments.start_at) as last_visit_date')
            )
            ->groupBy('users.id', 'users.first_name', 'users.last_name', 'users.email', 'users.phone', 'users.profile_photo', 'users.created_at')
            ->orderByDesc('total_spent')
            ->take(20)
            ->get();

        // Customer signups timeline
        $diffInDays = $startDate->diffInDays($endDate);
        $groupByMonth = $diffInDays > 90;

        if ($groupByMonth) {
            $regHistory = User::customers()
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month_key"), DB::raw('count(*) as total'))
                ->groupBy('month_key')
                ->pluck('total', 'month_key')
                ->toArray();

            $chartTimeline = [];
            $chartRegData = [];
            $current = clone $startDate;
            while ($current->lte($endDate)) {
                $monthStr = $current->format('Y-m');
                $chartTimeline[] = $current->translatedFormat('F Y');
                $chartRegData[] = $regHistory[$monthStr] ?? 0;
                $current->addMonth()->startOfMonth();
            }
        } else {
            $regHistory = User::customers()
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
                ->groupBy('date')
                ->pluck('total', 'date')
                ->toArray();

            $chartTimeline = [];
            $chartRegData = [];
            $current = clone $startDate;
            while ($current->lte($endDate)) {
                $dateStr = $current->format('Y-m-d');
                $chartTimeline[] = $current->format('d.m.Y');
                $chartRegData[] = $regHistory[$dateStr] ?? 0;
                $current->addDay();
            }
        }

        return view('reports.customers', compact(
            'newCustomers', 'totalCustomers', 'loyalCustomers', 'genderDistribution',
            'topCustomers', 'chartTimeline', 'chartRegData', 'filters'
        ));
    }
}
