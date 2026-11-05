@extends('layouts.admin')
@section('title',$schedule->exists?'Edit Jadwal':'Jadwal Baru')
@section('content')
<div class="card p-4">
    <form method="POST" id="scheduleForm" action="{{ $schedule->exists?route('admin.schedules.update',$schedule):route('admin.schedules.store') }}">
        @csrf
        @if($schedule->exists) @method('PUT') @endif
        @php
        $selectedTeams = old('surgical_team_ids', $schedule->exists ? $schedule->surgicalTeams->pluck('id')->map(fn($id)=>(int)$id)->all() : []);
        $selectedPatient = old('patient_id', $schedule->patient_id);
        $selectedOperator = old('operator_id', $schedule->operator_id);
        $selectedAnesthesia = old('anesthesia_id', $schedule->anesthesia_id);
        @endphp

        <div class="row g-3">
            <div class="col-md-5">
                <label class="form-label">Nama Pasien</label>
                <input type="text" id="patient_name" name="patient_name" class="form-control" list="patient_list" placeholder="Ketik atau pilih nama pasien..." autocomplete="off" value="{{ old('patient_name', $schedule->patient?->name) }}" required>
                <input type="hidden" name="patient_id" id="patient_id" value="{{ old('patient_id', $schedule->patient_id) }}">
                <datalist id="patient_list">
                    @foreach($patients as $p)
                    <option value="{{ $p->name }}" data-id="{{ $p->id }}" data-rm="{{ $p->medical_record_no }}" data-gender="{{ $p->gender }}"></option>
                    @endforeach
                </datalist>
                <div id="patient_hint" class="small-muted mt-1">Jika pasien baru, cukup isi Nama, No. RM, dan Jenis Kelamin lalu langsung Simpan.</div>
            </div>
            <div class="col-md-3">
                <label class="form-label">No. RM</label>
                <input type="text" id="patient_medical_record_no" name="patient_medical_record_no" class="form-control" placeholder="Nomor rekam medis" value="{{ old('patient_medical_record_no', $schedule->patient?->medical_record_no) }}" autocomplete="off">
            </div>
            <div class="col-md-2">
                <label class="form-label">Jenis Kelamin</label>
                <select id="patient_gender" name="patient_gender" class="form-select" required>
                    <option value="">Pilih</option>
                    <option value="P" @selected(old('patient_gender',$schedule->patient?->gender)==='P')>Perempuan</option>
                    <option value="L" @selected(old('patient_gender',$schedule->patient?->gender)==='L')>Laki-laki</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="age" class="form-label">Umur</label>
                <input type="text" name="age" id="age" class="form-control" value="{{ old('age', $patient->age ?? '') }}" placeholder="Cth: 35 Tahun atau 5 Bulan">
            </div>
            <div class="col-md-2">
                <label class="form-label">Tanggal Operasi</label>
                <input type="date" name="operation_date" class="form-control" value="{{ old('operation_date',$schedule->operation_date?->format('Y-m-d')??request('date',now()->toDateString())) }}" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Jam</label>
                <input type="time" name="operation_time" class="form-control" value="{{ old('operation_time', $schedule->operation_time ? substr((string)$schedule->operation_time,0,5) : '') }}" required>
            </div>

            <div class="col-md-3">
                <label class="form-label">Ruang Operasi / OK</label>
                <select name="ok_level" class="form-select">
                    <option @selected(old('ok_level',$schedule->ok_level)==='OK 1')>OK 1</option>
                    <option @selected(old('ok_level',$schedule->ok_level)==='OK 2')>OK 2</option>
                    <option @selected(old('ok_level',$schedule->ok_level)==='OK 3')>OK 3</option>
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label"><i class="bi bi-clipboard2-pulse me-1 text-primary"></i>Diagnosa</label>
                <input name="diagnosis" id="diagnosis" list="diagnosis_history" class="form-control input-animated" value="{{ old('diagnosis',$schedule->diagnosis) }}" placeholder="Ketik diagnosa..." autocomplete="off">
                <datalist id="diagnosis_history">@foreach($diagnosisHistory as $item)<option value="{{ $item }}"></option>@endforeach</datalist>
            </div>
            <div class="col-md-4">
                <label class="form-label"><i class="bi bi-scissors me-1 text-primary"></i>Tindakan</label>
                <input name="procedure" id="procedure" list="procedure_history" class="form-control input-animated" value="{{ old('procedure',$schedule->procedure) }}" placeholder="Ketik tindakan..." autocomplete="off" required>
                <datalist id="procedure_history">@foreach($procedureHistory as $item)<option value="{{ $item }}"></option>@endforeach</datalist>
            </div>

            <div class="col-md-4">
                <label class="form-label">Operator</label>
                <input type="text" id="operator_search" class="form-control" list="operator_list" placeholder="Pilih atau ketik nama operator..." autocomplete="off" value="{{ $selectedOperator && $operators->firstWhere('id',(int)$selectedOperator) ? $operators->firstWhere('id',(int)$selectedOperator)->name : '' }}">
                <input type="hidden" name="operator_id" id="operator_id" value="{{ $selectedOperator }}">
                <datalist id="operator_list">@foreach($operators as $x)<option value="{{ $x->name }}" data-id="{{ $x->id }}"></option>@endforeach</datalist>
                <div id="operator_hint" class="small-muted mt-1">Pilih operator dari daftar.</div>
            </div>
            <div class="col-md-4">
                <label class="form-label">Anestesi</label>
                <input type="text" id="anesthesia_search" class="form-control" list="anesthesia_list" placeholder="Pilih atau ketik nama anestesi..." autocomplete="off" value="{{ $selectedAnesthesia && $anesthesias->firstWhere('id',(int)$selectedAnesthesia) ? $anesthesias->firstWhere('id',(int)$selectedAnesthesia)->name : '' }}">
                <input type="hidden" name="anesthesia_id" id="anesthesia_id" value="{{ $selectedAnesthesia }}">
                <datalist id="anesthesia_list">@foreach($anesthesias as $x)<option value="{{ $x->name }}" data-id="{{ $x->id }}"></option>@endforeach</datalist>
                <div id="anesthesia_hint" class="small-muted mt-1">Pilih anestesi dari daftar.</div>
            </div>

            <div class="col-md-4">
                <label class="form-label">Tim Bedah <span class="text-muted">(pilih atau ketik, tanpa tombol Tambah)</span></label>
                <input type="text" id="team_search" class="form-control" list="team_list" placeholder="Pilih atau ketik nama anggota tim..." autocomplete="off">
                <datalist id="team_list">
                    @foreach($teams as $x)<option value="{{ $x->name }}" data-id="{{ $x->id }}">{{ $x->name }} — {{ $x->profession?->name ?? $x->role ?? 'Perawat' }}</option>@endforeach
                </datalist>
                <div id="team_chips" class="border rounded p-2 mt-2" style="min-height:48px">
                    @foreach($teams->whereIn('id',$selectedTeams) as $x)
                    <span class="badge rounded-pill text-bg-primary me-1 mb-1 team-chip" data-id="{{ $x->id }}" data-name="{{ $x->name }}">
                        {{ $x->name }} <small class="opacity-75">({{ $x->profession?->name ?? $x->role ?? 'Perawat' }})</small>
                        <button type="button" class="btn-close btn-close-white ms-1 remove-team" aria-label="Hapus"></button>
                        <input type="hidden" name="surgical_team_ids[]" value="{{ $x->id }}">
                    </span>
                    @endforeach
                    @if(empty($selectedTeams))<span id="team_empty" class="text-muted small">Belum ada anggota dipilih.</span>@endif
                </div>
                <div class="small-muted mt-1">Ketik nama lalu tekan Enter atau pilih nama dari daftar. Semua anggota baru otomatis disimpan saat tombol Simpan ditekan.</div>
            </div>

            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option @selected(old('status',$schedule->status)==='Terjadwal')>Terjadwal</option>
                    <option @selected(old('status',$schedule->status)==='Selesai')>Selesai</option>
                    <option @selected(old('status',$schedule->status)==='Batal')>Batal</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <div class="form-check form-switch mb-2"><input class="form-check-input" type="checkbox" name="cito" value="1" id="cito" @checked(old('cito',$schedule->cito))><label class="form-check-label" for="cito">CITO</label></div>
            </div>
            <div class="col-12"><label class="form-label">Catatan</label><textarea name="notes" class="form-control" rows="3">{{ old('notes',$schedule->notes) }}</textarea></div>
        </div>

        @if($errors->any())
        <div class="alert alert-danger mt-3 mb-0">
            <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle me-1"></i> Jadwal belum disimpan</div>
            <ul class="mb-0 ps-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
        @endif


        <div class="mt-4 d-flex gap-2"><button type="submit" class="btn btn-rs action-click"><i class="bi bi-check-circle me-1"></i>Simpan</button><a href="{{ route('admin.schedules.index',['date'=>request('date',$schedule->operation_date?->format('Y-m-d'))]) }}" class="btn btn-outline-secondary">Batal</a></div>
    </form>
