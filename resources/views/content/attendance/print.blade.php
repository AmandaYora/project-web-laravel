@php
 use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html>
<head>
    <title>Data Kehadiran Bulan {{ $namaBulan }} {{ $year }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        @media print {
            .no-print {
                display: none;
            }
            table {
                border-collapse: collapse !important;
                width: 100%;
                font-size: 12px;
            }
            th, td {
                border: 1px solid #ddd !important;
                padding: 8px !important;
            }
        }
        body {
            background-color: #f8f9fa;
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: none;
            margin-top: 20px;
        }
        .table thead th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        .table tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .header, .footer {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            font-size: 2rem;
            margin-bottom: 5px;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #888;
        }
        .badge {
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Data Kehadiran Siswa - Bulan {{ $namaBulan }} {{ $year }}</h2>
            <p class="text-muted">Laporan Kehadiran Siswa Sekolah</p>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <button class="btn btn-primary no-print mb-3" id="printButton">
                    <i class="bi bi-printer"></i> Cetak Laporan
                </button>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama Siswa</th>
                            <th>Mata Pelajaran</th>
                            <th>Kelas</th>
                            <th>Jurusan</th>
                            <th>Status Kehadiran</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendance as $data)
                            <tr>
                                <td>{{ $data->user->name }}</td>
                                <td>{{ $data->classSession->mapel->subject->subject }}</td>
                                <td>{{ $data->classSession->mapel->class->class }}</td>
                                <td>{{ $data->classSession->mapel->jurusan->jurusan }}</td>
                                <td>
                                    <span class="badge 
                                        @if($data->status === 'present') bg-success 
                                        @elseif($data->status === 'late') bg-warning 
                                        @else bg-danger 
                                        @endif">
                                        {{ ucfirst($data->status) }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($data->date)->translatedFormat('d F Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Sekolah Menengah. Semua Hak Dilindungi.</p>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#printButton').on('click', function() {
                window.print();
            });
            window.onload = function() {
                window.print();
            };
        });
    </script>
</body>
</html>