<?php

/**
 * =====================================================================
 * ERPGo SaaS — Seeders Orchestration Configuration
 * =====================================================================
 * Defines all microservices, their databases, dependency order,
 * and seeder classes for automated database population.
 *
 * IMPORTANT: Services are ordered by dependency graph.
 * Core MUST be seeded first as all other services depend on it.
 * =====================================================================
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Execution Settings
    |--------------------------------------------------------------------------
    */
    'timeout_per_service' => env('SEED_TIMEOUT', 300), // seconds
    'retry_attempts'      => env('SEED_RETRIES', 3),
    'retry_delay'         => env('SEED_RETRY_DELAY', 5), // seconds
    'parallel'            => env('SEED_PARALLEL', false),
    'log_channel'         => env('SEED_LOG_CHANNEL', 'seed'),

    /*
    |--------------------------------------------------------------------------
    | Environments Configuration
    |--------------------------------------------------------------------------
    */
    'environments' => [
        'dev' => [
            'reset_allowed' => true,
            'seed_demo_data' => true,
            'confirm_required' => false,
        ],
        'staging' => [
            'reset_allowed' => true,
            'seed_demo_data' => true,
            'confirm_required' => true,
        ],
        'production' => [
            'reset_allowed' => false,
            'seed_demo_data' => false,
            'confirm_required' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Service Dependency Graph & Database Mapping
    |--------------------------------------------------------------------------
    | Order matters: services are seeded in this sequence.
    | Each service defines its DB connection, container, priority,
    | and seeder classes.
    |
    | Priority levels:
    |   0 = Infrastructure (must run first)
    |   1 = Core dependencies
    |   2 = Independent modules
    |   3 = Dependent modules
    |--------------------------------------------------------------------------
    */
    'services' => [

        // ── PRIORITY 0: Core Service (Foundation) ─────────────────────
        'core' => [
            'priority'     => 0,
            'label'        => 'Core ERP',
            'db_connection'=> 'core',
            'db_host'      => env('CORE_DB_HOST', 'core-db'),
            'db_port'      => env('CORE_DB_PORT', 3306),
            'db_database'  => env('CORE_DB_DATABASE', 'core'),
            'db_username'  => env('CORE_DB_USERNAME', 'core'),
            'db_password'  => env('CORE_DB_PASSWORD', 'secret'),
            'container'    => 'core',
            'depends_on'   => [],
            'migrations'   => [
                'database/migrations',
                'Modules/LandingPage/Database/Migrations',
            ],
            'seeders' => [
                \Database\Seeders\NotificationSeeder::class,
                \Database\Seeders\PlansTableSeeder::class,
                \Database\Seeders\UsersTableSeeder::class,
                \Database\Seeders\EducationSeeder::class,
                \Database\Seeders\AiTemplateSeeder::class,
                \Modules\LandingPage\Database\Seeders\LandingPageDatabaseSeeder::class,
            ],
        ],

        // ── PRIORITY 1: First-tier services (depend only on Core) ─────
        'billing' => [
            'priority'     => 1,
            'label'        => 'Billing / Facturation',
            'db_connection'=> 'billing',
            'db_host'      => env('BILLING_DB_HOST', 'billing-db'),
            'db_port'      => env('BILLING_DB_PORT', 3306),
            'db_database'  => env('BILLING_DB_DATABASE', 'billing'),
            'db_username'  => env('BILLING_DB_USERNAME', 'billing'),
            'db_password'  => env('BILLING_DB_PASSWORD', 'secret'),
            'container'    => 'billing',
            'depends_on'   => ['core'],
            'migrations'   => [],
            'seeders'      => [
                \Database\Seeders\Modules\BillingSeeder::class,
            ],
        ],

        'saas' => [
            'priority'     => 1,
            'label'        => 'SaaS Multi-Tenant',
            'db_connection'=> 'saas',
            'db_host'      => env('SAAS_DB_HOST', 'saas-db'),
            'db_port'      => env('SAAS_DB_PORT', 3306),
            'db_database'  => env('SAAS_DB_DATABASE', 'saas'),
            'db_username'  => env('SAAS_DB_USERNAME', 'saas'),
            'db_password'  => env('SAAS_DB_PASSWORD', 'secret'),
            'container'    => 'saas-service',
            'depends_on'   => ['core'],
            'migrations'   => ['Modules/Saas/database/migrations'],
            'seeders'      => [
                \Database\Seeders\Modules\SaasSeeder::class,
            ],
        ],

        'approvals' => [
            'priority'     => 1,
            'label'        => 'Approvals / Approbations',
            'db_connection'=> 'approvals',
            'db_host'      => env('APPROVALS_DB_HOST', 'approvals-db'),
            'db_port'      => env('APPROVALS_DB_PORT', 3306),
            'db_database'  => env('APPROVALS_DB_DATABASE', 'approvals'),
            'db_username'  => env('APPROVALS_DB_USERNAME', 'approvals'),
            'db_password'  => env('APPROVALS_DB_PASSWORD', 'secret'),
            'container'    => 'approvals-service',
            'depends_on'   => ['core'],
            'migrations'   => [],
            'seeders'      => [
                \Database\Seeders\Modules\ApprovalsSeeder::class,
            ],
        ],

        // ── PRIORITY 2: Independent Domain Services ───────────────────
        'hrops' => [
            'priority'     => 2,
            'label'        => 'HR Operations',
            'db_connection'=> 'hrops',
            'db_host'      => env('HROPS_DB_HOST', 'hrops-db'),
            'db_port'      => env('HROPS_DB_PORT', 3306),
            'db_database'  => env('HROPS_DB_DATABASE', 'hrops'),
            'db_username'  => env('HROPS_DB_USERNAME', 'hrops'),
            'db_password'  => env('HROPS_DB_PASSWORD', 'secret'),
            'container'    => 'hrops-service',
            'depends_on'   => ['core'],
            'migrations'   => [],
            'seeders'      => [
                \Database\Seeders\Modules\HrOpsSeeder::class,
            ],
        ],

        'operations' => [
            'priority'     => 2,
            'label'        => 'Operations',
            'db_connection'=> 'operations',
            'db_host'      => env('OPERATIONS_DB_HOST', 'operations-db'),
            'db_port'      => env('OPERATIONS_DB_PORT', 3306),
            'db_database'  => env('OPERATIONS_DB_DATABASE', 'operations'),
            'db_username'  => env('OPERATIONS_DB_USERNAME', 'operations'),
            'db_password'  => env('OPERATIONS_DB_PASSWORD', 'secret'),
            'container'    => 'operations-service',
            'depends_on'   => ['core'],
            'migrations'   => [],
            'seeders'      => [
                \Database\Seeders\Modules\OperationsSeeder::class,
            ],
        ],

        'platform' => [
            'priority'     => 2,
            'label'        => 'Platform',
            'db_connection'=> 'platform',
            'db_host'      => env('PLATFORM_DB_HOST', 'platform-db'),
            'db_port'      => env('PLATFORM_DB_PORT', 3306),
            'db_database'  => env('PLATFORM_DB_DATABASE', 'platform'),
            'db_username'  => env('PLATFORM_DB_USERNAME', 'platform'),
            'db_password'  => env('PLATFORM_DB_PASSWORD', 'secret'),
            'container'    => 'platform-service',
            'depends_on'   => ['core'],
            'migrations'   => [],
            'seeders'      => [
                \Database\Seeders\Modules\PlatformSeeder::class,
            ],
        ],

        'mrp' => [
            'priority'     => 2,
            'label'        => 'MRP / Planification Ressources',
            'db_connection'=> 'mrp',
            'db_host'      => env('MRP_DB_HOST', 'mrp-db'),
            'db_port'      => env('MRP_DB_PORT', 3306),
            'db_database'  => env('MRP_DB_DATABASE', 'mrp'),
            'db_username'  => env('MRP_DB_USERNAME', 'mrp'),
            'db_password'  => env('MRP_DB_PASSWORD', 'secret'),
            'container'    => 'mrp-service',
            'depends_on'   => ['core'],
            'migrations'   => ['Modules/Mrp/database/migrations'],
            'seeders'      => [
                \Database\Seeders\Modules\MrpSeeder::class,
            ],
        ],

        'quality' => [
            'priority'     => 2,
            'label'        => 'Quality / Qualité',
            'db_connection'=> 'quality',
            'db_host'      => env('QUALITY_DB_HOST', 'quality-db'),
            'db_port'      => env('QUALITY_DB_PORT', 3306),
            'db_database'  => env('QUALITY_DB_DATABASE', 'quality'),
            'db_username'  => env('QUALITY_DB_USERNAME', 'quality'),
            'db_password'  => env('QUALITY_DB_PASSWORD', 'secret'),
            'container'    => 'quality-service',
            'depends_on'   => ['core'],
            'migrations'   => ['Modules/Quality/database/migrations'],
            'seeders'      => [
                \Database\Seeders\Modules\QualitySeeder::class,
            ],
        ],

        'maintenance' => [
            'priority'     => 2,
            'label'        => 'Maintenance',
            'db_connection'=> 'maintenance',
            'db_host'      => env('MAINTENANCE_DB_HOST', 'maintenance-db'),
            'db_port'      => env('MAINTENANCE_DB_PORT', 3306),
            'db_database'  => env('MAINTENANCE_DB_DATABASE', 'maintenance'),
            'db_username'  => env('MAINTENANCE_DB_USERNAME', 'maintenance'),
            'db_password'  => env('MAINTENANCE_DB_PASSWORD', 'secret'),
            'container'    => 'maintenance-service',
            'depends_on'   => ['core'],
            'migrations'   => ['Modules/Maintenance/database/migrations'],
            'seeders'      => [
                \Database\Seeders\Modules\MaintenanceSeeder::class,
            ],
        ],

        'chatgpt' => [
            'priority'     => 2,
            'label'        => 'ChatGPT / IA',
            'db_connection'=> 'chatgpt',
            'db_host'      => env('CHATGPT_DB_HOST', 'chatgpt-db'),
            'db_port'      => env('CHATGPT_DB_PORT', 3306),
            'db_database'  => env('CHATGPT_DB_DATABASE', 'chatgpt'),
            'db_username'  => env('CHATGPT_DB_USERNAME', 'chatgpt'),
            'db_password'  => env('CHATGPT_DB_PASSWORD', 'secret'),
            'container'    => 'chatgpt-service',
            'depends_on'   => ['core'],
            'migrations'   => ['Modules/ChatGpt/database/migrations'],
            'seeders'      => [
                \Database\Seeders\Modules\ChatGptSeeder::class,
            ],
        ],

        'integrations' => [
            'priority'     => 2,
            'label'        => 'Integrations',
            'db_connection'=> 'integrations',
            'db_host'      => env('INTEGRATIONS_DB_HOST', 'integrations-db'),
            'db_port'      => env('INTEGRATIONS_DB_PORT', 3306),
            'db_database'  => env('INTEGRATIONS_DB_DATABASE', 'integrations'),
            'db_username'  => env('INTEGRATIONS_DB_USERNAME', 'integrations'),
            'db_password'  => env('INTEGRATIONS_DB_PASSWORD', 'secret'),
            'container'    => 'integrations-service',
            'depends_on'   => ['core'],
            'migrations'   => ['Modules/Integrations/database/migrations'],
            'seeders'      => [
                \Database\Seeders\Modules\IntegrationsSeeder::class,
            ],
        ],

        'industry' => [
            'priority'     => 2,
            'label'        => 'Industry / Industrie',
            'db_connection'=> 'industry',
            'db_host'      => env('INDUSTRY_DB_HOST', 'industry-db'),
            'db_port'      => env('INDUSTRY_DB_PORT', 3306),
            'db_database'  => env('INDUSTRY_DB_DATABASE', 'industry'),
            'db_username'  => env('INDUSTRY_DB_USERNAME', 'industry'),
            'db_password'  => env('INDUSTRY_DB_PASSWORD', 'secret'),
            'container'    => 'industry-service',
            'depends_on'   => ['core'],
            'migrations'   => [],
            'seeders'      => [
                \Database\Seeders\Modules\IndustrySeeder::class,
            ],
        ],

        // ── PRIORITY 2: Domain-specific Services ──────────────────────
        'hotel' => [
            'priority'     => 2,
            'label'        => 'Hotel / Hôtellerie',
            'db_connection'=> 'hotel',
            'db_host'      => env('HOTEL_DB_HOST', 'hotel-db'),
            'db_port'      => env('HOTEL_DB_PORT', 3306),
            'db_database'  => env('HOTEL_DB_DATABASE', 'hotel'),
            'db_username'  => env('HOTEL_DB_USERNAME', 'hotel'),
            'db_password'  => env('HOTEL_DB_PASSWORD', 'secret'),
            'container'    => 'hotel-service',
            'depends_on'   => ['core', 'billing'],
            'migrations'   => ['Modules/Hotel/database/migrations'],
            'seeders'      => [
                \Database\Seeders\Modules\HotelSeeder::class,
            ],
        ],

        'btp' => [
            'priority'     => 2,
            'label'        => 'BTP / Construction',
            'db_connection'=> 'btp',
            'db_host'      => env('BTP_DB_HOST', 'btp-db'),
            'db_port'      => env('BTP_DB_PORT', 3306),
            'db_database'  => env('BTP_DB_DATABASE', 'btp'),
            'db_username'  => env('BTP_DB_USERNAME', 'btp'),
            'db_password'  => env('BTP_DB_PASSWORD', 'secret'),
            'container'    => 'btp-service',
            'depends_on'   => ['core'],
            'migrations'   => [],
            'seeders'      => [
                \Database\Seeders\Modules\BtpSeeder::class,
            ],
        ],

        // ── PRIORITY 3: Agriculture / Specialized Services ────────────
        'traceability' => [
            'priority'     => 3,
            'label'        => 'Traceability / Traçabilité',
            'db_connection'=> 'traceability',
            'db_host'      => env('TRACEABILITY_DB_HOST', 'traceability-db'),
            'db_port'      => env('TRACEABILITY_DB_PORT', 3306),
            'db_database'  => env('TRACEABILITY_DB_DATABASE', 'traceability'),
            'db_username'  => env('TRACEABILITY_DB_USERNAME', 'traceability'),
            'db_password'  => env('TRACEABILITY_DB_PASSWORD', 'secret'),
            'container'    => 'traceability-service',
            'depends_on'   => ['core'],
            'migrations'   => ['Modules/Traceability/database/migrations'],
            'seeders'      => [
                \Database\Seeders\Modules\TraceabilitySeeder::class,
            ],
        ],

        'cropplanning' => [
            'priority'     => 3,
            'label'        => 'CropPlanning / Planification Agricole',
            'db_connection'=> 'cropplanning',
            'db_host'      => env('CROPPLANNING_DB_HOST', 'cropplanning-db'),
            'db_port'      => env('CROPPLANNING_DB_PORT', 3306),
            'db_database'  => env('CROPPLANNING_DB_DATABASE', 'cropplanning'),
            'db_username'  => env('CROPPLANNING_DB_USERNAME', 'cropplanning'),
            'db_password'  => env('CROPPLANNING_DB_PASSWORD', 'secret'),
            'container'    => 'cropplanning-service',
            'depends_on'   => ['core', 'traceability'],
            'migrations'   => ['Modules/CropPlanning/database/migrations'],
            'seeders'      => [
                \Database\Seeders\Modules\CropPlanningSeeder::class,
            ],
        ],

        'cooperative' => [
            'priority'     => 3,
            'label'        => 'Cooperative / Coopérative',
            'db_connection'=> 'cooperative',
            'db_host'      => env('COOPERATIVE_DB_HOST', 'cooperative-db'),
            'db_port'      => env('COOPERATIVE_DB_PORT', 3306),
            'db_database'  => env('COOPERATIVE_DB_DATABASE', 'cooperative'),
            'db_username'  => env('COOPERATIVE_DB_USERNAME', 'cooperative'),
            'db_password'  => env('COOPERATIVE_DB_PASSWORD', 'secret'),
            'container'    => 'cooperative-service',
            'depends_on'   => ['core', 'traceability'],
            'migrations'   => ['Modules/Cooperative/database/migrations'],
            'seeders'      => [
                \Database\Seeders\Modules\CooperativeSeeder::class,
            ],
        ],

        'hedging' => [
            'priority'     => 3,
            'label'        => 'Hedging / Couverture Agricole',
            'db_connection'=> 'hedging',
            'db_host'      => env('HEDGING_DB_HOST', 'hedging-db'),
            'db_port'      => env('HEDGING_DB_PORT', 3306),
            'db_database'  => env('HEDGING_DB_DATABASE', 'hedging'),
            'db_username'  => env('HEDGING_DB_USERNAME', 'hedging'),
            'db_password'  => env('HEDGING_DB_PASSWORD', 'secret'),
            'container'    => 'hedging-service',
            'depends_on'   => ['core', 'cooperative'],
            'migrations'   => ['Modules/Hedging/database/migrations'],
            'seeders'      => [
                \Database\Seeders\Modules\HedgingSeeder::class,
            ],
        ],
    ],
];
