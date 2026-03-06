<?php

namespace Modules\Accounting\Http\Controllers;

use App\Models\Bill;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\JournalEntry;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Accounting\Jobs\AccountingEventJob;

class AccountingApiController extends Controller
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
        if (! $this->tokenAllows($request, 'accounting.read')) {
            return $this->error('Forbidden', 403);
        }

        $invoices = Invoice::query()
            ->where('created_by', $request->user()->creatorId())
            ->with(['customer', 'items', 'payments'])
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        AccountingEventJob::dispatch([
            'event' => 'InvoicesFetched',
            'user_id' => $request->user()->id,
        ]);

        return $this->success([
            'invoices' => $invoices,
        ], 'Invoices fetched successfully.');
    }

    public function invoiceShow(Request $request, Invoice $invoice)
    {
        if (! $this->tokenAllows($request, 'accounting.read')) {
            return $this->error('Forbidden', 403);
        }

        if ((int) $invoice->created_by !== (int) $request->user()->creatorId()) {
            return $this->error('Not found', 404);
        }

        $invoice->load(['customer', 'items', 'payments']);

        return $this->success([
            'invoice' => $invoice,
        ], 'Invoice fetched successfully.');
    }

    public function bills(Request $request)
    {
        if (! $this->tokenAllows($request, 'accounting.read')) {
            return $this->error('Forbidden', 403);
        }

        $bills = Bill::query()
            ->where('created_by', $request->user()->creatorId())
            ->with(['vender', 'items', 'payments'])
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        AccountingEventJob::dispatch([
            'event' => 'BillsFetched',
            'user_id' => $request->user()->id,
        ]);

        return $this->success([
            'bills' => $bills,
        ], 'Bills fetched successfully.');
    }

    public function billShow(Request $request, Bill $bill)
    {
        if (! $this->tokenAllows($request, 'accounting.read')) {
            return $this->error('Forbidden', 403);
        }

        if ((int) $bill->created_by !== (int) $request->user()->creatorId()) {
            return $this->error('Not found', 404);
        }

        $bill->load(['vender', 'items', 'payments']);

        return $this->success([
            'bill' => $bill,
        ], 'Bill fetched successfully.');
    }

    public function expenses(Request $request)
    {
        if (! $this->tokenAllows($request, 'accounting.read')) {
            return $this->error('Forbidden', 403);
        }

        $expenses = Expense::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        AccountingEventJob::dispatch([
            'event' => 'ExpensesFetched',
            'user_id' => $request->user()->id,
        ]);

        return $this->success([
            'expenses' => $expenses,
        ], 'Expenses fetched successfully.');
    }

    public function expenseShow(Request $request, Expense $expense)
    {
        if (! $this->tokenAllows($request, 'accounting.read')) {
            return $this->error('Forbidden', 403);
        }

        if ((int) $expense->created_by !== (int) $request->user()->creatorId()) {
            return $this->error('Not found', 404);
        }

        return $this->success([
            'expense' => $expense,
        ], 'Expense fetched successfully.');
    }

    public function journals(Request $request)
    {
        if (! $this->tokenAllows($request, 'accounting.read')) {
            return $this->error('Forbidden', 403);
        }

        $journals = JournalEntry::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        AccountingEventJob::dispatch([
            'event' => 'JournalsFetched',
            'user_id' => $request->user()->id,
        ]);

        return $this->success([
            'journals' => $journals,
        ], 'Journals fetched successfully.');
    }

    public function journalShow(Request $request, JournalEntry $journalEntry)
    {
        if (! $this->tokenAllows($request, 'accounting.read')) {
            return $this->error('Forbidden', 403);
        }

        if ((int) $journalEntry->created_by !== (int) $request->user()->creatorId()) {
            return $this->error('Not found', 404);
        }

        return $this->success([
            'journal' => $journalEntry,
        ], 'Journal fetched successfully.');
    }
}
