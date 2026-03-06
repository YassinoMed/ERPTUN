<?php

namespace Modules\Approvals\Http\Controllers;

use App\Models\ApprovalAction;
use App\Models\ApprovalFlow;
use App\Models\ApprovalRequest;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ApprovalsApiController extends Controller
{
    use ApiResponser;

    private function tokenAllows(Request $request, string $ability): bool
    {
        $token = $request->user()?->currentAccessToken();
        if (! $token) {
            return false;
        }

        return $request->user()->tokenCan('*') || $request->user()->tokenCan($ability);
    }

    public function flows(Request $request)
    {
        if (! $this->tokenAllows($request, 'approvals.read')) {
            return $this->error('Forbidden', 403);
        }

        $flows = ApprovalFlow::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'flows' => $flows,
        ], 'Approval flows fetched successfully.');
    }

    public function requests(Request $request)
    {
        if (! $this->tokenAllows($request, 'approvals.read')) {
            return $this->error('Forbidden', 403);
        }

        $requests = ApprovalRequest::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'requests' => $requests,
        ], 'Approval requests fetched successfully.');
    }

    public function actions(Request $request)
    {
        if (! $this->tokenAllows($request, 'approvals.read')) {
            return $this->error('Forbidden', 403);
        }

        $actions = ApprovalAction::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'actions' => $actions,
        ], 'Approval actions fetched successfully.');
    }
}
