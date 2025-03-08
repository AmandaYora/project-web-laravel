@extends('layouts.app')

@section('title', 'Critical Path Method - ' . $project->project_name)

@section('content')
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">CPM Activities - {{ $project->project_name }}</h6>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#addEditActivityModal">
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

                    <div class="table-responsive mt-3">
                        <table id="dataTableExample" class="table">
                            <thead>
                                <tr>
                                    <th>Activity</th>
                                    <th>Duration</th>
                                    <th>Predecessors</th>
                                    <th>Early Start</th>
                                    <th>Early Finish</th>
                                    <th>Late Start</th>
                                    <th>Late Finish</th>
                                    <th>Slack</th>
                                    <th>Critical</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($activities as $activity)
                                    @php
                                        $slack = $activity->late_start - $activity->early_start;
                                        $isCritical = $slack === 0;
                                    @endphp
                                    <tr data-activity="{{ json_encode($activity) }}" class="{{ $isCritical ? 'table-danger' : '' }}">
                                        <td>{{ $activity->activity_name }}</td>
                                        <td>{{ $activity->duration }}</td>
                                        <td>{{ $activity->predecessors ?: '-' }}</td>
                                        <td>{{ $activity->early_start }}</td>
                                        <td>{{ $activity->early_finish }}</td>
                                        <td>{{ $activity->late_start }}</td>
                                        <td>{{ $activity->late_finish }}</td>
                                        <td>{{ $slack }}</td>
                                        <td>
                                            @if($isCritical)
                                                <span class="badge bg-danger">Yes</span>
                                            @else
                                                <span class="badge bg-secondary">No</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info edit-btn"
                                                data-bs-toggle="modal" data-bs-target="#addEditActivityModal">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger delete-btn">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <form method="POST" action="{{ route('cpm.deleteActivity', $activity->activity_id) }}"
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

                    <div class="mt-4">
                        <h6>Network Diagram</h6>
                        <div id="network-diagram" style="height: 400px; border: 1px solid #ddd;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for add/edit activity -->
    <div class="modal fade" id="addEditActivityModal" tabindex="-1" aria-labelledby="addEditActivityModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEditActivityModalLabel">Add/Edit Activity</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('cpm.saveActivity') }}" id="activityForm">
                    @csrf
                    <input type="hidden" name="activity_id" id="activity_id">
                    <input type="hidden" name="project_id" value="{{ $project->project_id }}">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="activity_name" class="form-label">Activity Name</label>
                            <input type="text" class="form-control" id="activity_name" name="activity_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="duration" class="form-label">Duration (days)</label>
                            <input type="number" class="form-control" id="duration" name="duration" required min="1">
                        </div>
                        <div class="mb-3">
                            <label for="predecessors" class="form-label">Predecessors (comma-separated activity IDs)</label>
                            <input type="text" class="form-control" id="predecessors" name="predecessors" 
                                   placeholder="e.g., 1,2,3">
                            <small class="text-muted">Leave empty if this is a starting activity</small>
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
    <script src="https://cdn.jsdelivr.net/npm/vis-network@9.1.2/dist/vis-network.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#addEditActivityModal').on('hidden.bs.modal', function() {
                $('#activityForm')[0].reset();
                $('#activity_id').val('');
            });

            $('.edit-btn').on('click', function() {
                var row = $(this).closest('tr');
                var activity = row.data('activity');

                $('#activity_id').val(activity.activity_id);
                $('#activity_name').val(activity.activity_name);
                $('#duration').val(activity.duration);
                $('#predecessors').val(activity.predecessors);
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

            // Network diagram visualization
            var container = document.getElementById('network-diagram');
            var activities = @json($activities);
            
            var nodes = new vis.DataSet(activities.map(function(activity) {
                return {
                    id: activity.activity_id,
                    label: activity.activity_name + '\n' +
                           'Duration: ' + activity.duration + '\n' +
                           'ES: ' + activity.early_start + ' EF: ' + activity.early_finish + '\n' +
                           'LS: ' + activity.late_start + ' LF: ' + activity.late_finish,
                    color: {
                        background: (activity.late_start - activity.early_start === 0) ? '#f8d7da' : '#ffffff',
                        border: (activity.late_start - activity.early_start === 0) ? '#dc3545' : '#2c3e50'
                    }
                };
            }));

            var edges = new vis.DataSet();
            activities.forEach(function(activity) {
                if (activity.predecessors) {
                    activity.predecessors.split(',').forEach(function(predecessor) {
                        edges.add({
                            from: parseInt(predecessor),
                            to: activity.activity_id,
                            arrows: 'to'
                        });
                    });
                }
            });

            var data = {
                nodes: nodes,
                edges: edges
            };

            var options = {
                layout: {
                    hierarchical: {
                        direction: 'LR',
                        sortMethod: 'directed'
                    }
                },
                physics: false
            };

            new vis.Network(container, data, options);
        });
    </script>
@endsection
