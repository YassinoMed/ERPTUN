<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Partner;
use App\Models\Vender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartnerController extends Controller
{
    public function index()
    {
        if (! Auth::user()->can('manage partner') && ! Auth::user()->can('show partner')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $partners = Partner::where('created_by', Auth::user()->creatorId())
            ->with(['customer', 'vender'])
            ->latest('id')
            ->get();

        return view('partners.index', compact('partners'));
    }

    public function create()
    {
        if (! Auth::user()->can('create partner')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        return view('partners.create', $this->formData());
    }

    public function store(Request $request)
    {
        if (! Auth::user()->can('create partner')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'partner_code' => 'required|max:255',
            'name' => 'required|max:255',
            'partner_type' => 'required|max:100',
            'status' => 'required|max:100',
            'email' => 'nullable|email',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->errors()->first());
        }

        Partner::create($request->only([
            'partner_code', 'name', 'partner_type', 'status', 'contact_name', 'email',
            'phone', 'website', 'customer_id', 'vender_id', 'notes',
        ]) + ['created_by' => Auth::user()->creatorId()]);

        return redirect()->route('partners.index')->with('success', __('Partner successfully created.'));
    }

    public function show(Partner $partner)
    {
        if (! $this->canAccess($partner)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $partner->load(['customer', 'vender']);

        return view('partners.show', compact('partner'));
    }

    public function edit(Partner $partner)
    {
        if (! Auth::user()->can('edit partner') || ! $this->owns($partner)) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        return view('partners.edit', $this->formData() + compact('partner'));
    }

    public function update(Request $request, Partner $partner)
    {
        if (! Auth::user()->can('edit partner') || ! $this->owns($partner)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'partner_code' => 'required|max:255',
            'name' => 'required|max:255',
            'partner_type' => 'required|max:100',
            'status' => 'required|max:100',
            'email' => 'nullable|email',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->errors()->first());
        }

        $partner->update($request->only([
            'partner_code', 'name', 'partner_type', 'status', 'contact_name', 'email',
            'phone', 'website', 'customer_id', 'vender_id', 'notes',
        ]));

        return redirect()->route('partners.show', $partner)->with('success', __('Partner successfully updated.'));
    }

    public function destroy(Partner $partner)
    {
        if (! Auth::user()->can('delete partner') || ! $this->owns($partner)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $partner->delete();

        return redirect()->route('partners.index')->with('success', __('Partner successfully deleted.'));
    }

    protected function formData(): array
    {
        return [
            'types' => Partner::$types,
            'statuses' => Partner::$statuses,
            'customers' => Customer::where('created_by', Auth::user()->creatorId())->pluck('name', 'id'),
            'venders' => Vender::where('created_by', Auth::user()->creatorId())->pluck('name', 'id'),
        ];
    }

    protected function owns(Partner $partner): bool
    {
        return (int) $partner->created_by === (int) Auth::user()->creatorId();
    }

    protected function canAccess(Partner $partner): bool
    {
        return $this->owns($partner) && (Auth::user()->can('manage partner') || Auth::user()->can('show partner'));
    }
}
