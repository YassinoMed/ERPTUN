<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\LeasingContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeasingContractController extends Controller
{
    public function index()
    {
        if (! Auth::user()->can('manage leasing contract') && ! Auth::user()->can('show leasing contract')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $leasingContracts = LeasingContract::where('created_by', Auth::user()->creatorId())
            ->with('customer')
            ->latest('id')
            ->get();

        return view('leasing_contracts.index', compact('leasingContracts'));
    }

    public function create()
    {
        if (! Auth::user()->can('create leasing contract')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        return view('leasing_contracts.create', $this->formData());
    }

    public function store(Request $request)
    {
        if (! Auth::user()->can('create leasing contract')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'contract_number' => 'required|max:255',
            'asset_name' => 'required|max:255',
            'status' => 'required|max:100',
            'lease_amount' => 'nullable|numeric|min:0',
            'residual_amount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->errors()->first());
        }

        LeasingContract::create($request->only([
            'customer_id', 'contract_number', 'asset_name', 'lease_amount', 'residual_amount',
            'start_date', 'end_date', 'payment_frequency', 'status', 'notes',
        ]) + ['created_by' => Auth::user()->creatorId()]);

        return redirect()->route('leasing-contracts.index')->with('success', __('Leasing contract successfully created.'));
    }

    public function show(LeasingContract $leasingContract)
    {
        if (! $this->canAccess($leasingContract)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $leasingContract->load('customer');

        return view('leasing_contracts.show', compact('leasingContract'));
    }

    public function edit(LeasingContract $leasingContract)
    {
        if (! Auth::user()->can('edit leasing contract') || ! $this->owns($leasingContract)) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        return view('leasing_contracts.edit', $this->formData() + compact('leasingContract'));
    }

    public function update(Request $request, LeasingContract $leasingContract)
    {
        if (! Auth::user()->can('edit leasing contract') || ! $this->owns($leasingContract)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'contract_number' => 'required|max:255',
            'asset_name' => 'required|max:255',
            'status' => 'required|max:100',
            'lease_amount' => 'nullable|numeric|min:0',
            'residual_amount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->errors()->first());
        }

        $leasingContract->update($request->only([
            'customer_id', 'contract_number', 'asset_name', 'lease_amount', 'residual_amount',
            'start_date', 'end_date', 'payment_frequency', 'status', 'notes',
        ]));

        return redirect()->route('leasing-contracts.show', $leasingContract)->with('success', __('Leasing contract successfully updated.'));
    }

    public function destroy(LeasingContract $leasingContract)
    {
        if (! Auth::user()->can('delete leasing contract') || ! $this->owns($leasingContract)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $leasingContract->delete();

        return redirect()->route('leasing-contracts.index')->with('success', __('Leasing contract successfully deleted.'));
    }

    protected function formData(): array
    {
        return [
            'statuses' => LeasingContract::$statuses,
            'customers' => Customer::where('created_by', Auth::user()->creatorId())->pluck('name', 'id'),
        ];
    }

    protected function owns(LeasingContract $leasingContract): bool
    {
        return (int) $leasingContract->created_by === (int) Auth::user()->creatorId();
    }

    protected function canAccess(LeasingContract $leasingContract): bool
    {
        return $this->owns($leasingContract) && (Auth::user()->can('manage leasing contract') || Auth::user()->can('show leasing contract'));
    }
}
