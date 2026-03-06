<?php

namespace App\Http\Controllers;

use App\Models\Cheque;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ChequeController extends Controller
{
    public function index(Request $request)
    {
        if (!\Auth::user()->can('manage cheque')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $query = Cheque::where('created_by', \Auth::user()->creatorId());
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->search) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('cheque_number', 'like', '%' . $search . '%')
                    ->orWhere('beneficiary_name', 'like', '%' . $search . '%');
            });
        }

        $cheques = $query->orderBy('id', 'desc')->get();

        return view('cheque.index', compact('cheques'));
    }

    public function create()
    {
        if (!\Auth::user()->can('create cheque')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        return view('cheque.create');
    }

    public function store(Request $request)
    {
        if (!\Auth::user()->can('create cheque')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'beneficiary_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.001',
            'amount_text' => 'nullable|string|max:255',
            'currency' => 'required|string|max:10',
            'issue_date' => 'required|date',
            'due_date' => 'nullable|date',
            'bank_name' => 'nullable|string|max:255',
            'bank_agency' => 'nullable|string|max:255',
            'bank_account' => 'nullable|string|max:255',
            'rib' => 'nullable|string|max:255',
            'chequebook_number' => 'nullable|string|max:255',
            'cheque_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $cheque = new Cheque();
        $cheque->beneficiary_name = $request->beneficiary_name;
        $cheque->amount = $request->amount;
        $cheque->amount_text = $request->amount_text;
        $cheque->currency = $request->currency;
        $cheque->issue_date = $request->issue_date;
        $cheque->due_date = $request->due_date;
        $cheque->bank_name = $request->bank_name;
        $cheque->bank_agency = $request->bank_agency;
        $cheque->bank_account = $request->bank_account;
        $cheque->rib = $request->rib;
        $cheque->chequebook_number = $request->chequebook_number;
        $cheque->cheque_number = $request->cheque_number;
        $cheque->status = 'issued';
        $cheque->status_date = $request->issue_date;
        $cheque->notes = $request->notes;
        $cheque->created_by = \Auth::user()->creatorId();
        $cheque->save();

        return redirect()->route('cheques.index')->with('success', __('Cheque successfully created.'));
    }

    public function edit($id)
    {
        if (!\Auth::user()->can('edit cheque')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $cheque = Cheque::where('created_by', \Auth::user()->creatorId())->findOrFail($id);

        return view('cheque.edit', compact('cheque'));
    }

    public function update(Request $request, $id)
    {
        if (!\Auth::user()->can('edit cheque')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $cheque = Cheque::where('created_by', \Auth::user()->creatorId())->findOrFail($id);

        $validator = \Validator::make($request->all(), [
            'beneficiary_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.001',
            'amount_text' => 'nullable|string|max:255',
            'currency' => 'required|string|max:10',
            'issue_date' => 'required|date',
            'due_date' => 'nullable|date',
            'bank_name' => 'nullable|string|max:255',
            'bank_agency' => 'nullable|string|max:255',
            'bank_account' => 'nullable|string|max:255',
            'rib' => 'nullable|string|max:255',
            'chequebook_number' => 'nullable|string|max:255',
            'cheque_number' => 'nullable|string|max:255',
            'status' => 'required|string|max:255',
            'status_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $cheque->beneficiary_name = $request->beneficiary_name;
        $cheque->amount = $request->amount;
        $cheque->amount_text = $request->amount_text;
        $cheque->currency = $request->currency;
        $cheque->issue_date = $request->issue_date;
        $cheque->due_date = $request->due_date;
        $cheque->bank_name = $request->bank_name;
        $cheque->bank_agency = $request->bank_agency;
        $cheque->bank_account = $request->bank_account;
        $cheque->rib = $request->rib;
        $cheque->chequebook_number = $request->chequebook_number;
        $cheque->cheque_number = $request->cheque_number;
        $cheque->status = $request->status;
        $cheque->status_date = $request->status_date;
        $cheque->notes = $request->notes;
        $cheque->save();

        return redirect()->route('cheques.index')->with('success', __('Cheque successfully updated.'));
    }

    public function destroy($id)
    {
        if (!\Auth::user()->can('delete cheque')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $cheque = Cheque::where('created_by', \Auth::user()->creatorId())->findOrFail($id);
        $cheque->delete();

        return redirect()->route('cheques.index')->with('success', __('Cheque successfully deleted.'));
    }

    public function export()
    {
        if (!\Auth::user()->can('manage cheque')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $cheques = Cheque::where('created_by', \Auth::user()->creatorId())->orderBy('id', 'desc')->get();

        return response()->streamDownload(function () use ($cheques) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'Beneficiary', 'Amount', 'Currency', 'Issue Date', 'Due Date', 'Cheque Number', 'Status', 'Status Date']);
            foreach ($cheques as $cheque) {
                fputcsv($out, [
                    $cheque->id,
                    $cheque->beneficiary_name,
                    $cheque->amount,
                    $cheque->currency,
                    $cheque->issue_date,
                    $cheque->due_date,
                    $cheque->cheque_number,
                    $cheque->status,
                    $cheque->status_date,
                ]);
            }
            fclose($out);
        }, 'cheques-' . date('Ymd-His') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function importForm()
    {
        if (!\Auth::user()->can('manage cheque')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        return view('cheque.import');
    }

    public function importStore(Request $request)
    {
        if (!\Auth::user()->can('manage cheque')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'file' => 'required|file',
        ]);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $rows = Excel::toArray([], $request->file('file'));
        $dataRows = $rows[0] ?? [];

        $updated = 0;
        foreach ($dataRows as $row) {
            if (!isset($row[0]) || !isset($row[7])) {
                continue;
            }
            $chequeNumber = trim($row[6] ?? '');
            $status = trim($row[7] ?? '');
            $statusDate = isset($row[8]) ? $row[8] : null;

            if ($chequeNumber === '' || $status === '') {
                continue;
            }

            $cheque = Cheque::where('created_by', \Auth::user()->creatorId())
                ->where('cheque_number', $chequeNumber)
                ->first();
            if ($cheque) {
                $cheque->status = $status;
                $cheque->status_date = $statusDate;
                $cheque->save();
                $updated++;
            }
        }

        return redirect()->route('cheques.index')->with('success', __('Cheque reconciliation updated: ') . $updated);
    }

    public function print($id)
    {
        if (!\Auth::user()->can('print cheque')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $cheque = Cheque::where('created_by', \Auth::user()->creatorId())->findOrFail($id);

        return view('cheque.print', compact('cheque'));
    }
}