</div>
@endsection

@push('styles')
<style>
    .input-animated {
        transition: box-shadow .2s ease, transform .2s ease, border-color .2s ease
    }

    .input-animated:focus {
        transform: translateY(-1px);
        box-shadow: 0 0 0 .2rem rgba(27, 122, 90, .15);
        border-color: #1b7a5a
    }

    .action-click {
        transition: transform .15s ease, box-shadow .15s ease
    }

    .action-click:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 18px rgba(27, 122, 90, .2)
    }

    .action-click:active {
        transform: scale(.97)
    }
</style>
@endpush

@push('scripts')
<script>
    (function() {
        const normalize = s => (s || '').trim().toLowerCase();
        const cleanStr = s => (s || '').toLowerCase().replace(/[^a-z0-9]/g, '');

        const patientName = document.getElementById('patient_name');
        const patientRM = document.getElementById('patient_medical_record_no');
        const patientGender = document.getElementById('patient_gender');
        const patientId = document.getElementById('patient_id');
        const patientHint = document.getElementById('patient_hint');

        function findPatient() {
            const val = normalize(patientName.value);
            const valClean = cleanStr(patientName.value);
            let match = null;
            document.querySelectorAll('#patient_list option').forEach(o => {
                if (normalize(o.value) === val || cleanStr(o.value) === valClean) match = o;
            });
            if (match) {
                patientId.value = match.dataset.id || '';
                if (!patientRM.value && match.dataset.rm) patientRM.value = match.dataset.rm;
                if (!patientGender.value && match.dataset.gender) patientGender.value = match.dataset.gender;
                patientHint.textContent = 'Pasien terdaftar ditemukan (ID: ' + (match.dataset.id || '-') + '). Data tidak akan diduplikasi.';
                patientHint.className = 'small-muted mt-1 text-success';
            } else {
                patientId.value = '';
                patientHint.textContent = 'Pasien baru? Cukup isi No. RM dan Jenis Kelamin lalu Simpan.';
                patientHint.className = 'small-muted mt-1';
            }
        }
        patientName.addEventListener('input', findPatient);
        patientName.addEventListener('change', findPatient);
        findPatient();

        function bindSearch(inputId, hiddenId, listId, hintId) {
            const input = document.getElementById(inputId),
                hidden = document.getElementById(hiddenId),
                list = document.getElementById(listId),
                hint = document.getElementById(hintId);
            if (!input) return;

            function find() {
                const val = normalize(input.value);
                const valClean = cleanStr(input.value);
                let match = null;
                list.querySelectorAll('option').forEach(o => {
                    if (normalize(o.value) === val || cleanStr(o.value) === valClean) match = o;
                });
                hidden.value = match ? match.dataset.id : '';
                if (match) {
                    hint.textContent = 'Data ditemukan.';
                    hint.className = 'small-muted mt-1 text-success';
                } else {
                    hint.textContent = val ? 'Pilih nama yang tersedia di daftar.' : 'Pilih dari daftar.';
                    hint.className = 'small-muted mt-1';
                }
            }
            input.addEventListener('input', find);
            input.addEventListener('change', find);
            input.addEventListener('blur', find);
            find();
        }
        bindSearch('operator_search', 'operator_id', 'operator_list', 'operator_hint');
        bindSearch('anesthesia_search', 'anesthesia_id', 'anesthesia_list', 'anesthesia_hint');

        const teamInput = document.getElementById('team_search'),
            teamList = document.getElementById('team_list'),
            chips = document.getElementById('team_chips');

        function addSingleTeam(rawName) {
            const name = (rawName || '').trim();
            if (!name) return;

            const nameClean = cleanStr(name);
            const exists = [...chips.querySelectorAll('.team-chip')].some(x => {
                return normalize(x.dataset.name) === normalize(name) || (x.dataset.clean && x.dataset.clean === nameClean);
            });
            if (exists) return;

            let match = null;
            teamList.querySelectorAll('option').forEach(o => {
                if (normalize(o.value) === normalize(name) || cleanStr(o.value) === nameClean) {
                    match = o;
                }
            });

            document.getElementById('team_empty')?.remove();
            const chip = document.createElement('span');
            chip.className = 'badge rounded-pill text-bg-primary me-1 mb-1 team-chip';
            chip.dataset.id = match ? match.dataset.id : '';
            chip.dataset.name = match ? match.value : name;
            chip.dataset.clean = nameClean;

            const displayName = match ? match.value : name;
            const prof = match ? ((match.textContent || '').split(' — ')[1] || 'Petugas') : 'Anggota baru';

            // Only send surgical_team_ids[] if matched; ONLY send surgical_team_names[] if truly new
            const hiddenInput = match
                ? '<input type="hidden" name="surgical_team_ids[]" value="' + match.dataset.id + '">'
                : '<input type="hidden" name="surgical_team_names[]" value="' + displayName.replace(/&/g, '&amp;').replace(/"/g, '&quot;') + '">';

            chip.innerHTML = displayName.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;') +
                ' <small class="opacity-75">(' + prof.replace(/</g, '&lt;') + ')</small> ' +
                '<button type="button" class="btn-close btn-close-white ms-1 remove-team" aria-label="Hapus"></button>' +
                hiddenInput;

            chips.appendChild(chip);
        }

        function addTeamFromInput() {
            const val = (teamInput.value || '').trim();
            if (!val) return;

            // Split multiple names if separated by comma, semicolon, slash, or dot between words
            const parts = val.split(/[,;\/\n\r]+|\s+\.\s*|\b\s*\.\s*(?=[a-zA-Z])/).map(s => s.trim()).filter(s => s.length > 0);
            if (parts.length > 0) {
                parts.forEach(part => addSingleTeam(part));
            } else {
                addSingleTeam(val);
            }

            teamInput.value = '';
            teamInput.focus();
        }

        teamInput.addEventListener('keydown', e => {
            if (e.key === 'Enter') {
                e.preventDefault();
                addTeamFromInput();
            }
        });
        teamInput.addEventListener('change', () => {
            if (teamInput.value.trim()) addTeamFromInput();
        });
        teamInput.addEventListener('blur', () => {
            if (teamInput.value.trim()) addTeamFromInput();
        });
        chips.addEventListener('click', e => {
            if (e.target.classList.contains('remove-team')) {
                e.target.closest('.team-chip').remove();
                if (!chips.querySelector('.team-chip')) chips.innerHTML = '<span id="team_empty" class="text-muted small">Belum ada anggota dipilih.</span>';
            }
        });

        document.getElementById('scheduleForm').addEventListener('submit', e => {
            if (teamInput.value.trim()) {
                addTeamFromInput();
            }
            if (!patientName.value.trim() || !patientGender.value) {
                e.preventDefault();
                alert('Nama Pasien dan Jenis Kelamin wajib diisi.');
                patientName.focus();
                return false;
            }
            return true;
        });
    })();
</script>
@endpush