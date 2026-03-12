<?php

namespace App\Http\Controllers;

use App\Models\CapTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CapTableController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('manage cap table') && !Auth::user()->can('show cap table')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $entries = CapTable::where('created_by', Auth::user()->creatorId())->latest('id')->get();

        return view('cap_table.index', compact('entries'));
    }

    public function create()
    {
        if (!Auth::user()->can('create cap table')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $holderTypes = CapTable::$holderTypes;

        return view('cap_table.create', compact('holderTypes'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('create cap table')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'holder_name' => 'required|max:255',
            'holder_type' => 'required',
            'share_count' => 'required|numeric|min:0',
            'issue_price' => 'nullable|numeric|min:0',
            'ownership_percentage' => 'nullable|numeric|min:0|max:100',
            'voting_percentage' => 'nullable|numeric|min:0|max:100',
            'contact_email' => 'nullable|email',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        CapTable::create($request->only([
            'holder_name',
            'holder_type',
            'share_class',
            'share_count',
            'issue_price',
            'ownership_percentage',
            'voting_percentage',
            'contact_email',
            'contact_phone',
            'notes',
        ]) + ['created_by' => Auth::user()->creatorId()]);

        return redirect()->route('cap-table.index')->with('success', __('Cap table entry successfully created.'));
    }

    public function show(CapTable $capTable)
    {
        if (!$this->canAccess($capTable)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        return view('cap_table.show', compact('capTable'));
    }

    public function edit(CapTable $capTable)
    {
        if (!Auth::user()->can('edit cap table') || !$this->owns($capTable)) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $holderTypes = CapTable::$holderTypes;

        return view('cap_table.edit', compact('capTable', 'holderTypes'));
    }

    public function update(Request $request, CapTable $capTable)
    {
        if (!Auth::user()->can('edit cap table') || !$this->owns($capTable)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'holder_name' => 'required|max:255',
            'holder_type' => 'required',
            'share_count' => 'required|numeric|min:0',
            'issue_price' => 'nullable|numeric|min:0',
            'ownership_percentage' => 'nullable|numeric|min:0|max:100',
            'voting_percentage' => 'nullable|numeric|min:0|max:100',
            'contact_email' => 'nullable|email',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $capTable->update($request->only([
            'holder_name',
            'holder_type',
            'share_class',
            'share_count',
            'issue_price',
            'ownership_percentage',
            'voting_percentage',
            'contact_email',
            'contact_phone',
            'notes',
        ]));

        return redirect()->route('cap-table.index')->with('success', __('Cap table entry successfully updated.'));
    }

    public function destroy(CapTable $capTable)
    {
        if (!Auth::user()->can('delete cap table') || !$this->owns($capTable)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $capTable->delete();

        return redirect()->route('cap-table.index')->with('success', __('Cap table entry successfully deleted.'));
    }

    protected function owns(CapTable $capTable)
    {
        return (int) $capTable->created_by === (int) Auth::user()->creatorId();
    }

    protected function canAccess(CapTable $capTable)
    {
        return $this->owns($capTable) && (Auth::user()->can('manage cap table') || Auth::user()->can('show cap table'));
    }
}
