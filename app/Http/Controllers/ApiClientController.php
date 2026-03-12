<?php

namespace App\Http\Controllers;

use App\Models\ApiClient;
use App\Models\ApiLog;
use App\Services\Core\ApiClientService;
use Illuminate\Http\Request;

class ApiClientController extends Controller
{
    public function __construct(
        private readonly ApiClientService $apiClientService
    ) {
    }

    public function index()
    {
        $this->authorizeAccess('manage api client');
        $clients = ApiClient::query()->where('created_by', \Auth::user()->creatorId())->withCount('logs')->latest('id')->get();
        $recentLogs = ApiLog::query()
            ->whereHas('apiClient', fn ($query) => $query->where('created_by', \Auth::user()->creatorId()))
            ->latest('requested_at')
            ->limit(20)
            ->get();
        $stats = [
            'clients' => $clients->count(),
            'active' => $clients->where('is_active', true)->count(),
            'inactive' => $clients->where('is_active', false)->count(),
            'failedCalls' => $recentLogs->where('status_code', '>=', 400)->count(),
        ];

        return view('api_client.index', compact('clients', 'recentLogs', 'stats'));
    }

    public function create()
    {
        $this->authorizeAccess('create api client');

        $abilityCatalog = $this->apiClientService->abilityCatalog();

        return view('api_client.create', compact('abilityCatalog'));
    }

    public function store(Request $request)
    {
        $this->authorizeAccess('create api client');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'abilities' => 'nullable|string',
            'expires_at' => 'nullable|date',
        ]);

        $issued = $this->apiClientService->issue(
            \Auth::user()->creatorId(),
            $validated['name'],
            array_values(array_filter(array_map('trim', explode(',', $validated['abilities'] ?? '')))),
            isset($validated['expires_at']) ? new \DateTime($validated['expires_at']) : null
        );

        return redirect()->route('api-clients.index')->with('success', __('API client created. Secret: ') . $issued['plain_secret']);
    }

    public function destroy(ApiClient $apiClient)
    {
        $this->authorizeAccess('delete api client');
        $this->ensureOwner($apiClient);
        $apiClient->delete();

        return redirect()->route('api-clients.index')->with('success', __('API client deleted.'));
    }

    public function show(ApiClient $apiClient)
    {
        $this->authorizeAccess('show api client');
        $this->ensureOwner($apiClient);
        $logs = ApiLog::query()
            ->where('api_client_id', $apiClient->id)
            ->latest('requested_at')
            ->paginate(25);
        $stats = [
            'totalRequests' => ApiLog::query()->where('api_client_id', $apiClient->id)->count(),
            'requests24h' => ApiLog::query()->where('api_client_id', $apiClient->id)->where('requested_at', '>=', now()->subDay())->count(),
            'errorRequests' => ApiLog::query()->where('api_client_id', $apiClient->id)->where('status_code', '>=', 400)->count(),
            'uniqueRoutes' => ApiLog::query()->where('api_client_id', $apiClient->id)->distinct('route')->count('route'),
        ];

        $abilityCatalog = $this->apiClientService->abilityCatalog();

        return view('api_client.show', compact('apiClient', 'logs', 'stats', 'abilityCatalog'));
    }

    public function rotateSecret(ApiClient $apiClient)
    {
        $this->authorizeAccess('edit api client');
        $this->ensureOwner($apiClient);
        $issued = $this->apiClientService->rotateSecret($apiClient);

        return redirect()->route('api-clients.show', $apiClient)->with('success', __('API secret rotated. New secret: ') . $issued['plain_secret']);
    }

    public function toggleStatus(ApiClient $apiClient)
    {
        $this->authorizeAccess('edit api client');
        $this->ensureOwner($apiClient);
        $client = $this->apiClientService->setStatus($apiClient, ! $apiClient->is_active);

        return redirect()->route('api-clients.show', $apiClient)->with('success', $client->is_active ? __('API client activated.') : __('API client deactivated.'));
    }

    public function docs()
    {
        $this->authorizeAccess('show api client');

        $abilityCatalog = $this->apiClientService->abilityCatalog();

        return view('api_client.docs', compact('abilityCatalog'));
    }

    private function ensureOwner(ApiClient $apiClient): void
    {
        if ((int) $apiClient->created_by !== (int) \Auth::user()->creatorId()) {
            abort(403, 'Permission denied.');
        }
    }

    private function authorizeAccess(string $permission): void
    {
        if (! \Auth::user()->can($permission)) {
            abort(403, 'Permission denied.');
        }
    }
}
