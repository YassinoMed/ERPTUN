<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DeliveryNote;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\ProductService;
use App\Models\Purchase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class ApiClientGatewayController extends Controller
{
    public function customers(Request $request): JsonResponse
    {
        $client = $request->attributes->get('api_client');
        $this->authorizeAbility($client, 'customers:read');
        $customers = Customer::query()
            ->where('created_by', $client->created_by)
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = trim((string) $request->query('q'));
                $query->where(function ($builder) use ($term) {
                    $builder->where('name', 'like', '%'.$term.'%')
                        ->orWhere('email', 'like', '%'.$term.'%')
                        ->orWhere('mobile', 'like', '%'.$term.'%')
                        ->orWhere('customer_id', 'like', '%'.$term.'%');
                });
            })
            ->latest('id')
            ->paginate((int) $request->query('per_page', 20));

        return $this->respondWithPaginator($customers, 'customers', $client, $request);
    }

    public function products(Request $request): JsonResponse
    {
        $client = $request->attributes->get('api_client');
        $this->authorizeAbility($client, 'products:read');
        $products = ProductService::query()
            ->where('created_by', $client->created_by)
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = trim((string) $request->query('q'));
                $query->where(function ($builder) use ($term) {
                    $builder->where('name', 'like', '%'.$term.'%')
                        ->orWhere('sku', 'like', '%'.$term.'%')
                        ->orWhere('sale_price', 'like', '%'.$term.'%');
                });
            })
            ->latest('id')
            ->paginate((int) $request->query('per_page', 20));

        return $this->respondWithPaginator($products, 'products', $client, $request);
    }

    public function invoices(Request $request): JsonResponse
    {
        $client = $request->attributes->get('api_client');
        $this->authorizeAbility($client, 'invoices:read');
        $invoices = Invoice::query()
            ->where('created_by', $client->created_by)
            ->with('customer')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = trim((string) $request->query('q'));
                $query->where(function ($builder) use ($term) {
                    $builder->where('invoice_id', 'like', '%'.$term.'%')
                        ->orWhere('status', 'like', '%'.$term.'%');
                });
            })
            ->latest('id')
            ->paginate((int) $request->query('per_page', 20));

        return $this->respondWithPaginator($invoices, 'invoices', $client, $request);
    }

    public function purchases(Request $request): JsonResponse
    {
        $client = $request->attributes->get('api_client');
        $this->authorizeAbility($client, 'purchases:read');
        $purchases = Purchase::query()
            ->where('created_by', $client->created_by)
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = trim((string) $request->query('q'));
                $query->where(function ($builder) use ($term) {
                    $builder->where('purchase_id', 'like', '%'.$term.'%')
                        ->orWhere('status', 'like', '%'.$term.'%');
                });
            })
            ->latest('id')
            ->paginate((int) $request->query('per_page', 20));

        return $this->respondWithPaginator($purchases, 'purchases', $client, $request);
    }

    public function patients(Request $request): JsonResponse
    {
        $client = $request->attributes->get('api_client');
        $this->authorizeAbility($client, 'patients:read');
        $patients = Patient::query()
            ->where('created_by', $client->created_by)
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = trim((string) $request->query('q'));
                $query->where(function ($builder) use ($term) {
                    $builder->where('name', 'like', '%'.$term.'%')
                        ->orWhere('phone', 'like', '%'.$term.'%')
                        ->orWhere('email', 'like', '%'.$term.'%');
                });
            })
            ->latest('id')
            ->paginate((int) $request->query('per_page', 20));

        return $this->respondWithPaginator($patients, 'patients', $client, $request);
    }

    public function deliveryNotes(Request $request): JsonResponse
    {
        $client = $request->attributes->get('api_client');
        $this->authorizeAbility($client, 'delivery-notes:read');
        $deliveryNotes = DeliveryNote::query()
            ->where('created_by', $client->created_by)
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = trim((string) $request->query('q'));
                $query->where(function ($builder) use ($term) {
                    $builder->where('status', 'like', '%'.$term.'%')
                        ->orWhere('tracking_number', 'like', '%'.$term.'%')
                        ->orWhere('shipping_address', 'like', '%'.$term.'%')
                        ->orWhere('delivery_note_id', 'like', '%'.$term.'%');
                });
            })
            ->latest('id')
            ->paginate((int) $request->query('per_page', 20));

        return $this->respondWithPaginator($deliveryNotes, 'delivery_notes', $client, $request);
    }

    private function authorizeAbility($client, string $ability): void
    {
        $abilities = (array) ($client->abilities ?? []);
        if ($abilities === [] || in_array('*', $abilities, true) || in_array($ability, $abilities, true)) {
            return;
        }

        abort(403, 'This API client does not have permission for the requested resource: '.$ability);
    }

    private function respondWithPaginator(LengthAwarePaginator $paginator, string $resource, $client, Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'request_id' => (string) Str::uuid(),
            'resource' => $resource,
            'client' => $client->client_key,
            'data' => $paginator->items(),
            'meta' => [
                'filters' => [
                    'q' => $request->query('q'),
                    'per_page' => (int) $request->query('per_page', 20),
                ],
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                    'last_page' => $paginator->lastPage(),
                ],
            ],
        ]);
    }
}
