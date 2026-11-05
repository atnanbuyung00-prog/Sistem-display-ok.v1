@extends('layouts.admin')

@section('title', 'Rekap Aktivitas IT (Audit Trail)')

@push('styles')
<style>
    .stat-card {
        border-radius: 14px;
        transition: transform .18s ease, box-shadow .18s ease;
        border: 1px solid rgba(0, 0, 0, .05);
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(0, 0, 0, .08) !important;
    }
    .stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.45rem;
        flex-shrink: 0;
    }
    .filter-card {
        border-radius: 14px;
        border: 1px solid rgba(0, 0, 0, .05);
    }
    .filter-card .form-control,
    .filter-card .form-select,
    .filter-card .btn {
        height: 38px;
        font-size: .875rem;
        border-radius: 8px;
    }
    .table-card {
        border-radius: 14px;
        border: 1px solid rgba(0, 0, 0, .05);
        overflow: hidden;
    }
    .table-activity thead th {
        background: #1b7a5a !important;
        color: #ffffff !important;
        font-weight: 600;
        font-size: .84rem;
        letter-spacing: .3px;
        padding: .75rem .85rem;
        border: 0;
        vertical-align: middle;
    }
    .table-activity tbody td {
        padding: .7rem .85rem;
        vertical-align: middle;
        font-size: .875rem;
        border-bottom: 1px solid #edf2f0;
    }
    .table-activity tbody tr:hover {
        background-color: #f6faf8;
    }
    .badge-action {
        width: 82px;
        display: inline-block;
        text-align: center;
        font-weight: 600;
        font-size: 11px;
        padding: 5px 0;
        border-radius: 6px;
        letter-spacing: .3px;
    }
    .per-page-pill {
        border-radius: 20px;
        padding: .25rem .75rem;
        font-size: .8rem;
        font-weight: 600;
    }
    .pagination {
        gap: 4px;
        margin-bottom: 0;
    }
    .pagination .page-item .page-link {
        color: #1b7a5a;
        border-radius: 6px !important;
        border: 1px solid #dee2e6;
        padding: 0.35rem 0.75rem;
        font-weight: 500;
        font-size: 0.85rem;
        background-color: #ffffff;
        transition: all 0.15s ease-in-out;
    }
    .pagination .page-item.active .page-link {
        background-color: #1b7a5a !important;
        border-color: #1b7a5a !important;
        color: #ffffff !important;
        font-weight: 600;
        box-shadow: 0 2px 6px rgba(27, 122, 90, 0.25);
    }
    .pagination .page-item .page-link:hover {
        background-color: #e8f5f0;
        border-color: #1b7a5a;
        color: #1b7a5a;
    }
    .pagination .page-item.disabled .page-link {
        color: #adb5bd;
        background-color: #f8f9fa;
        border-color: #e9ecef;
    }
</style>
@endpush

@section('content')
<!-- Header Page -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h4 class="mb-1 fw-bold text-dark d-flex align-items-center gap-2">
            <span class="p-2 rounded-circle bg-success-subtle text-success d-inline-flex align-items-center justify-content-center" style="width:38px;height:38px">
                <i class="bi bi-shield-check fs-5"></i>
            </span>
            <span>Rekap Aktivitas IT & Pelacakan Data</span>
        </h4>
        <div class="text-muted small">Catatan riwayat siapa yang menginput, mengubah, dan menghapus jadwal operasi maupun data master untuk pelacakan kendala.</div>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('admin.activity_logs.index') }}" class="btn btn-outline-secondary btn-sm px-3" title="Segarkan dan kembalikan ke setelan awal">
            <i class="bi bi-arrow-clockwise me-1"></i>Reset
        </a>
        @if(auth()->user()->canAccess('backup.manage'))
        <a href="{{ route('admin.backups.index') }}" class="btn btn-rs btn-sm px-3 shadow-sm">
            <i class="bi bi-database-check me-1"></i>Ke Backup & Restore
        </a>
        @endif
    </div>
</div>

