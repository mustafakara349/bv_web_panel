<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
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

    public function show($type)
    {
        $branchId = session('active_branch_id', 1);

        switch ($type) {
            case 'finance':
                $incomes = Transaction::forBranch($branchId)->income()->latest('transaction_date')->take(50)->get();
                $expenses = Expense::forBranch($branchId)->latest('expense_date')->take(50)->get();
                return view('reports.finance', compact('incomes', 'expenses'));
                
            case 'appointments':
                $appointments = Appointment::forBranch($branchId)
                    ->select('status', DB::raw('count(*) as total'))
                    ->groupBy('status')
                    ->get();
                return view('reports.appointments', compact('appointments'));

            case 'customers':
                $topCustomers = User::customers()
                    ->withCount(['appointments' => function ($q) use ($branchId) {
                        $q->where('branch_id', $branchId)->where('status', 'completed');
                    }])
                    ->orderByDesc('appointments_count')
                    ->take(20)
                    ->get();
                return view('reports.customers', compact('topCustomers'));

            default:
                abort(404);
        }
    }
}
