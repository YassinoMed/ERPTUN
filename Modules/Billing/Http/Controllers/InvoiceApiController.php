<?php

namespace Modules\Billing\Http\Controllers;

use App\Models\Invoice;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class InvoiceApiController extends Controller
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

    public function invoices(Request $request)
    {
        if (! $this->tokenAllows($request, 'invoices.read')) {
            return $this->error('Forbidden', 403);
        }

        $invoices = Invoice::query()
            ->where('created_by', $request->user()->creatorId())
            ->with(['customer'])
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'invoices' => $invoices,
        ], 'Invoices fetched successfully.');
    }

    public function invoiceShow(Request $request, Invoice $invoice)
    {
        if (! $this->tokenAllows($request, 'invoices.read')) {
            return $this->error('Forbidden', 403);
        }

        if ((int) $invoice->created_by !== (int) $request->user()->creatorId()) {
            return $this->error('Not found', 404);
        }

        $invoice->load(['customer', 'items']);

        return $this->success([
            'invoice' => $invoice,
        ], 'Invoice fetched successfully.');
    }
}
