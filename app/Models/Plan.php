<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'price',
        'duration',
        'max_users',
        'max_customers',
        'max_venders',
        'max_clients',
        'trial',
        'trial_days',
        'is_disable',
        'description',
        'image',
        'crm',
        'hrm',
        'account',
        'project',
        'pos',
        'production',
        'integrations',
        'sales',
        'wms',
        'mrp',
        'quality',
        'maintenance',
        'enterprise_accounting',
        'approvals',
        'hr_ops',
        'saas',
        'chatgpt',
        'hotel',
        'traceability',
        'crop_planning',
        'cooperative',
        'hedging',
        'agri_operations',
        'btp_site_tracking',
        'btp_subcontractors',
        'btp_price_breakdowns',
        'btp_equipment_control',
        'board_meeting',
        'cap_table',
        'subsidiary',
        'customer_recovery',
        'visitor',
        'innovation_idea',
        'knowledge_base',
        'document_repository',
        'medical_service',
        'medical_invoice',
        'pharmacy_medication',
        'pharmacy_dispensation',
        'hospital_room',
        'hospital_bed',
        'hospital_admission',
        'medical_operations',
        'delivery_note',
        'retail_operations',
        'storage_limit',
    ];

    private static $getplans = NULL;

    public static $arrDuration = [
        'lifetime' => 'Lifetime',
        'month' => 'Per Month',
        'monthly' => 'Per Month',
        'Monthly' => 'Per Month',
        'year' => 'Per Year',
        'yearly' => 'Per Year',
        'Yearly' => 'Per Year',
    ];

    public function status()
    {
        return [
            __('lifetime'),
            __('Per Month'),
            __('Per Year'),
        ];
    }

    public static function total_plan()
    {
        return Plan::count();
    }

    public static function most_purchese_plan()
    {
        $free_plan = Plan::where('price', '<=', 0)->value('id');

        $query = User::select(DB::raw('count(*) as total'), 'plan')
            ->where('type', '=', 'company')
            ->whereNotNull('plan');

        if (!empty($free_plan)) {
            $query->where('plan', '!=', $free_plan);
        }

        $plan = $query->groupBy('plan')->orderByDesc('total')->first();

        return $plan;
    }

    public static function getPlan($id)
    {
        if(self::$getplans == null)
        {
            $plan = Plan::find($id);
            self::$getplans = $plan;
        }

        return self::$getplans;
    }
}
