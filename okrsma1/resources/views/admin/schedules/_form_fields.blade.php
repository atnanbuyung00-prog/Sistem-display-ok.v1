@if($schedule->exists) @method('PUT') @endif
@php
$patient = $schedule->patient;
$selectedTeams = old('surgical_team_ids', $schedule->exists ? $schedule->surgicalTeams->pluck('id')->map(fn($id)=>(int)$id)->all() : []);
@endphp
<input type="hidden" name="operation_date" id="operation_date" value="{{ old('operation_date',$date) }}">
<input type="hidden" name="patient_id" id="patient_id" value="{{ old('patient_id',$schedule->patient_id) }}">

<div class="row g-3">
    <div class="col-md-5">
        <label class="form-label fw-semibold">Nama Pasien</label>
        <input type="text" name="patient_name" id="patient_name" class="form-control input-animated" list="patient_list" value="{{ old('patient_name',$patient?->name) }}" placeholder="Ketik nama pasien..." autocomplete="off" required>
        <datalist id="patient_list">
            @foreach($patients as $p)<option value="{{ $p->name }}" data-id="{{ $p->id }}" data-rm="{{ $p->medical_record_no }}" data-gender="{{ $p->gender }}">{{ $p->name }} — {{ $p->medical_record_no ?: 'Tanpa No. RM' }}</option>@endforeach
        </datalist>
        <div class="small-muted mt-1">Bisa memilih pasien lama atau langsung mengetik pasien baru.</div>
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">No. RM</label>
        <input type="text" name="patient_medical_record_no" id="patient_medical_record_no" class="form-control input-animated" value="{{ old('patient_medical_record_no',$patient?->medical_record_no) }}" placeholder="Masukkan No. RM..." autocomplete="off">
    </div>
    <div class="col-md-2">
        <label class="form-label fw-semibold">Jenis Kelamin</label>
        <select name="patient_gender" id="patient_gender" class="form-select" required>
            <option value="P" @selected(old('patient_gender',$patient?->gender)==='P')>Perempuan</option>
            <option value="L" @selected(old('patient_gender',$patient?->gender)==='L')>Laki-laki</option>
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label fw-semibold">Jam</label>
        <input type="time" name="operation_time" class="form-control" value="{{ old('operation_time',$schedule->operation_time ? substr((string)$schedule->operation_time,0,5) : '') }}" required>
    </div>

    <div class="col-md-3"><label class="form-label">Ruang Operasi / OK</label><select name="ok_level" class="form-select">
            <option @selected(old('ok_level',$schedule->ok_level)==='OK 1')>OK 1</option>
            <option @selected(old('ok_level',$schedule->ok_level)==='OK 2')>OK 2</option>
            <option @selected(old('ok_level',$schedule->ok_level)==='OK 3')>OK 3</option>
        </select></div>
    <div class="col-md-5"><label class="form-label"><i class="bi bi-clipboard2-pulse me-1 text-primary"></i>Diagnosa</label><input name="diagnosis" id="diagnosis" list="diagnosis_history" class="form-control input-animated" value="{{ old('diagnosis',$schedule->diagnosis) }}" placeholder="Ketik diagnosa..." autocomplete="off"><datalist id="diagnosis_history">@foreach($diagnosisHistory as $item)<option value="{{ $item }}"></option>@endforeach</datalist></div>
    <div class="col-md-4"><label class="form-label"><i class="bi bi-scissors me-1 text-primary"></i>Tindakan</label><input name="procedure" id="procedure" list="procedure_history" class="form-control input-animated" value="{{ old('procedure',$schedule->procedure) }}" placeholder="Ketik tindakan..." autocomplete="off" required><datalist id="procedure_history">@foreach($procedureHistory as $item)<option value="{{ $item }}"></option>@endforeach</datalist></div>

    <div class="col-md-4"><label class="form-label">Operator</label><input type="text" id="operator_search" class="form-control" list="operator_list" placeholder="Pilih atau ketik nama operator..." autocomplete="off" value="{{ $schedule->operator?->name }}"><input type="hidden" name="operator_id" id="operator_id" value="{{ old('operator_id',$schedule->operator_id) }}"><datalist id="operator_list">@foreach($operators as $x)<option value="{{ $x->name }}" data-id="{{ $x->id }}">{{ $x->name }}</option>@endforeach</datalist></div>
    <div class="col-md-4"><label class="form-label">Anestesi</label><input type="text" id="anesthesia_search" class="form-control" list="anesthesia_list" placeholder="Pilih atau ketik nama anestesi..." autocomplete="off" value="{{ $schedule->anesthesia?->name }}"><input type="hidden" name="anesthesia_id" id="anesthesia_id" value="{{ old('anesthesia_id',$schedule->anesthesia_id) }}"><datalist id="anesthesia_list">@foreach($anesthesias as $x)<option value="{{ $x->name }}" data-id="{{ $x->id }}">{{ $x->name }}</option>@endforeach</datalist></div>
    <div class="col-md-4">
        <label class="form-label">Tim Bedah <span class="text-muted">(boleh lebih dari 1)</span></label>
        <div class="input-group"><input type="text" id="team_search" class="form-control" list="team_list" placeholder="Pilih / ketik nama perawat atau bidan..." autocomplete="off"><button type="button" class="btn btn-outline-primary" id="add_team">Tambah</button></div>
        <datalist id="team_list">@foreach($teams as $x)<option value="{{ $x->name }}" data-id="{{ $x->id }}">{{ $x->name }} — {{ $x->profession?->name ?? $x->role ?? 'Perawat' }}</option>@endforeach</datalist>
        <div id="team_chips" class="border rounded p-2 mt-2" style="min-height:48px">@foreach($teams->whereIn('id',$selectedTeams) as $x)<span class="badge rounded-pill text-bg-primary me-1 mb-1 team-chip" data-id="{{ $x->id }}" data-name="{{ $x->name }}">{{ $x->name }} <small class="opacity-75">({{ $x->profession?->name ?? $x->role ?? 'Perawat' }})</small> <button type="button" class="btn-close btn-close-white ms-1 remove-team"></button><input type="hidden" name="surgical_team_ids[]" value="{{ $x->id }}"></span>@endforeach @if(empty($selectedTeams))<span id="team_empty" class="text-muted small">Belum ada anggota dipilih.</span>@endif</div>
    </div>

    <div class="col-md-3"><label class="form-label">Status</label><select name="status" class="form-select">
            <option @selected(old('status',$schedule->status)==='Terjadwal')>Terjadwal</option>
            <option @selected(old('status',$schedule->status)==='Selesai')>Selesai</option>
            <option @selected(old('status',$schedule->status)==='Batal')>Batal</option>
        </select></div>
    <div class="col-md-3 d-flex align-items-end">
        <div class="form-check form-switch mb-2"><input class="form-check-input" type="checkbox" name="cito" value="1" id="cito" @checked(old('cito',$schedule->cito))><label class="form-check-label" for="cito">CITO</label></div>
    </div>
    <div class="col-12"><label class="form-label">Catatan</label><textarea name="notes" class="form-control" rows="3">{{ old('notes',$schedule->notes) }}</textarea></div>
