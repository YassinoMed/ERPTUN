<?php

namespace App\Services\Core;

use App\Models\ApiClient;
use Illuminate\Support\Str;

class ApiClientService
{
    public function abilityCatalog(): array
    {
        return [
            'customers:read' => __('Read customer records'),
            'products:read' => __('Read product catalog records'),
            'invoices:read' => __('Read sales invoices'),
            'purchases:read' => __('Read purchase documents'),
            'patients:read' => __('Read patient records'),
            'delivery-notes:read' => __('Read delivery notes'),
        ];
    }

    public function issue(int $createdBy, string $name, array $abilities = [], ?\DateTimeInterface $expiresAt = null): array
    {
        $plainSecret = Str::random(48);
        $client = ApiClient::create([
            'created_by' => $createdBy,
            'name' => $name,
            'client_key' => 'erp_'.Str::lower(Str::random(24)),
            'client_secret' => hash('sha256', $plainSecret),
            'abilities' => $abilities,
            'expires_at' => $expiresAt,
        ]);

        return [
            'client' => $client,
            'plain_secret' => $plainSecret,
        ];
    }

    public function rotateSecret(ApiClient $client): array
    {
        $plainSecret = Str::random(48);
        $client->forceFill([
            'client_secret' => hash('sha256', $plainSecret),
            'last_used_at' => null,
        ])->save();

        return [
            'client' => $client->fresh(),
            'plain_secret' => $plainSecret,
        ];
    }

    public function setStatus(ApiClient $client, bool $isActive): ApiClient
    {
        $client->forceFill([
            'is_active' => $isActive,
        ])->save();

        return $client->fresh();
    }

    public function validate(string $clientKey, string $secret): ?ApiClient
    {
        $client = ApiClient::query()
            ->where('client_key', $clientKey)
            ->where('is_active', true)
            ->first();

        if (! $client) {
            return null;
        }

        if ($client->expires_at && $client->expires_at->isPast()) {
            return null;
        }

        if (! hash_equals($client->client_secret, hash('sha256', $secret))) {
            return null;
        }

        $client->last_used_at = now();
        $client->save();

        return $client;
    }
}
