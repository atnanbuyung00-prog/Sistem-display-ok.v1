@extends('layouts.admin')
@section('title','Jadwal Operasi')
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@7.1.0/index.global.min.css">
<style>
    #calendar {
        min-height: 420px
    }

    .fc .fc-button-primary {
        background: #1b7a5a;
        border-color: #1b7a5a
    }

    .fc .fc-event {
        cursor: default
    }

    .bg-purple {
        background: #9333ea !important
    }

    .schedule-toolbar {
        position: sticky;
        top: 70px;
        z-index: 10
    }

    .table-wrap {
        max-height: 620px;
        overflow: auto
    }

    .table-wrap thead th {
        position: sticky;
        top: 0;
        z-index: 2
    }

    .quick-add {
        background: linear-gradient(135deg, #1b7a5a, #0e4e37);
        color: #fff;
        border-radius: 14px;
        padding: 18px 20px;
        box-shadow: 0 8px 22px rgba(27, 122, 90, .14)
    }

    .input-animated {
        transition: box-shadow .2s ease, transform .2s ease, border-color .2s ease
    }

    .input-animated:focus {
        transform: translateY(-1px);
        box-shadow: 0 0 0 .2rem rgba(27, 122, 90, .15);
        border-color: #1b7a5a
    }
</style>
@endpush
@section('content')
<div class="quick-add mb-3">
    <div class="small opacity-75">INPUT JADWAL OPERASI</div>
    <h4 class="mb-1">Tambah Jadwal Operasi</h4>
    <div class="opacity-75">Pilih tanggal sekali di bawah ini. Semua jadwal yang Anda input setelahnya otomatis menggunakan tanggal tersebut.</div>
</div>
<div class="card p-3 mb-3 schedule-toolbar">
    <div class="row g-2 align-items-end">
        <div class="col-md-4"><label class="form-label fw-semibold mb-1">Tanggal Jadwal</label><input type="date" id="schedule_date" value="{{ $date }}" min="{{ now('Asia/Jakarta')->toDateString() }}" class="form-control"></div>
        <div class="col-md-8 d-flex gap-2 flex-wrap"><button type="button" class="btn btn-primary date-quick" data-offset="0">Hari Ini</button><button type="button" class="btn btn-outline-primary date-quick" data-offset="1">Besok</button><button type="button" class="btn btn-outline-primary date-quick" data-offset="2">Lusa</button><button type="button" class="btn btn-outline-primary date-quick" data-offset="3">3 Hari Lagi</button></div>
    </div>
</div>
<div class="card p-4 mb-4">
    <form method="POST" id="scheduleForm" action="{{ route('admin.schedules.store') }}">@csrf @php $schedule=new \App\Models\SurgerySchedule(); @endphp @include('admin.schedules._form_fields')</form>
</div>
<div class="card p-3 mb-4">
    <div class="mb-3">
        <h5 class="mb-1"><i class="bi bi-table me-2"></i>Jadwal {{ \Carbon\Carbon::parse($date)->locale('id')->translatedFormat('l, d F Y') }}</h5><small class="text-muted">{{ $schedules->count() }} jadwal</small>
    </div>
    <div class="table-wrap">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jam</th>
                    <th>OK</th>
                    <th>Pasien</th>
                    <th>No. RM</th>
                    <th>Umur</th>
                    <th>JK</th>
                    <th>Diagnosa</th>
                    <th>Tindakan</th>
                    <th>Operator</th>
                    <th>Anestesi</th>
                    <th>Tim Bedah</th>
                    <th>Cito</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>@forelse($schedules as $i=>$s)<tr>
                    <td class="fw-bold">{{ $i+1 }}</td>
                    <td class="fw-bold">{{ substr($s->operation_time,0,5) }}</td>
                    <td><span class="badge {{ $s->ok_level==='OK 2'?'bg-success':($s->ok_level==='OK 3'?'bg-purple':'bg-primary') }}">{{ $s->ok_level }}</span></td>
                    <td>{{ $s->patient?->name }}</td>
                    <td>{{ $s->patient?->medical_record_no ?: '-' }}</td>
                    <td>{{ $s->patient?->age ?: '-' }}</td>
                    <td>{{ $s->patient?->gender==='P'?'Perempuan':'Laki-laki' }}</td>
                    <td>{{ $s->diagnosis ?: '-' }}</td>
                    <td>{{ $s->procedure }}</td>
                    <td>{{ $s->operator?->name ?: '-' }}</td>
                    <td>{{ $s->anesthesia?->name ?: '-' }}</td>
                    <td>{{ $s->surgical_team_names ?: '-' }}</td>
                    <td>@if($s->cito)<span class="badge bg-danger">CITO</span>@else-@endif</td>
                    <td>{{ $s->status }}</td>
                </tr>@empty<tr>
                    <td colspan="14" class="text-center py-4 text-muted">Belum ada jadwal pada tanggal ini.</td>
                </tr>@endforelse</tbody>
        </table>
    </div>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@7.1.0/index.global.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const dateInput = document.getElementById('schedule_date'),
            hidden = document.getElementById('operation_date');

        function syncDate(v) {
            if (!v) return;
            hidden.value = v;
            const url = new URL(window.location.href);
            url.searchParams.set('date', v);
            window.location.href = url.toString();
        }
        document.querySelectorAll('.date-quick').forEach(btn => btn.addEventListener('click', () => {
            const d = new Date();
            d.setHours(0, 0, 0, 0);
            d.setDate(d.getDate() + Number(btn.dataset.offset));
            const v = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
            syncDate(v);
        }));
        dateInput.addEventListener('change', () => syncDate(dateInput.value));
        const el = document.getElementById('calendar');
        if (el) {
            const cal = new FullCalendar.Calendar(el, {
                locale: 'id',
                initialView: 'dayGridMonth',
                initialDate: '{{ $date }}',
                height: 'auto',
                editable: false,
                eventDurationEditable: false,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listWeek'
                },
                events: '{{ route('
                admin.schedules.calendar.events ') }}',
                eventClick: info => info.jsEvent.preventDefault()
            });
            cal.render();
        }
    });
</script>@endpush