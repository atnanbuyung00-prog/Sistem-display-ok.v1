@extends('layouts.admin')
@section('title', 'Jadwal Shift')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
  <div>
    <h4 class="mb-1">Jadwal Shift</h4>
    <small class="text-muted">
      Plotting dan penugasan petugas Tim Bedah (Dinas, RR, On Call) per tanggal operasi.
    </small>
  </div>
  <div class="d-flex gap-2 flex-wrap">
    <a class="btn btn-outline-primary" href="{{ route('admin.shifts.manage') }}">
      <i class="bi bi-sliders me-1"></i> Kelola Master Shift
    </a>
    @if(auth()->user()->canAccess('shift.export'))
    <a class="btn btn-outline-success" href="{{ route('admin.shifts.export', ['date' => $date]) }}">
      <i class="bi bi-file-earmark-excel"></i> Excel
    </a>
    @endif
    <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#copyShift">
      <i class="bi bi-copy"></i> Salin Jadwal
    </button>
  </div>
</div>

<div class="card p-3 mb-3">
  <form class="row g-2 align-items-end">
    <div class="col-md-3">
      <label class="form-label">Tanggal yang dikelola</label>
      <input type="date" name="date" value="{{ $date }}" class="form-control">
    </div>
    <div class="col-md-2">
      <button class="btn btn-rs w-100">Tampilkan</button>
    </div>
    <div class="col-md-7 d-flex gap-2 flex-wrap align-items-end">
      <a class="btn btn-outline-secondary" href="?date={{ \Carbon\Carbon::parse($date)->subDay()->toDateString() }}">
        ← Sebelumnya
      </a>
      <a class="btn btn-outline-secondary" href="?date={{ now('Asia/Jakarta')->toDateString() }}">
        Hari Ini
      </a>
      <a class="btn btn-outline-secondary" href="?date={{ \Carbon\Carbon::parse($date)->addDay()->toDateString() }}">
        Berikutnya →
      </a>
      <span class="small text-muted align-self-center">
        Data tersimpan per tanggal.
      </span>
    </div>
  </form>
</div>

