<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\ConfigurationItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConfigurationItemController extends Controller
{
    public function index()
    {
        if (! Auth::user()->can('manage configuration item') && ! Auth::user()->can('show configuration item')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $items = ConfigurationItem::where('created_by', Auth::user()->creatorId())->with(['owner', 'asset'])->latest('id')->get();

        return view('configuration_items.index', compact('items'));
    }

    public function create()
    {
        if (! Auth::user()->can('create configuration item')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        return view('configuration_items.create', $this->formData());
    }

    public function store(Request $request)
    {
        if (! Auth::user()->can('create configuration item')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $request->validate([
            'name' => 'required|max:255',
            'status' => 'required|max:50',
        ]);

        ConfigurationItem::create($request->only([
            'name', 'ci_type', 'status', 'criticality', 'asset_id', 'owner_user_id', 'asset_tag',
            'serial_number', 'location', 'environment', 'vendor_name', 'acquired_at', 'notes',
        ]) + ['created_by' => Auth::user()->creatorId()]);

        return redirect()->route('configuration-items.index')->with('success', __('Configuration item successfully created.'));
    }

    public function show(ConfigurationItem $configurationItem)
    {
        if (! $this->canAccess($configurationItem)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $configurationItem->load(['owner', 'asset']);

        return view('configuration_items.show', compact('configurationItem'));
    }

    public function edit(ConfigurationItem $configurationItem)
    {
        if (! Auth::user()->can('edit configuration item') || ! $this->owns($configurationItem)) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        return view('configuration_items.edit', array_merge($this->formData(), compact('configurationItem')));
    }

    public function update(Request $request, ConfigurationItem $configurationItem)
    {
        if (! Auth::user()->can('edit configuration item') || ! $this->owns($configurationItem)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $request->validate([
            'name' => 'required|max:255',
            'status' => 'required|max:50',
        ]);

        $configurationItem->update($request->only([
            'name', 'ci_type', 'status', 'criticality', 'asset_id', 'owner_user_id', 'asset_tag',
            'serial_number', 'location', 'environment', 'vendor_name', 'acquired_at', 'notes',
        ]));

        return redirect()->route('configuration-items.index')->with('success', __('Configuration item successfully updated.'));
    }

    public function destroy(ConfigurationItem $configurationItem)
    {
        if (! Auth::user()->can('delete configuration item') || ! $this->owns($configurationItem)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $configurationItem->delete();

        return redirect()->route('configuration-items.index')->with('success', __('Configuration item successfully deleted.'));
    }

    protected function formData(): array
    {
        $creatorId = Auth::user()->creatorId();

        return [
            'statuses' => ConfigurationItem::$statuses,
            'criticalities' => ConfigurationItem::$criticalities,
            'assets' => Asset::where('created_by', $creatorId)->pluck('name', 'id'),
            'users' => User::where('created_by', $creatorId)->pluck('name', 'id'),
        ];
    }

    protected function owns(ConfigurationItem $configurationItem): bool
    {
        return (int) $configurationItem->created_by === (int) Auth::user()->creatorId();
    }

    protected function canAccess(ConfigurationItem $configurationItem): bool
    {
        return $this->owns($configurationItem) && (Auth::user()->can('manage configuration item') || Auth::user()->can('show configuration item'));
    }
}
