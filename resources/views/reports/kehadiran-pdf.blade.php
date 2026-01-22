<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kehadiran</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            padding: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 3px solid #05339C;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #05339C;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .header p {
            color: #666;
            font-size: 14px;
        }
        
        .report-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            font-size: 13px;
        }
        
        .report-info div {
            padding: 8px 0;
        }
        
        .report-info strong {
            display: block;
            color: #05339C;
            margin-bottom: 4px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        
        table thead {
            background-color: #05339C;
            color: white;
        }
        
        table th {
            padding: 12px;
            text-align: left;
            font-weight: bold;
            font-size: 12px;
            border: 1px solid #ddd;
        }
        
        table td {
            padding: 10px 12px;
            border: 1px solid #ddd;
            font-size: 12px;
        }
        
        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 11px;
        }
        
        .status-ongoing {
            background-color: #FCD34D;
            color: #7C2D12;
        }
        
        .status-upcoming {
            background-color: #FED7AA;
            color: #7C2D12;
        }
        
        .status-ended {
            background-color: #E5E7EB;
            color: #4B5563;
        }
        
        .summary {
            margin-top: 40px;
            padding: 20px;
            background-color: #F3F4F6;
            border-radius: 5px;
        }
        
        .summary h3 {
            color: #05339C;
            margin-bottom: 15px;
            font-size: 14px;
        }
        
        .summary-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }
        
        .stat-box {
            background: white;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #05339C;
            text-align: center;
        }
        
        .stat-box .label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .stat-box .value {
            font-size: 20px;
            font-weight: bold;
            color: #05339C;
        }
        
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        
        @media print {
            body {
                padding: 0;
            }
            .container {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>📊 LAPORAN KEHADIRAN PELATIHAN</h1>
            <p>Ringkasan Data Kehadiran Peserta Pelatihan</p>
        </div>
        
        <!-- Report Info -->
        <div class="report-info">
            <div>
                <strong>Tanggal Laporan:</strong>
                {{ now()->format('d M Y H:i') }}
            </div>
            <div>
                <strong>Total Jadwal:</strong>
                {{ count($jadwals) }}
            </div>
            <div>
                <strong>Total Peserta:</strong>
                {{ collect($jadwals)->sum('total_karyawan') }}
            </div>
        </div>
        
        <!-- Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 20%;">Nama Pelatihan</th>
                    <th style="width: 12%;">Tanggal</th>
                    <th style="width: 15%;">Lokasi</th>
                    <th class="text-center" style="width: 8%;">Total</th>
                    <th class="text-center" style="width: 8%;">Hadir</th>
                    <th class="text-center" style="width: 8%;">Belum</th>
                    <th class="text-center" style="width: 8%;">%</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jadwals as $key => $jadwal)
                <tr>
                    <td class="text-center">{{ $key + 1 }}</td>
                    <td>
                        <strong>{{ $jadwal['nama_jenis'] }}</strong><br>
                        <small style="color: #999;">Jam: {{ $jadwal['jam_mulai'] }} - {{ $jadwal['jam_selesai'] }}</small>
                    </td>
                    <td>{{ $jadwal['tanggal'] }}</td>
                    <td>{{ $jadwal['tempat'] }}</td>
                    <td class="text-center">
                        <strong>{{ $jadwal['total_karyawan'] }}</strong>
                    </td>
                    <td class="text-center">
                        <span style="color: #22C55E; font-weight: bold;">{{ $jadwal['hadir'] }}</span>
                    </td>
                    <td class="text-center">
                        <span style="color: #EF4444; font-weight: bold;">{{ $jadwal['belum_absen'] }}</span>
                    </td>
                    <td class="text-center">
                        <strong style="color: #05339C;">{{ $jadwal['persentase'] }}</strong>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px; color: #999;">
                        Tidak ada data laporan kehadiran
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <!-- Summary Statistics -->
        @if(count($jadwals) > 0)
        <div class="summary">
            <h3>📈 Ringkasan Statistik</h3>
            <div class="summary-stats">
                <div class="stat-box">
                    <div class="label">Total Jadwal</div>
                    <div class="value">{{ count($jadwals) }}</div>
                </div>
                <div class="stat-box">
                    <div class="label">Total Peserta</div>
                    <div class="value">{{ collect($jadwals)->sum('total_karyawan') }}</div>
                </div>
                <div class="stat-box">
                    <div class="label">Total Hadir</div>
                    <div class="value" style="color: #22C55E;">{{ collect($jadwals)->sum('hadir') }}</div>
                </div>
                <div class="stat-box">
                    <div class="label">Rata-rata Kehadiran</div>
                    <div class="value">
                        @php
                            $totalPeserta = collect($jadwals)->sum('total_karyawan');
                            $totalHadir = collect($jadwals)->sum('hadir');
                            $rataRata = $totalPeserta > 0 ? round(($totalHadir / $totalPeserta) * 100) : 0;
                        @endphp
                        {{ $rataRata }}%
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Footer -->
        <div class="footer">
            <p>Dokumen ini digenerate secara otomatis oleh Sistem Manajemen Kehadiran</p>
            <p style="margin-top: 10px;">© {{ date('Y') }} - Nama Perusahaan</p>
        </div>
    </div>
</body>
</html>
