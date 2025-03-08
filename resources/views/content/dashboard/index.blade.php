@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <style>
        .card {
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
    </style>
    <div class="container-fluid">
        <div class="row">
            <!-- Card for Project Statistics -->
            <div class="col-md-3 mb-4">
                <a href="{{ route('projects.index') }}" class="text-decoration-none">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-briefcase-fill fs-1 text-success"></i>
                            <h5 class="card-title mt-3 text-dark">Total Projects</h5>
                            <p class="card-text text-muted">{{ $projectStats['total'] }} Projects</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Card for Tasks Statistics -->
            <div class="col-md-3 mb-4">
                <a href="{{ route('tasks.index') }}" class="text-decoration-none">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-check-square-fill fs-1 text-warning"></i>
                            <h5 class="card-title mt-3 text-dark">Total Tasks</h5>
                            <p class="card-text text-muted">{{ $taskStats['total'] }} Tasks</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Card for Documents Management -->
            <div class="col-md-3 mb-4">
                <a href="{{ route('documents.index') }}" class="text-decoration-none">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-file-earmark-text-fill fs-1 text-info"></i>
                            <h5 class="card-title mt-3 text-dark">Documents</h5>
                            <p class="card-text text-muted">{{ $recentDocuments->count() }} Recent Documents</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Card for Project Health - On Track -->
            <div class="col-md-3 mb-4">
                <a href="{{ route('projects.index') }}" class="text-decoration-none">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-graph-up fs-1 text-success"></i>
                            <h5 class="card-title mt-3 text-dark">On Track Projects</h5>
                            <p class="card-text text-muted">{{ $projectHealth['on_track'] }} On Track</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Card for Project Health - At Risk -->
            <div class="col-md-3 mb-4">
                <a href="{{ route('projects.index') }}" class="text-decoration-none">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-exclamation-triangle-fill fs-1 text-warning"></i>
                            <h5 class="card-title mt-3 text-dark">At Risk Projects</h5>
                            <p class="card-text text-muted">{{ $projectHealth['at_risk'] }} At Risk</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Card for Project Health - Delayed -->
            <div class="col-md-3 mb-4">
                <a href="{{ route('projects.index') }}" class="text-decoration-none">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-alarm-fill fs-1 text-danger"></i>
                            <h5 class="card-title mt-3 text-dark">Delayed Projects</h5>
                            <p class="card-text text-muted">{{ $projectHealth['delayed'] }} Delayed</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Card for Urgent Tasks -->
            <div class="col-md-3 mb-4">
                <a href="{{ route('tasks.index') }}" class="text-decoration-none">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-lightning-fill fs-1 text-danger"></i>
                            <h5 class="card-title mt-3 text-dark">Urgent Tasks</h5>
                            <p class="card-text text-muted">{{ $urgentTasks->count() }} High Priority</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Card for Recent Projects -->
            <div class="col-md-3 mb-4">
                <a href="{{ route('projects.index') }}" class="text-decoration-none">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-clock-history fs-1 text-info"></i>
                            <h5 class="card-title mt-3 text-dark">Recent Projects</h5>
                            <p class="card-text text-muted">{{ $recentProjects->count() }} Projects</p>
                        </div>
                    </div>
                </a>
            </div>

        </div>
    </div>
@endsection
