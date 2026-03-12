<?php

namespace App\Http\Controllers;

use App\Models\PharmacyMedication;
use App\Models\ProductService;
use Illuminate\Http\Request;

class PharmacyMedicationController extends Controller
{
    public function index()
    {
        if (!\Auth::user()->can('manage pharmacy medication')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $medications = PharmacyMedication::where('created_by', \Auth::user()->creatorId())->latest()->get();

        return view('pharmacy_medication.index', compact('medications'));
    }

    public function create()
    {
        if (!\Auth::user()->can('create pharmacy medication')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $products = ProductService::where('created_by', \Auth::user()->creatorId())->orderBy('name')->pluck('name', 'id');

        return view('pharmacy_medication.create', compact('products'));
    }

    public function store(Request $request)
    {
        if (!\Auth::user()->can('create pharmacy medication')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'stock_quantity' => 'nullable|numeric|min:0',
            'reorder_level' => 'nullable|numeric|min:0',
            'unit_price' => 'nullable|numeric|min:0',
            'expiry_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        PharmacyMedication::create([
            'product_service_id' => $request->product_service_id,
            'name' => $request->name,
            'sku' => $request->sku,
            'dosage_form' => $request->dosage_form,
            'strength' => $request->strength,
            'lot_number' => $request->lot_number,
            'expiry_date' => $request->expiry_date,
            'stock_quantity' => $request->stock_quantity ?? 0,
            'reorder_level' => $request->reorder_level ?? 0,
            'unit_price' => $request->unit_price ?? 0,
            'created_by' => \Auth::user()->creatorId(),
        ]);

        return redirect()->route('pharmacy-medications.index')->with('success', __('Pharmacy medication successfully created.'));
    }

    public function edit($id)
    {
        if (!\Auth::user()->can('edit pharmacy medication')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $medication = PharmacyMedication::where('created_by', \Auth::user()->creatorId())->findOrFail($id);
        $products = ProductService::where('created_by', \Auth::user()->creatorId())->orderBy('name')->pluck('name', 'id');

        return view('pharmacy_medication.edit', compact('medication', 'products'));
    }

    public function update(Request $request, $id)
    {
        if (!\Auth::user()->can('edit pharmacy medication')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $medication = PharmacyMedication::where('created_by', \Auth::user()->creatorId())->findOrFail($id);
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'stock_quantity' => 'nullable|numeric|min:0',
            'reorder_level' => 'nullable|numeric|min:0',
            'unit_price' => 'nullable|numeric|min:0',
            'expiry_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $medication->update([
            'product_service_id' => $request->product_service_id,
            'name' => $request->name,
            'sku' => $request->sku,
            'dosage_form' => $request->dosage_form,
            'strength' => $request->strength,
            'lot_number' => $request->lot_number,
            'expiry_date' => $request->expiry_date,
            'stock_quantity' => $request->stock_quantity ?? 0,
            'reorder_level' => $request->reorder_level ?? 0,
            'unit_price' => $request->unit_price ?? 0,
        ]);

        return redirect()->route('pharmacy-medications.index')->with('success', __('Pharmacy medication successfully updated.'));
    }

    public function destroy($id)
    {
        if (!\Auth::user()->can('delete pharmacy medication')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $medication = PharmacyMedication::where('created_by', \Auth::user()->creatorId())->findOrFail($id);
        $medication->delete();

        return redirect()->route('pharmacy-medications.index')->with('success', __('Pharmacy medication successfully deleted.'));
    }
}
