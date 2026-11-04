@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="card p-3 mb-4 quick-actions-card">
    <div class="d-flex align-items-center gap-2 mb-3"><div class="quick-icon"><i class="bi bi-lightning-charge-fill"></i></div><div><h5 class="mb-0">Tombol Cepat</h5><small class="text-muted">Akses fungsi yang paling sering digunakan.</small></div></div>
    <div class="row g-2">
        @if(auth()->user()->canAccess('schedule.manage'))<div class="col-md-4"><a class="quick-action action-click" href="{{ route('admin.schedules.create', ['date' => $today]) }}"><span class="quick-action-icon"><i class="bi bi-calendar-plus"></i></span><span><b>Tambah Jadwal Operasi</b><small>Buat jadwal baru</small></span><i class="bi bi-chevron-right ms-auto"></i></a></div>@endif
        @if(auth()->user()->canAccess('shift.manage'))<div class="col-md-4"><a class="quick-action action-click" href="{{ route('admin.shifts.index', ['date' => $today]) }}"><span class="quick-action-icon"><i class="bi bi-clock-history"></i></span><span><b>Atur Shift</b><small>Kelola petugas</small></span><i class="bi bi-chevron-right ms-auto"></i></a></div>@endif
        @if(auth()->user()->canAccess('display.view'))<div class="col-md-4"><a class="quick-action action-click" target="_blank" href="{{ route('admin.display') }}"><span class="quick-action-icon"><i class="bi bi-tv"></i></span><span><b>Display TV</b><small>Lihat jadwal di layar</small></span><i class="bi bi-chevron-right ms-auto"></i></a></div>@endif
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3"><div class="card stat p-3 h-100"><div class="text-muted">Operasi Hari Ini</div><div class="display-6 fw-bold">{{ $todayCount }}</div></div></div>
    <div class="col-6 col-xl-3"><div class="card stat p-3 h-100"><div class="text-muted">Sedang Operasi</div><div class="display-6 fw-bold text-danger">{{ $activeCount }}</div></div></div>
    <div class="col-6 col-xl-3"><div class="card stat p-3 h-100"><div class="text-muted">Selesai</div><div class="display-6 fw-bold text-success">{{ $completedCount }}</div></div></div>
    <div class="col-6 col-xl-3"><div class="card stat p-3 h-100"><div class="text-muted">Petugas Shift</div><div class="display-6 fw-bold">{{ $shiftCount }}</div></div></div>
</div>

<div class="card p-4 mb-4 dashboard-chart-card">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div><h4 class="mb-1"><i class="bi bi-bar-chart-line me-2"></i>Grafik Operasi</h4><small class="text-muted" id="operationChartSubtitle">Tren jumlah operasi 7 hari terakhir.</small></div>
        <div class="btn-group" role="group" aria-label="Periode grafik operasi">
            <button type="button" class="btn btn-primary period-btn active" data-period="7">7 Hari</button>
            <button type="button" class="btn btn-outline-primary period-btn" data-period="30">1 Bulan</button>
            <a class="btn btn-rs ms-2" href="{{ route('admin.schedules.index', ['date' => $today]) }}"><i class="bi bi-calendar3"></i> Jadwal</a>
        </div>
    </div>
    <div style="height:300px"><canvas id="operationChart"></canvas></div>
</div>

<div class="card p-4 mb-4 analytics-card">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div><h4 class="mb-1"><i class="bi bi-pie-chart-fill me-2"></i>Analisis Operasi</h4><small class="text-muted">5 tindakan dan diagnosa terbanyak. Data diambil langsung dari jadwal tersimpan.</small></div>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-primary active analysis-btn" data-period="week">Minggu</button>
            <button type="button" class="btn btn-outline-primary analysis-btn" data-period="month">Bulan</button>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-xl-6"><div class="analytics-panel"><div class="analytics-title"><i class="bi bi-scissors"></i> Tindakan Terbanyak</div><div style="height:300px"><canvas id="procedureChart"></canvas></div><div id="procedureEmpty" class="chart-empty d-none">Belum ada data tindakan.</div></div></div>
        <div class="col-xl-6"><div class="analytics-panel"><div class="analytics-title"><i class="bi bi-clipboard2-pulse"></i> Diagnosa Terbanyak</div><div style="height:300px"><canvas id="diagnosisChart"></canvas></div><div id="diagnosisEmpty" class="chart-empty d-none">Belum ada data diagnosa.</div></div></div>
    </div>
