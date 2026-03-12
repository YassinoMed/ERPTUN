<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'approvals';

    public function up(): void
    {
        Schema::connection($this->connection)->table('approval_flows', function (Blueprint $table) {
            if (! Schema::connection($this->connection)->hasColumn('approval_flows', 'min_amount')) {
                $table->decimal('min_amount', 15, 2)->nullable()->after('resource_type');
            }
            if (! Schema::connection($this->connection)->hasColumn('approval_flows', 'max_amount')) {
                $table->decimal('max_amount', 15, 2)->nullable()->after('min_amount');
            }
            if (! Schema::connection($this->connection)->hasColumn('approval_flows', 'default_sla_hours')) {
                $table->unsignedInteger('default_sla_hours')->nullable()->after('max_amount');
            }
            if (! Schema::connection($this->connection)->hasColumn('approval_flows', 'escalation_user_id')) {
                $table->unsignedBigInteger('escalation_user_id')->nullable()->after('default_sla_hours');
            }
            if (! Schema::connection($this->connection)->hasColumn('approval_flows', 'allow_delegation')) {
                $table->boolean('allow_delegation')->default(false)->after('escalation_user_id');
            }
        });

        Schema::connection($this->connection)->table('approval_steps', function (Blueprint $table) {
            if (! Schema::connection($this->connection)->hasColumn('approval_steps', 'approver_type')) {
                $table->string('approver_type')->nullable()->after('sequence');
            }
            if (! Schema::connection($this->connection)->hasColumn('approval_steps', 'approver_id')) {
                $table->unsignedBigInteger('approver_id')->nullable()->after('approver_type');
            }
            if (! Schema::connection($this->connection)->hasColumn('approval_steps', 'threshold_min')) {
                $table->decimal('threshold_min', 15, 2)->nullable()->after('approver_id');
            }
            if (! Schema::connection($this->connection)->hasColumn('approval_steps', 'threshold_max')) {
                $table->decimal('threshold_max', 15, 2)->nullable()->after('threshold_min');
            }
            if (! Schema::connection($this->connection)->hasColumn('approval_steps', 'sla_hours')) {
                $table->unsignedInteger('sla_hours')->nullable()->after('threshold_max');
            }
            if (! Schema::connection($this->connection)->hasColumn('approval_steps', 'escalation_user_id')) {
                $table->unsignedBigInteger('escalation_user_id')->nullable()->after('sla_hours');
            }
            if (! Schema::connection($this->connection)->hasColumn('approval_steps', 'require_reject_reason')) {
                $table->boolean('require_reject_reason')->default(false)->after('escalation_user_id');
            }
        });

        Schema::connection($this->connection)->table('approval_requests', function (Blueprint $table) {
            if (! Schema::connection($this->connection)->hasColumn('approval_requests', 'current_step_id')) {
                $table->unsignedBigInteger('current_step_id')->nullable()->after('approval_flow_id');
            }
            if (! Schema::connection($this->connection)->hasColumn('approval_requests', 'delegated_to')) {
                $table->unsignedBigInteger('delegated_to')->nullable()->after('requested_by');
            }
            if (! Schema::connection($this->connection)->hasColumn('approval_requests', 'due_at')) {
                $table->timestamp('due_at')->nullable()->after('delegated_to');
            }
            if (! Schema::connection($this->connection)->hasColumn('approval_requests', 'escalated_at')) {
                $table->timestamp('escalated_at')->nullable()->after('due_at');
            }
            if (! Schema::connection($this->connection)->hasColumn('approval_requests', 'context')) {
                $table->json('context')->nullable()->after('escalated_at');
            }
            if (! Schema::connection($this->connection)->hasColumn('approval_requests', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('context');
            }
        });

        if (
            Schema::connection($this->connection)->hasColumn('approval_requests', 'current_step_id')
            && ! collect(Schema::connection($this->connection)->getForeignKeys('approval_requests'))
                ->contains(fn ($foreignKey) => in_array('current_step_id', $foreignKey['columns'] ?? [], true))
        ) {
            Schema::connection($this->connection)->table('approval_requests', function (Blueprint $table) {
                $table->foreign('current_step_id')->references('id')->on('approval_steps')->nullOnDelete();
            });
        }

        Schema::connection($this->connection)->table('approval_actions', function (Blueprint $table) {
            if (! Schema::connection($this->connection)->hasColumn('approval_actions', 'metadata')) {
                $table->json('metadata')->nullable()->after('comment');
            }
        });

        if (! Schema::connection($this->connection)->hasTable('workflow_delegations')) {
            Schema::connection($this->connection)->create('workflow_delegations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedBigInteger('delegate_user_id')->index();
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('created_by')->index();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('workflow_delegations');

        Schema::connection($this->connection)->table('approval_actions', function (Blueprint $table) {
            $table->dropColumn('metadata');
        });

        Schema::connection($this->connection)->table('approval_requests', function (Blueprint $table) {
            $table->dropForeign(['current_step_id']);
            $table->dropColumn([
                'current_step_id',
                'delegated_to',
                'due_at',
                'escalated_at',
                'context',
                'rejection_reason',
            ]);
        });

        Schema::connection($this->connection)->table('approval_steps', function (Blueprint $table) {
            $table->dropColumn([
                'approver_type',
                'approver_id',
                'threshold_min',
                'threshold_max',
                'sla_hours',
                'escalation_user_id',
                'require_reject_reason',
            ]);
        });

        Schema::connection($this->connection)->table('approval_flows', function (Blueprint $table) {
            $table->dropColumn([
                'min_amount',
                'max_amount',
                'default_sla_hours',
                'escalation_user_id',
                'allow_delegation',
            ]);
        });
    }
};
