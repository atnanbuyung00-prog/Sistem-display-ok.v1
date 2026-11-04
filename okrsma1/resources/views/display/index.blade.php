<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Informasi Jadwal Operasi - RS Mutiara Aini</title>
  <style>
    * {
      box-sizing: border-box;
    }

    html,
    body {
      margin: 0;
      width: 100%;
      height: 100%;
      font-family: Arial, Helvetica, sans-serif;
      color: #143d29;
      background: #f0f7f4;
      overflow: hidden;
    }

    .screen {
      position: fixed;
      inset: 0;
      width: 100dvw;
      height: 100dvh;
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }

    .head {
      height: 8vh;
      flex: 0 0 8vh;
      display: grid;
      grid-template-columns: 30% 42% 28%;
      align-items: center;
      background: #f0f7f4;
    }

    .brand {
      height: 100%;
      display: flex;
      align-items: center;
      padding-left: 2vw;
    }

    .brand img {
      width: 16vw;
      max-width: 320px;
    }

    .title {
      border-left: 4px solid #1b7a5a;
      height: 72%;
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding-left: 4vw;
    }

    .title h1 {
      margin: 0;
      font-size: 2.2vw;
      line-height: 1.05;
      font-weight: 800;
      color: #1b7a5a;
      text-transform: uppercase;
    }

    .clock {
      justify-self: end;
      margin-right: 2vw;
      border: 1px solid #43926e;
      border-radius: 8px;
      padding: .2vh 1vw;
      text-align: center;
      background: #fff;
      line-height: 1;
      box-shadow: 0 2px 8px rgba(0, 0, 0, .06);
    }

    .clock .date {
      font-size: .7vw;
      font-weight: 700;
    }

    .clock .time {
      font-size: 1.35vw;
      font-weight: 900;
      color: #1b7a5a;
      margin-top: .15vh;
    }

    .clock .time span {
      font-size: .75vw;
    }

    /* Tabel utama: Dibuat pas untuk 9 baris tanpa menabrak footer */
    .table-wrap {
      height: 77.5vh;
      flex: 0 0 77.5vh;
      background: #fff;
      overflow: hidden;
    }

    .grid {
      width: 100%;
      height: 100%;
      border-collapse: separate;
      border-spacing: 0;
      table-layout: fixed;
    }

    .grid thead {
      height: 5vh;
    }

    .grid tbody tr {
      height: 8.05vh;
    }

    .grid th {
      background: #13795b;
      color: #fff;
      font-size: 0.9vw;
      font-weight: 800;
      padding: .2vh .2vw;
      border-right: 2px solid #8cbca8;
      line-height: 1.05;
      text-transform: uppercase;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .grid td {
      font-size: 0.82vw;
      font-weight: 700;
      text-align: center;
      padding: .15vh .2vw;
      border-right: 2px solid #a3d0be;
      border-bottom: 1px solid #fff;
      line-height: 1.02;
      overflow: hidden;
      vertical-align: middle;
      transition: background .35s ease, box-shadow .35s ease;
    }

    .grid tbody tr:nth-child(odd) td {
      background: #e2f0ea;
    }

    .grid tbody tr:nth-child(even) td {
      background: #fff;
    }

    .grid tbody tr.row-active td {
      background: linear-gradient(90deg, #eaf6f0, #fff, #eaf6f0);
      background-size: 220% 100%;
      animation: activeRow 3s ease-in-out infinite;
      box-shadow: inset 0 2px 0 #198754, inset 0 -2px 0 #198754;
    }

    .grid tbody tr.row-active .active-pulse {
      animation: pulse 1.45s ease-in-out infinite;
    }

    .grid td:nth-child(5),
    .grid td:nth-child(6),
    .grid td:nth-child(7),
    .grid td:nth-child(8),
    .grid td:nth-child(9) {
      white-space: normal;
      word-break: break-word;
      line-height: 1.15;
    }

    .grid td:nth-child(5) {
      padding: .2vh .4vw;
      font-size: 0.82vw;
    }

    .grid td:nth-child(7) {
      padding: .2vh .4vw;
      font-size: 0.82vw;
      line-height: 1.18;
    }

    .grid td:nth-child(9) {
      padding: .2vh .3vw;
      font-size: 0.78vw;
    }

    .name {
      text-align: left !important;
      padding-left: .6vw !important;
      font-size: 0.86vw !important;
    }

    .ok {
      display: inline-block;
      border-radius: 4px;
      color: #fff;
      padding: .15vh .2vw;
      font-size: .75vw;
      font-weight: 800;
      min-width: 2.5vw;
    }

    .ok1 {
      background: #1284df;
      color: #fff;
    }

    .ok2 {
      background: #0bb52e;
      color: #fff;
    }

    .ok3 {
      background: #8b22c9;
      color: #fff;
      border: 1px solid #6f18a3;
      box-shadow: 0 1px 3px rgba(111, 24, 163, .25);
    }

    .cito {
      background: #ef1010;
      color: #fff;
      border-radius: 5px;
      padding: .15vh .3vw;
      display: inline-block;
      font-size: .70vw;
      font-weight: 900;
    }

    .running {
      display: block;
      margin-top: .05vh;
      font-size: .45vw;
      color: #c21f39;
      font-weight: 900;
      letter-spacing: .02vw;
    }

    .empty-row td {
      height: 8.05vh;
    }

    /* FOOTER / SHIFT: 14.5vh */
    .footer {
      height: 14.5vh;
      flex: 0 0 14.5vh;
      background: #f0f7f4;
      border-top: 2px solid #13795b;
      display: flex;
      flex-direction: column;
    }

    .foot-title {
      height: 2.3vh;
      display: grid;
      grid-template-columns: 12% 35% 15% 15% 23%;
      background: #13795b;
      color: #fff;
      font-weight: 800;
      font-size: .70vw;
      align-items: center;
      text-transform: uppercase;
    }

    .foot-title>div {
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      border-right: 2px solid #fff;
    }

    .foot-title>div:last-child {
      border-right: none;
    }

    .foot-grid {
      display: grid;
      grid-template-columns: 12% 35% 15% 15% 23%;
      flex: 1;
      min-height: 0;
    }

    .shift-col {
      display: grid;
      border-right: 1px solid #8cbca8;
    }

    .shift-name {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      font-size: .68vw;
      font-weight: 800;
      border-bottom: 1px solid #fff;
      line-height: 1.2;
      text-align: center;
      padding: 0 .2vw;
      background: #e2f0ea;
    }

    .shift-name:nth-child(even) {
      background: #f0f7f4;
    }

    .staff-col {
      display: grid;
      min-width: 0;
      border-right: 1px solid #8cbca8;
    }

    .staff-col:last-child {
      border-right: none;
    }

    .staff-row {
      display: grid;
      grid-template-columns: repeat(5, 1fr);
      align-items: center;
      border-bottom: 1px solid #fff;
      background: #e2f0ea;
      min-width: 0;
      height: 100%;
    }

    .staff-row:nth-child(even) {
      background: #f0f7f4;
    }

    .staff-row span {
      font-size: 0.68vw;
      /* Font diperbesar sedikit */
      font-weight: 700;
      padding: 0 2px;
      text-align: center;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      border-right: 1px dashed #b2d7c5;
    }

    .staff-row span:last-child {
      border-right: none;
    }

    .rr-col,
    .oncall {
      display: grid;
      border-right: 1px solid #8cbca8;
      min-width: 0;
    }

    .oncall-row {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: .2vw;
      flex-wrap: wrap;
      padding: 0 .2vw;
      border-bottom: 1px solid #fff;
      background: #e2f0ea;
      height: 100%;
    }

    .oncall-row:nth-child(even) {
      background: #f0f7f4;
    }

    .oncall-name {
      font-size: 0.70vw;
      /* Font diperbesar sedikit */
      font-weight: 700;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .shift-note-row {
      display: flex;
      align-items: center;
      justify-content: flex-start;
      padding: 0 .5vw;
      border-bottom: 1px solid #fff;
      background: #e2f0ea;
      height: 100%;
    }

    .shift-note-row:nth-child(even) {
      background: #f0f7f4;
    }

    .shift-note-row span {
      font-size: 0.68vw;
      /* Font diperbesar sedikit */
      font-weight: 700;
      white-space: pre-line;
      line-height: 1.15;
      text-align: left;
      width: 100%;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .fullscreen-btn {
      position: fixed;
      right: 1.2vw;
      top: 1.2vh;
      z-index: 30;
      border: 0;
      border-radius: 10px;
      padding: .8vh 1vw;
      background: #13795b;
      color: #fff;
      font-weight: 800;
      font-size: .75vw;
      cursor: pointer;
      box-shadow: 0 3px 10px rgba(0, 0, 0, .18);
    }

    .fullscreen-btn:hover {
      background: #0f5132;
    }

    .loading {
      position: fixed;
      inset: 0;
      background: #f0f7f4;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      font-size: 1.5vw;
      z-index: 20;
    }

    .hidden {
      display: none !important;
    }

    @keyframes pulse {

      0%,
      100% {
        transform: scale(1);
        opacity: 1;
      }

      50% {
        transform: scale(1.045);
        opacity: .75;
      }
    }

    @keyframes activeRow {

      0%,
      100% {
        background-position: 0 0;
      }

      50% {
        background-position: 100% 0;
      }
    }
  </style>
</head>

<body>
  <button
    id="fullscreenBtn"
    class="fullscreen-btn"
    type="button"
    onclick="requestTvFullscreen(true)">
    ⛶ LAYAR PENUH
  </button>

  <div id="loading" class="loading">
    Memuat jadwal operasi...
  </div>

  <div class="screen">
    <header class="head">
      <div class="brand">
        <img src="{{ asset('images/logo-rsma.png') }}" alt="Logo RS Mutiara Aini">
      </div>
      <div class="title">
        <h1>Informasi Jadwal Operasi</h1>
      </div>
      <div class="clock">
        <div id="dateText" class="date"></div>
        <div id="timeText" class="time"></div>
      </div>
    </header>

    <div class="table-wrap">
      <table class="grid">
        <colgroup>
          <col style="width:3%">
          <col style="width:13.5%">
          <col style="width:3%">
          <col style="width:6.5%">
          <col style="width:17.5%">
          <col style="width:9%">
          <col style="width:14%">
          <col style="width:9.5%">
          <col style="width:14%">
          <col style="width:4.5%">
          <col style="width:4.5%">
        </colgroup>
        <thead>
          <tr>
            <th>No</th>
            <th>Nama Pasien</th>
            <th>JK</th>
            <th>Jam</th>
            <th>Diagnosa</th>
            <th>Tindakan</th>
            <th>Operator</th>
            <th>Anastesi</th>
            <th>Tim Bedah</th>
            <th>CITO</th>
            <th>OK</th>
          </tr>
        </thead>
        <tbody id="rows"></tbody>
      </table>
    </div>

    <footer class="footer">
      <div class="foot-title">
        <div>Shift & Jam</div>
        <div>Dinas</div>
        <div>RR (Recovery Room)</div>
        <div>On Call</div>
        <div>Catatan Shift</div>
      </div>
      <div class="foot-grid">
        <div class="shift-col" id="shiftNames"></div>
        <div class="staff-col" id="staffRows"></div>
        <div class="rr-col" id="rrRows"></div>
        <div class="oncall" id="oncall"></div>
        <div class="staff-col" id="shiftNotes"></div>
      </div>
    </footer>
  </div>

  <script>
    const EVENTS_URL = @json(route('display.events'));
    const LOGIN_URL = @json(route('login'));

    async function requestTvFullscreen(showPrompt = false) {
      if (document.fullscreenElement) return true;
      try {
        await document.documentElement.requestFullscreen({
          navigationUI: 'hide'
        });
        return true;
      } catch (e) {
        return false;
      }
    }

    window.addEventListener('load', async function() {
      const ok = await requestTvFullscreen(false);
      if (!ok && !sessionStorage.getItem('displayFullscreenPrompted')) {
        sessionStorage.setItem('displayFullscreenPrompted', '1');
        setTimeout(async function() {
          const pesan = 'Tampilan Informasi Jadwal Operasi akan dibuka dalam mode layar penuh untuk TV.\n\n' +
            'Klik OK untuk masuk layar penuh.';
          const lanjut = window.confirm(pesan);
          if (lanjut) await requestTvFullscreen(true);
        }, 400);
      }
    });

    document.addEventListener('fullscreenchange', function() {
      const btn = document.getElementById('fullscreenBtn');
      if (document.fullscreenElement) {
        document.body.classList.add('tv-fullscreen');
        if (btn) btn.style.display = 'none';
      } else {
        document.body.classList.remove('tv-fullscreen');
        if (btn) btn.style.display = 'block';
      }
    });

    function escapeHtml(s) {
      return String(s).replace(/[&<>\"']/g, c => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '\"': '&quot;',
        "'": '&#39;'
      } [c]));
    }

    const fmtDate = new Intl.DateTimeFormat('id-ID', {
      weekday: 'long',
      day: '2-digit',
      month: 'long',
      year: 'numeric'
    });

    const pad = n => String(n).padStart(2, '0');
    let displayData = null;
    let lastSignature = '';

    const announcedIds = new Set();
    let speechQueue = [];
    let speaking = false;

    function enqueueAnnouncements(list) {
      if (!('speechSynthesis' in window)) return;
      const now = new Date();
      const active = (list || [])
        .filter(s => scheduleState(s, now) === 'active')
        .sort((a, b) => {
          const okCmp = String(a.ok_level || '').localeCompare(String(b.ok_level || ''));
          if (okCmp !== 0) return okCmp;
          return String(a.operation_time).localeCompare(String(b.operation_time));
        });

      for (const s of active) {
        if (announcedIds.has(String(s.id))) continue;
        announcedIds.add(String(s.id));
        const patient = s.patient || {};
        const namaPasien = patient.display_name || patient.name || 'tanpa nama';
        const ruangOk = s.ok_level || 'ruang operasi';
        const jamOp = String(s.operation_time || '').slice(0, 5);

        speechQueue.push({
          id: s.id,
          text: `Perhatian. Pasien ${namaPasien}, di ${ruangOk}, jadwal pukul ${jamOp}.`
        });
      }
      playNextAnnouncement();
    }

    function playNextAnnouncement() {
      if (speaking || !speechQueue.length || !('speechSynthesis' in window)) return;
      const item = speechQueue.shift();
      speaking = true;
      const utterance = new SpeechSynthesisUtterance(item.text);
      utterance.lang = 'id-ID';
      utterance.rate = 0.88;
      utterance.pitch = 1;
      const voices = speechSynthesis.getVoices();
      const voice = voices.find(v => /^id(-|_)/i.test(v.lang)) ||
        voices.find(v => /indonesia/i.test(v.name));
      if (voice) utterance.voice = voice;
      utterance.onend = utterance.onerror = () => {
        speaking = false;
        setTimeout(playNextAnnouncement, 350);
      };
      speechSynthesis.speak(utterance);
    }

    if ('speechSynthesis' in window) {
      speechSynthesis.onvoiceschanged = () => {};
    }

    function tick() {
      const n = new Date();
      document.getElementById('dateText').textContent = fmtDate.format(n);
      document.getElementById('timeText').innerHTML =
        `${pad(n.getHours())} : ${pad(n.getMinutes())} <span>WIB</span>`;
      if (displayData) render(displayData, false);
    }
    tick();
    setInterval(tick, 1000);

    function scheduleState(s, now) {
      const jam = String(s.operation_time).slice(0, 5);
      const start = new Date(`${s.operation_date}T${jam}:00`);
      const end = new Date(start.getTime() + 90 * 60000);
      if (s.status === 'Batal' || s.status === 'Selesai' || now >= end) return 'past';
      if (now >= start && now < end) return 'active';
      return 'upcoming';
    }

    function visibleSchedules(list) {
      const now = new Date();
      return (list || [])
        .map(s => ({
          ...s,
          _state: scheduleState(s, now)
        }))
        .filter(s => s._state !== 'past')
        .sort((a, b) => {
          const rank = {
            active: 0,
            upcoming: 1
          };
          const ra = rank[a._state];
          const rb = rank[b._state];
          if (ra !== rb) return ra - rb;
          return String(a.operation_time).localeCompare(String(b.operation_time));
        })
        .slice(0, 9); // Tepat 9 baris pasien
    }

    function render(data, force = true) {
      const visible = visibleSchedules(data.schedules || []);
      const sig = visible.map(s => `${s.id}:${s._state}:${s.operation_time}`).join('|');
      if (!force && sig === lastSignature) return;
      lastSignature = sig;

      const rows = document.getElementById('rows');
      rows.innerHTML = '';

      visible.forEach((s, i) => {
        const p = s.patient || {};
        const ok = String(s.ok_level || 'OK 1').replace(/\s+/g, '').toLowerCase();
        const active = s._state === 'active';
        const rowClass = active ? 'row-active' : '';
        const pulseClass = active ? 'active-pulse' : '';
        const jamText = String(s.operation_time || '').slice(0, 5);
        const runningBadge = active ?
          '<span class="running active-pulse">● SEDANG OPERASI</span>' :
          '';
        const citoBadge = s.cito ? '<span class="cito">CITO</span>' : '';
        const timBedah = s.surgical_team_names || (Array.isArray(s.surgical_teams) ? s.surgical_teams.map(t => t.name).filter(Boolean).join(', ') : '') || '-';

        const rowHtml = `
          <tr class="${rowClass}">
            <td>${i + 1}</td>
            <td class="name">${escapeHtml(p.display_name || p.name || '-')}</td>
            <td>${escapeHtml(p.gender || '-')}</td>
            <td>
              <strong>${jamText} WIB</strong>
              ${runningBadge}
            </td>
            <td>${escapeHtml(s.diagnosis || '-')}</td>
            <td>${escapeHtml(s.procedure || '-')}</td>
            <td>${escapeHtml(s.operator?.name || '-')}</td>
            <td>${escapeHtml(s.anesthesia?.name || '-')}</td>
            <td>${escapeHtml(timBedah)}</td>
            <td>${citoBadge}</td>
            <td>
              <span class="ok ${ok} ${pulseClass}">${escapeHtml(s.ok_level || '-')}</span>
            </td>
          </tr>
        `;
        rows.insertAdjacentHTML('beforeend', rowHtml);
      });

      const emptyRowHtml = `
        <tr class="empty-row">
          <td>&nbsp;</td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
      `;
      for (let i = visible.length; i < 9; i++) {
        rows.insertAdjacentHTML('beforeend', emptyRowHtml);
      }

      const shifts = data.shifts || [];
      const totalShifts = Math.max(1, shifts.length);
      const rowTemplate = `repeat(${totalShifts}, 1fr)`;

      document.querySelectorAll('.shift-col, .staff-col, .rr-col, .oncall').forEach(el => {
        el.style.gridTemplateRows = rowTemplate;
      });

      // 1. Kolom Nama Shift & Jam Dinas
      document.getElementById('shiftNames').innerHTML =
        shifts.map(s => {
          const startTime = s.start_time ? s.start_time.substring(0, 5) : '';
          const endTime = s.end_time ? s.end_time.substring(0, 5) : '';
          const timeStr = (startTime && endTime) ?
            `<span style="font-size:0.60vw; font-weight:700; opacity:0.85; margin-top:2px;">${escapeHtml(startTime)} - ${escapeHtml(endTime)}</span>` : '';
          return `<div class="shift-name"><span style="font-weight:900; font-size:0.75vw; color:#1b7a5a;">${escapeHtml(s.name)}</span>${timeStr}</div>`;
        }).join('');

      // 2. Kolom Dinas (Mendukung hingga seluruh anggota tim yang bertugas)
      document.getElementById('staffRows').innerHTML =
        shifts.map(s => {
          const m = s.members || [];
          const colCount = Math.max(5, m.length);
          let spans = '';
          for (let i = 0; i < colCount; i++) {
            const name = m[i] || '';
            spans += `<span title="${escapeHtml(name)}">${escapeHtml(name)}</span>`;
          }
          return `<div class="staff-row" style="grid-template-columns: repeat(${colCount}, 1fr);">${spans}</div>`;
        }).join('');

      // 3. Kolom RR (Recovery Room)
      document.getElementById('rrRows').innerHTML =
        shifts.map(s => {
          const listRr = s.rr || [];
          const content = listRr.length ?
            listRr.map(n => `<span class="oncall-name" title="${escapeHtml(n)}">${escapeHtml(n)}</span>`).join('') :
            '<span style="opacity:.45">-</span>';
          return `<div class="oncall-row">${content}</div>`;
        }).join('');

      // 4. Kolom On Call
      document.getElementById('oncall').innerHTML =
        shifts.map(s => {
          const listOncall = s.oncall || [];
          const content = listOncall.length ?
            listOncall.map(n => `<span class="oncall-name" title="${escapeHtml(n)}">${escapeHtml(n)}</span>`).join('') :
            '<span style="opacity:.45">-</span>';
          return `<div class="oncall-row">${content}</div>`;
        }).join('');

      // 5. Kolom Catatan Shift (Rata Kiri & Mendukung Poin-poin)
      document.getElementById('shiftNotes').innerHTML =
        shifts.map(s => {
          const noteTitle = escapeHtml(s.note || '');
          const noteText = escapeHtml(s.note || '-');
          return `
            <div class="shift-note-row">
              <span title="${noteTitle}">${noteText}</span>
            </div>
          `;
        }).join('');

      document.getElementById('loading').classList.add('hidden');
    }

    async function load() {
      try {
        const d = new Date();
        const q = d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
        const r = await fetch(EVENTS_URL + '?date=' + q, {
          cache: 'no-store',
          credentials: 'same-origin'
        });

        if (!r.ok) throw new Error('HTTP ' + r.status);

        displayData = await r.json();
        enqueueAnnouncements(displayData.schedules || []);
        render(displayData, true);
      } catch (e) {
        console.warn('Gagal memperbarui data display (mencoba lagi dalam 3 detik):', e);
        if (!displayData) {
          const loadingEl = document.getElementById('loading');
          if (loadingEl) {
            loadingEl.textContent = 'Menghubungkan ke server jadwal operasi...';
          }
        }
      }
    }

    load();
    setInterval(load, 3000);
  </script>
</body>

</html>