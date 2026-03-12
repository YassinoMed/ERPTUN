<?php

namespace App\Services\Core;

use App\Models\Employee;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

class GlobalSearchService
{
    public function search(Authenticatable $user, string $query, int $limit = 5): array
    {
        $query = trim(preg_replace('/\s+/', ' ', $query));
        $limit = max(1, min(10, $limit));

        $results = [
            'clients' => [],
            'invoices' => [],
            'projects' => [],
            'employees' => [],
        ];

        if ($query === '' || mb_strlen($query) < 2) {
            return $results;
        }

        $creatorId = $user->creatorId();

        if ($user->can('manage client')) {
            $results['clients'] = User::query()
                ->where('created_by', '=', $creatorId)
                ->where('type', '=', 'client')
                ->where(function ($builder) use ($query) {
                    $builder->where('name', 'LIKE', "%{$query}%")
                        ->orWhere('email', 'LIKE', "%{$query}%")
                        ->orWhere('job_title', 'LIKE', "%{$query}%");
                })
                ->orderByDesc('id')
                ->limit($limit)
                ->get(['id', 'name', 'email'])
                ->map(fn (User $client) => [
                    'id' => $client->id,
                    'title' => $client->name,
                    'subtitle' => $client->email,
                    'visit_url' => route('global.search.visit', ['type' => 'client', 'id' => $client->id]),
                ])->values()->all();
        }

        if ($user->can('manage invoice')) {
            $results['invoices'] = Invoice::query()
                ->with(['customer:id,name'])
                ->where('created_by', '=', $creatorId)
                ->where(function ($builder) use ($query) {
                    $builder->where('invoice_id', 'LIKE', "%{$query}%")
                        ->orWhere('ref_number', 'LIKE', "%{$query}%");
                })
                ->orderByDesc('id')
                ->limit($limit)
                ->get()
                ->map(fn (Invoice $invoice) => [
                    'id' => $invoice->id,
                    'title' => $user->invoiceNumberFormat($invoice->invoice_id),
                    'subtitle' => optional($invoice->customer)->name,
                    'visit_url' => route('global.search.visit', ['type' => 'invoice', 'id' => $invoice->id]),
                ])->values()->all();
        }

        if ($user->can('manage project')) {
            $results['projects'] = Project::query()
                ->where('created_by', '=', $creatorId)
                ->where('project_name', 'LIKE', "%{$query}%")
                ->orderByDesc('id')
                ->limit($limit)
                ->get(['id', 'project_name', 'status'])
                ->map(fn (Project $project) => [
                    'id' => $project->id,
                    'title' => $project->project_name,
                    'subtitle' => $project->status,
                    'visit_url' => route('global.search.visit', ['type' => 'project', 'id' => $project->id]),
                ])->values()->all();
        }

        if ($user->can('manage employee')) {
            $employeesQuery = Employee::query();
            if ($user->type == 'Employee') {
                $employeesQuery->where('user_id', '=', $user->id);
            } else {
                $employeesQuery->where('created_by', '=', $creatorId);
            }

            $results['employees'] = $employeesQuery
                ->where(function ($builder) use ($query) {
                    $builder->where('name', 'LIKE', "%{$query}%")
                        ->orWhere('email', 'LIKE', "%{$query}%")
                        ->orWhere('employee_id', 'LIKE', "%{$query}%");
                })
                ->orderByDesc('id')
                ->limit($limit)
                ->get(['id', 'name', 'email', 'employee_id'])
                ->map(fn (Employee $employee) => [
                    'id' => $employee->id,
                    'title' => $employee->name,
                    'subtitle' => $employee->email,
                    'visit_url' => route('global.search.visit', ['type' => 'employee', 'id' => $employee->id]),
                ])->values()->all();
        }

        return $results;
    }

    public function resolveVisit(Authenticatable $user, string $type, int $id): array
    {
        $creatorId = $user->creatorId();
        $type = strtolower(trim($type));

        if ($type === 'client') {
            abort_unless($user->can('manage client'), 403);

            $client = User::query()
                ->where('created_by', '=', $creatorId)
                ->where('type', '=', 'client')
                ->findOrFail($id);

            return [
                'redirect_url' => route('clients.show', $client->id),
                'recent' => [
                    'type' => 'client',
                    'id' => $client->id,
                    'title' => $client->name,
                    'subtitle' => $client->email,
                    'visit_url' => route('global.search.visit', ['type' => 'client', 'id' => $client->id]),
                ],
            ];
        }

        if ($type === 'invoice') {
            abort_unless($user->can('manage invoice'), 403);

            $invoice = Invoice::query()
                ->with(['customer:id,name'])
                ->where('created_by', '=', $creatorId)
                ->findOrFail($id);

            return [
                'redirect_url' => route('invoice.show', $invoice->id),
                'recent' => [
                    'type' => 'invoice',
                    'id' => $invoice->id,
                    'title' => $user->invoiceNumberFormat($invoice->invoice_id),
                    'subtitle' => optional($invoice->customer)->name,
                    'visit_url' => route('global.search.visit', ['type' => 'invoice', 'id' => $invoice->id]),
                ],
            ];
        }

        if ($type === 'project') {
            abort_unless($user->can('manage project'), 403);

            $project = Project::query()
                ->where('created_by', '=', $creatorId)
                ->findOrFail($id);

            return [
                'redirect_url' => route('projects.show', $project->id),
                'recent' => [
                    'type' => 'project',
                    'id' => $project->id,
                    'title' => $project->project_name,
                    'subtitle' => $project->status,
                    'visit_url' => route('global.search.visit', ['type' => 'project', 'id' => $project->id]),
                ],
            ];
        }

        if ($type === 'employee') {
            abort_unless($user->can('manage employee'), 403);

            $employeeQuery = Employee::query();
            if ($user->type == 'Employee') {
                $employeeQuery->where('user_id', '=', $user->id);
            } else {
                $employeeQuery->where('created_by', '=', $creatorId);
            }

            $employee = $employeeQuery->findOrFail($id);

            return [
                'redirect_url' => route('employee.show', $employee->id),
                'recent' => [
                    'type' => 'employee',
                    'id' => $employee->id,
                    'title' => $employee->name,
                    'subtitle' => $employee->email,
                    'visit_url' => route('global.search.visit', ['type' => 'employee', 'id' => $employee->id]),
                ],
            ];
        }

        abort(404);
    }
}
