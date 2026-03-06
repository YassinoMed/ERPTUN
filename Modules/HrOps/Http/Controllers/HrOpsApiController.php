<?php

namespace Modules\HrOps\Http\Controllers;

use App\Models\Employee;
use App\Models\Leave;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class HrOpsApiController extends Controller
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

    public function employees(Request $request)
    {
        if (! $this->tokenAllows($request, 'hr_ops.read')) {
            return $this->error('Forbidden', 403);
        }

        $employees = Employee::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'employees' => $employees,
        ], 'Employees fetched successfully.');
    }

    public function leaves(Request $request)
    {
        if (! $this->tokenAllows($request, 'hr_ops.read')) {
            return $this->error('Forbidden', 403);
        }

        $leaves = Leave::query()
            ->where('created_by', $request->user()->creatorId())
            ->with(['leaveType', 'employees'])
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'leaves' => $leaves,
        ], 'Leaves fetched successfully.');
    }
}
