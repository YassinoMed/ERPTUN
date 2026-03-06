<?php

namespace Modules\Crm\Http\Controllers;

use App\Models\Customer;
use App\Models\ProductService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CrmApiController extends Controller
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

    public function customers(Request $request)
    {
        if (! $this->tokenAllows($request, 'customers.read')) {
            return $this->error('Forbidden', 403);
        }

        $customers = Customer::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'customers' => $customers,
        ], 'Customers fetched successfully.');
    }

    public function customerShow(Request $request, Customer $customer)
    {
        if (! $this->tokenAllows($request, 'customers.read')) {
            return $this->error('Forbidden', 403);
        }

        if ((int) $customer->created_by !== (int) $request->user()->creatorId()) {
            return $this->error('Not found', 404);
        }

        return $this->success([
            'customer' => $customer,
        ], 'Customer fetched successfully.');
    }

    public function products(Request $request)
    {
        if (! $this->tokenAllows($request, 'products.read')) {
            return $this->error('Forbidden', 403);
        }

        $products = ProductService::query()
            ->where('created_by', $request->user()->creatorId())
            ->latest('id')
            ->paginate((int) ($request->query('per_page', 20)));

        return $this->success([
            'products' => $products,
        ], 'Products fetched successfully.');
    }

    public function productShow(Request $request, ProductService $productService)
    {
        if (! $this->tokenAllows($request, 'products.read')) {
            return $this->error('Forbidden', 403);
        }

        if ((int) $productService->created_by !== (int) $request->user()->creatorId()) {
            return $this->error('Not found', 404);
        }

        return $this->success([
            'product' => $productService,
        ], 'Product fetched successfully.');
    }
}
