<?php

namespace Modules\Projects\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\ProjectUser;
use App\Models\TimeTracker;
use App\Models\TrackPhoto;
use App\Models\Utility;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\Projects\Jobs\ProjectsEventJob;

class ProjectsApiController extends Controller
{
    use ApiResponser;

    public function getProjects(Request $request)
    {
        $user = auth()->user();

        if ($user->type != 'company') {
            $assignProIds = ProjectUser::where('user_id', $user->id)->pluck('project_id');
            $projects = Project::with('tasks')->whereIn('id', $assignProIds)->get()->toArray();
        } else {
            $projects = Project::with('tasks')->where('created_by', $user->id)->get()->toArray();
        }

        ProjectsEventJob::dispatch([
            'event' => 'ProjectsFetched',
            'user_id' => $user->id,
        ]);

        return $this->success([
            'projects' => $projects,
        ], 'Get Project List successfully.');
    }

    public function addTracker(Request $request)
    {
        $user = auth()->user();
        if ($request->has('action') && $request->action == 'start') {
            $validatorArray = [
                'task_id' => 'required|integer',
            ];
            $validator = Validator::make(
                $request->all(),
                $validatorArray
            );
            if ($validator->fails()) {
                return $this->error($validator->errors()->first(), 401);
            }
            $task = ProjectTask::find($request->task_id);

            if (empty($task)) {
                return $this->error('Invalid task', 401);
            }

            $projectId = isset($task->project_id) ? $task->project_id : '';
            TimeTracker::where('created_by', '=', $user->id)->where('is_active', '=', 1)->update(['end_time' => date("Y-m-d H:i:s")]);

            $track['name'] = $request->has('workin_on') ? $request->input('workin_on') : '';
            $track['project_id'] = $projectId;
            $track['is_billable'] = $request->has('is_billable') ? $request->is_billable : 0;
            $track['tag_id'] = $request->has('workin_on') ? $request->input('workin_on') : '';
            $track['start_time'] = $request->has('time') ? date("Y-m-d H:i:s", strtotime($request->input('time'))) : date("Y-m-d H:i:s");
            $track['task_id'] = $request->has('task_id') ? $request->input('task_id') : '';
            $track['user_id'] = $user->id;
            $track['created_by'] = $user->creatorId();
            $track = TimeTracker::create($track);
            $track->action = 'start';

            ProjectsEventJob::dispatch([
                'event' => 'TrackerStarted',
                'user_id' => $user->id,
                'tracker_id' => $track->id,
            ]);

            return $this->success($track, 'Track successfully create.');
        }

        return $this->stopTracker($request);
    }

    public function stopTracker(Request $request)
    {
        $validatorArray = [
            'task_id' => 'required|integer',
            'traker_id' => 'required|integer',
        ];
        $validator = Validator::make(
            $request->all(),
            $validatorArray
        );
        if ($validator->fails()) {
            return Utility::error_res($validator->errors()->first());
        }
        $tracker = TimeTracker::where('id', $request->traker_id)->first();
        if ($tracker) {
            $tracker->end_time = $request->has('time') ? date("Y-m-d H:i:s", strtotime($request->input('time'))) : date("Y-m-d H:i:s");
            $tracker->is_active = 0;
            $tracker->total_time = Utility::diffance_to_time($tracker->start_time, $tracker->end_time);
            $tracker->save();

            ProjectsEventJob::dispatch([
                'event' => 'TrackerStopped',
                'user_id' => $tracker->user_id,
                'tracker_id' => $tracker->id,
            ]);

            return $this->success($tracker, 'Stop time successfully.');
        }

        return $this->error('Invalid tracker', 404);
    }

    public function uploadImage(Request $request)
    {
        $user = auth()->user();
        $imageBase64 = base64_decode($request->img);
        $file = $request->imgName;
        if ($request->has('tracker_id') && ! empty($request->tracker_id)) {
            $appPath = storage_path('uploads/traker_images/') . $request->tracker_id . '/';
            if (! file_exists($appPath)) {
                mkdir($appPath, 0777, true);
            }
        } else {
            $appPath = storage_path('uploads/traker_images/');
            if (! is_dir($appPath)) {
                mkdir($appPath, 0777, true);
            }
        }
        $fileName = $appPath . $file;
        file_put_contents($fileName, $imageBase64);
        $new = new TrackPhoto();
        $new->track_id = $request->tracker_id;
        $new->user_id = $user->id;
        $new->img_path = 'uploads/traker_images/' . $request->tracker_id . '/' . $file;
        $new->time = $request->time;
        $new->status = 1;
        $new->created_by = $user->creatorId();
        $new->save();

        ProjectsEventJob::dispatch([
            'event' => 'TrackerPhotoUploaded',
            'user_id' => $user->id,
            'tracker_id' => $request->tracker_id,
        ]);

        return $this->success([], 'Uploaded successfully.');
    }
}
