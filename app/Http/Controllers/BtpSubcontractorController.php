<?php

namespace App\Http\Controllers;

use App\Models\BtpSubcontractInvoice;
use App\Models\BtpSubcontractPayment;
use App\Models\BtpSubcontractor;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BtpSubcontractorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'XSS', 'revalidate']);
    }

    public function index()
    {
        if (!\Auth::user()->can('manage btp subcontractors')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $creatorId = \Auth::user()->creatorId();
        $subcontractors = BtpSubcontractor::where('created_by', $creatorId)->latest('id')->get();
        $projects = Project::where('created_by', $creatorId)->orderBy('id', 'desc')->get();

        $invoices = BtpSubcontractInvoice::with('subcontractor')
            ->where('created_by', $creatorId)
            ->latest('id')
            ->get();

        $paymentTotals = BtpSubcontractPayment::query()
            ->selectRaw('invoice_id, SUM(amount) as total_paid')
            ->where('created_by', $creatorId)
            ->groupBy('invoice_id')
            ->pluck('total_paid', 'invoice_id');

        $totalOutstanding = 0;
        $totalRetention = 0;
        $totalVat = 0;

        foreach ($invoices as $invoice) {
            $paid = (float) ($paymentTotals[$invoice->id] ?? 0);
            $totalOutstanding += max(0, (float) $invoice->total_due - $paid);
            $totalRetention += (float) $invoice->retention_amount;
            $totalVat += (float) $invoice->vat_amount;
        }

        $recentPayments = BtpSubcontractPayment::where('created_by', $creatorId)
            ->latest('payment_date')
            ->limit(10)
            ->get();

        return view('btp/subcontractors', compact('subcontractors', 'projects', 'invoices', 'paymentTotals', 'totalOutstanding', 'totalRetention', 'totalVat', 'recentPayments'));
    }

    public function storeSubcontractor(Request $request)
    {
        if (!\Auth::user()->can('manage btp subcontractors')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $data = $request->validate([
            'name' => 'required|string|max:191',
            'contact_name' => 'nullable|string|max:191',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:191',
            'address' => 'nullable|string',
        ]);

        $data['created_by'] = \Auth::user()->creatorId();
        BtpSubcontractor::create($data);

        return redirect()->route('btp.subcontractors.index')->with('success', __('Subcontractor saved.'));
    }

    public function storeInvoice(Request $request)
    {
        if (!\Auth::user()->can('manage btp subcontractors')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $data = $request->validate([
            'subcontractor_id' => 'required|integer',
            'project_id' => 'required|integer',
            'reference' => 'nullable|string|max:191',
            'amount' => 'required|numeric|min:0',
            'retention_rate' => 'nullable|numeric|min:0',
            'vat_rate' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date',
        ]);

        $creatorId = \Auth::user()->creatorId();
        BtpSubcontractor::where('created_by', $creatorId)->findOrFail($data['subcontractor_id']);
        Project::where('created_by', $creatorId)->findOrFail($data['project_id']);

        $amount = (float) $data['amount'];
        $retentionRate = (float) ($data['retention_rate'] ?? 10);
        $vatRate = (float) ($data['vat_rate'] ?? 19);
        $retentionAmount = ($amount * $retentionRate) / 100;
        $vatAmount = ($amount * $vatRate) / 100;
        $totalDue = $amount - $retentionAmount + $vatAmount;

        BtpSubcontractInvoice::create([
            'subcontractor_id' => $data['subcontractor_id'],
            'project_id' => $data['project_id'],
            'reference' => $data['reference'] ?? 'INV-' . Carbon::now()->format('YmdHis'),
            'amount' => $amount,
            'retention_rate' => $retentionRate,
            'retention_amount' => $retentionAmount,
            'vat_rate' => $vatRate,
            'vat_amount' => $vatAmount,
            'total_due' => $totalDue,
            'status' => 'unpaid',
            'due_date' => $data['due_date'] ?? null,
            'created_by' => $creatorId,
        ]);

        return redirect()->route('btp.subcontractors.index')->with('success', __('Invoice saved.'));
    }

    public function storePayment(Request $request)
    {
        if (!\Auth::user()->can('manage btp subcontractors')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }

        $data = $request->validate([
            'invoice_id' => 'required|integer',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'note' => 'nullable|string',
        ]);

        $creatorId = \Auth::user()->creatorId();
        $invoice = BtpSubcontractInvoice::where('created_by', $creatorId)->findOrFail($data['invoice_id']);

        BtpSubcontractPayment::create([
            'invoice_id' => $invoice->id,
            'amount' => $data['amount'],
            'payment_date' => $data['payment_date'],
            'note' => $data['note'] ?? null,
            'created_by' => $creatorId,
        ]);

        $paidTotal = BtpSubcontractPayment::where('invoice_id', $invoice->id)->sum('amount');
        if ($paidTotal >= $invoice->total_due) {
            $invoice->status = 'paid';
        } elseif ($paidTotal > 0) {
            $invoice->status = 'partial';
        } else {
            $invoice->status = 'unpaid';
        }
        $invoice->save();

        return redirect()->route('btp.subcontractors.index')->with('success', __('Payment recorded.'));
    }
}
