<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeBaseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KnowledgeBaseCategoryController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('manage knowledge base category')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $categories = KnowledgeBaseCategory::where('created_by', Auth::user()->creatorId())
            ->withCount('articles')
            ->latest('id')
            ->get();

        return view('knowledge_base_categories.index', compact('categories'));
    }

    public function create()
    {
        if (!Auth::user()->can('create knowledge base category')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        return view('knowledge_base_categories.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('create knowledge base category')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'name' => 'required|max:255',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        KnowledgeBaseCategory::create([
            'name' => $request->name,
            'is_active' => $request->has('is_active'),
            'created_by' => Auth::user()->creatorId(),
        ]);

        return redirect()->route('kb-categories.index')->with('success', __('Knowledge base category successfully created.'));
    }

    public function edit(KnowledgeBaseCategory $kbCategory)
    {
        if (!Auth::user()->can('edit knowledge base category') || !$this->owns($kbCategory)) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        return view('knowledge_base_categories.edit', compact('kbCategory'));
    }

    public function update(Request $request, KnowledgeBaseCategory $kbCategory)
    {
        if (!Auth::user()->can('edit knowledge base category') || !$this->owns($kbCategory)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'name' => 'required|max:255',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $kbCategory->update([
            'name' => $request->name,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('kb-categories.index')->with('success', __('Knowledge base category successfully updated.'));
    }

    public function destroy(KnowledgeBaseCategory $kbCategory)
    {
        if (!Auth::user()->can('delete knowledge base category') || !$this->owns($kbCategory)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $kbCategory->delete();

        return redirect()->route('kb-categories.index')->with('success', __('Knowledge base category successfully deleted.'));
    }

    protected function owns(KnowledgeBaseCategory $kbCategory)
    {
        return (int) $kbCategory->created_by === (int) Auth::user()->creatorId();
    }
}
