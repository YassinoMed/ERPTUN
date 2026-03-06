<?php

namespace Modules\Hrm\Http\Controllers;

use App\Models\Employee;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class HrmApiController extends Controller
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
        if (! $this->tokenAllows($request, 'employees.read')) {
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

    public function employeeShow(Request $request, Employee $employee)
    {
        if (! $this->tokenAllows($request, 'employees.read')) {
            return $this->error('Forbidden', 403);
        }

        if ((int) $employee->created_by !== (int) $request->user()->creatorId()) {
            return $this->error('Not found', 404);
        }

        return $this->success([
            'employee' => $employee,
        ], 'Employee fetched successfully.');
    }
}
