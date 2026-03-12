{{ Form::open(['url' => 'document-repository', 'method' => 'post', 'enctype' => 'multipart/form-data', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('title', __('Title'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('title', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Title')]) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('document_repository_category_id', __('Category'), ['class' => 'form-label']) }}
                {{ Form::select('document_repository_category_id', $categories, null, ['class' => 'form-control select']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('reference', __('Reference'), ['class' => 'form-label']) }}
                {{ Form::text('reference', null, ['class' => 'form-control', 'placeholder' => __('Enter Reference')]) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('version', __('Version'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('version', '1.0', ['class' => 'form-control', 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::select('status', $statuses, null, ['class' => 'form-control select', 'required' => 'required']) }}
            </div>
        </div>
        <div class="col-md-6 form-group">
            {{ Form::label('document', __('Document'), ['class' => 'form-label']) }}
            <input type="file" class="form-control" name="document" id="document_repository_file">
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('effective_date', __('Effective Date'), ['class' => 'form-label']) }}
                {{ Form::date('effective_date', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('expires_at', __('Expiry Date'), ['class' => 'form-label']) }}
                {{ Form::date('expires_at', null, ['class' => 'form-control']) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
                {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => 4, 'placeholder' => __('Enter Description')]) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
</div>
{{ Form::close() }}
