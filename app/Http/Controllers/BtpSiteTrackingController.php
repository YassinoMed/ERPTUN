<?php

namespace App\Http\Controllers;

use App\Models\BtpSitePhoto;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\Utility;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BtpSiteTrackingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'XSS', 'revalidate']);
    }

    public function index(Request $request)
    {
        if (!\Auth::user()->can('manage btp site tracking')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $projects = Project::where('created_by', $creatorId)->orderBy('id', 'desc')->get();
        $selectedProjectId = $request->get('project_id');
        $selectedProject = $selectedProjectId ? $projects->firstWhere('id', (int) $selectedProjectId) : $projects->first();

        $photoQuery = BtpSitePhoto::query()->where('created_by', $creatorId);
        if ($selectedProject) {
            $photoQuery->where('project_id', $selectedProject->id);
        }

        $totalPhotos = (clone $photoQuery)->count();
        $photos = (clone $photoQuery)->latest('taken_at')->limit(20)->get();

        $taskStats = [
            'total' => 0,
            'completed' => 0,
            'delayed' => 0,
            'progress' => 0,
        ];
        $delayedTasks = collect();
        $ganttUrl = null;

        if ($selectedProject) {
            $taskQuery = ProjectTask::query()
                ->where('project_id', $selectedProject->id)
                ->where('created_by', $creatorId);

            $totalTasks = (clone $taskQuery)->count();
            $completedTasks = (clone $taskQuery)->where('is_complete', 1)->count();
            $delayedTasks = (clone $taskQuery)
                ->where('is_complete', 0)
                ->whereDate('end_date', '<', Carbon::today())
                ->count();

            $taskStats['total'] = $totalTasks;
            $taskStats['completed'] = $completedTasks;
            $taskStats['delayed'] = $delayedTasks;
            $taskStats['progress'] = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;

            $delayedTasks = (clone $taskQuery)
                ->where('is_complete', 0)
                ->whereDate('end_date', '<', Carbon::today())
                ->orderBy('end_date')
                ->limit(10)
                ->get();

            $ganttUrl = route('projects.gantt', $selectedProject->id);
        }

        return view('btp/site-tracking', compact('projects', 'selectedProject', 'photos', 'totalPhotos', 'taskStats', 'delayedTasks', 'ganttUrl'));
    }

    public function storePhoto(Request $request)
    {
        if (!\Auth::user()->can('manage btp site tracking')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $data = $request->validate([
            'project_id' => 'required|integer',
            'photo' => 'required|file|mimes:jpg,jpeg,png',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'taken_at' => 'nullable|date',
            'note' => 'nullable|string',
        ]);

        $creatorId = \Auth::user()->creatorId();
        $project = Project::where('created_by', $creatorId)->findOrFail($data['project_id']);

        $imageSize = $request->file('photo')->getSize();
        $result = Utility::updateStorageLimit($creatorId, $imageSize);
        if ($result != 1) {
            return redirect()->back()->with('error', __('Storage limit exceeded.'));
        }

        $fileName = time() . '_' . $project->id . '.' . $request->photo->extension();
        $dir = 'uploads/btp/site_photos/';
        $path = Utility::upload_file($request, 'photo', $fileName, $dir, []);
        if ($path['flag'] != 1) {
            return redirect()->back()->with('error', __($path['msg']));
        }

        BtpSitePhoto::create([
            'project_id' => $project->id,
            'file' => $path['url'],
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'taken_at' => $data['taken_at'] ?? null,
            'note' => $data['note'] ?? null,
            'created_by' => $creatorId,
        ]);

        return redirect()->route('btp.site-tracking.index')->with('success', __('Site photo saved.'));
    }
}
