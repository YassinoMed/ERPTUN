<?php

namespace App\Services;

use App\Models\AgriCertificate;
use App\Models\AgriLot;
use App\Models\AgriTraceEvent;
use Illuminate\Support\Facades\Auth;

class AgriTraceabilityService
{
    public function createTraceEvent(AgriLot $lot, array $payload): AgriTraceEvent
    {
        $previous = AgriTraceEvent::query()
            ->where('lot_id', $lot->id)
            ->latest('occurred_at')
            ->first();

        $creatorId = Auth::user()?->creatorId();
        $occurredAt = $payload['occurred_at'] ?? now()->toDateTimeString();
        $metadata = $payload['metadata'] ?? [];
        $previousHash = $previous?->current_hash;

        $hashPayload = json_encode([
            'lot_id' => $lot->id,
            'step' => $payload['step'] ?? null,
            'location' => $payload['location'] ?? null,
            'actor' => $payload['actor'] ?? null,
            'notes' => $payload['notes'] ?? null,
            'occurred_at' => $occurredAt,
            'metadata' => $metadata,
            'previous_hash' => $previousHash,
        ]);

        $currentHash = hash('sha256', $hashPayload ?? '');

        return AgriTraceEvent::create([
            'lot_id' => $lot->id,
            'step' => $payload['step'] ?? 'event',
            'location' => $payload['location'] ?? null,
            'actor' => $payload['actor'] ?? null,
            'notes' => $payload['notes'] ?? null,
            'occurred_at' => $occurredAt,
            'previous_hash' => $previousHash,
            'current_hash' => $currentHash,
            'metadata' => $metadata,
            'created_by' => $creatorId,
        ]);
    }

    public function issueCertificate(AgriLot $lot): AgriCertificate
    {
        $creatorId = Auth::user()?->creatorId();
        $issuedAt = now();
        $certificateNumber = sprintf('CERT-%s-%s', $lot->id, $issuedAt->format('YmdHis'));
        $verificationHash = hash('sha256', $lot->id . '|' . $lot->code . '|' . $certificateNumber . '|' . $issuedAt->toDateTimeString());

        $qrPayload = json_encode([
            'certificate_number' => $certificateNumber,
            'lot_code' => $lot->code,
            'lot_name' => $lot->name,
            'crop_type' => $lot->crop_type,
            'verification_hash' => $verificationHash,
        ]);

        return AgriCertificate::create([
            'lot_id' => $lot->id,
            'certificate_number' => $certificateNumber,
            'issued_at' => $issuedAt,
            'verification_hash' => $verificationHash,
            'qr_payload' => $qrPayload,
            'status' => 'issued',
            'created_by' => $creatorId,
        ]);
    }
}
