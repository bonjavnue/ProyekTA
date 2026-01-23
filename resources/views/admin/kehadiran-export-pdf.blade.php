<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Kehadiran Pelatihan</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #1e40af;
            padding-bottom: 15px;
        }
        
        .header h1 {
            margin: 0;
            color: #1e40af;
            font-size: 24px;
        }
        
        .header p {
            margin: 5px 0;
            color: #666;
            font-size: 12px;
        }
        
        .info-section {
            background-color: #f0f4f8;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border-left: 4px solid #1e40af;
        }
        
        .info-section h3 {
            margin-top: 0;
            color: #1e40af;
            font-size: 14px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            font-size: 12px;
        }
        
        .info-item {
            margin: 0;
        }
        
        .info-label {
            font-weight: bold;
            color: #555;
        }
        
        .info-value {
            color: #333;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 11px;
        }
        
        th {
            background-color: #1e40af;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #0f2847;
        }
        
        td {
            padding: 10px 12px;
            border: 1px solid #ddd;
        }
        
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        tr:hover {
            background-color: #f0f4f8;
        }
        
        .status-hadir {
            background-color: #dcfce7;
            color: #166534;
            padding: 4px 8px;
            border-radius: 3px;
            font-weight: bold;
        }
        
        .status-sakit {
            background-color: #fef3c7;
            color: #92400e;
            padding: 4px 8px;
            border-radius: 3px;
            font-weight: bold;
        }
        
        .status-izin {
            background-color: #dbeafe;
            color: #0c4a6e;
            padding: 4px 8px;
            border-radius: 3px;
            font-weight: bold;
        }
        
        .status-alpa {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 4px 8px;
            border-radius: 3px;
            font-weight: bold;
        }
        
        .status-belum {
            background-color: #f3f4f6;
            color: #374151;
            padding: 4px 8px;
            border-radius: 3px;
            font-weight: bold;
        }
        
        .summary {
            margin-top: 20px;
            padding: 15px;
            background-color: #f0f4f8;
            border-radius: 5px;
        }
        
        .summary h3 {
            margin-top: 0;
            color: #1e40af;
            font-size: 14px;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            font-size: 12px;
        }
        
        .summary-item {
            text-align: center;
            padding: 10px;
            background-color: white;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        
        .summary-number {
            font-size: 18px;
            font-weight: bold;
            color: #1e40af;
        }
        
        .summary-label {
            color: #666;
            font-size: 11px;
            margin-top: 5px;
        }
        
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
            color: #666;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }
        
        .no-data {
            text-align: center;
            padding: 20px;
            color: #999;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>📋 Laporan Kehadiran Pelatihan</h1>
    </div>

    <!-- Info Jadwal -->
    <div class="info-section">
        <h3>Informasi Jadwal Pelatihan</h3>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Jenis Pelatihan:</span>
                <span class="info-value">{{ $jadwal->JenisPelatihan->nama_jenis ?? '-' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Tanggal Pelaksanaan:</span>
                <span class="info-value">{{ $jadwal->tanggal_pelaksanaan->format('d M Y') }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Waktu Pelatihan:</span>
                <span class="info-value">{{ $jadwal->jam_mulai->format('H:i') }} - {{ $jadwal->jam_selesai->format('H:i') }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Tempat Pelaksanaan:</span>
                <span class="info-value">{{ $jadwal->tempat }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Total Peserta:</span>
                <span class="info-value">{{ count($karyawans) }} orang</span>
            </div>
            <div class="info-item">
                <span class="info-label">Tanggal Laporan:</span>
                <span class="info-value">{{ now()->format('d M Y H:i') }}</span>
            </div>
        </div>
    </div>

    <!-- Tabel Kehadiran -->
    @if(count($karyawans) > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th style="width: 25%;">Nama Karyawan</th>
                    <th style="width: 12%;">NIK</th>
                    <th style="width: 20%;">Bagian</th>
                    <th style="width: 15%;">Status</th>
                    <th style="width: 18%;">Waktu Presensi</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $no = 1;
                    $hadir = 0;
                    $sakit = 0;
                    $izin = 0;
                    $alpa = 0;
                    $belum = 0;
                @endphp
                
                @foreach($karyawans as $karyawan)
                    <tr>
                        <td style="text-align: center;">{{ $no++ }}</td>
                        <td>{{ $karyawan['nama_karyawan'] }}</td>
                        <td style="text-align: center;">{{ $karyawan['nik'] }}</td>
                        <td>{{ $karyawan['bagian'] }}</td>
                        <td style="text-align: center;">
                            @if($karyawan['status'] === 'Hadir')
                                @php $hadir++ @endphp
                                <span class="status-hadir">✓ Hadir</span>
                            @elseif($karyawan['status'] === 'Sakit')
                                @php $sakit++ @endphp
                                <span class="status-sakit">Sakit</span>
                            @elseif($karyawan['status'] === 'Izin')
                                @php $izin++ @endphp
                                <span class="status-izin">Izin</span>
                            @elseif($karyawan['status'] === 'Alpa')
                                @php $alpa++ @endphp
                                <span class="status-alpa">Alpa</span>
                            @else
                                @php $belum++ @endphp
                                <span class="status-belum">Belum</span>
                            @endif
                        </td>
                        <td style="text-align: center; font-family: monospace;">
                            @if($karyawan['waktu_presensi'])
                                {{ \Carbon\Carbon::parse($karyawan['waktu_presensi'])->format('d/m/Y H:i') }}
                            @else
                                <span style="color: #999;">--/--/-- --:--</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary -->
        <div class="summary">
            <h3>Ringkasan Kehadiran</h3>
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="summary-number" style="color: #16a34a;">{{ $hadir }}</div>
                    <div class="summary-label">Hadir</div>
                </div>
                <div class="summary-item">
                    <div class="summary-number" style="color: #ea580c;">{{ $sakit }}</div>
                    <div class="summary-label">Sakit</div>
                </div>
                <div class="summary-item">
                    <div class="summary-number" style="color: #0ea5e9;">{{ $izin }}</div>
                    <div class="summary-label">Izin</div>
                </div>
                <div class="summary-item">
                    <div class="summary-number" style="color: #dc2626;">{{ $alpa }}</div>
                    <div class="summary-label">Alpa</div>
                </div>
                <div class="summary-item">
                    <div class="summary-number" style="color: #6b7280;">{{ $belum }}</div>
                    <div class="summary-label">Belum Presensi</div>
                </div>
            </div>
        </div>
    @else
        <div class="no-data">
            Belum ada data karyawan untuk pelatihan ini
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Laporan ini dihasilkan secara otomatis pada {{ now()->format('d F Y \\j\\a\\m H:i:s') }}</p>
    </div>
</body>
</html>