<!-- 4 Symmetrical Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm p-3 h-100 bg-white" style="border-top: 4px solid #1b7a5a !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-semibold" style="letter-spacing:.3px">Total Log Tercatat</div>
                    <div class="fs-4 fw-bold text-dark mt-1">{{ number_format($stats['total']) }}</div>
                    <small class="text-success fw-medium"><i class="bi bi-clock-history me-1"></i>Hari ini: {{ number_format($stats['today_total']) }}</small>
                </div>
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="bi bi-journal-text"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm p-3 h-100 bg-white" style="border-top: 4px solid #198754 !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-semibold" style="letter-spacing:.3px">Total Input / Tambah</div>
                    <div class="fs-4 fw-bold text-success mt-1">{{ number_format($stats['total_input']) }}</div>
                    <small class="text-muted">Data jadwal & master baru</small>
                </div>
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="bi bi-plus-circle"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm p-3 h-100 bg-white" style="border-top: 4px solid #e69500 !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-semibold" style="letter-spacing:.3px">Total Perubahan</div>
                    <div class="fs-4 fw-bold text-warning-emphasis mt-1">{{ number_format($stats['total_ubah']) }}</div>
                    <small class="text-muted">Perubahan data / pindah jadwal</small>
                </div>
                <div class="stat-icon bg-warning-subtle text-warning-emphasis">
                    <i class="bi bi-pencil-square"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm p-3 h-100 bg-white" style="border-top: 4px solid #dc3545 !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-semibold" style="letter-spacing:.3px">Total Hapus Data</div>
                    <div class="fs-4 fw-bold text-danger mt-1">{{ number_format($stats['total_hapus']) }}</div>
                    <small class="text-danger fw-semibold"><i class="bi bi-exclamation-triangle me-1"></i>Pelacakan penting IT</small>
                </div>
                <div class="stat-icon bg-danger-subtle text-danger">
                    <i class="bi bi-trash3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Symmetrical Filter Bar -->
