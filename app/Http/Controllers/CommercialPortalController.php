<?php

namespace App\Http\Controllers;

use App\Models\CommercialContract;
use App\Models\Customer;
use App\Models\CustomerRecovery;
use App\Models\DeliveryNote;
use App\Models\Invoice;
use App\Models\Purchase;
use App\Models\RetailPromotion;
use App\Models\Vender;
use App\Services\Core\AuditTrailService;
use Illuminate\Http\Request;

class CommercialPortalController extends Controller
{
    public function __construct(
        private readonly AuditTrailService $auditTrail
    ) {
        $this->middleware(['auth', 'XSS', 'revalidate']);
    }

    public function customerPortal(Request $request)
    {
        if (!\Auth::user()->can('manage retail operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $customers = Customer::where('created_by', $creatorId)->orderBy('name')->get();
        $selectedCustomer = $customers->first();

        if ($request->filled('customer_id')) {
            $selectedCustomer = Customer::where('created_by', $creatorId)->findOrFail($request->integer('customer_id'));
        }

        $invoices = collect();
        $deliveryNotes = collect();
        $recoveries = collect();
        $contracts = collect();
        $customerSummary = [];

        if ($selectedCustomer) {
            $this->auditTrail->record('customer_portal_viewed', [
                'auditable' => $selectedCustomer,
                'new_values' => [
                    'portal' => 'customer',
                    'customer_id' => $selectedCustomer->id,
                ],
            ]);

            $invoices = Invoice::with('payments')
                ->where('created_by', $creatorId)
                ->where('customer_id', $selectedCustomer->id)
                ->latest('issue_date')
                ->limit(20)
                ->get();

            $deliveryNotes = DeliveryNote::where('created_by', $creatorId)
                ->where('customer_id', $selectedCustomer->id)
                ->latest('delivery_date')
                ->limit(20)
                ->get();

            $recoveries = CustomerRecovery::with('invoice')
                ->where('created_by', $creatorId)
                ->where('customer_id', $selectedCustomer->id)
                ->latest('id')
                ->limit(20)
                ->get();

            $contracts = CommercialContract::where('created_by', $creatorId)
                ->where('party_type', 'customer')
                ->where('party_id', $selectedCustomer->id)
                ->latest('start_date')
                ->limit(20)
                ->get();

            $customerSummary = [
                'invoice_total' => (float) $invoices->sum(fn ($invoice) => $invoice->getTotal()),
                'invoice_due' => (float) $invoices->sum(fn ($invoice) => $invoice->getDue()),
                'active_contracts' => $contracts->where('status', 'active')->count(),
                'delivery_in_transit' => $deliveryNotes->where('status', 'dispatched')->count(),
                'recovery_exposure' => (float) $recoveries->sum('due_amount'),
                'promotion_matches' => RetailPromotion::where('created_by', $creatorId)
                    ->whereIn('status', ['draft', 'active'])
                    ->whereIn('audience_type', ['all', 'vip', 'new_customers', 'loyalty'])
                    ->count(),
            ];
        }

        return view('retail.customer_portal', compact('customers', 'selectedCustomer', 'invoices', 'deliveryNotes', 'recoveries', 'contracts', 'customerSummary'));
    }

    public function supplierPortal(Request $request)
    {
        if (!\Auth::user()->can('manage retail operations')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $venders = Vender::where('created_by', $creatorId)->orderBy('name')->get();
        $selectedVender = $venders->first();

        if ($request->filled('vender_id')) {
            $selectedVender = Vender::where('created_by', $creatorId)->findOrFail($request->integer('vender_id'));
        }

        $purchases = collect();
        $contracts = collect();
        $supplierSummary = [];

        if ($selectedVender) {
            $this->auditTrail->record('supplier_portal_viewed', [
                'auditable_type' => Vender::class,
                'auditable_id' => $selectedVender->id,
                'created_by' => $creatorId,
                'new_values' => [
                    'portal' => 'supplier',
                    'vender_id' => $selectedVender->id,
                ],
            ]);

            $purchases = Purchase::with('payments')
                ->where('created_by', $creatorId)
                ->where('vender_id', $selectedVender->id)
                ->latest('purchase_date')
                ->limit(20)
                ->get();

            $contracts = CommercialContract::where('created_by', $creatorId)
                ->where('party_type', 'vender')
                ->where('party_id', $selectedVender->id)
                ->latest('start_date')
                ->limit(20)
                ->get();

            $supplierSummary = [
                'purchase_total' => (float) $purchases->sum(fn ($purchase) => $purchase->getTotal()),
                'purchase_due' => (float) $purchases->sum(fn ($purchase) => $purchase->getDue()),
                'active_contracts' => $contracts->where('status', 'active')->count(),
                'monthly_contracts' => $contracts->where('billing_cycle', 'monthly')->count(),
            ];
        }

        return view('retail.supplier_portal', compact('venders', 'selectedVender', 'purchases', 'contracts', 'supplierSummary'));
    }
}
