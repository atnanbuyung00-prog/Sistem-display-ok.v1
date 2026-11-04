<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Jadwal Operasi {{ $date }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #123;
            margin: 25px
        }

        h1 {
            margin: 0;
            color: #1b7a5a
        }

        p {
            margin: 4px 0 16px
        }

        .toolbar {
            margin-bottom: 16px
        }

        button {
            padding: 9px 14px;
            border: 1px solid #1b7a5a;
            background: #1b7a5a;
            color: white;
            border-radius: 6px
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px
        }

        th {
            background: #1b7a5a;
            color: white;
            padding: 8px;
            border: 1px solid #ccc
        }

        td {
            padding: 7px;
            border: 1px solid #ccc
        }

        tr:nth-child(even) {
            background: #f4f9f6
        }

        .cito {
            font-weight: bold;
            color: #c00
        }

        @media print {
            .toolbar {
                display: none
            }

            body {
                margin: 8mm
            }

            th {
                background: #1b7a5a !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact
            }
        }
    </style>
</head>

<body>
    <div class="toolbar"><button onclick="window.print()">Cetak / Simpan sebagai PDF</button> <button onclick="window.close()">Tutup</button></div>
    <h1>Jadwal Operasi</h1>
    <p>Rumah Sakit Mutiara Aini — {{ \Carbon\Carbon::parse($date)->locale('id')->translatedFormat('l, d F Y') }}</p>
    <table>
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
            </tr>
        </thead>
        <tbody>
            @forelse($schedules as $i=>$s)<tr>
                <td>{{ $i+1 }}</td>
                <td>{{ substr($s->operation_time,0,5) }}</td>
                <td>{{ $s->ok_level }}</td>
                <td>{{ $s->patient->name }}</td>
                <td>{{ $s->patient->gender }}</td>
                <td>{{ $s->diagnosis }}</td>
                <td>{{ $s->procedure }}</td>
                <td>{{ $s->operator?->name }}</td>
                <td>{{ $s->anesthesia?->name }}</td>
                <td>{{ $s->surgical_team_names }}</td>
                <td class="cito">{{ $s->cito?'CITO':'' }}</td>
            </tr>@empty<tr>
                <td colspan="12" style="text-align:center">Tidak ada jadwal.</td>
            </tr>@endforelse
        </tbody>
    </table>
    <p style="margin-top:20px">Dicetak dari Sistem Jadwal Operasi RS Mutiara Aini.</p>
</body>

</html>