<div class="card filter-card shadow-sm p-3 mb-4 bg-white">
    <form method="GET" action="{{ route('admin.activity_logs.index') }}" class="row g-2 align-items-end" id="filterForm">
        <!-- Cari Kata Kunci -->
        <div class="col-lg-3 col-md-6">
            <label class="form-label small fw-semibold text-muted mb-1"><i class="bi bi-search me-1"></i>Cari Kata Kunci / Pasien / Petugas</label>
            <div class="input-group">
                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" name="q" class="form-control border-start-0" placeholder="Ketik nama, RM, user, IP..." value="{{ request('q') }}" autocomplete="off">
            </div>
        </div>

        <!-- Jenis Aksi -->
        <div class="col-lg-2 col-md-3">
            <label class="form-label small fw-semibold text-muted mb-1"><i class="bi bi-tag me-1"></i>Jenis Aksi</label>
            <select name="action" class="form-select">
                <option value="">Semua Aksi</option>
                <option value="INPUT" @selected(request('action') === 'INPUT')>INPUT (Tambah)</option>
                <option value="UBAH" @selected(request('action') === 'UBAH')>UBAH (Edit / Pindah)</option>
                <option value="HAPUS" @selected(request('action') === 'HAPUS')>HAPUS (Delete)</option>
                <option value="SALIN" @selected(request('action') === 'SALIN')>SALIN (Copy)</option>
                <option value="RESTORE" @selected(request('action') === 'RESTORE')>RESTORE (Pulihkan)</option>
                <option value="BACKUP" @selected(request('action') === 'BACKUP')>BACKUP (Cadangkan)</option>
            </select>
        </div>

        <!-- Modul -->
        <div class="col-lg-2 col-md-3">
            <label class="form-label small fw-semibold text-muted mb-1"><i class="bi bi-layers me-1"></i>Modul</label>
            <select name="module" class="form-select">
                <option value="">Semua Modul</option>
                @foreach($modules as $m)
                <option value="{{ $m }}" @selected(request('module') === $m)>{{ $m }}</option>
                @endforeach
            </select>
        </div>

        <!-- Tanggal -->
        <div class="col-lg-2 col-md-4">
            <label class="form-label small fw-semibold text-muted mb-1"><i class="bi bi-calendar3 me-1"></i>Tanggal</label>
            <input type="date" name="date" class="form-control" value="{{ request('date') }}">
        </div>

        <!-- Tampilkan Log (10, 100, 1000) -->
        <div class="col-lg-1 col-md-2">
            <label class="form-label small fw-semibold text-muted mb-1"><i class="bi bi-list-ol me-1"></i>Tampilkan</label>
            <select name="per_page" class="form-select" onchange="document.getElementById('filterForm').submit()">
                <option value="10" @selected($perPage == 10)>10</option>
                <option value="25" @selected($perPage == 25)>25</option>
                <option value="50" @selected($perPage == 50)>50</option>
                <option value="100" @selected($perPage == 100)>100</option>
                <option value="500" @selected($perPage == 500)>500</option>
                <option value="1000" @selected($perPage == 1000)>1.000</option>
            </select>
        </div>

        <!-- Tombol Aksi Simetris -->
        <div class="col-lg-2 col-md-6 d-flex gap-2">
            <button type="submit" class="btn btn-rs flex-fill d-flex align-items-center justify-content-center gap-1 shadow-sm">
                <i class="bi bi-funnel"></i><span>Filter</span>
            </button>
            <a href="{{ route('admin.activity_logs.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="width: 44px;" title="Reset Semua Filter">
                <i class="bi bi-x-lg"></i>
            </a>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="card table-card shadow-sm p-4 bg-white">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 pb-2 border-bottom">
        <div>
            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <span>Daftar Rekap Aktivitas</span>
                <span class="badge rounded-pill bg-light text-dark border">{{ number_format($logs->total()) }} catatan</span>
            </h6>
        </div>

        <!-- Quick Per-Page Switcher (10, 100, 1000) -->
        <div class="d-flex align-items-center gap-2">
            <span class="small text-muted fw-semibold d-none d-sm-inline">Tampilkan:</span>
            <div class="btn-group btn-group-sm" role="group">
                @php($currentParams = request()->except('per_page', 'page'))
                <a href="{{ route('admin.activity_logs.index', array_merge($currentParams, ['per_page' => 10])) }}"
                   class="btn btn-outline-success {{ $perPage == 10 ? 'active' : '' }}">10</a>
                <a href="{{ route('admin.activity_logs.index', array_merge($currentParams, ['per_page' => 100])) }}"
                   class="btn btn-outline-success {{ $perPage == 100 ? 'active' : '' }}">100</a>
                <a href="{{ route('admin.activity_logs.index', array_merge($currentParams, ['per_page' => 1000])) }}"
                   class="btn btn-outline-success {{ $perPage == 1000 ? 'active' : '' }}">1.000</a>
            </div>
            <span class="small text-muted d-none d-md-inline">log / halaman</span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-activity align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 140px;">WAKTU</th>
                    <th style="width: 170px;">USER / PETUGAS</th>
                    <th style="width: 100px;" class="text-center">AKSI</th>
                    <th style="width: 120px;" class="text-center">MODUL</th>
                    <th>KETERANGAN AKTIVITAS</th>
                    <th style="width: 70px;" class="text-center">DETAIL</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <!-- Waktu -->
                    <td class="text-nowrap">
                        <div class="fw-semibold text-dark">{{ $log->created_at->format('d/m/Y') }}</div>
                        <small class="text-muted"><i class="bi bi-clock me-1"></i>{{ $log->created_at->format('H:i:s') }} WIB</small>
                    </td>

                    <!-- User / Petugas -->
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="rounded-circle bg-light border d-inline-flex align-items-center justify-content-center text-secondary fw-bold flex-shrink-0" style="width:32px;height:32px;font-size:12px">
                                {{ strtoupper(substr($log->user_name ?? 'U', 0, 1)) }}
                            </span>
                            <div class="overflow-hidden">
                                <div class="fw-semibold text-dark text-truncate small" style="max-width: 135px;" title="{{ $log->user_name }}">{{ $log->user_name ?? 'Anonim' }}</div>
                                <small class="text-muted d-block text-truncate font-monospace" style="font-size: 11px;">{{ '@' . ($log->user_username ?? 'system') }}</small>
                            </div>
                        </div>
                    </td>

                    <!-- Aksi -->
                    <td class="text-center">
                        @switch($log->action)
                            @case('INPUT')
                                <span class="badge text-bg-success badge-action"><i class="bi bi-plus-circle me-1"></i>INPUT</span>
                                @break
                            @case('UBAH')
                                <span class="badge text-bg-warning text-dark badge-action"><i class="bi bi-pencil me-1"></i>UBAH</span>
                                @break
                            @case('HAPUS')
                                <span class="badge text-bg-danger badge-action"><i class="bi bi-trash3-fill me-1"></i>HAPUS</span>
                                @break
                            @case('SALIN')
                                <span class="badge text-bg-info text-white badge-action"><i class="bi bi-copy me-1"></i>SALIN</span>
                                @break
                            @case('RESTORE')
                                <span class="badge text-bg-primary badge-action"><i class="bi bi-arrow-counterclockwise me-1"></i>RESTORE</span>
                                @break
                            @case('BACKUP')
                                <span class="badge text-bg-dark badge-action"><i class="bi bi-database me-1"></i>BACKUP</span>
                                @break
                            @default
                                <span class="badge text-bg-secondary badge-action">{{ $log->action }}</span>
                        @endswitch
                    </td>

                    <!-- Modul -->
                    <td class="text-center">
                        <span class="badge bg-light text-dark border px-2 py-1 small fw-normal">{{ $log->module }}</span>
                    </td>

                    <!-- Keterangan -->
                    <td>
                        <div class="text-break text-dark">{{ $log->description }}</div>
                        @if($log->patient_name)
                        <small class="text-muted d-block mt-1">
                            <span class="badge bg-light text-secondary border me-1"><i class="bi bi-person me-1"></i>Pasien: <b>{{ $log->patient_name }}</b></span>
                            @if($log->operation_date)
                            <span class="badge bg-light text-secondary border"><i class="bi bi-calendar-event me-1"></i>Tgl Operasi: {{ $log->operation_date->format('d/m/Y') }}</span>
                            @endif
                        </small>
                        @endif
                    </td>

                    <!-- Detail Modal Trigger -->
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-primary p-1 px-2 btn-detail"
                                data-bs-toggle="modal"
                                data-bs-target="#detailModal"
                                data-id="{{ $log->id }}"
                                data-action="{{ $log->action }}"
                                data-module="{{ $log->module }}"
                                data-user="{{ $log->user_name }} (@{{ $log->user_username }})"
                                data-time="{{ $log->created_at->format('d/m/Y H:i:s') }} WIB"
                                data-desc="{{ $log->description }}"
                                data-ip="{{ $log->ip_address }}"
                                data-ua="{{ $log->user_agent }}"
                                data-old='@json($log->old_values)'
                                data-new='@json($log->new_values)'
                                title="Lihat Snapshot Lengkap">
                            <i class="bi bi-search"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-journal-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                        <h6 class="fw-semibold">Belum ada catatan aktivitas</h6>
                        <small class="text-muted">Tidak ada log yang cocok dengan filter yang Anda tentukan.</small>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Symmetrical Table Footer -->
    <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="small text-muted">
            Menampilkan <b>{{ $logs->firstItem() ?? 0 }}</b> - <b>{{ $logs->lastItem() ?? 0 }}</b> dari <b>{{ number_format($logs->total()) }}</b> catatan
            <span class="mx-1">•</span>
            <span>Limit: <b>{{ $perPage }}</b> baris / halaman</span>
        </div>
        <div>
            {{ $logs->links() }}
        </div>
    </div>