</div>

<div class="card p-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div><h4 class="mb-1"><i class="bi bi-calendar-check me-2"></i>Jadwal Hari Ini</h4><small class="text-muted">Urut berdasarkan jam operasi.</small></div>
        <div class="d-flex gap-2"><span class="badge rounded-pill bg-primary align-self-center">{{ $todayCount }} jadwal</span><a href="{{ route('admin.schedules.manage', ['date' => $today]) }}" class="btn btn-outline-primary"><i class="bi bi-list-check"></i> Kelola</a></div>
    </div>
    <div class="today-schedule-list">
    @forelse($todaySchedules as $s)
        <div class="today-row">
            <div class="today-time"><i class="bi bi-clock"></i><strong>{{ substr($s->operation_time,0,5) }}</strong></div>
            <div class="today-main"><strong>{{ $s->patient?->name ?? '-' }}</strong><span>{{ $s->procedure ?: 'Tindakan belum diisi' }}</span></div>
            <span class="badge {{ $s->ok_level==='OK 2'?'bg-success':($s->ok_level==='OK 3'?'bg-purple':'bg-primary') }}">{{ $s->ok_level }}</span>
            @if($s->cito)<span class="badge bg-danger">CITO</span>@endif
            <div class="today-team"><i class="bi bi-people-fill"></i> {{ $s->surgical_team_names ?: 'Tim belum dipilih' }}</div>
        </div>
    @empty
        <div class="text-center text-muted py-5"><i class="bi bi-calendar-x fs-2 d-block mb-2"></i>Belum ada jadwal operasi hari ini.</div>
    @endforelse
    </div>
</div>

