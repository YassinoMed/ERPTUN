<?php

namespace App\Http\Controllers;

use App\Models\DocumentRepositoryCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentRepositoryCategoryController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('manage document repository category')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $categories = DocumentRepositoryCategory::where('created_by', Auth::user()->creatorId())
            ->withCount('documents')
            ->latest('id')
            ->get();

        return view('document_repository_categories.index', compact('categories'));
    }

    public function create()
    {
        if (!Auth::user()->can('create document repository category')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        return view('document_repository_categories.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('create document repository category')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'name' => 'required|max:255',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        DocumentRepositoryCategory::create([
            'name' => $request->name,
            'is_active' => $request->has('is_active'),
            'created_by' => Auth::user()->creatorId(),
        ]);

        return redirect()->route('document-repository-categories.index')->with('success', __('Document repository category successfully created.'));
    }

    public function edit(DocumentRepositoryCategory $documentRepositoryCategory)
    {
        if (!Auth::user()->can('edit document repository category') || !$this->owns($documentRepositoryCategory)) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        return view('document_repository_categories.edit', compact('documentRepositoryCategory'));
    }

    public function update(Request $request, DocumentRepositoryCategory $documentRepositoryCategory)
    {
        if (!Auth::user()->can('edit document repository category') || !$this->owns($documentRepositoryCategory)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'name' => 'required|max:255',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $documentRepositoryCategory->update([
            'name' => $request->name,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('document-repository-categories.index')->with('success', __('Document repository category successfully updated.'));
    }

    public function destroy(DocumentRepositoryCategory $documentRepositoryCategory)
    {
        if (!Auth::user()->can('delete document repository category') || !$this->owns($documentRepositoryCategory)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $documentRepositoryCategory->delete();

        return redirect()->route('document-repository-categories.index')->with('success', __('Document repository category successfully deleted.'));
    }

    protected function owns(DocumentRepositoryCategory $category)
    {
        return (int) $category->created_by === (int) Auth::user()->creatorId();
    }
}
