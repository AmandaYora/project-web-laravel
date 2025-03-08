@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">Data Class Sessions</h6>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#addEditSessionModal">
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
                                    <th>Schedule</th>
                                    <th>Teacher</th>
                                    <th>Subject</th>
                                    <th>Class</th>
                                    <th>Jurusan</th>
                                    <th>Time</th>
                                    <th>QR Code</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sessions as $session)
                                    <tr data-session="{{ json_encode($session) }}">
                                        <td>{{ $session->mapel->day }}</td>
                                        <td>{{ $session->guru->user->name }}</td>
                                        <td>{{ $session->mapel->subject->subject }}</td>
                                        <td>{{ $session->mapel->class->class }}</td>
                                        <td>{{ $session->mapel->jurusan->jurusan }}</td>
                                        <td>{{ $session->mapel->start_time->format('H:i') }} - {{ $session->mapel->end_time->format('H:i') }}</td>
                                        <td>
                                            <button type="button" class="btn btn-info btn-sm show-qr"
                                                data-barcode="{{ $session->barcode }}"
                                                data-teacher="{{ $session->guru->user->name }}"
                                                data-subject="{{ $session->mapel->subject->subject }}">
                                                Show QR Code
                                            </button>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $session->status === 'completed' ? 'success' : ($session->status === 'ongoing' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($session->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info edit-btn"
                                                data-bs-toggle="modal" data-bs-target="#addEditSessionModal">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger delete-btn">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <form method="POST" action="{{ route('sessions.delete', $session->class_session_id) }}"
                                                style="display:none;" class="delete-form">
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

    <!-- Modal for add/edit session -->
    <div class="modal fade" id="addEditSessionModal" tabindex="-1" aria-labelledby="addEditSessionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEditSessionModalLabel">Add/Edit Class Session</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('sessions.save') }}" id="sessionForm">
                    @csrf
                    <input type="hidden" name="class_session_id" id="class_session_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="mapel_id" class="form-label">Schedule</label>
                            <select class="form-select" id="mapel_id" name="mapel_id" required>
                                <option value="">Select Schedule</option>
                                @foreach($mapels as $mapel)
                                    <option value="{{ $mapel->mapel_id }}">
                                        {{ $mapel->day }} | {{ $mapel->subject->subject }} | 
                                        {{ $mapel->class->class }} {{ $mapel->jurusan->jurusan }} | 
                                        {{ $mapel->start_time->format('H:i') }} - {{ $mapel->end_time->format('H:i') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            @if (session('user')['role'] === 'guru')
                                <label class="form-label">Teacher</label>
                                <input type="text" class="form-control" 
                                       value="{{ session('user')['guru']['user']['name'] }}" 
                                       readonly>
                                <input type="hidden" name="guru_id" 
                                       value="{{ session('user')['guru']['guru_id'] }}">
                            @else
                                <label for="guru_id" class="form-label">Teacher</label>
                                <select class="form-select" id="guru_id" name="guru_id" required>
                                    <option value="">Select Teacher</option>
                                    @foreach($gurus as $guru)
                                        <option value="{{ $guru->guru_id }}">
                                            {{ $guru->user->name }} ({{ $guru->subject->subject }})
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="">Select Status</option>
                                <option value="pending">Pending</option>
                                <option value="ongoing">Ongoing</option>
                                <option value="completed">Completed</option>
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

    <!-- Modal for QR code -->
    <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrModalLabel">Session QR Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-4">
                        <h4 id="sessionTeacher" class="mb-2"></h4>
                        <h5 id="sessionSubject" class="text-muted"></h5>
                    </div>
                    <div class="mb-4">
                        <div id="qrcode" style="display: inline-block;"></div>
                    </div>
                    <div>
                        <h3 id="barcodeText" class="font-monospace"></h3>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script>
        $(document).ready(function() {

            // Reset form when modal is closed
            $('#addEditSessionModal').on('hidden.bs.modal', function() {
                $('#sessionForm')[0].reset();
                $('#class_session_id').val('');
            });

            // Clear QR code when modal is closed
            $('#qrModal').on('hidden.bs.modal', function() {
                $('#qrcode').empty();
            });

            // Fill form with session data when edit button is clicked
            $('.edit-btn').on('click', function() {
                var row = $(this).closest('tr');
                var session = row.data('session');

                $('#class_session_id').val(session.class_session_id);
                $('#mapel_id').val(session.mapel_id);
                $('#guru_id').val(session.guru_id);
                $('#status').val(session.status);
            });

            // Show QR code modal when button is clicked
            $('.show-qr').on('click', function() {
                var barcode = $(this).data('barcode');
                var teacher = $(this).data('teacher');
                var subject = $(this).data('subject');

                $('#sessionTeacher').text(teacher);
                $('#sessionSubject').text(subject);
                $('#barcodeText').text(barcode);

                // Clear previous QR code
                $('#qrcode').empty();

                // Generate QR code
                var qrcode = new QRCode(document.getElementById("qrcode"), {
                    text: barcode,
                    width: 256,
                    height: 256,
                    colorDark : "#000000",
                    colorLight : "#ffffff",
                    correctLevel : QRCode.CorrectLevel.H
                });

                $('#qrModal').modal('show');
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
        });
    </script>
@endpush
