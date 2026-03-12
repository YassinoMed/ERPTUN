<?php

namespace App\Services\Core;

use App\Models\Utility;

class DocumentNumberService
{
    private const PREFIX_KEYS = [
        'purchase' => 'purchase_prefix',
        'quotation' => 'quotation_prefix',
        'pos' => 'pos_prefix',
        'contract' => 'contract_prefix',
        'invoice' => 'invoice_prefix',
        'proposal' => 'proposal_prefix',
        'bill' => 'bill_prefix',
        'expense' => 'expense_prefix',
        'journal' => 'journal_prefix',
        'customer' => 'customer_prefix',
        'vendor' => 'vender_prefix',
        'employee' => 'employee_prefix',
        'bug' => 'bug_prefix',
    ];

    public function format(
        string $documentType,
        int|string $number,
        ?int $ownerId = null,
        ?array $settings = null,
        int $padLength = 5
    ): string {
        $prefixKey = self::PREFIX_KEYS[$documentType] ?? null;

        if ($prefixKey === null) {
            return (string) $number;
        }

        $settings ??= $ownerId ? Utility::settingsById($ownerId) : Utility::settings();
        $prefix = $settings[$prefixKey] ?? '';

        return $prefix.sprintf('%0'.$padLength.'d', (int) $number);
    }
}