</div>

<!-- Modal Detail Snapshot -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius:14px;overflow:hidden">
            <div class="modal-header text-white" id="modalHeader" style="background: #1b7a5a;">
                <h5 class="modal-title fs-6 fw-bold"><i class="bi bi-shield-check me-2"></i>Rincian Snapshot Pelacakan Aktivitas</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="card bg-light border-0 p-3 mb-3" style="border-radius: 10px;">
                    <div class="row g-2">
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Pelaku / Petugas:</small>
                            <span class="fw-bold text-dark" id="modalUser">-</span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Waktu Eksekusi:</small>
                            <span class="fw-bold text-dark" id="modalTime">-</span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Jenis Aksi & Modul:</small>
                            <span class="badge text-bg-primary me-1" id="modalAction">-</span>
                            <span class="badge bg-secondary" id="modalModule">-</span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">IP Address & Browser:</small>
                            <code class="small text-dark" id="modalIp">-</code>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark">Keterangan Aktivitas:</label>
                    <div class="p-3 border rounded bg-white text-dark small" id="modalDesc">-</div>
                </div>

                <!-- Snapshot Data View -->
                <div id="snapshotSection">
                    <label class="form-label fw-bold text-dark d-flex justify-content-between align-items-center">
                        <span>Snapshot Data (Data Tersimpan / Dihapus):</span>
                        <span class="badge bg-info text-white" id="snapshotBadge">Snapshot</span>
                    </label>

                    <div id="snapshotContainer" class="p-3 border rounded bg-light" style="max-height: 300px; overflow-y: auto; font-family: monospace; font-size: 0.85rem;">
                        <!-- Injected by JavaScript -->
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('detailModal');
        modal.addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;
            const action = btn.getAttribute('data-action');
            const module = btn.getAttribute('data-module');
            const user = btn.getAttribute('data-user');
            const time = btn.getAttribute('data-time');
            const desc = btn.getAttribute('data-desc');
            const ip = btn.getAttribute('data-ip') || '-';
            const ua = btn.getAttribute('data-ua') || '-';
            const oldValRaw = btn.getAttribute('data-old');
            const newValRaw = btn.getAttribute('data-new');

            document.getElementById('modalUser').textContent = user;
            document.getElementById('modalTime').textContent = time;
            document.getElementById('modalAction').textContent = action;
            document.getElementById('modalModule').textContent = module;
            document.getElementById('modalDesc').textContent = desc;
            document.getElementById('modalIp').textContent = ip + ' (' + ua.substring(0, 45) + '...)';

            const header = document.getElementById('modalHeader');
            if (action === 'HAPUS') {
                header.style.background = '#dc3545';
            } else if (action === 'INPUT') {
                header.style.background = '#198754';
            } else if (action === 'UBAH') {
                header.style.background = '#e69500';
            } else {
                header.style.background = '#1b7a5a';
            }

            let oldVal = null;
            let newVal = null;
            try { oldVal = oldValRaw ? JSON.parse(oldValRaw) : null; } catch(e){}
            try { newVal = newValRaw ? JSON.parse(newValRaw) : null; } catch(e){}

            const container = document.getElementById('snapshotContainer');
            const badge = document.getElementById('snapshotBadge');

            if (action === 'HAPUS') {
                badge.className = 'badge bg-danger';
                badge.textContent = 'Data yang Dihapus';
                container.innerHTML = oldVal
                    ? '<pre class="mb-0 text-danger-emphasis"></pre>'
                    : '<div class="text-muted">Tidak ada snapshot khusus.</div>';
                if (oldVal) container.querySelector('pre').textContent = JSON.stringify(oldVal, null, 2);
            } else if (action === 'INPUT') {
                badge.className = 'badge bg-success';
                badge.textContent = 'Data Baru yang Diinput';
                container.innerHTML = newVal
                    ? '<pre class="mb-0 text-success-emphasis"></pre>'
                    : '<div class="text-muted">Tidak ada snapshot khusus.</div>';
                if (newVal) container.querySelector('pre').textContent = JSON.stringify(newVal, null, 2);
            } else if (action === 'UBAH') {
                badge.className = 'badge bg-warning text-dark';
                badge.textContent = 'Perubahan (Lama & Baru)';
                let html = '<div class="row g-2">';
                html += '<div class="col-md-6"><b>Data Sebelum:</b><pre class="p-2 border rounded bg-white text-danger mt-1" style="font-size:11px"></pre></div>';
                html += '<div class="col-md-6"><b>Data Sesudah:</b><pre class="p-2 border rounded bg-white text-success mt-1" style="font-size:11px"></pre></div>';
                html += '</div>';
                container.innerHTML = html;
                const [before, after] = container.querySelectorAll('pre');
                before.textContent = JSON.stringify(oldVal || {}, null, 2);
                after.textContent = JSON.stringify(newVal || {}, null, 2);
            } else {
                badge.className = 'badge bg-info text-white';
                badge.textContent = 'Snapshot Data';
                container.innerHTML = '<pre class="mb-0"></pre>';
                container.querySelector('pre').textContent = JSON.stringify(newVal || oldVal || {}, null, 2);
            }
        });
    });
</script>
@endpush
