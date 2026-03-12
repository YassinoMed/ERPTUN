<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerRecovery;
use App\Models\Employee;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerRecoveryController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('manage customer recovery') && !Auth::user()->can('show customer recovery')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $recoveries = CustomerRecovery::where('created_by', Auth::user()->creatorId())
            ->with(['customer', 'invoice', 'assignee'])
            ->latest('id')
            ->get();

        return view('customer_recoveries.index', compact('recoveries'));
    }

    public function create()
    {
        if (!Auth::user()->can('create customer recovery')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $customers = Customer::where('created_by', Auth::user()->creatorId())->pluck('name', 'id');
        $invoices = Invoice::where('created_by', Auth::user()->creatorId())->get()->mapWithKeys(function ($invoice) {
            $customerName = optional($invoice->customer)->name ?: __('Unknown customer');
            return [$invoice->id => Auth::user()->invoiceNumberFormat($invoice->invoice_id) . ' - ' . $customerName];
        });
        $employees = Employee::where('created_by', Auth::user()->creatorId())->pluck('name', 'id');
        $stages = CustomerRecovery::$stages;
        $priorities = CustomerRecovery::$priorities;
        $statuses = CustomerRecovery::$statuses;

        return view('customer_recoveries.create', compact('customers', 'invoices', 'employees', 'stages', 'priorities', 'statuses'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('create customer recovery')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'customer_id' => 'required|integer',
            'stage' => 'required',
            'priority' => 'required',
            'due_amount' => 'required|numeric|min:0',
            'status' => 'required',
            'next_follow_up_date' => 'nullable|date',
            'last_contact_date' => 'nullable|date',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        CustomerRecovery::create($request->only([
            'customer_id',
            'invoice_id',
            'reference',
            'stage',
            'priority',
            'due_amount',
            'next_follow_up_date',
            'last_contact_date',
            'assigned_to',
            'status',
            'notes',
        ]) + ['created_by' => Auth::user()->creatorId()]);

        return redirect()->route('customer-recoveries.index')->with('success', __('Customer recovery successfully created.'));
    }

    public function show(CustomerRecovery $customerRecovery)
    {
        if (!$this->canAccess($customerRecovery)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $customerRecovery->load(['customer', 'invoice', 'assignee']);

        return view('customer_recoveries.show', compact('customerRecovery'));
    }

    public function edit(CustomerRecovery $customerRecovery)
    {
        if (!Auth::user()->can('edit customer recovery') || !$this->owns($customerRecovery)) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $customers = Customer::where('created_by', Auth::user()->creatorId())->pluck('name', 'id');
        $invoices = Invoice::where('created_by', Auth::user()->creatorId())->get()->mapWithKeys(function ($invoice) {
            $customerName = optional($invoice->customer)->name ?: __('Unknown customer');
            return [$invoice->id => Auth::user()->invoiceNumberFormat($invoice->invoice_id) . ' - ' . $customerName];
        });
        $employees = Employee::where('created_by', Auth::user()->creatorId())->pluck('name', 'id');
        $stages = CustomerRecovery::$stages;
        $priorities = CustomerRecovery::$priorities;
        $statuses = CustomerRecovery::$statuses;

        return view('customer_recoveries.edit', compact('customerRecovery', 'customers', 'invoices', 'employees', 'stages', 'priorities', 'statuses'));
    }

    public function update(Request $request, CustomerRecovery $customerRecovery)
    {
        if (!Auth::user()->can('edit customer recovery') || !$this->owns($customerRecovery)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'customer_id' => 'required|integer',
            'stage' => 'required',
            'priority' => 'required',
            'due_amount' => 'required|numeric|min:0',
            'status' => 'required',
            'next_follow_up_date' => 'nullable|date',
            'last_contact_date' => 'nullable|date',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $customerRecovery->update($request->only([
            'customer_id',
            'invoice_id',
            'reference',
            'stage',
            'priority',
            'due_amount',
            'next_follow_up_date',
            'last_contact_date',
            'assigned_to',
            'status',
            'notes',
        ]));

        return redirect()->route('customer-recoveries.index')->with('success', __('Customer recovery successfully updated.'));
    }

    public function destroy(CustomerRecovery $customerRecovery)
    {
        if (!Auth::user()->can('delete customer recovery') || !$this->owns($customerRecovery)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $customerRecovery->delete();

        return redirect()->route('customer-recoveries.index')->with('success', __('Customer recovery successfully deleted.'));
    }

    protected function owns(CustomerRecovery $customerRecovery)
    {
        return (int) $customerRecovery->created_by === (int) Auth::user()->creatorId();
    }

    protected function canAccess(CustomerRecovery $customerRecovery)
    {
        return $this->owns($customerRecovery) && (Auth::user()->can('manage customer recovery') || Auth::user()->can('show customer recovery'));
    }
}
