@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">Data Users</h6>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addEditUserModal">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert" id="errorAlert">
                            <strong>Error!</strong> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
                            <strong>Success!</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <div class="table-responsive">
                        <table id="dataTableExample" class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Details</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr data-user="{{ json_encode($user) }}">
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->phone }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->username }}</td>
                                        <td>{{ ucfirst($user->role) }}</td>
                                        <td>
                                            @if($user->role === 'guru' && $user->guru)
                                                NIP: {{ $user->guru->nip }}<br>
                                                Subject: {{ $user->guru->subject->subject ?? '-' }}
                                            @elseif($user->role === 'siswa' && $user->siswa)
                                                NIS: {{ $user->siswa->nis }}<br>
                                                Class: {{ $user->siswa->kelas->class ?? '-' }} {{ $user->siswa->jurusan->jurusan ?? '-' }}
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info edit-btn" data-bs-toggle="modal" data-bs-target="#addEditUserModal">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger delete-btn">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <form method="POST" action="{{ route('users.delete', $user->user_id) }}" style="display:none;" class="delete-form">
                                                @csrf
                                                @method('DELETE')
                                            </form>
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

    <div class="modal fade" id="addEditUserModal" tabindex="-1" aria-labelledby="addEditUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEditUserModalLabel">Add/Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('users.save') }}" id="userForm">
                    @csrf
                    <input type="hidden" name="user_id" id="user_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password">
                            <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="">Select Role</option>
                                <option value="admin">Admin</option>
                                <option value="guru">Guru</option>
                                <option value="siswa">Siswa</option>
                            </select>
                        </div>
                        <div id="guruFields" style="display: none;">
                            <hr>
                            <h6>Guru Details</h6>
                            <div class="mb-3">
                                <label for="nip" class="form-label">NIP</label>
                                <input type="text" class="form-control" id="nip" name="nip">
                            </div>
                            <div class="mb-3">
                                <label for="subject_id" class="form-label">Subject</label>
                                <select class="form-select" id="subject_id" name="subject_id">
                                    <option value="">Select Subject</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->subject_id }}">{{ $subject->subject }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="education" class="form-label">Education</label>
                                <input type="text" class="form-control" id="education" name="education">
                            </div>
                            <div class="mb-3">
                                <label for="hire_date" class="form-label">Hire Date</label>
                                <input type="date" class="form-control" id="hire_date" name="hire_date">
                            </div>
                            <div class="mb-3">
                                <label for="guru_gender" class="form-label">Gender</label>
                                <select class="form-select" id="guru_gender" name="gender_guru">
                                    <option value="">Select Gender</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                        </div>
                        <div id="siswaFields" style="display: none;">
                            <hr>
                            <h6>Siswa Details</h6>
                            <div class="mb-3">
                                <label for="nis" class="form-label">NIS</label>
                                <input type="text" class="form-control" id="nis" name="nis">
                            </div>
                            <div class="mb-3">
                                <label for="tahun_masuk" class="form-label">Tahun Masuk</label>
                                <input type="number" class="form-control" id="tahun_masuk" name="tahun_masuk">
                            </div>
                            <div class="mb-3">
                                <label for="class_id" class="form-label">Class</label>
                                <select class="form-select" id="class_id" name="class_id">
                                    <option value="">Select Class</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->class_id }}">{{ $class->class }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="jurusan_id" class="form-label">Jurusan</label>
                                <select class="form-select" id="jurusan_id" name="jurusan_id">
                                    <option value="">Select Jurusan</option>
                                    @foreach($jurusans as $jurusan)
                                        <option value="{{ $jurusan->jurusan_id }}">{{ $jurusan->jurusan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="siswa_gender" class="form-label">Gender</label>
                                <select class="form-select" id="siswa_gender" name="gender_siswa">
                                    <option value="">Select Gender</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('#role').change(function() {
            var role = $(this).val();
            $('#guruFields').hide();
            $('#siswaFields').hide();
            $('#guruFields input, #guruFields select').prop('required', false);
            $('#siswaFields input, #siswaFields select').prop('required', false);
            if (role === 'guru') {
                $('#guruFields').show();
                $('#guruFields input, #guruFields select').prop('required', true);
            } else if (role === 'siswa') {
                $('#siswaFields').show();
                $('#siswaFields input, #siswaFields select').prop('required', true);
            }
        });

        $('#addEditUserModal').on('hidden.bs.modal', function() {
            $('#userForm')[0].reset();
            $('#user_id').val('');
            $('#guruFields').hide();
            $('#siswaFields').hide();
            $('#guruFields input, #guruFields select').prop('required', false);
            $('#siswaFields input, #siswaFields select').prop('required', false);
        });

        $('.edit-btn').on('click', function() {
            var row = $(this).closest('tr');
            var user = row.data('user');
            $('#user_id').val(user.user_id);
            $('#name').val(user.name);
            $('#phone').val(user.phone);
            $('#email').val(user.email);
            $('#username').val(user.username);
            $('#role').val(user.role).trigger('change');
            if (user.role === 'guru' && user.guru) {
                $('#nip').val(user.guru.nip);
                $('#subject_id').val(user.guru.subject_id);
                $('#education').val(user.guru.education);
                $('#hire_date').val(user.guru.hire_date);
                $('#guru_gender').val(user.guru.gender);
            } else if (user.role === 'siswa' && user.siswa) {
                $('#nis').val(user.siswa.nis);
                $('#tahun_masuk').val(user.siswa.tahun_masuk);
                $('#class_id').val(user.siswa.class_id);
                $('#jurusan_id').val(user.siswa.jurusan_id);
                $('#siswa_gender').val(user.siswa.gender);
            }
        });

        setTimeout(function() {
            $('#errorAlert').alert('close');
            $('#successAlert').alert('close');
        }, 3000);

        $('.delete-btn').on('click', function(e) {
            e.preventDefault();
            var form = $(this).siblings('.delete-form');
            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
