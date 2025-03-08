@extends('layouts.app')

@section('title', 'Project Details')

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">Project Details</h6>
                        <div>
                            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                data-bs-target="#addEditProjectModal">
                                <i class="fas fa-edit"></i> Edit Project
                            </button>
                            <a href="{{ route('projects.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to Projects
                            </a>
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

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Project Information</h6>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th width="30%">Project Name</th>
                                                <td>{{ $project->project_name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Description</th>
                                                <td>{{ $project->description }}</td>
                                            </tr>
                                            <tr>
                                                <th>Start Date</th>
                                                <td>{{ $project->start_date }}</td>
                                            </tr>
                                            <tr>
                                                <th>End Date</th>
                                                <td>{{ $project->end_date }}</td>
                                            </tr>
                                            <tr>
                                                <th>Progress</th>
                                                <td>
                                                    <div class="progress">
                                                        <div class="progress-bar" role="progressbar"
                                                            style="width: {{ $project->progress }}%"
                                                            aria-valuenow="{{ $project->progress }}" aria-valuemin="0"
                                                            aria-valuemax="100">
                                                            {{ $project->progress }}%
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Status</th>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $project->status === 'Completed' ? 'success' : ($project->status === 'In Progress' ? 'primary' : ($project->status === 'On Hold' ? 'warning' : 'secondary')) }}">
                                                        {{ $project->status }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Project Team</h6>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Role</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($project->users as $projectUser)
                                                    <tr>
                                                        <td>{{ $projectUser->user->name }}</td>
                                                        <td>{{ $projectUser->user->role }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="card-title mb-0">Project Tasks</h6>
                                        <div>
                                            <a href="{{ route('reports.index', $project->project_id) }}" class="btn btn-success btn-sm me-2">
                                                <i class="fas fa-file-alt"></i> Reports
                                            </a>
                                            <a href="{{ route('cpm.index', $project->project_id) }}" class="btn btn-info btn-sm me-2">
                                                <i class="fas fa-project-diagram"></i> CPM Analysis
                                            </a>
                                            <a href="{{ route('tasks.project', $project->project_id) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-tasks"></i> Manage Tasks
                                            </a>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Task Name</th>
                                                    <th>Description</th>
                                                    <th>Due Date</th>
                                                    <th>Status</th>
                                                    <th>Progress</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($project->tasks as $task)
                                                    <tr>
                                                        <td>{{ $task->task_name }}</td>
                                                        <td>{{ Str::limit($task->description, 50) }}</td>
                                                        <td>{{ $task->due_date }}</td>
                                                        <td>
                                                            <span
                                                                class="badge bg-{{ $task->status === 'Completed' ? 'success' : ($task->status === 'In Progress' ? 'primary' : ($task->status === 'On Hold' ? 'warning' : 'secondary')) }}">
                                                                {{ $task->status }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="progress">
                                                                <div class="progress-bar" role="progressbar"
                                                                    style="width: {{ $task->progress }}%"
                                                                    aria-valuenow="{{ $task->progress }}" aria-valuemin="0"
                                                                    aria-valuemax="100">
                                                                    {{ $task->progress }}%
                                                                </div>
                                                            </div>
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
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for edit project -->
    <div class="modal fade" id="addEditProjectModal" tabindex="-1" aria-labelledby="addEditProjectModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEditProjectModalLabel">Edit Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('projects.save') }}" id="projectForm">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project->project_id }}">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="project_name" class="form-label">Project Name</label>
                            <input type="text" class="form-control" id="project_name" name="project_name"
                                value="{{ $project->project_name }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ $project->description }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date"
                                value="{{ $project->start_date }}">
                        </div>
                        <div class="mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date"
                                value="{{ $project->end_date }}">
                        </div>
                        <div class="mb-3">
                            <label for="progress" class="form-label">Progress (%)</label>
                            <input type="number" class="form-control" id="progress" name="progress" min="0"
                                max="100" value="{{ $project->progress }}">
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="">Select Status</option>
                                <option value="Pending" {{ $project->status === 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="In Progress" {{ $project->status === 'In Progress' ? 'selected' : '' }}>In Progress
                                </option>
                                <option value="On Hold" {{ $project->status === 'On Hold' ? 'selected' : '' }}>On Hold</option>
                                <option value="Completed" {{ $project->status === 'Completed' ? 'selected' : '' }}>Completed
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            setTimeout(function() {
                $('#errorAlert').alert('close');
                $('#successAlert').alert('close');
            }, 3000);
        });
    </script>
@endsection
