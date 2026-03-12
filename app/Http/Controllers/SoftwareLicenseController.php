<?php

namespace App\Http\Controllers;

use App\Models\ConfigurationItem;
use App\Models\SoftwareLicense;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SoftwareLicenseController extends Controller
{
    public function index()
    {
        if (! Auth::user()->can('manage software license') && ! Auth::user()->can('show software license')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $licenses = SoftwareLicense::where('created_by', Auth::user()->creatorId())->with(['configurationItem', 'assignedUser'])->latest('id')->get();

        return view('software_licenses.index', compact('licenses'));
    }

    public function create()
    {
        if (! Auth::user()->can('create software license')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        return view('software_licenses.create', $this->formData());
    }

    public function store(Request $request)
    {
        if (! Auth::user()->can('create software license')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $request->validate([
            'name' => 'required|max:255',
            'status' => 'required|max:50',
            'seats_total' => 'nullable|integer|min:1',
            'seats_used' => 'nullable|integer|min:0',
        ]);

        SoftwareLicense::create($request->only([
            'name', 'vendor_name', 'license_key', 'license_type', 'status', 'configuration_item_id',
            'assigned_user_id', 'seats_total', 'seats_used', 'cost', 'renewal_date', 'notes',
        ]) + ['created_by' => Auth::user()->creatorId()]);

        return redirect()->route('software-licenses.index')->with('success', __('Software license successfully created.'));
    }

    public function show(SoftwareLicense $softwareLicense)
    {
        if (! $this->canAccess($softwareLicense)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $softwareLicense->load(['configurationItem', 'assignedUser']);

        return view('software_licenses.show', compact('softwareLicense'));
    }

    public function edit(SoftwareLicense $softwareLicense)
    {
        if (! Auth::user()->can('edit software license') || ! $this->owns($softwareLicense)) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        return view('software_licenses.edit', array_merge($this->formData(), compact('softwareLicense')));
    }

    public function update(Request $request, SoftwareLicense $softwareLicense)
    {
        if (! Auth::user()->can('edit software license') || ! $this->owns($softwareLicense)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $request->validate([
            'name' => 'required|max:255',
            'status' => 'required|max:50',
            'seats_total' => 'nullable|integer|min:1',
            'seats_used' => 'nullable|integer|min:0',
        ]);

        $softwareLicense->update($request->only([
            'name', 'vendor_name', 'license_key', 'license_type', 'status', 'configuration_item_id',
            'assigned_user_id', 'seats_total', 'seats_used', 'cost', 'renewal_date', 'notes',
        ]));

        return redirect()->route('software-licenses.index')->with('success', __('Software license successfully updated.'));
    }

    public function destroy(SoftwareLicense $softwareLicense)
    {
        if (! Auth::user()->can('delete software license') || ! $this->owns($softwareLicense)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $softwareLicense->delete();

        return redirect()->route('software-licenses.index')->with('success', __('Software license successfully deleted.'));
    }

    protected function formData(): array
    {
        $creatorId = Auth::user()->creatorId();

        return [
            'statuses' => SoftwareLicense::$statuses,
            'configurationItems' => ConfigurationItem::where('created_by', $creatorId)->pluck('name', 'id'),
            'users' => User::where('created_by', $creatorId)->pluck('name', 'id'),
        ];
    }

    protected function owns(SoftwareLicense $softwareLicense): bool
    {
        return (int) $softwareLicense->created_by === (int) Auth::user()->creatorId();
    }

    protected function canAccess(SoftwareLicense $softwareLicense): bool
    {
        return $this->owns($softwareLicense) && (Auth::user()->can('manage software license') || Auth::user()->can('show software license'));
    }
}
