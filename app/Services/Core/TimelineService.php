<?php

namespace App\Services\Core;

use App\Models\InternalNote;
use App\Models\SavedView;
use App\Models\TimelineEntry;
use Illuminate\Database\Eloquent\Model;

class TimelineService
{
    public function record($target, string $title, ?string $body = null, array $metadata = [], string $entryType = 'system', ?int $createdBy = null, ?int $userId = null): TimelineEntry
    {
        return TimelineEntry::create([
            'created_by' => $createdBy ?: ($target instanceof Model ? ($target->created_by ?? $userId ?? 0) : ($userId ?? 0)),
            'user_id' => $userId,
            'timelineable_type' => $target instanceof Model ? get_class($target) : null,
            'timelineable_id' => $target instanceof Model ? $target->getKey() : null,
            'entry_type' => $entryType,
            'title' => $title,
            'body' => $body,
            'metadata' => $metadata,
            'happened_at' => now(),
        ]);
    }

    public function addNote(Model $target, string $body, int $createdBy, ?int $userId = null, bool $isPinned = false, string $visibility = 'private'): InternalNote
    {
        return InternalNote::create([
            'created_by' => $createdBy,
            'user_id' => $userId,
            'notable_type' => get_class($target),
            'notable_id' => $target->getKey(),
            'body' => $body,
            'is_pinned' => $isPinned,
            'visibility' => $visibility,
        ]);
    }

    public function saveView(int $userId, string $module, string $name, array $definition, bool $isDefault = false): SavedView
    {
        if ($isDefault) {
            SavedView::query()->where('user_id', $userId)->where('module', $module)->update(['is_default' => false]);
        }

        return SavedView::updateOrCreate([
            'user_id' => $userId,
            'module' => $module,
            'name' => $name,
        ], [
            'filters' => $definition['filters'] ?? [],
            'columns' => $definition['columns'] ?? [],
            'sorts' => $definition['sorts'] ?? [],
            'is_default' => $isDefault,
        ]);
    }
}
