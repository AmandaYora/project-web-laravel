@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">Data Guru</h6>
                        <button type="button" class="btn btn-primary btn-sm" onclick="window.location.href='{{ route('users.index') }}'">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table id="dataTableExample" class="table">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>NIP</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Pendidikan</th>
                                    <th>Tanggal Bergabung</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Kontak</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($gurus as $guru)
                                    <tr>
                                        <td>{{ $guru->user->name }}</td>
                                        <td>{{ $guru->nip }}</td>
                                        <td>{{ $guru->subject->subject }}</td>
                                        <td>{{ $guru->education }}</td>
                                        <td>{{ $guru->hire_date->format('d-m-Y') }}</td>
                                        <td>{{ $guru->gender }}</td>
                                        <td>
                                            <div>Email: {{ $guru->user->email }}</div>
                                            <div>Telepon: {{ $guru->user->phone }}</div>
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
