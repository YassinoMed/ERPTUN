<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\NpsCampaign;
use App\Models\NpsResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NpsCampaignController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('manage nps campaign') && !Auth::user()->can('show nps campaign')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $campaigns = NpsCampaign::where('created_by', Auth::user()->creatorId())
            ->withCount('responses')
            ->latest('id')
            ->get();

        return view('nps_campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        if (!Auth::user()->can('create nps campaign')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $statuses = NpsCampaign::$statuses;

        return view('nps_campaigns.create', compact('statuses'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('create nps campaign')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'name' => 'required|max:255',
            'channel' => 'required|max:100',
            'status' => 'required|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->errors()->first());
        }

        NpsCampaign::create($request->only([
            'name',
            'channel',
            'status',
            'audience_type',
            'sent_at',
            'closes_at',
            'description',
        ]) + ['created_by' => Auth::user()->creatorId()]);

        return redirect()->route('nps-campaigns.index')->with('success', __('NPS campaign successfully created.'));
    }

    public function show(NpsCampaign $npsCampaign)
    {
        if (!$this->canAccess($npsCampaign)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $npsCampaign->load('responses.customer');
        $statuses = NpsCampaign::$statuses;
        $customers = Customer::where('created_by', Auth::user()->creatorId())->pluck('name', 'id');

        return view('nps_campaigns.show', compact('npsCampaign', 'statuses', 'customers'));
    }

    public function edit(NpsCampaign $npsCampaign)
    {
        if (!Auth::user()->can('edit nps campaign') || !$this->owns($npsCampaign)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $statuses = NpsCampaign::$statuses;

        return view('nps_campaigns.edit', compact('npsCampaign', 'statuses'));
    }

    public function update(Request $request, NpsCampaign $npsCampaign)
    {
        if (!Auth::user()->can('edit nps campaign') || !$this->owns($npsCampaign)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'name' => 'required|max:255',
            'channel' => 'required|max:100',
            'status' => 'required|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->errors()->first());
        }

        $npsCampaign->update($request->only([
            'name',
            'channel',
            'status',
            'audience_type',
            'sent_at',
            'closes_at',
            'description',
        ]));

        return redirect()->route('nps-campaigns.show', $npsCampaign)->with('success', __('NPS campaign successfully updated.'));
    }

    public function destroy(NpsCampaign $npsCampaign)
    {
        if (!Auth::user()->can('delete nps campaign') || !$this->owns($npsCampaign)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $npsCampaign->delete();

        return redirect()->route('nps-campaigns.index')->with('success', __('NPS campaign successfully deleted.'));
    }

    public function storeResponse(Request $request, NpsCampaign $npsCampaign)
    {
        if (!Auth::user()->can('create nps response') || !$this->owns($npsCampaign)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'score' => 'required|integer|min:0|max:10',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->errors()->first());
        }

        NpsResponse::create([
            'nps_campaign_id' => $npsCampaign->id,
            'customer_id' => $request->customer_id,
            'score' => $request->score,
            'sentiment' => $this->sentimentForScore((int) $request->score),
            'feedback' => $request->feedback,
            'responded_at' => $request->responded_at ?: now(),
            'created_by' => Auth::user()->creatorId(),
        ]);

        return redirect()->route('nps-campaigns.show', $npsCampaign)->with('success', __('NPS response successfully created.'));
    }

    public function editResponse(NpsResponse $npsResponse)
    {
        if (!Auth::user()->can('edit nps response') || !$this->ownsResponse($npsResponse)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $customers = Customer::where('created_by', Auth::user()->creatorId())->pluck('name', 'id');

        return view('nps_campaigns.edit_response', compact('npsResponse', 'customers'));
    }

    public function updateResponse(Request $request, NpsResponse $npsResponse)
    {
        if (!Auth::user()->can('edit nps response') || !$this->ownsResponse($npsResponse)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'score' => 'required|integer|min:0|max:10',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->errors()->first());
        }

        $npsResponse->update([
            'customer_id' => $request->customer_id,
            'score' => $request->score,
            'sentiment' => $this->sentimentForScore((int) $request->score),
            'feedback' => $request->feedback,
            'responded_at' => $request->responded_at ?: $npsResponse->responded_at,
        ]);

        return redirect()->route('nps-campaigns.show', $npsResponse->nps_campaign_id)->with('success', __('NPS response successfully updated.'));
    }

    public function destroyResponse(NpsResponse $npsResponse)
    {
        if (!Auth::user()->can('delete nps response') || !$this->ownsResponse($npsResponse)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $campaignId = $npsResponse->nps_campaign_id;
        $npsResponse->delete();

        return redirect()->route('nps-campaigns.show', $campaignId)->with('success', __('NPS response successfully deleted.'));
    }

    protected function sentimentForScore(int $score): string
    {
        if ($score >= 9) {
            return 'promoter';
        }

        if ($score >= 7) {
            return 'passive';
        }

        return 'detractor';
    }

    protected function owns(NpsCampaign $npsCampaign)
    {
        return (int) $npsCampaign->created_by === (int) Auth::user()->creatorId();
    }

    protected function ownsResponse(NpsResponse $npsResponse)
    {
        return (int) $npsResponse->created_by === (int) Auth::user()->creatorId();
    }

    protected function canAccess(NpsCampaign $npsCampaign)
    {
        return $this->owns($npsCampaign) && (Auth::user()->can('manage nps campaign') || Auth::user()->can('show nps campaign'));
    }
}
