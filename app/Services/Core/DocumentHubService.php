<?php

namespace App\Services\Core;

use App\Models\DocumentLink;
use App\Models\DocumentRepository;
use App\Models\DocumentVersion;
use Illuminate\Database\Eloquent\Model;

class DocumentHubService
{
    public function __construct(
        private readonly TimelineService $timeline
    ) {
    }

    public function createVersion(DocumentRepository $document, ?string $fileName = null, ?string $versionLabel = null, array $metadata = []): DocumentVersion
    {
        $version = DocumentVersion::create([
            'document_repository_id' => $document->id,
            'version_label' => $versionLabel ?: ($document->version ?: '1.0'),
            'file_name' => $fileName ?: $document->document,
            'metadata' => $metadata,
            'created_by' => $document->created_by,
        ]);

        $this->timeline->record(
            $document,
            'Document version recorded',
            'Version '.$version->version_label.' added to repository history.',
            ['document_version_id' => $version->id],
            'document',
            $document->created_by
        );

        return $version;
    }

    public function link(DocumentRepository $document, Model $target, string $relationType = 'attachment', ?int $linkedBy = null): DocumentLink
    {
        $link = DocumentLink::updateOrCreate([
            'document_repository_id' => $document->id,
            'linkable_type' => get_class($target),
            'linkable_id' => $target->getKey(),
            'relation_type' => $relationType,
        ], [
            'linked_by' => $linkedBy,
            'created_by' => $document->created_by,
        ]);

        $this->timeline->record(
            $target,
            'Document linked',
            $document->title.' linked as '.$relationType.'.',
            ['document_repository_id' => $document->id, 'document_link_id' => $link->id],
            'document',
            $document->created_by,
            $linkedBy
        );

        return $link;
    }
}
