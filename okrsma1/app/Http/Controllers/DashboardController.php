<?php

namespace App\Http\Controllers;

use App\Models\Anesthesia;
use App\Models\Operator;
use App\Models\Patient;
use App\Models\SurgerySchedule;
use App\Models\SurgicalTeam;
use App\Models\ShiftAssignment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = now('Asia/Jakarta')->toDateString();
        $todayCarbon = Carbon::parse($today, 'Asia/Jakarta');
        $start = $todayCarbon->copy()->subDays(29)->startOfDay();
        $end = $todayCarbon->copy()->endOfDay();

        $daily = SurgerySchedule::whereBetween('operation_date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('operation_date, COUNT(*) total')
            ->groupBy('operation_date')
            ->pluck('total', 'operation_date');

        $chartLabels = [];
        $chartValues = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = $todayCarbon->copy()->subDays($i);
            $chartLabels[] = $date->format('d/m');
            $chartValues[] = (int) ($daily[$date->toDateString()] ?? 0);
        }

        $todaySchedules = SurgerySchedule::with(['patient', 'surgicalTeams'])
            ->whereDate('operation_date', $today)
            ->orderBy('operation_time')
            ->get();

        $active = $todaySchedules->filter(function ($schedule) {
            $start = Carbon::parse(
                $schedule->operation_date->format('Y-m-d') . ' ' . $schedule->operation_time,
                'Asia/Jakarta'
            );
            $end = $start->copy()->addMinutes(90);

            return $schedule->status === 'Terjadwal'
                && now('Asia/Jakarta')->gte($start)
                && now('Asia/Jakarta')->lt($end);
        });

        $weekStart = $todayCarbon->copy()->startOfWeek(Carbon::MONDAY);
        $monthStart = $todayCarbon->copy()->startOfMonth();

        $weeklyStats = $this->topStats($weekStart, $todayCarbon->copy()->endOfDay());
        $monthlyStats = $this->topStats($monthStart, $todayCarbon->copy()->endOfDay());

        $todayShift = ShiftAssignment::whereDate('shift_date', $today)->count();

        return view('admin.dashboard', [
            'today' => $today,
            'todayCount' => $todaySchedules->count(),
            'patientCount' => Patient::count(),
            'operatorCount' => Operator::where('active', true)->count(),
            'anesthesiaCount' => Anesthesia::where('active', true)->count(),
            'teamCount' => SurgicalTeam::where('active', true)->count(),
            'activeCount' => $active->count(),
            'completedCount' => $todaySchedules->where('status', 'Selesai')->count(),
            'shiftCount' => $todayShift,
            'chartLabels' => $chartLabels,
            'chartValues' => $chartValues,
            'todaySchedules' => $todaySchedules->take(6),
            'weeklyProcedureLabels' => $weeklyStats['procedureLabels'],
            'weeklyProcedureValues' => $weeklyStats['procedureValues'],
            'weeklyDiagnosisLabels' => $weeklyStats['diagnosisLabels'],
            'weeklyDiagnosisValues' => $weeklyStats['diagnosisValues'],
            'monthlyProcedureLabels' => $monthlyStats['procedureLabels'],
            'monthlyProcedureValues' => $monthlyStats['procedureValues'],
            'monthlyDiagnosisLabels' => $monthlyStats['diagnosisLabels'],
            'monthlyDiagnosisValues' => $monthlyStats['diagnosisValues'],
        ]);
    }

    private function topStats(Carbon $start, Carbon $end): array
    {
        $rows = SurgerySchedule::whereBetween('operation_date', [$start->toDateString(), $end->toDateString()])
            ->where('status', '!=', 'Batal')
            ->get(['procedure', 'diagnosis']);

        $procedures = $rows->map(fn($row) => trim((string) $row->procedure))
            ->filter()->countBy()->sortDesc()->take(5);

        $diagnoses = $rows->map(fn($row) => trim((string) $row->diagnosis))
            ->filter()->countBy()->sortDesc()->take(5);

        return [
            'procedureLabels' => $procedures->keys()->values()->all(),
            'procedureValues' => $procedures->values()->map(fn($value) => (int) $value)->all(),
            'diagnosisLabels' => $diagnoses->keys()->values()->all(),
            'diagnosisValues' => $diagnoses->values()->map(fn($value) => (int) $value)->all(),
        ];
    }
}
