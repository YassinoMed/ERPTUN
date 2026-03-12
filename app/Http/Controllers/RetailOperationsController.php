<?php

namespace App\Http\Controllers;

use App\Models\CashMovement;
use App\Models\CashRegister;
use App\Models\CommercialContract;
use App\Models\Customer;
use App\Models\DeliveryNote;
use App\Models\DeliveryRoute;
use App\Models\Invoice;
use App\Models\InvoiceProduct;
use App\Models\LoyaltyAccount;
use App\Models\Pos;
use App\Models\PosPayment;
use App\Models\ProductService;
use App\Models\ProductServiceCategory;
use App\Models\PosSession;
use App\Models\Purchase;
use App\Models\PurchaseProduct;
use App\Models\RetailPromotion;
use App\Models\RetailProcurementRequest;
use App\Models\RetailStore;
use App\Models\StoreReplenishmentRequest;
use App\Models\Vender;
use App\Models\Warehouse;
use App\Services\Core\AuditTrailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RetailOperationsController extends Controller
{
    public function __construct(
        private readonly AuditTrailService $auditTrail
    ) {
        $this->middleware(['auth', 'XSS', 'revalidate']);
    }

    public function index()
    {
        if (!\Auth::user()->can('manage retail operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $customers = Customer::where('created_by', $creatorId)->latest('id')->get();
        $venders = Vender::where('created_by', $creatorId)->latest('id')->get();
        $deliveryNotes = DeliveryNote::where('created_by', $creatorId)->latest('id')->limit(20)->get();
        $cashRegisters = CashRegister::withCount('movements')->where('created_by', $creatorId)->latest('id')->get();
        $cashMovements = CashMovement::with('cashRegister')->where('created_by', $creatorId)->latest('movement_date')->limit(20)->get();
        $loyaltyAccounts = LoyaltyAccount::with('customer')->where('created_by', $creatorId)->latest('id')->get();
        $deliveryRoutes = DeliveryRoute::with('deliveryNote')->where('created_by', $creatorId)->latest('route_date')->get();
        $retailStores = RetailStore::withCount('posSessions')->where('created_by', $creatorId)->latest('id')->get();
        $posSessions = PosSession::with(['cashRegister', 'retailStore', 'opener'])->where('created_by', $creatorId)->latest('opened_at')->limit(20)->get();
        $promotions = RetailPromotion::where('created_by', $creatorId)->latest('id')->get();
        $commercialContracts = CommercialContract::with(['customer', 'vender', 'retailStore'])->where('created_by', $creatorId)->latest('start_date')->limit(20)->get();
        $warehouses = Warehouse::where('created_by', $creatorId)->latest('id')->get();
        $categories = ProductServiceCategory::where('created_by', $creatorId)->latest('id')->get();
        $products = ProductService::where('created_by', $creatorId)->where('type', 'product')->latest('id')->limit(100)->get();
        $procurementRequests = RetailProcurementRequest::with(['retailStore', 'vender', 'category', 'approver'])
            ->where('created_by', $creatorId)
            ->latest('id')
            ->limit(20)
            ->get();
        $replenishments = StoreReplenishmentRequest::with(['sourceStore', 'destinationStore', 'product'])
            ->where('created_by', $creatorId)
            ->latest('id')
            ->limit(20)
            ->get();
        $posSales = Pos::with(['customer', 'warehouse', 'posPayment'])->where('created_by', $creatorId)->latest('pos_date')->limit(20)->get();

        return view('retail.operations', compact(
            'customers',
            'venders',
            'deliveryNotes',
            'cashRegisters',
            'cashMovements',
            'loyaltyAccounts',
            'deliveryRoutes',
            'retailStores',
            'posSessions',
            'promotions',
            'commercialContracts',
            'warehouses',
            'categories',
            'products',
            'procurementRequests',
            'replenishments',
            'posSales'
        ));
    }

    public function storeCashRegister(Request $request)
    {
        if (!\Auth::user()->can('create retail operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $data = $request->validate([
            'name' => 'required|string|max:191',
            'location' => 'nullable|string|max:191',
            'opening_balance' => 'nullable|numeric',
            'status' => 'nullable|string|in:open,closed',
        ]);

        $data['opening_balance'] = $data['opening_balance'] ?? 0;
        $data['current_balance'] = $data['opening_balance'];
        $data['status'] = $data['status'] ?? 'open';
        $data['created_by'] = \Auth::user()->creatorId();

        CashRegister::create($data);

        return redirect()->route('retail.operations.index')->with('success', __('Cash register created.'));
    }

    public function storeMovement(Request $request)
    {
        if (!\Auth::user()->can('create retail operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $data = $request->validate([
            'cash_register_id' => 'required|integer',
            'type' => 'required|string|in:in,out',
            'amount' => 'required|numeric|min:0.01',
            'movement_date' => 'required|date',
            'reference' => 'nullable|string|max:191',
            'notes' => 'nullable|string',
        ]);

        $register = CashRegister::where('created_by', $creatorId)->findOrFail($data['cash_register_id']);
        $data['created_by'] = $creatorId;
        CashMovement::create($data);

        $delta = (float) $data['amount'];
        $register->current_balance = $data['type'] === 'in'
            ? ((float) $register->current_balance + $delta)
            : ((float) $register->current_balance - $delta);
        $register->save();

        return redirect()->route('retail.operations.index')->with('success', __('Cash movement recorded.'));
    }

    public function storeLoyaltyAccount(Request $request)
    {
        if (!\Auth::user()->can('create retail operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $data = $request->validate([
            'customer_id' => 'required|integer',
            'code' => 'required|string|max:100',
            'points_balance' => 'nullable|integer|min:0',
            'tier' => 'nullable|string|max:64',
            'status' => 'nullable|string|in:active,inactive',
        ]);

        Customer::where('created_by', $creatorId)->findOrFail($data['customer_id']);

        $data['points_balance'] = $data['points_balance'] ?? 0;
        $data['tier'] = $data['tier'] ?? 'standard';
        $data['status'] = $data['status'] ?? 'active';
        $data['created_by'] = $creatorId;

        LoyaltyAccount::updateOrCreate(
            [
                'code' => $data['code'],
                'created_by' => $creatorId,
            ],
            $data
        );

        return redirect()->route('retail.operations.index')->with('success', __('Loyalty account saved.'));
    }

    public function storeDeliveryRoute(Request $request)
    {
        if (!\Auth::user()->can('create retail operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $data = $request->validate([
            'delivery_note_id' => 'nullable|integer',
            'name' => 'required|string|max:191',
            'driver_name' => 'nullable|string|max:191',
            'vehicle_no' => 'nullable|string|max:100',
            'route_date' => 'required|date',
            'status' => 'nullable|string|in:planned,in_transit,completed',
            'notes' => 'nullable|string',
        ]);

        if (!empty($data['delivery_note_id'])) {
            DeliveryNote::where('created_by', $creatorId)->findOrFail($data['delivery_note_id']);
        }

        $data['status'] = $data['status'] ?? 'planned';
        $data['created_by'] = $creatorId;

        DeliveryRoute::create($data);

        return redirect()->route('retail.operations.index')->with('success', __('Delivery route saved.'));
    }

    public function storeRetailStore(Request $request)
    {
        if (!\Auth::user()->can('create retail operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('retail_stores', 'code')->where(fn ($query) => $query->where('created_by', $creatorId)),
            ],
            'store_type' => 'nullable|string|in:hq,store,kiosk,warehouse_hub',
            'region' => 'nullable|string|max:191',
            'manager_name' => 'nullable|string|max:191',
            'parent_store_id' => 'nullable|integer',
            'warehouse_id' => 'nullable|integer',
            'target_revenue' => 'nullable|numeric|min:0',
            'target_margin' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        if (!empty($data['parent_store_id'])) {
            RetailStore::where('created_by', $creatorId)->findOrFail($data['parent_store_id']);
        }

        if (!empty($data['warehouse_id'])) {
            Warehouse::where('created_by', $creatorId)->findOrFail($data['warehouse_id']);
        }

        $data['store_type'] = $data['store_type'] ?? 'store';
        $data['target_revenue'] = $data['target_revenue'] ?? 0;
        $data['target_margin'] = $data['target_margin'] ?? 0;
        $data['status'] = $data['status'] ?? 'active';
        $data['created_by'] = $creatorId;

        $store = RetailStore::create($data);
        $this->auditTrail->record('retail_store_created', [
            'auditable' => $store,
            'new_values' => $store->only(['name', 'code', 'store_type', 'region', 'status', 'target_revenue', 'target_margin']),
        ]);

        return redirect()->route('retail.operations.index')->with('success', __('Retail store created.'));
    }

    public function storePosSession(Request $request)
    {
        if (!\Auth::user()->can('create retail operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $data = $request->validate([
            'cash_register_id' => 'nullable|integer',
            'retail_store_id' => 'nullable|integer',
            'opened_at' => 'required|date',
            'closed_at' => 'nullable|date|after_or_equal:opened_at',
            'expected_amount' => 'nullable|numeric',
            'actual_amount' => 'nullable|numeric',
            'transactions_count' => 'nullable|integer|min:0',
            'mixed_payment_enabled' => 'nullable|boolean',
            'session_mode' => 'nullable|string|in:counter,mobile,self_checkout',
            'status' => 'nullable|string|in:open,closed,reconciled',
            'notes' => 'nullable|string',
        ]);

        if (!empty($data['cash_register_id'])) {
            CashRegister::where('created_by', $creatorId)->findOrFail($data['cash_register_id']);
        }

        if (!empty($data['retail_store_id'])) {
            RetailStore::where('created_by', $creatorId)->findOrFail($data['retail_store_id']);
        }

        $data['opened_by'] = \Auth::id();
        $data['expected_amount'] = $data['expected_amount'] ?? 0;
        $data['actual_amount'] = $data['actual_amount'] ?? 0;
        $data['transactions_count'] = $data['transactions_count'] ?? 0;
        $data['mixed_payment_enabled'] = (bool) ($data['mixed_payment_enabled'] ?? true);
        $data['session_mode'] = $data['session_mode'] ?? 'counter';
        $data['variance_amount'] = (float) $data['actual_amount'] - (float) $data['expected_amount'];
        $data['status'] = $data['status'] ?? 'open';
        $data['created_by'] = $creatorId;

        $session = PosSession::create($data);
        $this->auditTrail->record('pos_session_created', [
            'auditable' => $session,
            'new_values' => $session->only(['cash_register_id', 'retail_store_id', 'opened_at', 'status', 'variance_amount']),
        ]);

        return redirect()->route('retail.operations.index')->with('success', __('POS session saved.'));
    }

    public function storePromotion(Request $request)
    {
        if (!\Auth::user()->can('create retail operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('retail_promotions', 'code')->where(fn ($query) => $query->where('created_by', $creatorId)),
            ],
            'promotion_type' => 'nullable|string|in:discount,bundle,cashback,gift',
            'scope_type' => 'nullable|string|in:global,store,customer,product',
            'retail_store_id' => 'nullable|integer',
            'audience_type' => 'nullable|string|in:all,vip,wholesale,new_customers,loyalty',
            'auto_apply' => 'nullable|boolean',
            'discount_value' => 'nullable|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'budget_amount' => 'nullable|numeric|min:0',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'status' => 'nullable|string|in:draft,active,paused,expired',
            'notes' => 'nullable|string',
        ]);

        if (!empty($data['retail_store_id'])) {
            RetailStore::where('created_by', $creatorId)->findOrFail($data['retail_store_id']);
        }

        $data['promotion_type'] = $data['promotion_type'] ?? 'discount';
        $data['scope_type'] = $data['scope_type'] ?? 'global';
        $data['audience_type'] = $data['audience_type'] ?? 'all';
        $data['auto_apply'] = (bool) ($data['auto_apply'] ?? false);
        $data['discount_value'] = $data['discount_value'] ?? 0;
        $data['minimum_amount'] = $data['minimum_amount'] ?? 0;
        $data['budget_amount'] = $data['budget_amount'] ?? 0;
        $data['status'] = $data['status'] ?? 'draft';
        $data['created_by'] = $creatorId;

        $promotion = RetailPromotion::create($data);
        $this->auditTrail->record('retail_promotion_created', [
            'auditable' => $promotion,
            'new_values' => $promotion->only(['name', 'code', 'promotion_type', 'scope_type', 'status']),
        ]);

        return redirect()->route('retail.operations.index')->with('success', __('Retail promotion created.'));
    }

    public function storeCommercialContract(Request $request)
    {
        if (!\Auth::user()->can('create retail operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $data = $request->validate([
            'contract_number' => [
                'required',
                'string',
                'max:100',
                Rule::unique('commercial_contracts', 'contract_number')->where(fn ($query) => $query->where('created_by', $creatorId)),
            ],
            'title' => 'required|string|max:191',
            'party_type' => 'required|string|in:customer,vender',
            'party_id' => 'nullable|integer',
            'retail_store_id' => 'nullable|integer',
            'amount' => 'nullable|numeric|min:0',
            'category' => 'nullable|string|max:64',
            'owner_name' => 'nullable|string|max:191',
            'billing_cycle' => 'nullable|string|in:one_off,monthly,quarterly,yearly',
            'renewal_notice_days' => 'nullable|integer|min:0|max:365',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|string|in:draft,active,suspended,expired',
            'notes' => 'nullable|string',
        ]);

        if (!empty($data['party_id'])) {
            if ($data['party_type'] === 'customer') {
                Customer::where('created_by', $creatorId)->findOrFail($data['party_id']);
            } else {
                Vender::where('created_by', $creatorId)->findOrFail($data['party_id']);
            }
        }

        if (!empty($data['retail_store_id'])) {
            RetailStore::where('created_by', $creatorId)->findOrFail($data['retail_store_id']);
        }

        $data['amount'] = $data['amount'] ?? 0;
        $data['renewal_notice_days'] = $data['renewal_notice_days'] ?? 30;
        $data['status'] = $data['status'] ?? 'draft';
        $data['created_by'] = $creatorId;

        $contract = CommercialContract::create($data);
        $this->auditTrail->record('commercial_contract_created', [
            'auditable' => $contract,
            'new_values' => $contract->only(['contract_number', 'party_type', 'party_id', 'status', 'amount']),
        ]);

        return redirect()->route('retail.operations.index')->with('success', __('Commercial contract created.'));
    }

    public function storeProcurementRequest(Request $request)
    {
        if (!\Auth::user()->can('create retail operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $data = $request->validate([
            'retail_store_id' => 'nullable|integer',
            'vender_id' => 'nullable|integer',
            'category_id' => 'nullable|integer',
            'reference' => [
                'required',
                'string',
                'max:100',
                Rule::unique('retail_procurement_requests', 'reference')->where(fn ($query) => $query->where('created_by', $creatorId)),
            ],
            'title' => 'required|string|max:191',
            'budget_amount' => 'nullable|numeric|min:0',
            'needed_by' => 'nullable|date',
            'status' => 'nullable|string|in:draft,pending,approved,ordered,closed',
            'notes' => 'nullable|string',
        ]);

        if (!empty($data['retail_store_id'])) {
            RetailStore::where('created_by', $creatorId)->findOrFail($data['retail_store_id']);
        }
        if (!empty($data['vender_id'])) {
            Vender::where('created_by', $creatorId)->findOrFail($data['vender_id']);
        }
        if (!empty($data['category_id'])) {
            ProductServiceCategory::where('created_by', $creatorId)->findOrFail($data['category_id']);
        }

        $data['budget_amount'] = $data['budget_amount'] ?? 0;
        $data['status'] = $data['status'] ?? 'draft';
        $data['created_by'] = $creatorId;

        $requestRecord = RetailProcurementRequest::create($data);
        $this->auditTrail->record('retail_procurement_request_created', [
            'auditable' => $requestRecord,
            'new_values' => $requestRecord->only(['reference', 'title', 'status', 'budget_amount']),
        ]);

        return redirect()->route('retail.operations.index')->with('success', __('Procurement request created.'));
    }

    public function storeReplenishment(Request $request)
    {
        if (!\Auth::user()->can('create retail operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $data = $request->validate([
            'source_store_id' => 'nullable|integer',
            'destination_store_id' => 'nullable|integer',
            'product_id' => 'nullable|integer',
            'suggested_quantity' => 'nullable|numeric|min:0',
            'approved_quantity' => 'nullable|numeric|min:0',
            'needed_by' => 'nullable|date',
            'status' => 'nullable|string|in:draft,planned,approved,in_transit,received',
            'notes' => 'nullable|string',
        ]);

        if (!empty($data['source_store_id'])) {
            RetailStore::where('created_by', $creatorId)->findOrFail($data['source_store_id']);
        }
        if (!empty($data['destination_store_id'])) {
            RetailStore::where('created_by', $creatorId)->findOrFail($data['destination_store_id']);
        }
        if (!empty($data['product_id'])) {
            ProductService::where('created_by', $creatorId)->findOrFail($data['product_id']);
        }

        $data['suggested_quantity'] = $data['suggested_quantity'] ?? 0;
        $data['approved_quantity'] = $data['approved_quantity'] ?? 0;
        $data['status'] = $data['status'] ?? 'draft';
        $data['created_by'] = $creatorId;

        $replenishment = StoreReplenishmentRequest::create($data);
        $this->auditTrail->record('store_replenishment_created', [
            'auditable' => $replenishment,
            'new_values' => $replenishment->only(['source_store_id', 'destination_store_id', 'product_id', 'status']),
        ]);

        return redirect()->route('retail.operations.index')->with('success', __('Store replenishment request created.'));
    }

    public function reports()
    {
        if (!\Auth::user()->can('manage retail operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $stores = RetailStore::where('created_by', $creatorId)->get();
        $posSessions = PosSession::where('created_by', $creatorId)->latest('opened_at')->limit(20)->get();
        $promotions = RetailPromotion::where('created_by', $creatorId)->get();
        $contracts = CommercialContract::where('created_by', $creatorId)->with(['customer', 'vender', 'retailStore'])->latest('start_date')->limit(15)->get();
        $invoices = Invoice::with('customer')->where('created_by', $creatorId)->latest('issue_date')->limit(12)->get();
        $purchases = Purchase::with('vender')->where('created_by', $creatorId)->latest('purchase_date')->limit(12)->get();
        $procurementRequests = RetailProcurementRequest::with(['retailStore', 'vender'])->where('created_by', $creatorId)->latest('id')->limit(12)->get();
        $replenishments = StoreReplenishmentRequest::with(['sourceStore', 'destinationStore', 'product'])->where('created_by', $creatorId)->latest('id')->limit(12)->get();
        $paymentMix = PosPayment::select('created_by', DB::raw('COUNT(*) as payments_count'), DB::raw('SUM(amount) as payments_total'))
            ->where('created_by', $creatorId)
            ->groupBy('created_by')
            ->first();
        $storePerformance = RetailStore::where('created_by', $creatorId)
            ->get()
            ->map(function (RetailStore $store) use ($creatorId) {
                $sessions = PosSession::where('created_by', $creatorId)->where('retail_store_id', $store->id)->get();

                return [
                    'store' => $store,
                    'sessions' => $sessions->count(),
                    'transactions' => (int) $sessions->sum('transactions_count'),
                    'variance' => (float) $sessions->sum('variance_amount'),
                    'target_revenue' => (float) $store->target_revenue,
                ];
            });
        $topProducts = InvoiceProduct::query()
            ->select('product_id', DB::raw('SUM(quantity) as sold_quantity'))
            ->whereHas('product', fn ($query) => $query->where('created_by', $creatorId))
            ->groupBy('product_id')
            ->orderByDesc('sold_quantity')
            ->limit(10)
            ->get()
            ->load('product');

        $kpis = [
            'active_stores' => $stores->where('status', 'active')->count(),
            'open_sessions' => $posSessions->where('status', 'open')->count(),
            'active_promotions' => $promotions->where('status', 'active')->count(),
            'contract_value' => (float) $contracts->sum('amount'),
        ];

        return view('retail.reports', compact(
            'kpis',
            'stores',
            'posSessions',
            'promotions',
            'contracts',
            'invoices',
            'purchases',
            'procurementRequests',
            'replenishments',
            'paymentMix',
            'storePerformance',
            'topProducts'
        ));
    }

    public function bi()
    {
        if (!\Auth::user()->can('manage retail operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $stores = RetailStore::where('created_by', $creatorId)->get();
        $sessions = PosSession::where('created_by', $creatorId)->get();
        $contracts = CommercialContract::where('created_by', $creatorId)->get();
        $promotions = RetailPromotion::where('created_by', $creatorId)->get();
        $procurement = RetailProcurementRequest::where('created_by', $creatorId)->get();
        $replenishments = StoreReplenishmentRequest::where('created_by', $creatorId)->get();
        $invoiceItems = InvoiceProduct::query()
            ->select('product_id', DB::raw('SUM(quantity) as quantity_sold'), DB::raw('SUM(total) as total_revenue'))
            ->groupBy('product_id')
            ->orderByDesc('total_revenue')
            ->limit(12)
            ->get()
            ->load('product');
        $purchaseItems = PurchaseProduct::query()
            ->select('product_id', DB::raw('SUM(quantity) as quantity_bought'), DB::raw('SUM(price * quantity) as spend_total'))
            ->groupBy('product_id')
            ->orderByDesc('spend_total')
            ->limit(12)
            ->get()
            ->load('product');

        $scoreboard = [
            'store_count' => $stores->count(),
            'sessions_count' => $sessions->count(),
            'transactions_count' => (int) $sessions->sum('transactions_count'),
            'mixed_payment_sessions' => $sessions->where('mixed_payment_enabled', true)->count(),
            'promotion_budget' => (float) $promotions->sum('budget_amount'),
            'contract_exposure' => (float) $contracts->sum('amount'),
            'procurement_backlog' => $procurement->whereIn('status', ['pending', 'approved'])->count(),
            'replenishment_backlog' => $replenishments->whereIn('status', ['planned', 'approved', 'in_transit'])->count(),
        ];

        return view('retail.bi', compact(
            'scoreboard',
            'stores',
            'sessions',
            'contracts',
            'promotions',
            'procurement',
            'replenishments',
            'invoiceItems',
            'purchaseItems'
        ));
    }
}
