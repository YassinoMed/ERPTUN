<?php

namespace App\Http\Controllers;

use App\Models\MedicalService;
use App\Models\ProductService;
use Illuminate\Http\Request;

class MedicalServiceController extends Controller
{
    public function index()
    {
        if (!\Auth::user()->can('manage medical service')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $services = MedicalService::where('created_by', \Auth::user()->creatorId())->latest()->get();

        return view('medical_service.index', compact('services'));
    }

    public function create()
    {
        if (!\Auth::user()->can('create medical service')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $products = ProductService::where('created_by', \Auth::user()->creatorId())->orderBy('name')->pluck('name', 'id');

        return view('medical_service.create', compact('products'));
    }

    public function store(Request $request)
    {
        if (!\Auth::user()->can('create medical service')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255',
            'service_type' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'default_coverage_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        MedicalService::create([
            'product_service_id' => $request->product_service_id,
            'code' => $request->code,
            'name' => $request->name,
            'service_type' => $request->service_type,
            'price' => $request->price,
            'default_coverage_rate' => $request->default_coverage_rate ?? 0,
            'notes' => $request->notes,
            'created_by' => \Auth::user()->creatorId(),
        ]);

        return redirect()->route('medical-services.index')->with('success', __('Medical service successfully created.'));
    }

    public function edit($id)
    {
        if (!\Auth::user()->can('edit medical service')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $service = MedicalService::where('created_by', \Auth::user()->creatorId())->findOrFail($id);
        $products = ProductService::where('created_by', \Auth::user()->creatorId())->orderBy('name')->pluck('name', 'id');

        return view('medical_service.edit', compact('service', 'products'));
    }

    public function update(Request $request, $id)
    {
        if (!\Auth::user()->can('edit medical service')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $service = MedicalService::where('created_by', \Auth::user()->creatorId())->findOrFail($id);
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:255',
            'service_type' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'default_coverage_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $service->update([
            'product_service_id' => $request->product_service_id,
            'code' => $request->code,
            'name' => $request->name,
            'service_type' => $request->service_type,
            'price' => $request->price,
            'default_coverage_rate' => $request->default_coverage_rate ?? 0,
            'notes' => $request->notes,
        ]);

        return redirect()->route('medical-services.index')->with('success', __('Medical service successfully updated.'));
    }

    public function destroy($id)
    {
        if (!\Auth::user()->can('delete medical service')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $service = MedicalService::where('created_by', \Auth::user()->creatorId())->findOrFail($id);
        $service->delete();

        return redirect()->route('medical-services.index')->with('success', __('Medical service successfully deleted.'));
    }
}
