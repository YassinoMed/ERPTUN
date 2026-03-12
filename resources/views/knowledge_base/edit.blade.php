{{ Form::model($knowledgeBase, ['route' => ['knowledge-base.update', $knowledgeBase->id], 'method' => 'PUT', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-8"><div class="form-group">{{ Form::label('title', __('Title'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::text('title', null, ['class' => 'form-control', 'required' => 'required']) }}</div></div>
        <div class="col-md-4"><div class="form-group">{{ Form::label('knowledge_base_category_id', __('Category'), ['class' => 'form-label']) }}{{ Form::select('knowledge_base_category_id', $categories, $knowledgeBase->knowledge_base_category_id, ['class' => 'form-control select']) }}</div></div>
        <div class="col-md-8"><div class="form-group">{{ Form::label('summary', __('Summary'), ['class' => 'form-label']) }}{{ Form::textarea('summary', null, ['class' => 'form-control', 'rows' => 3]) }}</div></div>
        <div class="col-md-4"><div class="form-group">{{ Form::label('status', __('Status'), ['class' => 'form-label']) }}<x-required></x-required>{{ Form::select('status', $statuses, $knowledgeBase->status, ['class' => 'form-control select', 'required' => 'required']) }}</div></div>
        <div class="col-12"><div class="form-group">{{ Form::label('content', __('Content'), ['class' => 'form-label']) }}{{ Form::textarea('content', null, ['class' => 'form-control', 'rows' => 10]) }}</div></div>
        <div class="col-12">
            <div class="form-check form-switch mt-2">
                <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" {{ $knowledgeBase->is_featured ? 'checked' : '' }}>
                <label class="form-check-label" for="is_featured">{{ __('Featured Article') }}</label>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn btn-primary">
</div>
{{ Form::close() }}