<div class="row">
  <div class="col-12">
    <form method="POST" action="{{ route('admin.shifts.assignments.bulk') }}" id="bulkShiftForm">
      @csrf
      <div class="card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3 gap-2 flex-wrap">
          <div>
            <h4 class="mb-0">Jadwal Shift {{ \Carbon\Carbon::parse($date)->locale('id')->translatedFormat('d F Y') }}</h4>
            <small class="text-muted">
              Pilih banyak nama pada setiap shift. Semua pilihan disimpan dengan satu tombol.
            </small>
          </div>
          <button type="submit" class="btn btn-rs">
            <i class="bi bi-check2-all me-1"></i>Simpan Semua Jadwal Shift
          </button>
        </div>

        @foreach($shifts as $s)
        @php
        $fieldPrefix = 'assignments[' . $s->id . ']';
        @endphp
        <div class="border rounded-3 p-3 mb-3" style="border-left:5px solid {{ $s->color ?: '#1b7a5a' }}!important">
          <div class="d-flex justify-content-between align-items-center gap-2">
            <div>
              <b class="fs-6" style="color: {{ $s->color ?: '#1b7a5a' }}">{{ $s->name }}</b>
              <span class="badge bg-light text-dark border ms-2">
                <i class="bi bi-clock me-1 text-success"></i>{{ substr($s->start_time, 0, 5) }} - {{ substr($s->end_time, 0, 5) }} WIB
              </span>
            </div>
            <a href="{{ route('admin.shifts.manage', ['edit' => $s->id]) }}" class="btn btn-sm btn-outline-secondary" title="Edit jam atau pengaturan shift">
              <i class="bi bi-gear me-1"></i>Atur Shift
            </a>
          </div>

          <div class="mt-2 d-flex flex-wrap gap-1">
            @forelse($s->assignments as $a)
            @php
            $roleClass = match($a->staff_role) {
            'On Call' => 'text-danger',
            'RR' => 'text-success',
            default => 'text-primary',
            };
            @endphp
            <span class="badge text-bg-light border py-2 px-2">
              {{ $a->staff_name }}
              <b class="{{ $roleClass }}">
                ({{ $a->staff_role }})
              </b>
              <button
                type="submit"
                form="deleteAssignment{{ $a->id }}"
                class="btn btn-sm p-0 text-danger border-0 bg-transparent"
                title="Hapus">
                ×
              </button>
            </span>
            @empty
            <span class="text-muted small">Belum ada petugas untuk tanggal ini.</span>
            @endforelse
          </div>

          <div class="mt-3">
            <label class="form-label fw-semibold">Catatan Shift {{ $s->name }}</label>
            <textarea
              class="form-control"
              name="{{ $fieldPrefix }}[note]"
              rows="2"
              maxlength="1000"
              placeholder="Contoh: serah terima alat, petugas standby, informasi khusus...">{{ old('assignments.' . $s->id . '.note', $s->note) }}</textarea>
            <div class="form-text">
              Catatan ini tampil di tabel shift pada Display TV untuk shift ini.
            </div>
          </div>

          <div class="row g-2 mt-2 align-items-start">
            <div class="col-md-7">
              <div class="multi-team-picker" data-shift-id="{{ $s->id }}">
                <div class="form-control team-picker-box" tabindex="0">
                  <div class="team-chips"></div>
                  <input
                    type="text"
                    name="{{ $fieldPrefix }}[staff_name]"
                    class="team-search-input border-0 flex-grow-1"
                    placeholder="Ketik atau pilih beberapa nama..."
                    autocomplete="off">
                </div>
                <div class="team-options shadow-sm"></div>
              </div>
              <input type="hidden" name="{{ $fieldPrefix }}[shift_date]" value="{{ $date }}">
            </div>
            <div class="col-md-5">
              <select class="form-select" name="{{ $fieldPrefix }}[staff_role]">
                <option value="Dinas">Dinas</option>
                <option value="RR">RR (Recovery Room)</option>
                <option value="On Call">On Call</option>
              </select>
            </div>
          </div>
        </div>
        @endforeach

        @if($shifts->isNotEmpty())
        <div class="text-end pt-1">
          <button type="submit" class="btn btn-rs">
            <i class="bi bi-check2-all me-1"></i>Simpan Semua Jadwal Shift
          </button>
        </div>
        @else
        <div class="text-center text-muted py-4">
          Belum ada shift aktif. Tambahkan shift terlebih dahulu.
        </div>
        @endif
      </div>
    </form>

    @foreach($shifts as $s)
    <form id="deleteShift{{ $s->id }}" method="POST" action="{{ route('admin.shifts.destroy', $s) }}">
      @csrf
      @method('DELETE')
    </form>
    @foreach($s->assignments as $a)
    <form
      id="deleteAssignment{{ $a->id }}"
      method="POST"
      action="{{ route('admin.shifts.assignments.destroy', [$s, $a]) }}">
      @csrf
      @method('DELETE')
    </form>
    @endforeach
    @endforeach
  </div>
</div>

<div class="modal fade" id="copyShift" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('admin.shifts.copy') }}">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Salin Jadwal Shift</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted">
          Salin seluruh petugas Dinas, RR, dan On Call dari satu tanggal ke tanggal lain.
          Data yang sama tidak akan digandakan.
        </p>
        <div class="mb-3">
          <label class="form-label">Tanggal sumber</label>
          <input type="date" name="source_date" value="{{ $date }}" class="form-control" required>
        </div>
        <div>
          <label class="form-label">Salin ke tanggal</label>
          <input type="date" name="target_date" class="form-control" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-rs">Salin Jadwal</button>
      </div>
    </form>
  </div>
</div>

