<?php

namespace App\Http\Controllers;

use App\Models\PpmInitiative;
use App\Models\PpmPortfolio;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PpmPortfolioController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('manage ppm portfolio') && !Auth::user()->can('show ppm portfolio')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $portfolios = PpmPortfolio::where('created_by', Auth::user()->creatorId())
            ->withCount('initiatives')
            ->with('owner')
            ->latest('id')
            ->get();

        return view('ppm_portfolios.index', compact('portfolios'));
    }

    public function create()
    {
        if (!Auth::user()->can('create ppm portfolio')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $owners = $this->owners();
        $statuses = PpmPortfolio::$statuses;

        return view('ppm_portfolios.create', compact('owners', 'statuses'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('create ppm portfolio')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'name' => 'required|max:255',
            'status' => 'required|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->errors()->first());
        }

        PpmPortfolio::create($request->only([
            'name',
            'owner_id',
            'status',
            'priority',
            'start_date',
            'end_date',
            'description',
        ]) + ['created_by' => Auth::user()->creatorId()]);

        return redirect()->route('ppm-portfolios.index')->with('success', __('Portfolio successfully created.'));
    }

    public function show(PpmPortfolio $ppmPortfolio)
    {
        if (!$this->canAccess($ppmPortfolio)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $ppmPortfolio->load(['owner', 'initiatives.project', 'initiatives.sponsor']);
        $owners = $this->owners();
        $projects = $this->projects();
        $statuses = PpmPortfolio::$statuses;
        $initiativeStatuses = PpmInitiative::$statuses;
        $healthStatuses = PpmInitiative::$healthStatuses;

        return view('ppm_portfolios.show', compact('ppmPortfolio', 'owners', 'projects', 'statuses', 'initiativeStatuses', 'healthStatuses'));
    }

    public function edit(PpmPortfolio $ppmPortfolio)
    {
        if (!Auth::user()->can('edit ppm portfolio') || !$this->owns($ppmPortfolio)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $owners = $this->owners();
        $statuses = PpmPortfolio::$statuses;

        return view('ppm_portfolios.edit', compact('ppmPortfolio', 'owners', 'statuses'));
    }

    public function update(Request $request, PpmPortfolio $ppmPortfolio)
    {
        if (!Auth::user()->can('edit ppm portfolio') || !$this->owns($ppmPortfolio)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'name' => 'required|max:255',
            'status' => 'required|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->errors()->first());
        }

        $ppmPortfolio->update($request->only([
            'name',
            'owner_id',
            'status',
            'priority',
            'start_date',
            'end_date',
            'description',
        ]));

        return redirect()->route('ppm-portfolios.show', $ppmPortfolio)->with('success', __('Portfolio successfully updated.'));
    }

    public function destroy(PpmPortfolio $ppmPortfolio)
    {
        if (!Auth::user()->can('delete ppm portfolio') || !$this->owns($ppmPortfolio)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $ppmPortfolio->delete();

        return redirect()->route('ppm-portfolios.index')->with('success', __('Portfolio successfully deleted.'));
    }

    public function storeInitiative(Request $request, PpmPortfolio $ppmPortfolio)
    {
        if (!Auth::user()->can('create ppm initiative') || !$this->owns($ppmPortfolio)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'name' => 'required|max:255',
            'status' => 'required|max:50',
            'health_status' => 'required|max:50',
            'budget' => 'nullable|numeric|min:0',
            'target_value' => 'nullable|numeric|min:0',
            'achieved_value' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->errors()->first());
        }

        PpmInitiative::create($request->only([
            'project_id',
            'sponsor_id',
            'name',
            'status',
            'health_status',
            'budget',
            'target_value',
            'achieved_value',
            'start_date',
            'end_date',
            'description',
        ]) + [
            'ppm_portfolio_id' => $ppmPortfolio->id,
            'created_by' => Auth::user()->creatorId(),
        ]);

        return redirect()->route('ppm-portfolios.show', $ppmPortfolio)->with('success', __('Initiative successfully created.'));
    }

    public function editInitiative(PpmInitiative $ppmInitiative)
    {
        if (!Auth::user()->can('edit ppm initiative') || !$this->ownsInitiative($ppmInitiative)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $owners = $this->owners();
        $projects = $this->projects();
        $initiativeStatuses = PpmInitiative::$statuses;
        $healthStatuses = PpmInitiative::$healthStatuses;

        return view('ppm_portfolios.edit_initiative', compact('ppmInitiative', 'owners', 'projects', 'initiativeStatuses', 'healthStatuses'));
    }

    public function updateInitiative(Request $request, PpmInitiative $ppmInitiative)
    {
        if (!Auth::user()->can('edit ppm initiative') || !$this->ownsInitiative($ppmInitiative)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'name' => 'required|max:255',
            'status' => 'required|max:50',
            'health_status' => 'required|max:50',
            'budget' => 'nullable|numeric|min:0',
            'target_value' => 'nullable|numeric|min:0',
            'achieved_value' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->errors()->first());
        }

        $ppmInitiative->update($request->only([
            'project_id',
            'sponsor_id',
            'name',
            'status',
            'health_status',
            'budget',
            'target_value',
            'achieved_value',
            'start_date',
            'end_date',
            'description',
        ]));

        return redirect()->route('ppm-portfolios.show', $ppmInitiative->ppm_portfolio_id)->with('success', __('Initiative successfully updated.'));
    }

    public function destroyInitiative(PpmInitiative $ppmInitiative)
    {
        if (!Auth::user()->can('delete ppm initiative') || !$this->ownsInitiative($ppmInitiative)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $portfolioId = $ppmInitiative->ppm_portfolio_id;
        $ppmInitiative->delete();

        return redirect()->route('ppm-portfolios.show', $portfolioId)->with('success', __('Initiative successfully deleted.'));
    }

    protected function owners()
    {
        return User::where('created_by', Auth::user()->creatorId())
            ->whereNotIn('type', ['client', 'super admin'])
            ->pluck('name', 'id');
    }

    protected function projects()
    {
        return Project::where('created_by', Auth::user()->creatorId())->pluck('project_name', 'id');
    }

    protected function owns(PpmPortfolio $ppmPortfolio)
    {
        return (int) $ppmPortfolio->created_by === (int) Auth::user()->creatorId();
    }

    protected function ownsInitiative(PpmInitiative $ppmInitiative)
    {
        return (int) $ppmInitiative->created_by === (int) Auth::user()->creatorId();
    }

    protected function canAccess(PpmPortfolio $ppmPortfolio)
    {
        return $this->owns($ppmPortfolio) && (Auth::user()->can('manage ppm portfolio') || Auth::user()->can('show ppm portfolio'));
    }
}