@push('styles')
<style>
.dashboard-chart-card,.analytics-card{border:1px solid #dcece3}
.analytics-panel{position:relative;border:1px solid #dcece3;border-radius:16px;padding:16px;background:#f9fdfb}
.analytics-title{font-weight:700;margin-bottom:8px;color:#15553e}
.analytics-title i{margin-right:7px;color:#1b7a5a}
.chart-empty{position:absolute;inset:80px 20px 20px;display:grid;place-items:center;color:#8a98a3}
.today-schedule-list{display:flex;flex-direction:column;gap:8px}
.today-row{display:grid;grid-template-columns:110px minmax(180px,1.4fr) auto auto minmax(220px,1fr);align-items:center;gap:14px;padding:13px 15px;border:1px solid #dcece3;border-radius:13px;background:#fff;transition:.18s}
.today-row:hover{transform:translateY(-1px);box-shadow:0 7px 18px rgba(27,122,90,.08)}
.today-time{display:flex;align-items:center;gap:8px;color:#1b7a5a}
.today-main strong,.today-main span{display:block}
.today-main span{font-size:.82rem;color:#7b8993;margin-top:2px}
.today-team{font-size:.82rem;color:#60717c}
.bg-purple{background:#7c3aed!important}
.period-btn,.analysis-btn{transition:.18s}
.period-btn:hover,.analysis-btn:hover{transform:translateY(-1px)}

.quick-actions-card{background:linear-gradient(135deg,#fff,#f0f8f4);border:1px solid #d7e9df}
.quick-icon{width:40px;height:40px;border-radius:12px;display:grid;place-items:center;background:#1b7a5a;color:#fff;font-size:1.15rem}
.quick-action{min-height:74px;display:flex;align-items:center;gap:12px;padding:12px 14px;border:1px solid #d4e7dc;border-radius:14px;background:#fff;color:#18384d;text-decoration:none;transition:transform .18s ease,box-shadow .18s ease,border-color .18s ease}
.quick-action:hover{transform:translateY(-3px);box-shadow:0 10px 24px rgba(27,122,90,.12);border-color:#78c4a4;color:#1b7a5a}
.quick-action-icon{width:42px;height:42px;border-radius:11px;display:grid;place-items:center;background:#eaf6f1;color:#1b7a5a;font-size:1.15rem;transition:transform .25s ease}
.quick-action:hover .quick-action-icon{transform:rotate(-6deg) scale(1.08)}
.quick-action b,.quick-action small{display:block}
.quick-action small{color:#71808c;margin-top:2px}

@media(max-width:900px){.today-row{grid-template-columns:90px 1fr auto}.today-team{grid-column:2/-1}.today-row .badge:nth-of-type(2){grid-column:3}.today-main{min-width:0}}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const operationSeries = {
    7: {labels: @json(array_slice($chartLabels,-7)), values: @json(array_slice($chartValues,-7))},
    30: {labels: @json($chartLabels), values: @json($chartValues)}
};
const opCtx = document.getElementById('operationChart');
const opChart = new Chart(opCtx, {
    type: 'line',
    data: {
        labels: operationSeries[7].labels,
        datasets: [{
            label: 'Operasi',
            data: operationSeries[7].values,
            borderColor: '#1b7a5a',
            backgroundColor: 'rgba(27, 122, 90, 0.16)',
            pointBackgroundColor: '#1b7a5a',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointHoverBackgroundColor: '#ffffff',
            pointHoverBorderColor: '#1b7a5a',
            pointHoverBorderWidth: 2,
            borderWidth: 3,
            tension: .35,
            fill: true,
            pointRadius: 4,
            pointHoverRadius: 7
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {display: false},
            tooltip: {displayColors: false}
        },
        scales: {
            y: {beginAtZero: true, ticks: {precision: 0}},
            x: {grid: {display: false}}
        }
    }
});

document.querySelectorAll('.period-btn').forEach(btn => btn.addEventListener('click', () => {
    const n = btn.dataset.period;
    document.querySelectorAll('.period-btn').forEach(x => {
        x.classList.remove('active', 'btn-primary');
        x.classList.add('btn-outline-primary');
    });
    btn.classList.add('active', 'btn-primary');
    btn.classList.remove('btn-outline-primary');
    opChart.data.labels = operationSeries[n].labels;
    opChart.data.datasets[0].data = operationSeries[n].values;
    opChart.update();
    document.getElementById('operationChartSubtitle').textContent = n === '7' ? 'Tren jumlah operasi 7 hari terakhir.' : 'Tren jumlah operasi 30 hari terakhir.';
}));

const analysis = {
    week: {
        procedure: {l: @json($weeklyProcedureLabels), v: @json($weeklyProcedureValues)},
        diagnosis: {l: @json($weeklyDiagnosisLabels), v: @json($weeklyDiagnosisValues)}
    },
    month: {
        procedure: {l: @json($monthlyProcedureLabels), v: @json($monthlyProcedureValues)},
        diagnosis: {l: @json($monthlyDiagnosisLabels), v: @json($monthlyDiagnosisValues)}
    }
};

function makeRankChart(id, data, label) {
    return new Chart(document.getElementById(id), {
        type: 'bar',
        data: {
            labels: data.l,
            datasets: [{
                label,
                data: data.v,
                backgroundColor: 'rgba(27, 122, 90, 0.8)',
                borderColor: '#1b7a5a',
                hoverBackgroundColor: '#145a43',
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {legend: {display: false}, tooltip: {displayColors: false}},
            scales: {
                x: {beginAtZero: true, ticks: {precision: 0}},
                y: {grid: {display: false}, ticks: {autoSkip: false}}
            }
        }
    });
}

const pc = makeRankChart('procedureChart', analysis.week.procedure, 'Jumlah tindakan');
const dc = makeRankChart('diagnosisChart', analysis.week.diagnosis, 'Jumlah diagnosa');

function updateAnalysis(period) {
    [['procedure', pc], ['diagnosis', dc]].forEach(([k, c]) => {
        const d = analysis[period][k];
        c.data.labels = d.l;
        c.data.datasets[0].data = d.v;
        c.update();
        document.getElementById(k + 'Empty').classList.toggle('d-none', d.v.length > 0);
    });
}

updateAnalysis('week');

document.querySelectorAll('.analysis-btn').forEach(btn => btn.addEventListener('click', () => {
    document.querySelectorAll('.analysis-btn').forEach(x => {
        x.classList.remove('active', 'btn-primary');
        x.classList.add('btn-outline-primary');
    });
    btn.classList.add('active', 'btn-primary');
    btn.classList.remove('btn-outline-primary');
    updateAnalysis(btn.dataset.period);
}));
</script>
@endpush
@endsection
