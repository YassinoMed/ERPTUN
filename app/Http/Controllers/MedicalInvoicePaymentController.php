<?php

namespace App\Http\Controllers;

use App\Models\MedicalInvoice;
use App\Models\MedicalInvoicePayment;
use App\Models\MedicalRecordAccessLog;
use Illuminate\Http\Request;

class MedicalInvoicePaymentController extends Controller
{
    public function store(Request $request, $invoiceId)
    {
        if (!\Auth::user()->can('create medical invoice payment')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $invoice = MedicalInvoice::where('created_by', \Auth::user()->creatorId())->findOrFail($invoiceId);
        $validator = \Validator::make($request->all(), [
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'nullable|string|max:255',
            'reference' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag()->first());
        }

        $invoice->payments()->create([
            'payment_date' => $request->payment_date,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'reference' => $request->reference,
            'notes' => $request->notes,
            'created_by' => \Auth::user()->creatorId(),
        ]);

        $invoice->status = $invoice->dueAmount() <= 0 ? 'paid' : 'partial';
        $invoice->save();
        MedicalRecordAccessLog::record($invoice->patient_id, 'create_medical_payment', 'medical-invoice-payments.store');

        return redirect()->route('medical-invoices.show', $invoice->id)->with('success', __('Medical payment successfully recorded.'));
    }

    public function destroy($id)
    {
        if (!\Auth::user()->can('delete medical invoice payment')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $payment = MedicalInvoicePayment::where('created_by', \Auth::user()->creatorId())->findOrFail($id);
        $invoice = MedicalInvoice::where('created_by', \Auth::user()->creatorId())->findOrFail($payment->medical_invoice_id);
        $payment->delete();

        $invoice->status = $invoice->paidAmount() > 0 ? 'partial' : 'unpaid';
        $invoice->save();

        return redirect()->route('medical-invoices.show', $invoice->id)->with('success', __('Medical payment successfully deleted.'));
    }
}
