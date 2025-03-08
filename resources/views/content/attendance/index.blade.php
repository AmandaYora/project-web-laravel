@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">Data Attendance</h6>
                        <div>
                            @if(session('user.role') === 'admin' || session('user.role') === 'guru')
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#addEditAttendanceModal">
                                <i class="fas fa-plus"></i>
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#printModal">
                                <i class="fas fa-print"></i>
                            </button>
                            @endif
                        </div>
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
                                    <th>Student</th>
                                    <th>Session Code</th>
                                    <th>Teacher</th>
                                    <th>Subject</th>
                                    <th>Class</th>
                                    <th>Clock In</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    @if(session('user.role') === 'admin' || session('user.role') === 'guru')
                                    <th>Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($attendance as $record)
                                    <tr data-attendance="{{ json_encode($record) }}">
                                        <td>{{ $record->user->name }}</td>
                                        <td>{{ $record->classSession->barcode }}</td>
                                        <td>{{ $record->classSession->guru->user->name }}</td>
                                        <td>{{ $record->classSession->mapel->subject->subject }}</td>
                                        <td>
                                            {{ $record->classSession->mapel->class->class }}
                                            {{ $record->classSession->mapel->jurusan->jurusan }}
                                        </td>
                                        <td>{{ $record->clock_in->format('H:i') }}</td>
                                        <td>{{ $record->date->format('Y-m-d') }}</td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $record->status === 'present' ? 'success' : ($record->status === 'late' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($record->status) }}
                                            </span>
                                        </td>
                                        @if(session('user.role') === 'admin' || session('user.role') === 'guru')
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info edit-btn"
                                                data-bs-toggle="modal" data-bs-target="#addEditAttendanceModal">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger delete-btn">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <form method="POST"
                                                action="{{ route('attendance.delete', $record->attendance_id) }}"
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

    <!-- Modal for print attendance -->
    <div class="modal fade" id="printModal" tabindex="-1" aria-labelledby="printModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="printModalLabel">Print Attendance by Month</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="GET" action="{{ route('attendance.print', ['monthYear' => ':monthYear']) }}" id="printForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="month" class="form-label">Select Month</label>
                            <select class="form-select" id="month" name="month" required>
                                <option value="" selected>Select Month</option>
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}">
                                        {{ DateTime::createFromFormat('!m', $i)->format('F') }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="year" class="form-label">Select Year</label>
                            <select class="form-select" id="year" name="year" required>
                                <option value="" selected>Select Year</option>
                                @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-sm">Print</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Modal for add/edit attendance -->
    <div class="modal fade" id="addEditAttendanceModal" tabindex="-1" aria-labelledby="addEditAttendanceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEditAttendanceModalLabel">Add/Edit Attendance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('attendance.save') }}" id="attendanceForm">
                    @csrf
                    <input type="hidden" name="attendance_id" id="attendance_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="class_session_id" class="form-label">Session Code</label>
                            <select class="form-select" id="class_session_id" name="class_session_id" required>
                                <option value="">Select Session Code</option>
                                @foreach ($sessions as $session)
                                    <option value="{{ $session->class_session_id }}">
                                        {{ $session->barcode }} -
                                        {{ $session->guru->user->name }}
                                        ({{ $session->mapel->subject->subject }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Student</label>
                            <select class="form-select" id="user_id" name="user_id" required>
                                <option value="">Select Student</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->user_id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="clock_in" class="form-label">Clock In</label>
                            <input type="time" class="form-control" id="clock_in" name="clock_in" required>
                        </div>
                        <div class="mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" name="date" required>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="">Select Status</option>
                                <option value="present">Present</option>
                                <option value="late">Late</option>
                                <option value="absent">Absent</option>
                            </select>
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

            // Reset form when modal is closed
            $('#addEditAttendanceModal').on('hidden.bs.modal', function() {
                $('#attendanceForm')[0].reset();
                $('#attendance_id').val('');
            });

            // Fill form with attendance data when edit button is clicked
            $('.edit-btn').on('click', function() {
                var row = $(this).closest('tr');
                var attendance = row.data('attendance');

                $('#attendance_id').val(attendance.attendance_id);
                $('#class_session_id').val(attendance.class_session_id);
                $('#user_id').val(attendance.user_id);
                $('#clock_in').val(attendance.clock_in);
                $('#date').val(attendance.date);
                $('#status').val(attendance.status);
            });

            // Close alerts after 3 seconds
            setTimeout(function() {
                $('#errorAlert').alert('close');
                $('#successAlert').alert('close');
            }, 3000);

            // Delete confirmation using SweetAlert2
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

            $('#printForm').on('submit', function(e) {
                e.preventDefault();
                var month = $('#month').val().padStart(2, '0');
                var year = $('#year').val();

                if (month && year) {
                    var url = "{{ route('attendance.print', ['monthYear' => ':monthYear']) }}";
                    url = url.replace(':monthYear', month + '-' + year);
                    window.open(url, '_blank');
                    $('#printModal').modal('hide');
                } else {
                    alert('Silakan pilih bulan dan tahun');
                }
            });
        });
    </script>
@endpush
