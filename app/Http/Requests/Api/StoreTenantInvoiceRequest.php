<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreTenantInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->tokenCan('erp.finance.write') || $this->user()?->tokenCan('*');
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer', 'min:0'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:issue_date'],
            'category_id' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'string', 'max:50'],
            'ref_number' => ['nullable', 'string', 'max:255'],
        ];
    }
}
