<?php

namespace Modules\Pos\Http\Controllers;

use App\Models\Pos;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Pos\Jobs\PosEventJob;

class PosApiController extends Controller
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

    public function index(Request $request)
    {
        if (! $this->tokenAllows($request, 'pos.read')) {
            return $this->error('Forbidden', 403);
        }

        $poses = Pos::query()
            ->where('created_by', $request->user()->creatorId())
            ->with(['items', 'customer', 'warehouse', 'posPayment'])
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        PosEventJob::dispatch([
            'event' => 'PosListFetched',
            'user_id' => $request->user()->id,
        ]);

        return $this->success([
            'pos' => $poses,
        ], 'POS fetched successfully.');
    }

    public function show(Request $request, Pos $pos)
    {
        if (! $this->tokenAllows($request, 'pos.read')) {
            return $this->error('Forbidden', 403);
        }

        if ((int) $pos->created_by !== (int) $request->user()->creatorId()) {
            return $this->error('Not found', 404);
        }

        $pos->load(['items', 'customer', 'warehouse', 'posPayment']);

        PosEventJob::dispatch([
            'event' => 'PosFetched',
            'user_id' => $request->user()->id,
            'pos_id' => $pos->id,
        ]);

        return $this->success([
            'pos' => $pos,
        ], 'POS fetched successfully.');
    }
}
