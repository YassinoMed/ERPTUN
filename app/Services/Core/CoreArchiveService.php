<?php

namespace App\Services\Core;

use App\Models\ArchivedRecord;
use App\Models\Customer;
use App\Models\Patient;
use App\Models\ProductService;
use App\Models\User;
use App\Models\Vender;
use Illuminate\Database\Eloquent\Model;

class CoreArchiveService
{
    /**
     * @return array<string, class-string<Model>>
     */
    public function modelMap(): array
    {
        return [
            'customer' => Customer::class,
            'vender' => Vender::class,
            'product_service' => ProductService::class,
            'patient' => Patient::class,
        ];
    }

    public function archive(User $actor, string $recordType, int $recordId, ?string $reason = null): ArchivedRecord
    {
        $modelClass = $this->modelMap()[$recordType] ?? null;
        abort_unless($modelClass, 422, 'Invalid archive type.');

        $record = $modelClass::query()
            ->where('created_by', $actor->creatorId())
            ->findOrFail($recordId);

        $record->forceFill([
            'archived_at' => now(),
            'archived_by' => $actor->id,
        ])->save();

        return ArchivedRecord::updateOrCreate([
            'created_by' => $actor->creatorId(),
            'record_type' => $modelClass,
            'record_id' => $record->getKey(),
        ], [
            'record_owner_id' => $record->created_by ?? $actor->creatorId(),
            'display_name' => $this->displayName($record),
            'reason' => $reason,
            'archived_by' => $actor->id,
            'archived_at' => now(),
            'restored_by' => null,
            'restored_at' => null,
            'payload' => [
                'record_type' => $recordType,
                'record_name' => $this->displayName($record),
            ],
        ]);
    }

    public function restore(User $actor, ArchivedRecord $archivedRecord): void
    {
        abort_unless((int) $archivedRecord->created_by === (int) $actor->creatorId(), 403, 'Permission denied.');
        /** @var class-string<Model> $modelClass */
        $modelClass = $archivedRecord->record_type;
        /** @var Model|null $record */
        $record = $modelClass::query()->find($archivedRecord->record_id);

        if ($record) {
            $record->forceFill([
                'archived_at' => null,
                'archived_by' => null,
            ])->save();
        }

        $archivedRecord->update([
            'restored_by' => $actor->id,
            'restored_at' => now(),
        ]);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function archiveOptions(int $creatorId): array
    {
        return [
            'customer' => Customer::query()->where('created_by', $creatorId)->whereNull('archived_at')->latest('id')->limit(50)->get()->pluck('name', 'id')->all(),
            'vender' => Vender::query()->where('created_by', $creatorId)->whereNull('archived_at')->latest('id')->limit(50)->get()->pluck('name', 'id')->all(),
            'product_service' => ProductService::query()->where('created_by', $creatorId)->whereNull('archived_at')->latest('id')->limit(50)->get()->pluck('name', 'id')->all(),
            'patient' => Patient::query()->where('created_by', $creatorId)->whereNull('archived_at')->latest('id')->limit(50)->get()->mapWithKeys(function (Patient $patient) {
                return [$patient->id => trim($patient->first_name . ' ' . $patient->last_name)];
            })->all(),
        ];
    }

    private function displayName(Model $record): string
    {
        return match (true) {
            $record instanceof Patient => trim($record->first_name . ' ' . $record->last_name),
            isset($record->name) => (string) $record->name,
            default => class_basename($record) . '#' . $record->getKey(),
        };
    }
}
