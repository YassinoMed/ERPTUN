<?php

namespace App\Http\Controllers;

use App\Models\DocumentRepository;
use App\Models\DocumentRepositoryCategory;
use App\Models\Utility;
use App\Services\Core\DocumentHubService;
use App\Services\Core\SecurityAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentRepositoryController extends Controller
{
    public function __construct(
        private readonly DocumentHubService $documentHub,
        private readonly SecurityAccessService $securityAccess
    ) {
    }

    public function index()
    {
        if (!Auth::user()->can('manage document repository') && !Auth::user()->can('show document repository')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $documents = DocumentRepository::where('created_by', Auth::user()->creatorId())
            ->with('category')
            ->latest('id')
            ->get();

        return view('document_repository.index', compact('documents'));
    }

    public function create()
    {
        if (!Auth::user()->can('create document repository')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $categories = DocumentRepositoryCategory::where('created_by', Auth::user()->creatorId())
            ->where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id');
        $categories->prepend(__('Select Category'), '');
        $statuses = DocumentRepository::$statuses;

        return view('document_repository.create', compact('categories', 'statuses'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('create document repository')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'title' => 'required|max:255',
            'document_repository_category_id' => 'nullable|integer|exists:document_repository_categories,id',
            'version' => 'required|max:50',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $document = new DocumentRepository();
        $document->document_repository_category_id = $request->document_repository_category_id;
        $document->title = $request->title;
        $document->reference = $request->reference;
        $document->version = $request->version;
        $document->status = $request->status;
        $document->description = $request->description;
        $document->effective_date = $request->effective_date;
        $document->expires_at = $request->expires_at;
        $document->created_by = Auth::user()->creatorId();

        if ($request->hasFile('document')) {
            $imageSize = $request->file('document')->getSize();
            $result = Utility::updateStorageLimit(Auth::user()->creatorId(), $imageSize);

            if ($result == 1) {
                $fileName = time() . '_' . $request->file('document')->getClientOriginalName();
                $dir = 'uploads/document_repository';
                $path = Utility::upload_file($request, 'document', $fileName, $dir, []);
                if ($path['flag'] == 0) {
                    return redirect()->back()->with('error', __($path['msg']));
                }
                $document->document = $fileName;
            }
        }

        $document->save();
        $this->documentHub->createVersion($document, $document->document, $document->version, [
            'status' => $document->status,
            'source' => 'document_repository.store',
        ]);

        return redirect()->route('document-repository.index')->with('success', __('Document repository entry successfully created.'));
    }

    public function show(DocumentRepository $documentRepository)
    {
        if (!$this->canAccess($documentRepository)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $documentRepository->load(['category', 'versions', 'links']);
        $this->securityAccess->logSensitiveAccess('view_document_repository', DocumentRepository::class, $documentRepository->id, [
            'title' => $documentRepository->title,
        ]);

        return view('document_repository.show', compact('documentRepository'));
    }

    public function edit(DocumentRepository $documentRepository)
    {
        if (!Auth::user()->can('edit document repository') || !$this->owns($documentRepository)) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $categories = DocumentRepositoryCategory::where('created_by', Auth::user()->creatorId())
            ->orderBy('name')
            ->pluck('name', 'id');
        $categories->prepend(__('Select Category'), '');
        $statuses = DocumentRepository::$statuses;

        return view('document_repository.edit', compact('documentRepository', 'categories', 'statuses'));
    }

    public function update(Request $request, DocumentRepository $documentRepository)
    {
        if (!Auth::user()->can('edit document repository') || !$this->owns($documentRepository)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'title' => 'required|max:255',
            'document_repository_category_id' => 'nullable|integer|exists:document_repository_categories,id',
            'version' => 'required|max:50',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $documentRepository->document_repository_category_id = $request->document_repository_category_id;
        $documentRepository->title = $request->title;
        $documentRepository->reference = $request->reference;
        $documentRepository->version = $request->version;
        $documentRepository->status = $request->status;
        $documentRepository->description = $request->description;
        $documentRepository->effective_date = $request->effective_date;
        $documentRepository->expires_at = $request->expires_at;
        $previousDocument = $documentRepository->document;
        $previousVersion = $documentRepository->version;

        if ($request->hasFile('document')) {
            $imageSize = $request->file('document')->getSize();
            $result = Utility::updateStorageLimit(Auth::user()->creatorId(), $imageSize);

            if ($result == 1) {
                $fileName = time() . '_' . $request->file('document')->getClientOriginalName();
                $dir = 'uploads/document_repository';
                $path = Utility::upload_file($request, 'document', $fileName, $dir, []);
                if ($path['flag'] == 0) {
                    return redirect()->back()->with('error', __($path['msg']));
                }
                $documentRepository->document = $fileName;
            }
        }

        $documentRepository->save();
        if ($previousDocument !== $documentRepository->document || $previousVersion !== $documentRepository->version) {
            $this->documentHub->createVersion($documentRepository, $documentRepository->document, $documentRepository->version, [
                'status' => $documentRepository->status,
                'source' => 'document_repository.update',
                'previous_document' => $previousDocument,
                'previous_version' => $previousVersion,
            ]);
        }

        return redirect()->route('document-repository.index')->with('success', __('Document repository entry successfully updated.'));
    }

    public function destroy(DocumentRepository $documentRepository)
    {
        if (!Auth::user()->can('delete document repository') || !$this->owns($documentRepository)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $documentRepository->delete();

        return redirect()->route('document-repository.index')->with('success', __('Document repository entry successfully deleted.'));
    }

    protected function owns(DocumentRepository $documentRepository)
    {
        return (int) $documentRepository->created_by === (int) Auth::user()->creatorId();
    }

    protected function canAccess(DocumentRepository $documentRepository)
    {
        return $this->owns($documentRepository) && (Auth::user()->can('manage document repository') || Auth::user()->can('show document repository'));
    }
}
