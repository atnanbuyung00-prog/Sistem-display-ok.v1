@extends('layouts.admin')

@section('title', 'Backup & Restore Database')

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
    .table-card {
        border-radius: 14px;
        border: 1px solid rgba(0, 0, 0, .05);
        overflow: hidden;
    }
    .table-backup thead th {
        background: #1b7a5a !important;
        color: #ffffff !important;
        font-weight: 600;
        font-size: .84rem;
        letter-spacing: .3px;
        padding: .75rem .85rem;
        border: 0;
        vertical-align: middle;
    }
    .table-backup tbody td {
        padding: .7rem .85rem;
        vertical-align: middle;
        font-size: .875rem;
        border-bottom: 1px solid #edf2f0;
    }
    .table-backup tbody tr:hover {
        background-color: #f6faf8;
    }
</style>
@endpush

@section('content')
<!-- Header Page -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h4 class="mb-1 fw-bold text-dark d-flex align-items-center gap-2">
            <span class="p-2 rounded-circle bg-success-subtle text-success d-inline-flex align-items-center justify-content-center" style="width:38px;height:38px">
                <i class="bi bi-database-check fs-5"></i>
            </span>
            <span>Cadangan & Pemulihan Data (Backup & Restore)</span>
        </h4>
        <div class="text-muted small">Buat cadangan database secara berkala atau pulihkan data saat terjadi kerusakan/crash sistem.</div>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('admin.activity_logs.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-shield-check me-1"></i>Rekap Aktivitas IT
        </a>
        <button type="button" class="btn btn-outline-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#uploadRestoreModal">
            <i class="bi bi-upload me-1"></i>Upload File .sql
        </button>
        <form method="POST" action="{{ route('admin.backups.store') }}" id="createBackupForm" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-rs btn-sm px-3 shadow-sm" id="btnCreateBackup">
                <i class="bi bi-cloud-arrow-down me-1"></i>Buat Backup Sekarang (.sql)
            </button>
        </form>
    </div>
</div>

<!-- 4 Symmetrical Health & Status Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm p-3 h-100 bg-white" style="border-top: 4px solid #1b7a5a !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-semibold" style="letter-spacing:.3px">Database Aktif</div>
                    <div class="fs-5 fw-bold text-dark mt-1 font-monospace">{{ $stats['database'] }}</div>
                    <small class="text-success fw-medium"><i class="bi bi-check-circle me-1"></i>Ukuran: <b>{{ $stats['formatted_size'] }}</b></small>
                </div>
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="bi bi-database"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm p-3 h-100 bg-white" style="border-top: 4px solid #0d6efd !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-semibold" style="letter-spacing:.3px">Total Jadwal Operasi</div>
                    <div class="fs-4 fw-bold text-primary mt-1">{{ number_format($stats['schedules_count']) }}</div>
                    <small class="text-muted">Jadwal operasi tercatat</small>
                </div>
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="bi bi-calendar3"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm p-3 h-100 bg-white" style="border-top: 4px solid #198754 !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-semibold" style="letter-spacing:.3px">Total Pasien</div>
                    <div class="fs-4 fw-bold text-success mt-1">{{ number_format($stats['patients_count']) }}</div>
                    <small class="text-muted">Data rekam medis</small>
                </div>
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm p-3 h-100 bg-white" style="border-top: 4px solid #6f42c1 !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-semibold" style="letter-spacing:.3px">File Cadangan</div>
                    <div class="fs-4 fw-bold mt-1" style="color:#6f42c1">{{ count($backups) }}</div>
                    <small class="text-muted">Siap dipulihkan kapan saja</small>
                </div>
                <div class="stat-icon" style="background: #f1eafd; color: #6f42c1;">
                    <i class="bi bi-archive"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Information Alert -->
<div class="alert alert-info border-0 shadow-sm d-flex gap-3 align-items-start mb-4" style="border-radius: 12px;">
    <i class="bi bi-info-circle-fill fs-4 text-info flex-shrink-0 mt-1"></i>
    <div>
        <div class="fw-bold mb-1">Panduan Pengembalian / Pemulihan Data (Restore):</div>
        <div class="small text-secondary">
            File cadangan (.sql) menyimpan seluruh struktur tabel dan data operasional (jadwal operasi, pasien, operator, tim bedah, shift, dan user).
            Jika terjadi kerusakan data, kesalahan input massal, atau sistem bermasalah, Anda dapat menekan tombol <b>Pulihkan (Restore)</b> pada salah satu file di bawah ini atau mengupload file <code>.sql</code> cadangan Anda.
        </div>
    </div>
</div>

<!-- Backups Table Card -->
<div class="card table-card shadow-sm p-4 bg-white">
    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
            <span>Daftar File Cadangan Database (.sql)</span>
            <span class="badge rounded-pill bg-light text-dark border">{{ count($backups) }} File Tersedia</span>
        </h6>
        <small class="text-muted font-monospace">Direktori: storage/app/backups/</small>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-backup align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 50px;" class="text-center">#</th>
                    <th>NAMA FILE CADANGAN</th>
                    <th style="width: 200px;">WAKTU PEMBUATAN</th>
                    <th style="width: 130px;" class="text-center">UKURAN FILE</th>
                    <th style="width: 240px;" class="text-center">AKSI PEMULIHAN & DOWNLOAD</th>
                </tr>
            </thead>
            <tbody>
                @forelse($backups as $index => $b)
                <tr>
                    <td class="text-center fw-semibold text-muted">{{ $index + 1 }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-filetype-sql fs-3 text-primary"></i>
                            <div>
                                <span class="fw-bold text-dark font-monospace">{{ $b['filename'] }}</span>
                                <small class="text-muted d-block">Snapshot Lengkap Database</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="fw-semibold text-dark">{{ $b['created_at']->format('d/m/Y') }}</div>
                        <small class="text-muted"><i class="bi bi-clock me-1"></i>{{ $b['created_at']->format('H:i:s') }} WIB</small>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark border font-monospace px-2 py-1">{{ $b['formatted_size'] }}</span>
                    </td>
                    <td class="text-center text-nowrap">
                        <!-- Restore Button -->
                        <button type="button" class="btn btn-sm btn-outline-danger me-1 btn-restore"
                                data-bs-toggle="modal"
                                data-bs-target="#confirmRestoreModal"
                                data-filename="{{ $b['filename'] }}"
                                data-time="{{ $b['created_at']->format('d/m/Y H:i') }} WIB"
                                data-size="{{ $b['formatted_size'] }}"
                                title="Kembalikan data ke cadangan ini">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Pulihkan
                        </button>

                        <!-- Download Button -->
                        <a href="{{ route('admin.backups.download', $b['filename']) }}" class="btn btn-sm btn-outline-primary me-1" title="Download file backup ke komputer">
                            <i class="bi bi-download"></i>
                        </a>

                        <!-- Delete Button -->
                        <form method="POST" action="{{ route('admin.backups.destroy', $b['filename']) }}" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus file backup {{ $b['filename'] }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-secondary" title="Hapus file ini">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="bi bi-database-exclamation fs-1 d-block mb-2 text-secondary opacity-50"></i>
                        <h6 class="fw-semibold">Belum ada file cadangan yang tersimpan</h6>
                        <small class="text-muted">Klik tombol <b>"Buat Backup Sekarang"</b> di kanan atas untuk membuat backup database pertama.</small>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Konfirmasi Restore -->
<div class="modal fade" id="confirmRestoreModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border-0 shadow-lg" method="POST" action="{{ route('admin.backups.restore') }}" id="restoreForm" style="border-radius:14px;overflow:hidden">
            @csrf
            <input type="hidden" name="filename" id="restoreFilename">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fs-6 fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Konfirmasi Pemulihan Data (Restore)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-warning border-0 small mb-3" style="border-radius: 8px;">
                    <i class="bi bi-exclamation-circle-fill me-1"></i> <b>PERHATIAN:</b> Pemulihan database akan menimpa data yang sedang berjalan saat ini dengan data dari file cadangan yang dipilih.
                </div>
                <p class="mb-2 text-dark">Anda akan memulihkan database ke titik cadangan berikut:</p>
                <div class="p-3 border rounded bg-light mb-3">
                    <div class="small text-muted">Nama File:</div>
                    <code class="fw-bold d-block text-dark mb-2" id="restoreNameDisplay">-</code>
                    <div class="row g-2 small">
                        <div class="col-6"><span class="text-muted">Waktu Backup:</span> <b id="restoreTimeDisplay">-</b></div>
                        <div class="col-6"><span class="text-muted">Ukuran:</span> <b id="restoreSizeDisplay">-</b></div>
                    </div>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="confirmCheckbox" required>
                    <label class="form-check-label small fw-semibold text-danger" for="confirmCheckbox">
                        Saya memahami risiko bahwa data saat ini akan digantikan dengan data cadangan ini.
                    </label>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger btn-sm px-3" id="btnExecuteRestore">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Ya, Pulihkan Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Upload & Restore File .sql -->
<div class="modal fade" id="uploadRestoreModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border-0 shadow-lg" method="POST" action="{{ route('admin.backups.restore') }}" enctype="multipart/form-data" id="uploadRestoreForm" style="border-radius:14px;overflow:hidden">
            @csrf
            <div class="modal-header text-white" style="background: #1b7a5a;">
                <h5 class="modal-title fs-6 fw-bold"><i class="bi bi-upload me-2"></i>Upload & Pulihkan Database (.sql)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="small text-muted mb-3">
                    Pilih file backup database berformat <code>.sql</code> dari komputer Anda untuk dipulihkan langsung ke database sistem.
                </p>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Pilih File .sql</label>
                    <input class="form-control" type="file" name="backup_file" accept=".sql" required>
                    <div class="form-text">Maksimal ukuran file disesuaikan dengan konfigurasi upload PHP server Anda.</div>
                </div>
                <div class="alert alert-warning border-0 small mb-0" style="border-radius: 8px;">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Pastikan file yang diupload adalah file backup resmi dari sistem OK RS Mutiara Aini.
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-rs btn-sm px-3">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Upload & Pulihkan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const confirmModal = document.getElementById('confirmRestoreModal');
        confirmModal.addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;
            const filename = btn.getAttribute('data-filename');
            const time = btn.getAttribute('data-time');
            const size = btn.getAttribute('data-size');

            document.getElementById('restoreFilename').value = filename;
            document.getElementById('restoreNameDisplay').textContent = filename;
            document.getElementById('restoreTimeDisplay').textContent = time;
            document.getElementById('restoreSizeDisplay').textContent = size;
            document.getElementById('confirmCheckbox').checked = false;
        });

        const createForm = document.getElementById('createBackupForm');
        createForm?.addEventListener('submit', function () {
            const btn = document.getElementById('btnCreateBackup');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Membuat Backup...';
            }
        });

        const restoreForm = document.getElementById('restoreForm');
        restoreForm?.addEventListener('submit', function () {
            const btn = document.getElementById('btnExecuteRestore');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Memulihkan Database...';
            }
        });
    });
</script>
@endpush
