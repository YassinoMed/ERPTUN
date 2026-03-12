<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\ProductLifecycleRecord;
use App\Models\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductLifecycleRecordController extends Controller
{
    public function index()
    {
        if (! Auth::user()->can('manage product lifecycle record') && ! Auth::user()->can('show product lifecycle record')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $productLifecycleRecords = ProductLifecycleRecord::where('created_by', Auth::user()->creatorId())
            ->with(['productService', 'owner'])
            ->latest('id')
            ->get();

        return view('product_lifecycle_records.index', compact('productLifecycleRecords'));
    }

    public function create()
    {
        if (! Auth::user()->can('create product lifecycle record')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        return view('product_lifecycle_records.create', $this->formData());
    }

    public function store(Request $request)
    {
        if (! Auth::user()->can('create product lifecycle record')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'product_service_id' => 'required|exists:product_services,id',
            'stage' => 'required|max:100',
            'status' => 'required|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->errors()->first());
        }

        ProductLifecycleRecord::create($request->only([
            'product_service_id', 'stage', 'status', 'effective_date',
            'owner_employee_id', 'compliance_status', 'notes',
        ]) + ['created_by' => Auth::user()->creatorId()]);

        return redirect()->route('product-lifecycle-records.index')->with('success', __('Lifecycle record successfully created.'));
    }

    public function show(ProductLifecycleRecord $productLifecycleRecord)
    {
        if (! $this->canAccess($productLifecycleRecord)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $productLifecycleRecord->load(['productService', 'owner']);

        return view('product_lifecycle_records.show', compact('productLifecycleRecord'));
    }

    public function edit(ProductLifecycleRecord $productLifecycleRecord)
    {
        if (! Auth::user()->can('edit product lifecycle record') || ! $this->owns($productLifecycleRecord)) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        return view('product_lifecycle_records.edit', $this->formData() + compact('productLifecycleRecord'));
    }

    public function update(Request $request, ProductLifecycleRecord $productLifecycleRecord)
    {
        if (! Auth::user()->can('edit product lifecycle record') || ! $this->owns($productLifecycleRecord)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'product_service_id' => 'required|exists:product_services,id',
            'stage' => 'required|max:100',
            'status' => 'required|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->errors()->first());
        }

        $productLifecycleRecord->update($request->only([
            'product_service_id', 'stage', 'status', 'effective_date',
            'owner_employee_id', 'compliance_status', 'notes',
        ]));

        return redirect()->route('product-lifecycle-records.show', $productLifecycleRecord)->with('success', __('Lifecycle record successfully updated.'));
    }

    public function destroy(ProductLifecycleRecord $productLifecycleRecord)
    {
        if (! Auth::user()->can('delete product lifecycle record') || ! $this->owns($productLifecycleRecord)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $productLifecycleRecord->delete();

        return redirect()->route('product-lifecycle-records.index')->with('success', __('Lifecycle record successfully deleted.'));
    }

    protected function formData(): array
    {
        return [
            'stages' => ProductLifecycleRecord::$stages,
            'statuses' => ProductLifecycleRecord::$statuses,
            'products' => ProductService::where('created_by', Auth::user()->creatorId())->pluck('name', 'id'),
            'employees' => Employee::where('created_by', Auth::user()->creatorId())->pluck('name', 'id'),
        ];
    }

    protected function owns(ProductLifecycleRecord $productLifecycleRecord): bool
    {
        return (int) $productLifecycleRecord->created_by === (int) Auth::user()->creatorId();
    }

    protected function canAccess(ProductLifecycleRecord $productLifecycleRecord): bool
    {
        return $this->owns($productLifecycleRecord) && (Auth::user()->can('manage product lifecycle record') || Auth::user()->can('show product lifecycle record'));
    }
}
