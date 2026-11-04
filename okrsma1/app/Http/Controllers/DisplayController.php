<?php

namespace App\Http\Controllers;

use App\Models\SurgerySchedule;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DisplayController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->get('date', now('Asia/Jakarta')->toDateString());
        return view('display.index', compact('date'));
    }

    public function events(Request $request)
    {
        $date = $request->get('date', now('Asia/Jakarta')->toDateString());

        $schedules = SurgerySchedule::with(['patient', 'operator', 'anesthesia', 'surgicalTeams'])
            ->whereDate('operation_date', $date)
            ->orderBy('operation_time')
            ->get();

        $shifts = Shift::where('active', true)
            ->with(['assignments' => function ($q) use ($date) {
                $q->whereDate('shift_date', $date);
            }])
            ->orderBy('id')
            ->get();

        $shiftsData = $shifts->map(function ($shift) {
            $assignments = $shift->assignments;

            $members = $assignments->filter(function ($a) {
                $role = strtolower(trim((string) $a->staff_role));
                return $role === 'dinas' || $role === '';
            })->pluck('staff_name')->values()->all();

            $rr = $assignments->filter(function ($a) {
                return strtolower(trim((string) $a->staff_role)) === 'rr';
            })->pluck('staff_name')->values()->all();

            $oncall = $assignments->filter(function ($a) {
                $role = strtolower(trim((string) $a->staff_role));
                return $role === 'on call' || $role === 'oncall' || $role === 'on-call';
            })->pluck('staff_name')->values()->all();

            $startTime = $shift->start_time ? substr($shift->start_time, 0, 5) : '';
            $endTime = $shift->end_time ? substr($shift->end_time, 0, 5) : '';

            return [
                'id'         => $shift->id,
                'name'       => $shift->name,
                'start_time' => $startTime,
                'end_time'   => $endTime,
                'color'      => $shift->color,
                'members'    => $members,
                'rr'         => $rr,
                'oncall'     => $oncall,
                'note'       => $shift->note ?? '',
            ];
        });

        return response()->json([
            'date'      => $date,
            'schedules' => $schedules,
            'shifts'    => $shiftsData,
        ]);
    }
}
