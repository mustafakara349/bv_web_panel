<?php

namespace App\Http\Controllers\Web;

use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\StoreAppointmentRequest;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\Service;
use App\Repositories\Contracts\AppointmentRepositoryInterface;
use App\Services\AppointmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function __construct(
        private AppointmentRepositoryInterface $appointmentRepo,
        private AppointmentService $appointmentService
    ) {}

    public function index(Request $request): View
    {
        $branchId = session('active_branch_id', 1);

        $appointments = $this->appointmentRepo->getForBranch($branchId, [
            'status' => $request->get('status'),
            'employee_id' => $request->get('employee_id'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
            'search' => $request->get('search'),
        ], $request->get('per_page', 15));

        $employees = Employee::forBranch($branchId)->active()->with('user')->get();
        $statuses = AppointmentStatus::cases();

        return view('appointments.index', compact('appointments', 'employees', 'statuses'));
    }

    public function create(): View
    {
        $branchId = session('active_branch_id', 1);
        $employees = Employee::forBranch($branchId)->active()->visible()->with('user')->get();
        $services = Service::forBranch($branchId)->active()->get();
        $customers = \App\Models\User::customers()->active()->get();

        return view('appointments.create', compact('employees', 'services', 'customers'));
    }

    public function store(StoreAppointmentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $data['source'] = 'admin_panel';

        $this->appointmentService->createAppointment($data);

        return redirect()->route('appointments.index')->with('success', 'Randevu başarıyla oluşturuldu.');
    }

    public function show(Appointment $appointment): View
    {
        $appointment->load([
            'customer', 'employee.user', 'branch',
            'appointmentServices.service', 'payments',
            'statusLogs.changedBy', 'review',
        ]);

        return view('appointments.show', compact('appointment'));
    }

    public function updateStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        $request->validate(['status' => 'required|string']);

        $status = AppointmentStatus::from($request->status);

        $this->appointmentService->updateStatus(
            $appointment,
            $status,
            auth()->id(),
            $request->get('note')
        );

        return back()->with('success', 'Randevu durumu güncellendi.');
    }

    public function availableSlots(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'employee_id' => 'required|exists:employees,id',
        ]);

        $date = $request->date;
        $employeeId = $request->employee_id;
        $branchId = session('active_branch_id', 1);

        $appointments = Appointment::forBranch($branchId)
            ->where('employee_id', $employeeId)
            ->whereDate('start_at', $date)
            ->whereNotIn('status', ['cancelled', 'rejected', 'no_show'])
            ->get();

        $slots = [];
        $startTime = \Carbon\Carbon::parse($date . ' 08:00:00');
        $endTime = \Carbon\Carbon::parse($date . ' 20:00:00');

        while ($startTime < $endTime) {
            $slotTime = $startTime->copy();
            $isAvailable = true;

            foreach ($appointments as $apt) {
                $aptStart = \Carbon\Carbon::parse($apt->start_at);
                $aptEnd = $apt->end_at ? \Carbon\Carbon::parse($apt->end_at) : $aptStart->copy()->addMinutes($apt->total_duration ?? 30);
                
                if ($slotTime >= $aptStart && $slotTime < $aptEnd) {
                    $isAvailable = false;
                    break;
                }
            }
            
            if ($date === today()->toDateString() && $slotTime <= now()) {
                $isAvailable = false;
            }

            $slots[] = [
                'time' => $slotTime->format('H:i'),
                'is_available' => $isAvailable
            ];

            $startTime->addMinutes(30);
        }

        return response()->json($slots);
    }
}
