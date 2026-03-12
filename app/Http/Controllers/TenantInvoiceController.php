<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\StoreTenantInvoiceRequest;
use App\Models\Invoice;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenantInvoiceController extends Controller
{
    use ApiResponser;

    public function index(Request $request, int $tenant): JsonResponse
    {
        $tenantId = (int) $request->attributes->get('tenant_id');

        $invoices = Invoice::query()
            ->where('created_by', $tenantId)
            ->latest('id')
            ->paginate((int) $request->query('per_page', 20));

        return $this->success([
            'tenant_id' => $tenantId,
            'invoices' => $invoices,
        ], 'Tenant invoices fetched successfully.');
    }

    public function show(Request $request, int $tenant, Invoice $invoice): JsonResponse
    {
        $tenantId = (int) $request->attributes->get('tenant_id');

        if ((int) $invoice->created_by !== $tenantId) {
            return $this->error('Not found', 404);
        }

        return $this->success([
            'tenant_id' => $tenantId,
            'invoice' => $invoice,
        ], 'Tenant invoice fetched successfully.');
    }

    public function store(StoreTenantInvoiceRequest $request, int $tenant): JsonResponse
    {
        $tenantId = (int) $request->attributes->get('tenant_id');

        $nextInvoiceNumber = ((int) Invoice::query()
            ->where('created_by', $tenantId)
            ->max('invoice_id')) + 1;

        $invoice = Invoice::create([
            'invoice_id' => $nextInvoiceNumber,
            'customer_id' => (int) $request->integer('customer_id'),
            'issue_date' => $request->string('issue_date')->toString(),
            'due_date' => $request->string('due_date')->toString(),
            'category_id' => (int) $request->integer('category_id', 0),
            'status' => $request->string('status', 'Draft')->toString(),
            'ref_number' => $request->input('ref_number'),
            'created_by' => $tenantId,
        ]);

        return $this->success([
            'tenant_id' => $tenantId,
            'invoice' => $invoice,
        ], 'Tenant invoice created successfully.', 201);
    }
}
