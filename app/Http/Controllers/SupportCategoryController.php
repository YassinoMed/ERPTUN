<?php

namespace App\Http\Controllers;

use App\Models\SupportCategory;
use Illuminate\Http\Request;

class SupportCategoryController extends Controller
{
    protected function guardCompanyAccess()
    {
        if (\Auth::user()->type !== 'company') {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        return null;
    }

    public function index()
    {
        if ($response = $this->guardCompanyAccess()) {
            return $response;
        }

        $categories = SupportCategory::where('created_by', \Auth::user()->creatorId())
            ->withCount('supports')
            ->latest()
            ->get();

        return view('support_categories.index', compact('categories'));
    }

    public function create()
    {
        if ($response = $this->guardCompanyAccess()) {
            return $response;
        }

        return view('support_categories.create');
    }

    public function store(Request $request)
    {
        if ($response = $this->guardCompanyAccess()) {
            return $response;
        }

        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'color' => ['nullable', 'regex:/^#(?:[0-9a-fA-F]{3}){1,2}$/'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('support-categories.index')->with('error', $validator->errors()->first());
        }

        SupportCategory::create([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color ?: '#3B82F6',
            'is_active' => $request->has('is_active'),
            'created_by' => \Auth::user()->creatorId(),
        ]);

        return redirect()->route('support-categories.index')->with('success', __('Support category successfully created.'));
    }

    public function edit(SupportCategory $supportCategory)
    {
        if ($response = $this->guardCompanyAccess()) {
            return $response;
        }

        if ((int) $supportCategory->created_by !== (int) \Auth::user()->creatorId()) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        return view('support_categories.edit', compact('supportCategory'));
    }

    public function update(Request $request, SupportCategory $supportCategory)
    {
        if ($response = $this->guardCompanyAccess()) {
            return $response;
        }

        if ((int) $supportCategory->created_by !== (int) \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'color' => ['nullable', 'regex:/^#(?:[0-9a-fA-F]{3}){1,2}$/'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('support-categories.index')->with('error', $validator->errors()->first());
        }

        $supportCategory->name = $request->name;
        $supportCategory->description = $request->description;
        $supportCategory->color = $request->color ?: '#3B82F6';
        $supportCategory->is_active = $request->has('is_active');
        $supportCategory->save();

        return redirect()->route('support-categories.index')->with('success', __('Support category successfully updated.'));
    }

    public function destroy(SupportCategory $supportCategory)
    {
        if ($response = $this->guardCompanyAccess()) {
            return $response;
        }

        if ((int) $supportCategory->created_by !== (int) \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $supportCategory->delete();

        return redirect()->route('support-categories.index')->with('success', __('Support category successfully deleted.'));
    }
}
