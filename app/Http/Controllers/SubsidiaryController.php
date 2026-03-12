<?php

namespace App\Http\Controllers;

use App\Models\Subsidiary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubsidiaryController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('manage subsidiary') && !Auth::user()->can('show subsidiary')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $subsidiaries = Subsidiary::where('created_by', Auth::user()->creatorId())->latest('id')->get();

        return view('subsidiaries.index', compact('subsidiaries'));
    }

    public function create()
    {
        if (!Auth::user()->can('create subsidiary')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $methods = Subsidiary::$consolidationMethods;
        $statuses = Subsidiary::$statuses;

        return view('subsidiaries.create', compact('methods', 'statuses'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('create subsidiary')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'name' => 'required|max:255',
            'ownership_percentage' => 'nullable|numeric|min:0|max:100',
            'consolidation_method' => 'required',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        Subsidiary::create($request->only([
            'name',
            'registration_number',
            'country',
            'currency',
            'ownership_percentage',
            'consolidation_method',
            'status',
            'parent_company',
            'notes',
        ]) + ['created_by' => Auth::user()->creatorId()]);

        return redirect()->route('subsidiaries.index')->with('success', __('Subsidiary successfully created.'));
    }

    public function show(Subsidiary $subsidiary)
    {
        if (!$this->canAccess($subsidiary)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        return view('subsidiaries.show', compact('subsidiary'));
    }

    public function edit(Subsidiary $subsidiary)
    {
        if (!Auth::user()->can('edit subsidiary') || !$this->owns($subsidiary)) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $methods = Subsidiary::$consolidationMethods;
        $statuses = Subsidiary::$statuses;

        return view('subsidiaries.edit', compact('subsidiary', 'methods', 'statuses'));
    }

    public function update(Request $request, Subsidiary $subsidiary)
    {
        if (!Auth::user()->can('edit subsidiary') || !$this->owns($subsidiary)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'name' => 'required|max:255',
            'ownership_percentage' => 'nullable|numeric|min:0|max:100',
            'consolidation_method' => 'required',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $subsidiary->update($request->only([
            'name',
            'registration_number',
            'country',
            'currency',
            'ownership_percentage',
            'consolidation_method',
            'status',
            'parent_company',
            'notes',
        ]));

        return redirect()->route('subsidiaries.index')->with('success', __('Subsidiary successfully updated.'));
    }

    public function destroy(Subsidiary $subsidiary)
    {
        if (!Auth::user()->can('delete subsidiary') || !$this->owns($subsidiary)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $subsidiary->delete();

        return redirect()->route('subsidiaries.index')->with('success', __('Subsidiary successfully deleted.'));
    }

    protected function owns(Subsidiary $subsidiary)
    {
        return (int) $subsidiary->created_by === (int) Auth::user()->creatorId();
    }

    protected function canAccess(Subsidiary $subsidiary)
    {
        return $this->owns($subsidiary) && (Auth::user()->can('manage subsidiary') || Auth::user()->can('show subsidiary'));
    }
}
