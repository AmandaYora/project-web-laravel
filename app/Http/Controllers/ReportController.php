<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Report;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends Controller
{
    public function index($projectId)
    {
        $project = Project::with(['tasks', 'users', 'documents', 'cpmActivities'])->findOrFail($projectId);
        $reports = Report::where('project_id', $projectId)->get();
        return view('content.reports.index', compact('project', 'reports'));
    }

    public function generateReport(Request $request, $projectId)
    {
        $request->validate([
            'report_name' => 'required|string|max:255',
            'format' => 'required|in:pdf,excel',
        ]);

        $project = Project::with(['tasks', 'users', 'documents', 'cpmActivities'])->findOrFail($projectId);
        
        if ($request->format === 'pdf') {
            return $this->generatePdfReport($project, $request->report_name);
        } else {
            return $this->generateExcelReport($project, $request->report_name);
        }
    }

    private function generatePdfReport($project, $reportName)
    {
        $data = $this->getReportData($project);
        
        $pdf = PDF::loadView('content.reports.pdf_template', $data);
        $fileName = $reportName . '_' . now()->format('Y-m-d_His') . '.pdf';
        $path = 'reports/' . $fileName;
        
        Storage::disk('public')->put($path, $pdf->output());

        Report::create([
            'project_id' => $project->project_id,
            'report_name' => $reportName,
            'report_file_path' => $path,
        ]);

        return response()->download(storage_path('app/public/' . $path), $fileName);
    }

    private function generateExcelReport($project, $reportName)
    {
        $data = $this->getReportData($project);
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Project Information
        $sheet->setCellValue('A1', 'Project Report: ' . $project->project_name);
        $sheet->setCellValue('A3', 'Project Details');
        $sheet->setCellValue('A4', 'Name');
        $sheet->setCellValue('B4', $project->project_name);
        $sheet->setCellValue('A5', 'Description');
        $sheet->setCellValue('B5', $project->description);
        $sheet->setCellValue('A6', 'Start Date');
        $sheet->setCellValue('B6', $project->start_date);
        $sheet->setCellValue('A7', 'End Date');
        $sheet->setCellValue('B7', $project->end_date);
        $sheet->setCellValue('A8', 'Status');
        $sheet->setCellValue('B8', $project->status);
        $sheet->setCellValue('A9', 'Progress');
        $sheet->setCellValue('B9', $project->progress . '%');

        // Tasks
        $sheet->setCellValue('A11', 'Tasks');
        $sheet->setCellValue('A12', 'Task Name');
        $sheet->setCellValue('B12', 'Description');
        $sheet->setCellValue('C12', 'Due Date');
        $sheet->setCellValue('D12', 'Status');
        $sheet->setCellValue('E12', 'Progress');

        $row = 13;
        foreach ($project->tasks as $task) {
            $sheet->setCellValue('A' . $row, $task->task_name);
            $sheet->setCellValue('B' . $row, $task->description);
            $sheet->setCellValue('C' . $row, $task->due_date);
            $sheet->setCellValue('D' . $row, $task->status);
            $sheet->setCellValue('E' . $row, $task->progress . '%');
            $row++;
        }

        // Team Members
        $row += 2;
        $sheet->setCellValue('A' . $row, 'Team Members');
        $row++;
        $sheet->setCellValue('A' . $row, 'Name');
        $sheet->setCellValue('B' . $row, 'Role');
        $row++;

        foreach ($project->users as $projectUser) {
            $sheet->setCellValue('A' . $row, $projectUser->user->name);
            $sheet->setCellValue('B' . $row, $projectUser->user->role);
            $row++;
        }

        // Critical Path Activities
        if ($project->cpmActivities->count() > 0) {
            $row += 2;
            $sheet->setCellValue('A' . $row, 'Critical Path Activities');
            $row++;
            $sheet->setCellValue('A' . $row, 'Activity');
            $sheet->setCellValue('B' . $row, 'Duration');
            $sheet->setCellValue('C' . $row, 'Early Start');
            $sheet->setCellValue('D' . $row, 'Early Finish');
            $sheet->setCellValue('E' . $row, 'Late Start');
            $sheet->setCellValue('F' . $row, 'Late Finish');
            $row++;

            foreach ($project->cpmActivities as $activity) {
                $sheet->setCellValue('A' . $row, $activity->activity_name);
                $sheet->setCellValue('B' . $row, $activity->duration);
                $sheet->setCellValue('C' . $row, $activity->early_start);
                $sheet->setCellValue('D' . $row, $activity->early_finish);
                $sheet->setCellValue('E' . $row, $activity->late_start);
                $sheet->setCellValue('F' . $row, $activity->late_finish);
                $row++;
            }
        }

        $fileName = $reportName . '_' . now()->format('Y-m-d_His') . '.xlsx';
        $path = 'reports/' . $fileName;
        
        $writer = new Xlsx($spreadsheet);
        $writer->save(storage_path('app/public/' . $path));

        Report::create([
            'project_id' => $project->project_id,
            'report_name' => $reportName,
            'report_file_path' => $path,
        ]);

        return response()->download(storage_path('app/public/' . $path), $fileName);
    }

    private function getReportData($project)
    {
        return [
            'project' => $project,
            'tasks' => $project->tasks,
            'team' => $project->users,
            'documents' => $project->documents,
            'cpmActivities' => $project->cpmActivities,
            'completedTasks' => $project->tasks->where('status', 'Completed')->count(),
            'totalTasks' => $project->tasks->count(),
            'generatedAt' => now()->format('Y-m-d H:i:s'),
        ];
    }

    public function download($id)
    {
        $report = Report::findOrFail($id);
        return Storage::disk('public')->download($report->report_file_path);
    }

    public function delete($id)
    {
        $report = Report::findOrFail($id);
        Storage::disk('public')->delete($report->report_file_path);
        $report->delete();

        return redirect()->back()->with('success', 'Report deleted successfully');
    }
}
