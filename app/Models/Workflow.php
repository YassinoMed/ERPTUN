<?php

namespace App\Models;

use App\Services\WorkflowEngine;
use Illuminate\Database\Eloquent\Model;

class Workflow extends Model
{
    protected $fillable = [
        'created_by',
        'name',
        'description',
        'trigger_model',
        'trigger_conditions',
        'actions',
        'is_active',
    ];

    protected $casts = [
        'trigger_conditions' => 'array',
        'actions' => 'array',
        'is_active' => 'boolean',
    ];

    public function executions()
    {
        return $this->hasMany(WorkflowExecution::class, 'workflow_id');
    }

    public static function getAvailableTriggers()
    {
        return config('advanced_features.workflow.triggers', []);
    }

    public static function getAvailableActions()
    {
        return config('advanced_features.workflow.actions', []);
    }

    public static function getAvailableTemplates()
    {
        return config('advanced_features.workflow.templates', []);
    }

    public function execute($model, array $context = [])
    {
        return app(WorkflowEngine::class)->execute($this, $model, $context);
    }
}
