@extends('layouts.app')

@section('title', 'Documents Management')

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">Documents</h6>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#addEditDocumentModal">
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

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert" id="errorAlert">
                            <strong>Error!</strong> 
                            <ul>
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
                                    <th>Document Name</th>
                                    <th>Project</th>
                                    <th>Uploaded By</th>
                                    <th>Upload Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($documents as $document)
                                    <tr data-document="{{ json_encode($document) }}">
                                        <td>{{ $document->document_name }}</td>
                                        <td>{{ $document->project->project_name }}</td>
                                        <td>{{ $document->user->name }}</td>
                                        <td>{{ $document->created_at->format('Y-m-d H:i:s') }}</td>
                                        <td>
                                            <a href="{{ route('documents.download', $document->document_id) }}" 
                                               class="btn btn-sm btn-success">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-info edit-btn"
                                                data-bs-toggle="modal" data-bs-target="#addEditDocumentModal">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger delete-btn">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <form method="POST" action="{{ route('documents.delete', $document->document_id) }}"
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

    <!-- Modal for add/edit document -->
    <div class="modal fade" id="addEditDocumentModal" tabindex="-1" aria-labelledby="addEditDocumentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEditDocumentModalLabel">Add/Edit Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('documents.save') }}" id="documentForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="document_id" id="document_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="project_id" class="form-label">Project</label>
                            <select class="form-select" id="project_id" name="project_id" required>
                                <option value="">Select Project</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->project_id }}">{{ $project->project_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="document_name" class="form-label">Document Name</label>
                            <input type="text" class="form-control" id="document_name" name="document_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="document_file" class="form-label">File</label>
                            <input type="file" class="form-control" id="document_file" name="document_file">
                            <small class="text-muted">Leave empty to keep existing file when editing</small>
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

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#addEditDocumentModal').on('hidden.bs.modal', function() {
                $('#documentForm')[0].reset();
                $('#document_id').val('');
            });

            $('.edit-btn').on('click', function() {
                var row = $(this).closest('tr');
                var document = row.data('document');

                $('#document_id').val(document.document_id);
                $('#project_id').val(document.project_id);
                $('#document_name').val(document.document_name);
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
@endsection
