<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\MicrofinanceLoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MicrofinanceLoanController extends Controller
{
    public function index()
    {
        if (! Auth::user()->can('manage microfinance loan') && ! Auth::user()->can('show microfinance loan')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $microfinanceLoans = MicrofinanceLoan::where('created_by', Auth::user()->creatorId())
            ->with('customer')
            ->latest('id')
            ->get();

        return view('microfinance_loans.index', compact('microfinanceLoans'));
    }

    public function create()
    {
        if (! Auth::user()->can('create microfinance loan')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        return view('microfinance_loans.create', $this->formData());
    }

    public function store(Request $request)
    {
        if (! Auth::user()->can('create microfinance loan')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'loan_number' => 'required|max:255',
            'status' => 'required|max:100',
            'principal_amount' => 'nullable|numeric|min:0',
            'interest_rate' => 'nullable|numeric|min:0',
            'installment_amount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->errors()->first());
        }

        MicrofinanceLoan::create($request->only([
            'customer_id', 'loan_number', 'principal_amount', 'interest_rate',
            'installment_amount', 'start_date', 'maturity_date', 'status', 'purpose', 'notes',
        ]) + ['created_by' => Auth::user()->creatorId()]);

        return redirect()->route('microfinance-loans.index')->with('success', __('Microfinance loan successfully created.'));
    }

    public function show(MicrofinanceLoan $microfinanceLoan)
    {
        if (! $this->canAccess($microfinanceLoan)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $microfinanceLoan->load('customer');

        return view('microfinance_loans.show', compact('microfinanceLoan'));
    }

    public function edit(MicrofinanceLoan $microfinanceLoan)
    {
        if (! Auth::user()->can('edit microfinance loan') || ! $this->owns($microfinanceLoan)) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        return view('microfinance_loans.edit', $this->formData() + compact('microfinanceLoan'));
    }

    public function update(Request $request, MicrofinanceLoan $microfinanceLoan)
    {
        if (! Auth::user()->can('edit microfinance loan') || ! $this->owns($microfinanceLoan)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'loan_number' => 'required|max:255',
            'status' => 'required|max:100',
            'principal_amount' => 'nullable|numeric|min:0',
            'interest_rate' => 'nullable|numeric|min:0',
            'installment_amount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->errors()->first());
        }

        $microfinanceLoan->update($request->only([
            'customer_id', 'loan_number', 'principal_amount', 'interest_rate',
            'installment_amount', 'start_date', 'maturity_date', 'status', 'purpose', 'notes',
        ]));

        return redirect()->route('microfinance-loans.show', $microfinanceLoan)->with('success', __('Microfinance loan successfully updated.'));
    }

    public function destroy(MicrofinanceLoan $microfinanceLoan)
    {
        if (! Auth::user()->can('delete microfinance loan') || ! $this->owns($microfinanceLoan)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $microfinanceLoan->delete();

        return redirect()->route('microfinance-loans.index')->with('success', __('Microfinance loan successfully deleted.'));
    }

    protected function formData(): array
    {
        return [
            'statuses' => MicrofinanceLoan::$statuses,
            'customers' => Customer::where('created_by', Auth::user()->creatorId())->pluck('name', 'id'),
        ];
    }

    protected function owns(MicrofinanceLoan $microfinanceLoan): bool
    {
        return (int) $microfinanceLoan->created_by === (int) Auth::user()->creatorId();
    }

    protected function canAccess(MicrofinanceLoan $microfinanceLoan): bool
    {
        return $this->owns($microfinanceLoan) && (Auth::user()->can('manage microfinance loan') || Auth::user()->can('show microfinance loan'));
    }
}
