@extends('layouts.admin')
@section('title','Manage Jadwal Operasi')
@push('styles')
<style>
  .manage-head {
    background: linear-gradient(135deg, #1b7a5a, #0e4e37);
    color: #fff;
    border-radius: 14px;
    padding: 18px 20px;
    box-shadow: 0 8px 22px rgba(27, 122, 90, .14);
  }

  .date-nav .btn {
    min-width: 120px
  }

  .schedule-card {
    border: 1px solid #dcece3;
    border-radius: 12px;
    overflow: hidden
  }

  .schedule-card thead th {
    background: #1b7a5a;
    color: #fff;
    white-space: nowrap
  }

  .schedule-card tbody tr:hover {
    background: #f4f9f6
  }

  .status-dot {
    display: inline-flex;
    align-items: center;
    gap: 6px
  }

  .dot {
    width: 9px;
    height: 9px;
    border-radius: 50%;
    display: inline-block
  }

  .dot-green {
    background: #16a34a
  }

  .dot-red {
    background: #dc2626
  }

  .dot-gray {
    background: #64748b
  }

  .ok-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 56px;
    padding: .35rem .55rem;
    border-radius: 7px;
    color: #fff;
    font-weight: 700;
    line-height: 1
  }

  .ok1-badge {
    background: #1284df
  }

  .ok2-badge {
    background: #0bb52e
  }

  .ok3-badge {
    background: #8b22c9
  }

  .date-label {
    text-transform: capitalize
  }

  .empty-box {
    padding: 55px 20px;
    text-align: center;
    color: #6c757d
  }

  @media(max-width:768px) {
    .date-nav .btn {
      min-width: auto;
      flex: 1
    }

    .schedule-card {
      font-size: .85rem
    }

    .schedule-card th,
    .schedule-card td {
      white-space: nowrap
    }

    .table-responsive {
      overflow-x: auto
    }
  }
</style>
@endpush
@section('content')
<div class="manage-head mb-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
  <div>
    <div class="small opacity-75">DATA TERSIMPAN PER TANGGAL</div>
    <h4 class="mb-1 date-label">{{ \Carbon\Carbon::parse($date)->locale('id')->translatedFormat('l, d F Y') }}</h4>
    <div class="opacity-75">{{ $schedules->count() }} jadwal operasi pada tanggal ini</div>
  </div>
  <div class="d-flex gap-2 flex-wrap">
    @if(auth()->user()->canAccess('schedule.export'))<a class="btn btn-light" href="{{ route('admin.schedules.export',['date'=>$date]) }}"><i class="bi bi-file-earmark-excel me-1"></i> Excel</a>@endif
    @if(auth()->user()->canAccess('schedule.manage'))<a class="btn btn-warning" href="{{ route('admin.schedules.create',['date'=>$date]) }}"><i class="bi bi-plus-lg me-1"></i> Jadwal Baru</a>@endif
  </div>
</div>

<div class="card p-3 mb-3">
  <div class="row g-2 align-items-end">
    <div class="col-lg-4"><label class="form-label fw-semibold">Pilih tanggal jadwal</label>
      <form id="dateForm"><input type="date" name="date" value="{{ $date }}" class="form-control" onchange="this.form.submit()"></form>
    </div>
    <div class="col-lg-8">
      <div class="date-nav d-flex gap-2 justify-content-lg-end flex-wrap">
        <a class="btn btn-outline-secondary" href="{{ route('admin.schedules.manage',['date'=>$previous]) }}">← Sebelumnya</a>
        <a class="btn btn-outline-primary" href="{{ route('admin.schedules.manage',['date'=>$today]) }}">Hari Ini</a>
        <a class="btn btn-outline-secondary" href="{{ route('admin.schedules.manage',['date'=>$next]) }}">Berikutnya →</a>
      </div>
    </div>
  </div>
</div>

<div class="card schedule-card">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead>
        <tr>
          <th>No</th>
          <th>Jam</th>
          <th>OK</th>
          <th>Pasien</th>
          <th>JK</th>
          <th>Diagnosa</th>
          <th>Tindakan</th>
          <th>Operator</th>
          <th>Anestesi</th>
          <th>Tim Bedah</th>
          <th>CITO</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($schedules as $i=>$s)
        <tr>
          <td class="fw-bold">{{ $i+1 }}</td>
          <td class="fw-bold">{{ substr($s->operation_time,0,5) }}</td>
          <td><span class="ok-badge {{ $s->ok_level==='OK 3'?'ok3-badge':($s->ok_level==='OK 2'?'ok2-badge':'ok1-badge') }}">{{ $s->ok_level }}</span></td>
          <td class="fw-semibold">{{ $s->patient?->name }}</td>
          <td>{{ $s->patient?->gender }}</td>
          <td>{{ $s->diagnosis ?: '-' }}</td>
          <td>{{ $s->procedure }}</td>
          <td>{{ $s->operator?->name ?: '-' }}</td>
          <td>{{ $s->anesthesia?->name ?: '-' }}</td>
          <td>{{ $s->surgical_team_names ?: '-' }}</td>
          <td>@if($s->cito)<span class="badge bg-danger">CITO</span>@else-@endif</td>
          <td>@if($s->status==='Terjadwal')<span class="status-dot"><span class="dot dot-green"></span>Terjadwal</span>@elseif($s->status==='Batal')<span class="status-dot"><span class="dot dot-red"></span>Batal</span>@else<span class="status-dot"><span class="dot dot-gray"></span>Selesai</span>@endif</td>
          <td class="text-nowrap">@if(auth()->user()->canAccess('schedule.manage'))<a class="btn btn-sm btn-outline-primary" href="{{ route('admin.schedules.edit',$s) }}?date={{ $date }}">Edit</a>
            <form class="d-inline" method="POST" action="{{ route('admin.schedules.destroy',$s) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus jadwal ini?')">Hapus</button></form>@endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="13">
            <div class="empty-box"><i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
              <h5>Tidak ada jadwal operasi</h5>
              <div>Belum ada jadwal tersimpan untuk <b>{{ \Carbon\Carbon::parse($date)->locale('id')->translatedFormat('d F Y') }}</b>.</div>@if(auth()->user()->canAccess('schedule.manage'))<a class="btn btn-rs mt-3" href="{{ route('admin.schedules.create',['date'=>$date]) }}">+ Tambah Jadwal</a>@endif
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2 small text-muted"><span>Data pada halaman ini diambil langsung dari database berdasarkan tanggal yang dipilih.</span><span>Gunakan ← Sebelumnya / Berikutnya → untuk mengecek tanggal lain.</span></div>
@endsection