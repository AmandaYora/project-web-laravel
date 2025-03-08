@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">Data Schedule</h6>
                        @if (session('user.role') !== 'siswa')
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#addEditMapelModal">
                                <i class="fas fa-plus"></i>
                            </button>
                        @endif
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

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert" id="validationErrorAlert">
                            <strong>Error!</strong> Silakan periksa inputan Anda.
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table id="dataTableExample" class="table">
                            <thead>
                                <tr>
                                    <th>Day</th>
                                    <th>Subject</th>
                                    <th>Jurusan</th>
                                    <th>Class</th>
                                    <th>Time</th>
                                    <th>Date</th>
                                    @if (session('user.role') !== 'siswa')
                                        <th>Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($mapels as $mapel)
                                    <tr data-mapel="{{ json_encode($mapel) }}">
                                        <td>{{ $mapel->day }}</td>
                                        <td>{{ $mapel->subject->subject }}</td>
                                        <td>{{ $mapel->jurusan->jurusan }}</td>
                                        <td>{{ $mapel->class->class }}</td>
                                        <td>{{ $mapel->start_time->format('H:i') }} - {{ $mapel->end_time->format('H:i') }}</td>
                                        <td>{{ $mapel->date->format('Y-m-d') }}</td>
                                        @if (session('user.role') !== 'siswa')
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info edit-btn"
                                                    data-bs-toggle="modal" data-bs-target="#addEditMapelModal">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger delete-btn">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <form method="POST" action="{{ route('mapel.delete', $mapel->mapel_id) }}"
                                                    style="display:none;" class="delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (session('user.role') !== 'siswa')
        <div class="modal fade" id="addEditMapelModal" tabindex="-1" aria-labelledby="addEditMapelModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addEditMapelModalLabel">Add/Edit Schedule</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('mapel.save') }}" id="mapelForm">
                        @csrf
                        <input type="hidden" name="mapel_id" id="mapel_id">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="day" class="form-label">Day</label>
                                <select class="form-select" id="day" name="day" required>
                                    <option value="">Select Day</option>
                                    <option value="Monday">Monday</option>
                                    <option value="Tuesday">Tuesday</option>
                                    <option value="Wednesday">Wednesday</option>
                                    <option value="Thursday">Thursday</option>
                                    <option value="Friday">Friday</option>
                                    <option value="Saturday">Saturday</option>
                                    <option value="Sunday">Sunday</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="subject_id" class="form-label">Subject</label>
                                <select class="form-select" id="subject_id" name="subject_id" required>
                                    <option value="">Select Subject</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->subject_id }}">{{ $subject->subject }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="jurusan_id" class="form-label">Jurusan</label>
                                <select class="form-select" id="jurusan_id" name="jurusan_id" required>
                                    <option value="">Select Jurusan</option>
                                    @foreach($jurusans as $jurusan)
                                        <option value="{{ $jurusan->jurusan_id }}">{{ $jurusan->jurusan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="class_id" class="form-label">Class</label>
                                <select class="form-select" id="class_id" name="class_id" required>
                                    <option value="">Select Class</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->class_id }}">{{ $class->class }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="start_time" class="form-label">Start Time</label>
                                <input type="time" class="form-control" id="start_time" name="start_time" required>
                            </div>
                            <div class="mb-3">
                                <label for="end_time" class="form-label">End Time</label>
                                <input type="time" class="form-control" id="end_time" name="end_time" required>
                            </div>
                            <div class="mb-3">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="date" name="date" required>
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
    @endif
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#addEditMapelModal').on('hidden.bs.modal', function() {
                $('#mapelForm')[0].reset();
                $('#mapel_id').val('');
            });

            $('.edit-btn').on('click', function() {
                var row = $(this).closest('tr');
                var mapel = row.data('mapel');

                $('#mapel_id').val(mapel.mapel_id);
                $('#day').val(mapel.day);
                $('#subject_id').val(mapel.subject_id);
                $('#jurusan_id').val(mapel.jurusan_id);
                $('#class_id').val(mapel.class_id);
                $('#start_time').val(mapel.start_time);
                $('#end_time').val(mapel.end_time);
                $('#date').val(mapel.date);
            });

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
