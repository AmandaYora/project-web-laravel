@extends('layouts.app')

@section('title', isset($project) ? "Tasks for {$project->project_name}" : 'Tasks Management')

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">
                                @if(isset($project))
                                    Tasks for Project: {{ $project->project_name }}
                                    <small class="text-muted">
                                        Status: 
                                        <span class="badge bg-{{ $project->status === 'Completed' ? 'success' : ($project->status === 'In Progress' ? 'primary' : ($project->status === 'On Hold' ? 'warning' : 'secondary')) }}">
                                            {{ $project->status }}
                                        </span>
                                    </small>
                                @else
                                    All Tasks
                                @endif
                            </h6>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#addEditTaskModal">
                                <i class="fas fa-plus"></i> Add Task
                            </button>
                            @if(isset($project))
                                <a href="{{ route('projects.show', $project->project_id) }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Back to Project
                                </a>
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

                    <div class="table-responsive mt-3">
                        <table id="dataTableExample" class="table">
                            <thead>
                                <tr>
                                    <th>Task Name</th>
                                    @if(!isset($project))
                                        <th>Project</th>
                                    @endif
                                    <th>Description</th>
                                    <th>Assigned To</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tasks as $task)
                                    <tr data-task="{{ json_encode($task) }}">
                                        <td>{{ $task->task_name }}</td>
                                        @if(!isset($project))
                                            <td>
                                                <a href="{{ route('tasks.project', $task->project_id) }}">
                                                    {{ $task->project->project_name }}
                                                </a>
                                            </td>
                                        @endif
                                        <td>{{ Str::limit($task->description, 50) }}</td>
                                        <td>{{ $task->user ? $task->user->name : 'Unassigned' }}</td>
                                        <td>{{ $task->start_date }}</td>
                                        <td>{{ $task->end_date }}</td>
                                        <td>
                                            <select class="form-select form-select-sm status-select" 
                                                    data-task-id="{{ $task->task_id }}">
                                                <option value="Pending" {{ $task->status === 'Pending' ? 'selected' : '' }}>
                                                    Pending
                                                </option>
                                                <option value="In Progress" {{ $task->status === 'In Progress' ? 'selected' : '' }}>
                                                    In Progress
                                                </option>
                                                <option value="Completed" {{ $task->status === 'Completed' ? 'selected' : '' }}>
                                                    Completed
                                                </option>
                                            </select>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info edit-btn"
                                                data-bs-toggle="modal" data-bs-target="#addEditTaskModal">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger delete-btn">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <form method="POST" action="{{ route('tasks.delete', $task->task_id) }}"
                                                style="display:none;" class="delete-form">
                                                @csrf
                                                @method('DELETE')
                                                @if(isset($project))
                                                    <input type="hidden" name="redirect_to_project" value="1">
                                                @endif
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

    <!-- Modal for add/edit task -->
    <div class="modal fade" id="addEditTaskModal" tabindex="-1" aria-labelledby="addEditTaskModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEditTaskModalLabel">Add/Edit Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('tasks.save') }}" id="taskForm">
                    @csrf
                    <input type="hidden" name="task_id" id="task_id">
                    @if(isset($project))
                        <input type="hidden" name="project_id" value="{{ $project->project_id }}">
                        <input type="hidden" name="redirect_to_project" value="1">
                    @endif
                    <div class="modal-body">
                        @if(!isset($project))
                            <div class="mb-3">
                                <label for="project_id" class="form-label">Project</label>
                                <select class="form-select" id="project_id" name="project_id" required>
                                    <option value="">Select Project</option>
                                    @foreach($projects as $proj)
                                        <option value="{{ $proj->project_id }}">{{ $proj->project_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="mb-3">
                            <label for="task_name" class="form-label">Task Name</label>
                            <input type="text" class="form-control" id="task_name" name="task_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="assigned_to" class="form-label">Assigned To</label>
                            <select class="form-select" id="assigned_to" name="assigned_to">
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->user_id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date">
                        </div>
                        <div class="mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date">
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="">Select Status</option>
                                <option value="Pending">Pending</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="progress" class="form-label">Progress (%)</label>
                            <input type="number" class="form-control" id="progress" name="progress" min="0" max="100" required>
                        </div>
                        <div class="mb-3">
                            <label for="priority" class="form-label">Priority</label>
                            <select class="form-select" id="priority" name="priority" required>
                                <option value="">Select Priority</option>
                                <option value="1">Low</option>
                                <option value="2">Medium</option>
                                <option value="3">High</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="weight" class="form-label">Weight</label>
                            <input type="number" class="form-control" id="weight" name="weight" min="1" required>
                            <small class="form-text text-muted">Task weight for project progress calculation</small>
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
            $('#addEditTaskModal').on('hidden.bs.modal', function() {
                $('#taskForm')[0].reset();
                $('#task_id').val('');
            });

            $('.edit-btn').on('click', function() {
                var row = $(this).closest('tr');
                var task = row.data('task');

                $('#task_id').val(task.task_id);
                $('#project_id').val(task.project_id);
                $('#task_name').val(task.task_name);
                $('#description').val(task.description);
                $('#assigned_to').val(task.assigned_to);
                $('#start_date').val(task.start_date);
                $('#end_date').val(task.end_date);
                $('#status').val(task.status);
                $('#progress').val(task.progress);
                $('#priority').val(task.priority);
                $('#weight').val(task.weight);
            });

            $('.status-select').on('change', function() {
                var taskId = $(this).data('task-id');
                var newStatus = $(this).val();
                var select = $(this);

                $.ajax({
                    url: `/tasks/${taskId}/status`,
                    type: 'PATCH',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: newStatus
                    },
                    success: function(response) {
                        select.closest('td').find('.badge')
                            .removeClass('bg-secondary bg-primary bg-success')
                            .addClass(getStatusBadgeClass(newStatus))
                            .text(newStatus);
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to update task status'
                        });
                        // Revert select to original value
                        select.val(select.find('option[selected]').val());
                    }
                });
            });

            function getStatusBadgeClass(status) {
                switch(status) {
                    case 'Completed':
                        return 'bg-success';
                    case 'In Progress':
                        return 'bg-primary';
                    default:
                        return 'bg-secondary';
                }
            }

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
