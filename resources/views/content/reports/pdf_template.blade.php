<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Project Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
        }
        .progress-bar {
            background-color: #e9ecef;
            height: 20px;
            border-radius: 4px;
            overflow: hidden;
        }
        .progress-bar-fill {
            background-color: #3498db;
            height: 100%;
        }
        .status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-completed { background-color: #2ecc71; color: white; }
        .status-progress { background-color: #3498db; color: white; }
        .status-hold { background-color: #f1c40f; color: white; }
        .status-pending { background-color: #95a5a6; color: white; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Project Report</h1>
        <p>Generated on: {{ $generatedAt }}</p>
    </div>

    <div class="section">
        <div class="section-title">Project Information</div>
        <table>
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
                    <div class="progress-bar">
                        <div class="progress-bar-fill" style="width: {{ $project->progress }}%"></div>
                    </div>
                    {{ $project->progress }}%
                </td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <span class="status status-{{ strtolower($project->status) }}">
                        {{ $project->status }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Task Progress</div>
        <p>Completed Tasks: {{ $completedTasks }} of {{ $totalTasks }}</p>
        <table>
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
                @foreach($tasks as $task)
                    <tr>
                        <td>{{ $task->task_name }}</td>
                        <td>{{ $task->description }}</td>
                        <td>{{ $task->due_date }}</td>
                        <td>
                            <span class="status status-{{ strtolower($task->status) }}">
                                {{ $task->status }}
                            </span>
                        </td>
                        <td>
                            <div class="progress-bar">
                                <div class="progress-bar-fill" style="width: {{ $task->progress }}%"></div>
                            </div>
                            {{ $task->progress }}%
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Project Team</div>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Role</th>
                </tr>
            </thead>
            <tbody>
                @foreach($team as $member)
                    <tr>
                        <td>{{ $member->user->name }}</td>
                        <td>{{ $member->user->role }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($cpmActivities->count() > 0)
    <div class="section">
        <div class="section-title">Critical Path Activities</div>
        <table>
            <thead>
                <tr>
                    <th>Activity</th>
                    <th>Duration</th>
                    <th>Early Start</th>
                    <th>Early Finish</th>
                    <th>Late Start</th>
                    <th>Late Finish</th>
                    <th>Slack</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cpmActivities as $activity)
                    @php
                        $slack = $activity->late_start - $activity->early_start;
                        $isCritical = $slack === 0;
                    @endphp
                    <tr style="{{ $isCritical ? 'background-color: #ffebee;' : '' }}">
                        <td>{{ $activity->activity_name }}</td>
                        <td>{{ $activity->duration }}</td>
                        <td>{{ $activity->early_start }}</td>
                        <td>{{ $activity->early_finish }}</td>
                        <td>{{ $activity->late_start }}</td>
                        <td>{{ $activity->late_finish }}</td>
                        <td>{{ $slack }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="section">
        <div class="section-title">Project Documents</div>
        <table>
            <thead>
                <tr>
                    <th>Document Name</th>
                    <th>Uploaded By</th>
                    <th>Upload Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($documents as $document)
                    <tr>
                        <td>{{ $document->document_name }}</td>
                        <td>{{ $document->user->name }}</td>
                        <td>{{ $document->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>This report was automatically generated by the Project Management System</p>
    </div>
</body>
</html>
