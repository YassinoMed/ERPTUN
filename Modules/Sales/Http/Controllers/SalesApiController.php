<?php

namespace Modules\Sales\Http\Controllers;

use App\Models\Proposal;
use App\Models\Quotation;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Sales\Jobs\SalesEventJob;

class SalesApiController extends Controller
{
    use ApiResponser;

    private function tokenAllows(Request $request, string $ability): bool
    {
        $token = $request->user()?->currentAccessToken();
        if (! $token) {
            return false;
        }

        return $request->user()->tokenCan('*') || $request->user()->tokenCan($ability);
    }

    public function quotations(Request $request)
    {
        if (! $this->tokenAllows($request, 'sales.read')) {
            return $this->error('Forbidden', 403);
        }

        $quotations = Quotation::query()
            ->where('created_by', $request->user()->creatorId())
            ->with(['customer', 'warehouse'])
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        SalesEventJob::dispatch([
            'event' => 'SalesQuotationsFetched',
            'user_id' => $request->user()->id,
        ]);

        return $this->success([
            'quotations' => $quotations,
        ], 'Quotations fetched successfully.');
    }

    public function quotationShow(Request $request, Quotation $quotation)
    {
        if (! $this->tokenAllows($request, 'sales.read')) {
            return $this->error('Forbidden', 403);
        }

        if ((int) $quotation->created_by !== (int) $request->user()->creatorId()) {
            return $this->error('Not found', 404);
        }

        $quotation->load(['customer', 'warehouse', 'items']);

        SalesEventJob::dispatch([
            'event' => 'SalesQuotationFetched',
            'user_id' => $request->user()->id,
            'quotation_id' => $quotation->id,
        ]);

        return $this->success([
            'quotation' => $quotation,
        ], 'Quotation fetched successfully.');
    }

    public function proposals(Request $request)
    {
        if (! $this->tokenAllows($request, 'sales.read')) {
            return $this->error('Forbidden', 403);
        }

        $proposals = Proposal::query()
            ->where('created_by', $request->user()->creatorId())
            ->with(['customer', 'category'])
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        SalesEventJob::dispatch([
            'event' => 'SalesProposalsFetched',
            'user_id' => $request->user()->id,
        ]);

        return $this->success([
            'proposals' => $proposals,
        ], 'Proposals fetched successfully.');
    }

    public function proposalShow(Request $request, Proposal $proposal)
    {
        if (! $this->tokenAllows($request, 'sales.read')) {
            return $this->error('Forbidden', 403);
        }

        if ((int) $proposal->created_by !== (int) $request->user()->creatorId()) {
            return $this->error('Not found', 404);
        }

        $proposal->load(['customer', 'category', 'items']);

        SalesEventJob::dispatch([
            'event' => 'SalesProposalFetched',
            'user_id' => $request->user()->id,
            'proposal_id' => $proposal->id,
        ]);

        return $this->success([
            'proposal' => $proposal,
        ], 'Proposal fetched successfully.');
    }
}
