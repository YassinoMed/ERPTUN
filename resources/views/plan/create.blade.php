<style>
    .plan-modal-shell {
        --plan-surface: #f7f5ef;
        --plan-card: #ffffff;
        --plan-border: rgba(20, 30, 45, 0.08);
        --plan-text: #142235;
        --plan-muted: #617185;
        --plan-accent: #0f766e;
    }

    .plan-modal-shell {
        background: linear-gradient(180deg, #fbfaf6 0%, #f5f7fb 100%);
        border-radius: 24px;
        padding: 0.35rem;
    }

    .plan-modal-hero {
        background: linear-gradient(135deg, #132238 0%, #1f4b6e 100%);
        color: #fff;
        border-radius: 22px;
        padding: 1.2rem 1.25rem;
        margin-bottom: 1rem;
    }

    .plan-modal-hero h4 {
        margin: 0;
        font-size: 1.2rem;
        font-weight: 700;
        letter-spacing: -0.03em;
    }

    .plan-modal-hero p {
        margin: 0.4rem 0 0;
        color: rgba(255,255,255,0.78);
        font-size: 0.92rem;
    }

    .plan-section-card {
        background: var(--plan-card);
        border: 1px solid var(--plan-border);
        border-radius: 22px;
        padding: 1rem;
        box-shadow: 0 14px 28px rgba(15, 23, 42, 0.05);
        margin-bottom: 1rem;
    }

    .plan-section-head {
        margin-bottom: 0.9rem;
    }

    .plan-section-head h5 {
        margin: 0;
        color: var(--plan-text);
        font-size: 1rem;
        font-weight: 700;
    }

    .plan-section-head p {
        margin: 0.25rem 0 0;
        color: var(--plan-muted);
        font-size: 0.84rem;
    }

    .plan-modal-shell .form-control,
    .plan-modal-shell .form-select,
    .plan-modal-shell .input-group-text {
        border-radius: 14px;
    }

    .plan-switch-grid .form-group {
        margin-bottom: 0;
    }

    .plan-switch-grid .form-check {
        border: 1px solid var(--plan-border);
        border-radius: 18px;
        padding: 0.9rem 0.95rem 0.9rem 2.8rem;
        background: #fafbfc;
        min-height: 100%;
    }

    .plan-switch-grid .form-check-label {
        color: var(--plan-text);
        font-weight: 600;
    }
</style>
{{ Form::open(array('url' => 'plans', 'enctype' => "multipart/form-data", 'class'=>'needs-validation', 'novalidate')) }}
<div class="modal-body">
    <div class="plan-modal-shell">
    {{-- start for ai module--}}
    @php
        $settings = \App\Models\Utility::settings();
    @endphp
    @if(!empty($settings['chat_gpt_key']))
    <div class="text-end">
        <a href="#" data-size="md" class="btn  btn-primary btn-icon btn-sm" data-ajax-popup-over="true" data-url="{{ route('generate',['plan']) }}"
           data-bs-placement="top" data-title="{{ __('Generate content with AI') }}">
            <i class="fas fa-robot"></i> <span>{{__('Generate with AI')}}</span>
        </a>
    </div>
    @endif
    {{-- end for ai module--}}
    <div class="plan-modal-hero">
        <h4>{{ __('Create Subscription Plan') }}</h4>
        <p>{{ __('Configure pricing, capacity limits and module access from one structured form.') }}</p>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="plan-section-card">
                <div class="plan-section-head">
                    <h5>{{ __('Core Information') }}</h5>
                    <p>{{ __('Commercial identity, billing cycle and main quotas.') }}</p>
                </div>
                <div class="row">
        <div class="form-group col-md-6">
            {{Form::label('name',__('Name'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::text('name',null,array('class'=>'form-control font-style','placeholder'=>__('Enter Plan Name'),'required'=>'required'))}}
        </div>
        <div class="form-group col-md-6">
            {{Form::label('price',__('Price'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::number('price',null,array('class'=>'form-control','placeholder'=>__('Enter Plan Price'),'required'=>'required' ,'step' => '0.01'))}}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('duration', __('Duration'),['class'=>'form-label']) }}<x-required></x-required>
            {!! Form::select('duration', $arrDuration, null,array('class' => 'form-control select','required'=>'required')) !!}
        </div>
        <div class="form-group col-md-6">
            {{Form::label('max_users',__('Maximum Users'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::number('max_users',null,array('class'=>'form-control','required'=>'required' , 'placeholder' => __('Enter Maximum Users')))}}
            <span class="small">{{__('Note: "-1" for Unlimited')}}</span>
        </div>
        <div class="form-group col-md-6">
            {{Form::label('max_customers',__('Maximum Customers'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::number('max_customers',null,array('class'=>'form-control','required'=>'required' , 'placeholder' => __('Enter Maximum Customers')))}}
            <span class="small">{{__('Note: "-1" for Unlimited')}}</span>
        </div>
        <div class="form-group col-md-6">
            {{Form::label('max_venders',__('Maximum Vendors'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::number('max_venders',null,array('class'=>'form-control','required'=>'required' , 'placeholder' => __('Enter Maximum Vendors')))}}
            <span class="small">{{__('Note: "-1" for Unlimited')}}</span>
        </div>
        <div class="form-group col-md-6">
            {{Form::label('max_clients',__('Maximum Clients'),['class'=>'form-label'])}}<x-required></x-required>
            {{Form::number('max_clients',null,array('class'=>'form-control','required'=>'required' , 'placeholder' => __('Enter Maximum Clients')))}}
            <span class="small">{{__('Note: "-1" for Unlimited')}}</span>
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('storage_limit', __('Storage limit'), ['class' => 'form-label']) }}<x-required></x-required>
            <div class="input-group">
                {{ Form::number('storage_limit', null, ['class' => 'form-control','required'=>'required', 'placeholder' => __('Maximum Storage Limit')]) }}
                <div class="input-group-append">
                <span class="input-group-text"
                      id="basic-addon2">{{__('MB')}}</span>
                </div>
            </div>
        </div>

        <div class="form-group col-md-12">
            {{ Form::label('description', __('Description'),['class'=>'form-label']) }}
            {!! Form::textarea('description', null, ['class'=>'form-control','rows'=>'2' , 'placeholder' => __('Enter Description')]) !!}
        </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="plan-section-card">
                <div class="plan-section-head">
                    <h5>{{ __('Commercial Rules') }}</h5>
                    <p>{{ __('Control trial activation and trial duration.') }}</p>
                </div>
                <div class="row align-items-end">
                    <div class="col-md-6">
                        <label class="form-check-label" for="trial"></label>
                        <div class="form-group">
                            <label for="trial" class="form-label">{{ __('Trial is enable(on/off)') }}</label>
                            <div class="form-check form-switch custom-switch-v1 float-end">
                                <input type="checkbox" name="trial" class="form-check-input input-primary pointer" value="1" id="trial">
                                <label class="form-check-label" for="trial"></label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 ">
                        <div class="form-group plan_div d-none">
                            {{ Form::label('trial_days', __('Trial Days'), ['class' => 'form-label']) }}
                            {{ Form::number('trial_days',null, ['class' => 'form-control trial_days','placeholder' => __('Enter Trial days'),'step' => '1','min'=>'1']) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="plan-section-card">
                <div class="plan-section-head">
                    <h5>{{ __('Module Access') }}</h5>
                    <p>{{ __('Select the ERP capabilities included in this offer.') }}</p>
                </div>
                <div class="row g-3 plan-switch-grid">
        <div class="form-group col-md-3 mt-2">
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" name="enable_crm" id="enable_crm" checked>
                <label class="custom-control-label form-label" for="enable_crm">{{__('CRM')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3 mt-2">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_project" id="enable_project" checked>
                <label class="custom-control-label form-label" for="enable_project">{{__('Project')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3 mt-2">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_hrm" id="enable_hrm" checked>
                <label class="custom-control-label form-label" for="enable_hrm">{{__('HRM')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3 mt-2">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_account" id="enable_account" checked>
                <label class="custom-control-label form-label" for="enable_account">{{__('Account')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_pos" id="enable_pos" checked>
                <label class="custom-control-label form-label" for="enable_pos">{{__('POS')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_chatgpt" id="enable_chatgpt" checked>
                <label class="custom-control-label form-label" for="enable_chatgpt">{{__('Chat GPT')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_production" id="enable_production" checked>
                <label class="custom-control-label form-label" for="enable_production">{{__('Production')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_integrations" id="enable_integrations" checked>
                <label class="custom-control-label form-label" for="enable_integrations">{{__('Integrations')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_sales" id="enable_sales" checked>
                <label class="custom-control-label form-label" for="enable_sales">{{__('Sales')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_wms" id="enable_wms" checked>
                <label class="custom-control-label form-label" for="enable_wms">{{__('WMS')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_mrp" id="enable_mrp" checked>
                <label class="custom-control-label form-label" for="enable_mrp">{{__('MRP')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_quality" id="enable_quality" checked>
                <label class="custom-control-label form-label" for="enable_quality">{{__('Quality')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_maintenance" id="enable_maintenance" checked>
                <label class="custom-control-label form-label" for="enable_maintenance">{{__('Maintenance')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_enterprise_accounting" id="enable_enterprise_accounting" checked>
                <label class="custom-control-label form-label" for="enable_enterprise_accounting">{{__('Enterprise Accounting')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_approvals" id="enable_approvals" checked>
                <label class="custom-control-label form-label" for="enable_approvals">{{__('Approvals')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_hr_ops" id="enable_hr_ops" checked>
                <label class="custom-control-label form-label" for="enable_hr_ops">{{__('HR Ops')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_saas" id="enable_saas" checked>
                <label class="custom-control-label form-label" for="enable_saas">{{__('SaaS')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_hotel" id="enable_hotel" checked>
                <label class="custom-control-label form-label" for="enable_hotel">{{__('Hotel')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_traceability" id="enable_traceability" checked>
                <label class="custom-control-label form-label" for="enable_traceability">{{__('Traceability')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_crop_planning" id="enable_crop_planning" checked>
                <label class="custom-control-label form-label" for="enable_crop_planning">{{__('Crop Planning')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_cooperative" id="enable_cooperative" checked>
                <label class="custom-control-label form-label" for="enable_cooperative">{{__('Cooperative')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_hedging" id="enable_hedging" checked>
                <label class="custom-control-label form-label" for="enable_hedging">{{__('Hedging')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_agri_operations" id="enable_agri_operations" checked>
                <label class="custom-control-label form-label" for="enable_agri_operations">{{__('Agri Operations')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_btp_site_tracking" id="enable_btp_site_tracking" checked>
                <label class="custom-control-label form-label" for="enable_btp_site_tracking">{{__('BTP Site Tracking')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_btp_subcontractors" id="enable_btp_subcontractors" checked>
                <label class="custom-control-label form-label" for="enable_btp_subcontractors">{{__('BTP Subcontractors')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_btp_price_breakdowns" id="enable_btp_price_breakdowns" checked>
                <label class="custom-control-label form-label" for="enable_btp_price_breakdowns">{{__('BTP Price Breakdown')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_btp_equipment_control" id="enable_btp_equipment_control" checked>
                <label class="custom-control-label form-label" for="enable_btp_equipment_control">{{__('BTP Equipment Control')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_board_meeting" id="enable_board_meeting" checked>
                <label class="custom-control-label form-label" for="enable_board_meeting">{{__('Board Meetings')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_cap_table" id="enable_cap_table" checked>
                <label class="custom-control-label form-label" for="enable_cap_table">{{__('Cap Table')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_subsidiary" id="enable_subsidiary" checked>
                <label class="custom-control-label form-label" for="enable_subsidiary">{{__('Subsidiaries')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_customer_recovery" id="enable_customer_recovery" checked>
                <label class="custom-control-label form-label" for="enable_customer_recovery">{{__('Customer Recovery')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_visitor" id="enable_visitor" checked>
                <label class="custom-control-label form-label" for="enable_visitor">{{__('Visitors')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_innovation_idea" id="enable_innovation_idea" checked>
                <label class="custom-control-label form-label" for="enable_innovation_idea">{{__('Innovation Ideas')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_knowledge_base" id="enable_knowledge_base" checked>
                <label class="custom-control-label form-label" for="enable_knowledge_base">{{__('Knowledge Base')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_document_repository" id="enable_document_repository" checked>
                <label class="custom-control-label form-label" for="enable_document_repository">{{__('Document Repository')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_medical_service" id="enable_medical_service" checked>
                <label class="custom-control-label form-label" for="enable_medical_service">{{__('Medical Services')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_medical_invoice" id="enable_medical_invoice" checked>
                <label class="custom-control-label form-label" for="enable_medical_invoice">{{__('Medical Billing')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_pharmacy_medication" id="enable_pharmacy_medication" checked>
                <label class="custom-control-label form-label" for="enable_pharmacy_medication">{{__('Pharmacy Stock')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_pharmacy_dispensation" id="enable_pharmacy_dispensation" checked>
                <label class="custom-control-label form-label" for="enable_pharmacy_dispensation">{{__('Pharmacy Dispensing')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_hospital_room" id="enable_hospital_room" checked>
                <label class="custom-control-label form-label" for="enable_hospital_room">{{__('Hospital Rooms')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_hospital_bed" id="enable_hospital_bed" checked>
                <label class="custom-control-label form-label" for="enable_hospital_bed">{{__('Hospital Beds')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_hospital_admission" id="enable_hospital_admission" checked>
                <label class="custom-control-label form-label" for="enable_hospital_admission">{{__('Hospital Admissions')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_medical_operations" id="enable_medical_operations" checked>
                <label class="custom-control-label form-label" for="enable_medical_operations">{{__('Advanced Medical Ops')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_delivery_note" id="enable_delivery_note" checked>
                <label class="custom-control-label form-label" for="enable_delivery_note">{{__('Delivery Notes')}}</label>
            </div>
        </div>
        <div class="form-group col-md-3">
            <div class="form-check form-switch ">
                <input type="checkbox" class="form-check-input" name="enable_retail_operations" id="enable_retail_operations" checked>
                <label class="custom-control-label form-label" for="enable_retail_operations">{{__('Retail Operations')}}</label>
            </div>
        </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<div class="modal-footer">
    <input type="button" value="{{__('Cancel')}}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Create')}}" class="btn  btn-primary">
</div>
    {{ Form::close() }}
