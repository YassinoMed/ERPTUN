<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeBaseArticle;
use App\Models\KnowledgeBaseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class KnowledgeBaseController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('manage knowledge base') && !Auth::user()->can('show knowledge base')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $articles = KnowledgeBaseArticle::where('created_by', Auth::user()->creatorId())
            ->with('category')
            ->latest('id')
            ->get();

        return view('knowledge_base.index', compact('articles'));
    }

    public function create()
    {
        if (!Auth::user()->can('create knowledge base')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $categories = KnowledgeBaseCategory::where('created_by', Auth::user()->creatorId())
            ->where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id');
        $categories->prepend(__('Select Category'), '');
        $statuses = KnowledgeBaseArticle::$statuses;

        return view('knowledge_base.create', compact('categories', 'statuses'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('create knowledge base')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'title' => 'required|max:255',
            'knowledge_base_category_id' => 'nullable|integer|exists:knowledge_base_categories,id',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $slug = $this->makeSlug($request->title);

        KnowledgeBaseArticle::create([
            'knowledge_base_category_id' => $request->knowledge_base_category_id,
            'title' => $request->title,
            'slug' => $slug,
            'summary' => $request->summary,
            'content' => $request->content,
            'status' => $request->status,
            'is_featured' => $request->has('is_featured'),
            'created_by' => Auth::user()->creatorId(),
        ]);

        return redirect()->route('knowledge-base.index')->with('success', __('Knowledge base article successfully created.'));
    }

    public function show(KnowledgeBaseArticle $knowledgeBase)
    {
        if (!$this->canAccess($knowledgeBase)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $knowledgeBase->load('category');

        return view('knowledge_base.show', compact('knowledgeBase'));
    }

    public function edit(KnowledgeBaseArticle $knowledgeBase)
    {
        if (!Auth::user()->can('edit knowledge base') || !$this->owns($knowledgeBase)) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        $categories = KnowledgeBaseCategory::where('created_by', Auth::user()->creatorId())
            ->orderBy('name')
            ->pluck('name', 'id');
        $categories->prepend(__('Select Category'), '');
        $statuses = KnowledgeBaseArticle::$statuses;

        return view('knowledge_base.edit', compact('knowledgeBase', 'categories', 'statuses'));
    }

    public function update(Request $request, KnowledgeBaseArticle $knowledgeBase)
    {
        if (!Auth::user()->can('edit knowledge base') || !$this->owns($knowledgeBase)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'title' => 'required|max:255',
            'knowledge_base_category_id' => 'nullable|integer|exists:knowledge_base_categories,id',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        $knowledgeBase->update([
            'knowledge_base_category_id' => $request->knowledge_base_category_id,
            'title' => $request->title,
            'slug' => $this->makeSlug($request->title, $knowledgeBase->id),
            'summary' => $request->summary,
            'content' => $request->content,
            'status' => $request->status,
            'is_featured' => $request->has('is_featured'),
        ]);

        return redirect()->route('knowledge-base.index')->with('success', __('Knowledge base article successfully updated.'));
    }

    public function destroy(KnowledgeBaseArticle $knowledgeBase)
    {
        if (!Auth::user()->can('delete knowledge base') || !$this->owns($knowledgeBase)) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $knowledgeBase->delete();

        return redirect()->route('knowledge-base.index')->with('success', __('Knowledge base article successfully deleted.'));
    }

    protected function owns(KnowledgeBaseArticle $knowledgeBase)
    {
        return (int) $knowledgeBase->created_by === (int) Auth::user()->creatorId();
    }

    protected function canAccess(KnowledgeBaseArticle $knowledgeBase)
    {
        return $this->owns($knowledgeBase) && (Auth::user()->can('manage knowledge base') || Auth::user()->can('show knowledge base'));
    }

    protected function makeSlug($title, $ignoreId = null)
    {
        $base = Str::slug($title);
        $slug = $base ?: 'article';
        $suffix = 1;

        while (
            KnowledgeBaseArticle::where('created_by', Auth::user()->creatorId())
                ->where('slug', $slug)
                ->when($ignoreId, function ($query) use ($ignoreId) {
                    $query->where('id', '!=', $ignoreId);
                })
                ->exists()
        ) {
            $slug = $base . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }
}
