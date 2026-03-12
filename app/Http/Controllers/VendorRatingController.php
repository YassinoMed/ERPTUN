<?php

namespace App\Http\Controllers;

use App\Models\Vender;
use App\Models\VendorRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorRatingController extends Controller
{
    public function index()
    {
        if (! Auth::user()->can('manage vendor rating') && ! Auth::user()->can('show vendor rating')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $vendorRatings = VendorRating::where('created_by', Auth::user()->creatorId())
            ->with('vender')
            ->latest('id')
            ->get();

        return view('vendor_ratings.index', compact('vendorRatings'));
    }

    public function create()
    {
        if (! Auth::user()->can('create vendor rating')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        return view('vendor_ratings.create', $this->formData());
    }

    public function store(Request $request)
    {
        if (! Auth::user()->can('create vendor rating')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'vender_id' => 'required|exists:venders,id',
            'period_label' => 'required|max:255',
            'status' => 'required|max:100',
            'quality_score' => 'nullable|numeric|min:0|max:100',
            'delivery_score' => 'nullable|numeric|min:0|max:100',
            'cost_score' => 'nullable|numeric|min:0|max:100',
            'service_score' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->errors()->first());
        }

        VendorRating::create($this->payload($request) + ['created_by' => Auth::user()->creatorId()]);

        return redirect()->route('vendor-ratings.index')->with('success', __('Vendor rating successfully created.'));
    }

    public function show(VendorRating $vendorRating)
    {
        if (! $this->canAccess($vendorRating)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $vendorRating->load('vender');

        return view('vendor_ratings.show', compact('vendorRating'));
    }

    public function edit(VendorRating $vendorRating)
    {
        if (! Auth::user()->can('edit vendor rating') || ! $this->owns($vendorRating)) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        return view('vendor_ratings.edit', $this->formData() + compact('vendorRating'));
    }

    public function update(Request $request, VendorRating $vendorRating)
    {
        if (! Auth::user()->can('edit vendor rating') || ! $this->owns($vendorRating)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'vender_id' => 'required|exists:venders,id',
            'period_label' => 'required|max:255',
            'status' => 'required|max:100',
            'quality_score' => 'nullable|numeric|min:0|max:100',
            'delivery_score' => 'nullable|numeric|min:0|max:100',
            'cost_score' => 'nullable|numeric|min:0|max:100',
            'service_score' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->errors()->first());
        }

        $vendorRating->update($this->payload($request));

        return redirect()->route('vendor-ratings.show', $vendorRating)->with('success', __('Vendor rating successfully updated.'));
    }

    public function destroy(VendorRating $vendorRating)
    {
        if (! Auth::user()->can('delete vendor rating') || ! $this->owns($vendorRating)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $vendorRating->delete();

        return redirect()->route('vendor-ratings.index')->with('success', __('Vendor rating successfully deleted.'));
    }

    protected function formData(): array
    {
        return [
            'statuses' => VendorRating::$statuses,
            'venders' => Vender::where('created_by', Auth::user()->creatorId())->pluck('name', 'id'),
        ];
    }

    protected function payload(Request $request): array
    {
        $quality = (float) $request->quality_score;
        $delivery = (float) $request->delivery_score;
        $cost = (float) $request->cost_score;
        $service = (float) $request->service_score;

        return [
            'vender_id' => $request->vender_id,
            'period_label' => $request->period_label,
            'quality_score' => $quality,
            'delivery_score' => $delivery,
            'cost_score' => $cost,
            'service_score' => $service,
            'total_score' => round(($quality + $delivery + $cost + $service) / 4, 2),
            'status' => $request->status,
            'notes' => $request->notes,
        ];
    }

    protected function owns(VendorRating $vendorRating): bool
    {
        return (int) $vendorRating->created_by === (int) Auth::user()->creatorId();
    }

    protected function canAccess(VendorRating $vendorRating): bool
    {
        return $this->owns($vendorRating) && (Auth::user()->can('manage vendor rating') || Auth::user()->can('show vendor rating'));
    }
}
