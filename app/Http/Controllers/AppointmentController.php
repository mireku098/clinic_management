<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Patient;
use App\Models\PatientVisit;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $today = Carbon::today();

        $query = PatientVisit::appointments()
            ->with(['patient', 'services.service']);

        if ($request->filled('patient_search')) {
            $search = $request->patient_search;
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('patient_code', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('visit_date')) {
            $query->whereDate('visit_date', $request->visit_date);
        }

        if ($request->filled('status_filter')) {
            $query->where('status', $request->status_filter);
        }

        $appointments = $query
            ->orderBy('visit_date')
            ->orderBy('visit_time')
            ->paginate(20)
            ->appends($request->query());

        $stats = [
            'today' => PatientVisit::appointments()->whereDate('visit_date', $today)->where('status', '!=', 'cancelled')->count(),
            'scheduled' => PatientVisit::appointments()->where('status', 'scheduled')->count(),
            'pending_today' => PatientVisit::appointments()->whereDate('visit_date', $today)->where('status', 'pending')->count(),
            'cancelled' => PatientVisit::appointments()->where('status', 'cancelled')->count(),
        ];

        $calendarCounts = PatientVisit::appointments()
            ->where('status', '!=', 'cancelled')
            ->whereDate('visit_date', '>=', $today->copy()->startOfMonth())
            ->whereDate('visit_date', '<=', $today->copy()->endOfMonth()->addMonths(2))
            ->selectRaw('visit_date, COUNT(*) as total')
            ->groupBy('visit_date')
            ->pluck('total', 'visit_date')
            ->mapWithKeys(fn ($count, $date) => [Carbon::parse($date)->format('Y-m-d') => $count])
            ->all();

        $todayTimeline = PatientVisit::appointments()
            ->with('patient')
            ->whereDate('visit_date', $today)
            ->where('status', '!=', 'cancelled')
            ->orderBy('visit_time')
            ->get();

        return view('appointments', compact('appointments', 'stats', 'calendarCounts', 'todayTimeline'));
    }

    public function create(): View
    {
        $services = Service::orderBy('name')->get(['id', 'name']);
        $recentPatients = Patient::latest('created_at')->limit(5)->get(['id', 'patient_code', 'first_name', 'last_name']);

        $todayStats = [
            'total' => PatientVisit::appointments()->whereDate('visit_date', today())->where('status', '!=', 'cancelled')->count(),
            'scheduled' => PatientVisit::appointments()->whereDate('visit_date', today())->where('status', 'scheduled')->count(),
            'checked_in' => PatientVisit::appointments()->whereDate('visit_date', today())->where('status', 'pending')->count(),
        ];

        return view('appointments.add', compact('services', 'recentPatients', 'todayStats'));
    }

    public function store(StoreAppointmentRequest $request)
    {
        $isAjax = $request->ajax() || $request->wantsJson();

        try {
            $data = $request->validated();
            $data['user_id'] = auth()->id();
            $data['attended_by'] = auth()->user()->name ?? 'System';
            $data['status'] = 'scheduled';
            $data['created_at'] = now();

            if ($this->hasSchedulingConflict($data['visit_date'], $data['visit_time'], $data['practitioner'])) {
                $message = 'This practitioner already has an appointment at the selected date and time.';

                if ($isAjax) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }

                return redirect()->back()->withInput()->with('error', $message);
            }

            $visit = PatientVisit::create($data);

            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'message' => 'Appointment scheduled successfully.',
                    'visit_id' => $visit->id,
                ]);
            }

            return redirect()->route('appointments')->with('success', 'Appointment scheduled successfully.');
        } catch (\Throwable $e) {
            \Log::error('Error scheduling appointment: ' . $e->getMessage());

            if ($isAjax) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }

            return redirect()->back()->withInput()->with('error', 'Could not schedule appointment.');
        }
    }

    public function checkIn($id)
    {
        $visit = PatientVisit::appointments()->findOrFail($id);

        if ($visit->status === 'cancelled') {
            return redirect()->route('appointments')->with('error', 'Cancelled appointments cannot be checked in.');
        }

        if ($visit->status === 'completed') {
            return redirect()->route('visits.show', $visit->id);
        }

        $visit->update(['status' => 'pending']);

        return redirect()
            ->route('visits.edit', $visit->id)
            ->with('success', 'Patient checked in. Complete vitals and services to finish the visit.');
    }

    public function cancel($id)
    {
        $visit = PatientVisit::appointments()->findOrFail($id);

        if ($visit->status === 'completed') {
            return redirect()->route('appointments')->with('error', 'Completed visits cannot be cancelled from appointments.');
        }

        $visit->update(['status' => 'cancelled']);

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Appointment cancelled.']);
        }

        return redirect()->route('appointments')->with('success', 'Appointment cancelled.');
    }

    public function availability(Request $request): JsonResponse
    {
        $request->validate([
            'visit_date' => ['required', 'date'],
            'visit_time' => ['nullable'],
            'practitioner' => ['required', 'array', 'min:1'],
        ]);

        $conflict = $this->hasSchedulingConflict(
            $request->visit_date,
            $request->visit_time,
            $request->practitioner,
            $request->integer('exclude_id')
        );

        return response()->json([
            'available' => !$conflict,
            'message' => $conflict
                ? 'This practitioner already has an appointment at the selected date and time.'
                : 'Time slot is available.',
        ]);
    }

    protected function hasSchedulingConflict(string $date, ?string $time, array $practitioners, ?int $excludeId = null): bool
    {
        if (!$time) {
            return false;
        }

        $query = PatientVisit::appointments()
            ->where('status', 'scheduled')
            ->whereDate('visit_date', $date)
            ->whereTime('visit_time', '=', $time);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        foreach ($query->get() as $existing) {
            $existingPractitioners = $existing->practitioner;
            if (array_intersect($practitioners, $existingPractitioners)) {
                return true;
            }
        }

        return false;
    }
}