</div>
@if($errors->any())<div class="alert alert-danger mt-3 mb-0">
    <div class="fw-bold mb-1">Jadwal belum disimpan</div>
    <ul class="mb-0 ps-3">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
</div>@endif
<div class="mt-4 d-flex gap-2"><button type="submit" class="btn btn-rs action-click"><i class="bi bi-check-circle me-1"></i>Simpan Jadwal</button>@if($schedule->exists)<a href="{{ route('admin.schedules.manage',['date'=>$schedule->operation_date?->format('Y-m-d')]) }}" class="btn btn-outline-secondary">Batal</a>@endif</div>
@push('styles')<style>
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
</style>@endpush
@push('scripts')
<script>
    (function() {
        const norm = s => (s || '').trim().toLowerCase();
        const patientInput = document.getElementById('patient_name'),
            rmInput = document.getElementById('patient_medical_record_no'),
            genderInput = document.getElementById('patient_gender');

        function fillPatient() {
            const val = norm(patientInput.value);
            let m = null;
            document.querySelectorAll('#patient_list option').forEach(o => {
                if (norm(o.value) === val) m = o;
            });
            if (m) {
                rmInput.value = m.dataset.rm || '';
                genderInput.value = m.dataset.gender || genderInput.value;
                document.getElementById('patient_id').value = m.dataset.id || '';
            } else {
                document.getElementById('patient_id').value = '';
            }
        }
        patientInput.addEventListener('input', fillPatient);
        patientInput.addEventListener('change', fillPatient);
        fillPatient();

        function bindSearch(inputId, hiddenId, listId) {
            const input = document.getElementById(inputId),
                hidden = document.getElementById(hiddenId);
            if (!input) return;
            const find = () => {
                const v = norm(input.value);
                let m = null;
                document.querySelectorAll('#' + listId + ' option').forEach(o => {
                    if (norm(o.value) === v) m = o;
                });
                hidden.value = m ? m.dataset.id : '';
            };
            input.addEventListener('input', find);
            input.addEventListener('change', find);
            input.addEventListener('blur', find);
            find();
        }
        bindSearch('operator_search', 'operator_id', 'operator_list');
        bindSearch('anesthesia_search', 'anesthesia_id', 'anesthesia_list');
        const teamInput = document.getElementById('team_search'),
            list = document.getElementById('team_list'),
            chips = document.getElementById('team_chips');
        const cleanStr = s => (s || '').toLowerCase().replace(/[^a-z0-9]/g, '');

        function addSingleTeam(rawName) {
            const name = (rawName || '').trim();
            if (!name) return;
            const nameClean = cleanStr(name);
            const exists = [...chips.querySelectorAll('.team-chip')].some(x => norm(x.dataset.name) === norm(name) || (x.dataset.clean && x.dataset.clean === nameClean));
            if (exists) return;
            let m = null;
            list.querySelectorAll('option').forEach(o => {
                if (norm(o.value) === norm(name) || cleanStr(o.value) === nameClean) m = o;
            });
            document.getElementById('team_empty')?.remove();
            const chip = document.createElement('span');
            chip.className = 'badge rounded-pill text-bg-primary me-1 mb-1 team-chip';
            chip.dataset.id = m ? m.dataset.id : '';
            chip.dataset.name = m ? m.value : name;
            chip.dataset.clean = nameClean;
            const prof = m ? ((m.textContent || '').split(' — ')[1] || 'Petugas') : 'Belum ditentukan';
            const hiddenInput = m
                ? '<input type="hidden" name="surgical_team_ids[]" value="' + m.dataset.id + '">'
                : '<input type="hidden" name="surgical_team_names[]" value="' + (m ? m.value : name).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/"/g, '&quot;') + '">';
            chip.innerHTML = (m ? m.value : name) + ' <small class="opacity-75">(' + prof + ')</small> <button type="button" class="btn-close btn-close-white ms-1 remove-team"></button>' + hiddenInput;
            chips.appendChild(chip);
        }

        function addTeam() {
            const val = (teamInput.value || '').trim();
            if (!val) return;
            const parts = val.split(/[,;\/\n\r]+|\s+\.\s*|\b\s*\.\s*(?=[a-zA-Z])/).map(s => s.trim()).filter(s => s.length > 0);
            if (parts.length > 0) {
                parts.forEach(p => addSingleTeam(p));
            } else {
                addSingleTeam(val);
            }
            teamInput.value = '';
            teamInput.focus();
        }
        document.getElementById('add_team')?.addEventListener('click', addTeam);
        teamInput.addEventListener('keydown', e => {
            if (e.key === 'Enter') {
                e.preventDefault();
                addTeam();
            }
        });
        teamInput.addEventListener('change', () => {
            if (teamInput.value.trim()) addTeam();
        });
        teamInput.addEventListener('blur', () => {
            if (teamInput.value.trim()) addTeam();
        });
        chips.addEventListener('click', e => {
            if (e.target.classList.contains('remove-team')) {
                e.target.closest('.team-chip').remove();
                if (!chips.querySelector('.team-chip')) chips.innerHTML = '<span id="team_empty" class="text-muted small">Belum ada anggota dipilih.</span>';
            }
        });
        document.getElementById('scheduleForm')?.addEventListener('submit', e => {
            if (teamInput.value.trim()) {
                addTeam();
            }
            if (!patientInput.value.trim()) {
                e.preventDefault();
                patientInput.focus();
                alert('Nama pasien wajib diisi.');
            }
        });
    })();
</script>@endpush