<style>
  .multi-team-picker {
    position: relative;
  }

  .team-picker-box {
    min-height: 42px;
    height: auto;
    display: flex;
    align-items: center;
    gap: 5px;
    flex-wrap: wrap;
    padding: 5px 8px;
    cursor: text;
  }

  .team-search-input {
    min-width: 150px;
    outline: 0;
    box-shadow: none !important;
    padding: 5px 3px;
  }

  .team-chips {
    display: flex;
    gap: 4px;
    flex-wrap: wrap;
  }

  .team-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #e9f2fb;
    border: 1px solid #b9d3ea;
    border-radius: 999px;
    padding: 4px 8px;
    font-size: .85rem;
  }

  .team-chip button {
    border: 0;
    background: transparent;
    color: #b42318;
    font-weight: 700;
    padding: 0;
    line-height: 1;
  }

  .team-options {
    display: none;
    position: absolute;
    z-index: 1050;
    left: 0;
    right: 0;
    top: calc(100% + 3px);
    background: #fff;
    border: 1px solid #ced4da;
    border-radius: 8px;
    max-height: 230px;
    overflow: auto;
  }

  .team-option {
    padding: 9px 11px;
    cursor: pointer;
    border-bottom: 1px solid #f0f0f0;
  }

  .team-option:hover {
    background: #f1f7fc;
  }

  .team-option small {
    display: block;
    color: #6c757d;
  }

  .team-empty {
    padding: 10px;
    color: #6c757d;
    font-size: .9rem;
  }
</style>

@php
$mappedTeams = $teams->map(function ($t) {
return [
'name' => $t->name,
'profession' => $t->profession?->name ?? $t->role ?? '',
];
})->values();
@endphp

<script>
  (function() {
    const teams = @json($mappedTeams);

    document.querySelectorAll('.multi-team-picker').forEach(function(picker) {
      const input = picker.querySelector('.team-search-input');
      const box = picker.querySelector('.team-picker-box');
      const options = picker.querySelector('.team-options');
      const chips = picker.querySelector('.team-chips');
      const shiftId = picker.dataset.shiftId;
      const selected = new Set();

      function render(filter = '') {
        const f = filter.trim().toLowerCase();
        options.innerHTML = '';
        const matches = teams.filter(t => {
          if (selected.has(t.name)) return false;
          if (!f) return true;
          return t.name.toLowerCase().includes(f) ||
            String(t.profession).toLowerCase().includes(f);
        });

        if (!matches.length) {
          options.innerHTML = '<div class="team-empty">Tekan Enter untuk menambahkan nama ini.</div>';
          options.style.display = 'block';
          return;
        }

        matches.forEach(t => {
          const el = document.createElement('div');
          el.className = 'team-option';
          el.dataset.name = t.name;
          const profHtml = t.profession ? '<small>' + escapeHtml(t.profession) + '</small>' : '';
          el.innerHTML = '<strong>' + escapeHtml(t.name) + '</strong>' + profHtml;
          el.addEventListener('mousedown', function(e) {
            e.preventDefault();
            add(t.name);
          });
          options.appendChild(el);
        });
        options.style.display = 'block';
      }

      function add(name) {
        name = String(name || '').trim();
        if (!name || selected.has(name)) return;
        const found = teams.find(t => t.name.toLowerCase() === name.toLowerCase());
        const chosenName = found ? found.name : name;
        const chosenProf = found ? found.profession : '';

        if (selected.has(chosenName)) {
          input.value = '';
          return;
        }

        selected.add(chosenName);
        const chip = document.createElement('span');
        chip.className = 'team-chip';
        const profText = chosenProf ? ' — ' + escapeHtml(chosenProf) : '';
        chip.innerHTML =
          '<span>' + escapeHtml(chosenName) + profText + '</span>' +
          '<button type="button" aria-label="Hapus">×</button>' +
          '<input type="hidden" name="assignments[' + shiftId + '][staff_names][]" value="' + escapeAttr(chosenName) + '">';

        chip.querySelector('button').addEventListener('click', () => {
          selected.delete(chosenName);
          chip.remove();
          render(input.value);
        });
        chips.appendChild(chip);
        input.value = '';
        render('');
        input.focus();
      }

      input.addEventListener('focus', () => render(input.value));
      input.addEventListener('input', () => render(input.value));
      input.addEventListener('keydown', e => {
        if (e.key === 'Enter') {
          e.preventDefault();
          if (input.value.trim()) {
            add(input.value.trim());
          }
        }
      });
      box.addEventListener('click', () => input.focus());
      document.addEventListener('click', e => {
        if (!picker.contains(e.target)) options.style.display = 'none';
      });
    });

    function escapeHtml(s) {
      return String(s).replace(/[&<>'"]/g, c => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        "'": '&#39;',
        '"': '&quot;'
      } [c]));
    }

    function escapeAttr(s) {
      return escapeHtml(s);
    }
  })();
</script>
@endsection