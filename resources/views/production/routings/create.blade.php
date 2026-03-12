{{ Form::open(['url' => route('production.routings.store'), 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('product_id', __('Product'), ['class' => 'form-label']) }}
            {{ Form::select('product_id', $products, null, ['class' => 'form-control', 'placeholder' => __('Select Product')]) }}
        </div>
        <div class="form-group col-md-3">
            {{ Form::label('code', __('Code'), ['class' => 'form-label']) }}
            {{ Form::text('code', null, ['class' => 'form-control']) }}
        </div>
        <div class="form-group col-md-3">
            {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}
            {{ Form::select('status', ['active' => __('Active'), 'draft' => __('Draft'), 'archived' => __('Archived')], 'active', ['class' => 'form-control']) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
        </div>
        @for ($i = 0; $i < 5; $i++)
            <div class="col-md-12 border rounded p-3 mb-2">
                <div class="row">
                    <div class="form-group col-md-3">
                        {{ Form::label("steps[$i][sequence]", __('Sequence'), ['class' => 'form-label']) }}
                        {{ Form::number("steps[$i][sequence]", $i + 1, ['class' => 'form-control', 'min' => 1]) }}
                    </div>
                    <div class="form-group col-md-9">
                        {{ Form::label("steps[$i][name]", __('Step Name'), ['class' => 'form-label']) }}
                        {{ Form::text("steps[$i][name]", null, ['class' => 'form-control']) }}
                    </div>
                    <div class="form-group col-md-4">
                        {{ Form::label("steps[$i][work_center_id]", __('Work Center'), ['class' => 'form-label']) }}
                        {{ Form::select("steps[$i][work_center_id]", $workCenters, null, ['class' => 'form-control', 'placeholder' => __('Select Work Center')]) }}
                    </div>
                    <div class="form-group col-md-4">
                        {{ Form::label("steps[$i][industrial_resource_id]", __('Resource'), ['class' => 'form-label']) }}
                        {{ Form::select("steps[$i][industrial_resource_id]", $resources, null, ['class' => 'form-control', 'placeholder' => __('Select Resource')]) }}
                    </div>
                    <div class="form-group col-md-4">
                        {{ Form::label("steps[$i][planned_minutes]", __('Planned Minutes'), ['class' => 'form-label']) }}
                        {{ Form::number("steps[$i][planned_minutes]", 0, ['class' => 'form-control', 'min' => 0]) }}
                    </div>
                    <div class="form-group col-md-4">
                        {{ Form::label("steps[$i][setup_cost]", __('Setup Cost'), ['class' => 'form-label']) }}
                        {{ Form::number("steps[$i][setup_cost]", 0, ['class' => 'form-control', 'step' => '0.01', 'min' => 0]) }}
                    </div>
                    <div class="form-group col-md-4">
                        {{ Form::label("steps[$i][run_cost]", __('Run Cost'), ['class' => 'form-label']) }}
                        {{ Form::number("steps[$i][run_cost]", 0, ['class' => 'form-control', 'step' => '0.01', 'min' => 0]) }}
                    </div>
                    <div class="form-group col-md-4">
                        {{ Form::label("steps[$i][scrap_percent]", __('Scrap %'), ['class' => 'form-label']) }}
                        {{ Form::number("steps[$i][scrap_percent]", 0, ['class' => 'form-control', 'step' => '0.01', 'min' => 0]) }}
                    </div>
                    <div class="form-group col-md-12">
                        {{ Form::label("steps[$i][instructions]", __('Instructions'), ['class' => 'form-label']) }}
                        {{ Form::textarea("steps[$i][instructions]", null, ['class' => 'form-control', 'rows' => 2]) }}
                    </div>
                </div>
            </div>
        @endfor
        <div class="form-group col-md-12">
            {{ Form::label('notes', __('Notes'), ['class' => 'form-label']) }}
            {{ Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 3]) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
</div>
{{ Form::close() }}
