<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboardService) {}

    public function index(): View
    {
        $branchId = $this->getActiveBranchId();

        $widgets = $this->dashboardService->getWidgetData($branchId);
        $barbers = $this->dashboardService->getBarberPerformance($branchId);
        $topServices = $this->dashboardService->getTopServices($branchId);
        $revenueChart = $this->dashboardService->getRevenueChart($branchId);
        $todayAppointments = $this->dashboardService->getTodayAppointments($branchId);

        return view('dashboard.index', compact('widgets', 'barbers', 'topServices', 'revenueChart', 'todayAppointments'));
    }

    private function getActiveBranchId(): int
    {
        return session('active_branch_id', 1);
    }
}
