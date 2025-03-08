@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">Data Siswa</h6>
                        <button type="button" class="btn btn-primary btn-sm" onclick="window.location.href='{{ route('users.index') }}'">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table id="dataTableExample" class="table">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>NIS</th>
                                    <th>Tahun Masuk</th>
                                    <th>Kelas</th>
                                    <th>Jurusan</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Kontak</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($siswas as $siswa)
                                    <tr>
                                        <td>{{ $siswa->user->name }}</td>
                                        <td>{{ $siswa->nis }}</td>
                                        <td>{{ $siswa->tahun_masuk }}</td>
                                        <td>{{ $siswa->kelas->class ?? 'Kelas tidak ditemukan' }}</td>
                                        <td>{{ $siswa->jurusan->jurusan ?? 'Jurusan tidak ditemukan' }}</td>
                                        <td>{{ $siswa->gender }}</td>
                                        <td>
                                            <div>Email: {{ $siswa->user->email }}</div>
                                            <div>Phone: {{ $siswa->user->phone }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
