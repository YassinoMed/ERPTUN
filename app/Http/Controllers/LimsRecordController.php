<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\LimsRecord;
use App\Models\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LimsRecordController extends Controller
{
    public function index()
    {
        if (! Auth::user()->can('manage lims record') && ! Auth::user()->can('show lims record')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $limsRecords = LimsRecord::where('created_by', Auth::user()->creatorId())
            ->with(['productService', 'approver'])
            ->latest('id')
            ->get();

        return view('lims_records.index', compact('limsRecords'));
    }

    public function create()
    {
        if (! Auth::user()->can('create lims record')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        return view('lims_records.create', $this->formData());
    }

    public function store(Request $request)
    {
        if (! Auth::user()->can('create lims record')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'sample_code' => 'required|max:255',
            'test_type' => 'required|max:255',
            'status' => 'required|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->errors()->first());
        }

        LimsRecord::create($request->only([
            'sample_code', 'product_service_id', 'lot_reference', 'test_type', 'status',
            'result_summary', 'tested_at', 'approved_by', 'notes',
        ]) + ['created_by' => Auth::user()->creatorId()]);

        return redirect()->route('lims-records.index')->with('success', __('LIMS record successfully created.'));
    }

    public function show(LimsRecord $limsRecord)
    {
        if (! $this->canAccess($limsRecord)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $limsRecord->load(['productService', 'approver']);

        return view('lims_records.show', compact('limsRecord'));
    }

    public function edit(LimsRecord $limsRecord)
    {
        if (! Auth::user()->can('edit lims record') || ! $this->owns($limsRecord)) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        return view('lims_records.edit', $this->formData() + compact('limsRecord'));
    }

    public function update(Request $request, LimsRecord $limsRecord)
    {
        if (! Auth::user()->can('edit lims record') || ! $this->owns($limsRecord)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'sample_code' => 'required|max:255',
            'test_type' => 'required|max:255',
            'status' => 'required|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->errors()->first());
        }

        $limsRecord->update($request->only([
            'sample_code', 'product_service_id', 'lot_reference', 'test_type', 'status',
            'result_summary', 'tested_at', 'approved_by', 'notes',
        ]));

        return redirect()->route('lims-records.show', $limsRecord)->with('success', __('LIMS record successfully updated.'));
    }

    public function destroy(LimsRecord $limsRecord)
    {
        if (! Auth::user()->can('delete lims record') || ! $this->owns($limsRecord)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $limsRecord->delete();

        return redirect()->route('lims-records.index')->with('success', __('LIMS record successfully deleted.'));
    }

    protected function formData(): array
    {
        return [
            'statuses' => LimsRecord::$statuses,
            'products' => ProductService::where('created_by', Auth::user()->creatorId())->pluck('name', 'id'),
            'employees' => Employee::where('created_by', Auth::user()->creatorId())->pluck('name', 'id'),
        ];
    }

    protected function owns(LimsRecord $limsRecord): bool
    {
        return (int) $limsRecord->created_by === (int) Auth::user()->creatorId();
    }

    protected function canAccess(LimsRecord $limsRecord): bool
    {
        return $this->owns($limsRecord) && (Auth::user()->can('manage lims record') || Auth::user()->can('show lims record'));
    }
}
