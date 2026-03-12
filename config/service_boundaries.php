<?php

$serviceModuleDirectory = base_path('bootstrap/cache/service-modules');
$serviceMigrationDirectory = base_path('database/migrations/services');

return [
    'module_status_directory' => $serviceModuleDirectory,
    'sql_init_path' => base_path('docker/mysql/init/01-service-databases.sql'),

    'services' => [
        'core' => [
            'module_status_file' => $serviceModuleDirectory.'/core.json',
            'modules' => [
                'LandingPage',
                'Crm',
                'Hrm',
                'Projects',
                'Pos',
                'Inventory',
                'Accounting',
                'Sales',
                'Wms',
                'Production',
            ],
        ],
        'billing' => [
            'module_status_file' => $serviceModuleDirectory.'/billing.json',
            'modules' => ['Billing'],
        ],
        'approvals' => [
            'module_status_file' => $serviceModuleDirectory.'/approvals.json',
            'modules' => ['Approvals'],
        ],
        'mrp' => [
            'module_status_file' => $serviceModuleDirectory.'/mrp.json',
            'modules' => ['Mrp'],
        ],
        'quality' => [
            'module_status_file' => $serviceModuleDirectory.'/quality.json',
            'modules' => ['Quality'],
        ],
        'maintenance' => [
            'module_status_file' => $serviceModuleDirectory.'/maintenance.json',
            'modules' => ['Maintenance'],
        ],
        'chatgpt' => [
            'module_status_file' => $serviceModuleDirectory.'/chatgpt.json',
            'modules' => ['ChatGpt'],
        ],
        'hotel' => [
            'module_status_file' => $serviceModuleDirectory.'/hotel.json',
            'modules' => ['Hotel'],
        ],
        'traceability' => [
            'module_status_file' => $serviceModuleDirectory.'/traceability.json',
            'modules' => ['Traceability'],
        ],
        'cropplanning' => [
            'module_status_file' => $serviceModuleDirectory.'/cropplanning.json',
            'modules' => ['CropPlanning'],
        ],
        'cooperative' => [
            'module_status_file' => $serviceModuleDirectory.'/cooperative.json',
            'modules' => ['Cooperative'],
        ],
        'hedging' => [
            'module_status_file' => $serviceModuleDirectory.'/hedging.json',
            'modules' => ['Hedging'],
        ],
        'hrops' => [
            'module_status_file' => $serviceModuleDirectory.'/hrops.json',
            'modules' => ['HrOps'],
        ],
        'operations' => [
            'module_status_file' => $serviceModuleDirectory.'/operations.json',
            'modules' => ['Operations'],
        ],
        'platform' => [
            'module_status_file' => $serviceModuleDirectory.'/platform.json',
            'modules' => ['Platform'],
        ],
        'industry' => [
            'module_status_file' => $serviceModuleDirectory.'/industry.json',
            'modules' => ['Industry'],
        ],
        'btp' => [
            'module_status_file' => $serviceModuleDirectory.'/btp.json',
            'modules' => ['Btp'],
        ],
        'integrations' => [
            'module_status_file' => $serviceModuleDirectory.'/integrations.json',
            'modules' => ['Integrations'],
        ],
        'saas' => [
            'module_status_file' => $serviceModuleDirectory.'/saas.json',
            'modules' => ['Saas'],
        ],
    ],

    'databases' => [
        'core' => ['connection' => 'core', 'database' => 'erpgo'],
        'billing' => ['connection' => 'billing', 'database' => 'erpgo_billing'],
        'approvals' => ['connection' => 'approvals', 'database' => 'erpgo_approvals'],
        'mrp' => ['connection' => 'mrp', 'database' => 'erpgo_mrp'],
        'quality' => ['connection' => 'quality', 'database' => 'erpgo_quality'],
        'maintenance' => ['connection' => 'maintenance', 'database' => 'erpgo_maintenance'],
        'chatgpt' => ['connection' => 'chatgpt', 'database' => 'erpgo_chatgpt'],
        'hotel' => ['connection' => 'hotel', 'database' => 'erpgo_hotel'],
        'traceability' => ['connection' => 'traceability', 'database' => 'erpgo_traceability'],
        'cropplanning' => ['connection' => 'crop_planning', 'database' => 'erpgo_cropplanning'],
        'cooperative' => ['connection' => 'cooperative', 'database' => 'erpgo_cooperative'],
        'hedging' => ['connection' => 'hedging', 'database' => 'erpgo_hedging'],
        'hrops' => ['connection' => 'hr_ops', 'database' => 'erpgo_hrops'],
        'operations' => ['connection' => 'operations', 'database' => 'erpgo_operations'],
        'platform' => ['connection' => 'platform', 'database' => 'erpgo_platform'],
        'industry' => ['connection' => 'industry', 'database' => 'erpgo_industry'],
        'btp' => ['connection' => 'btp', 'database' => 'erpgo_btp'],
        'integrations' => ['connection' => 'integrations', 'database' => 'erpgo_integrations'],
        'saas' => ['connection' => 'saas', 'database' => 'erpgo_saas'],
        'production' => ['connection' => 'production', 'database' => 'erpgo_production'],
    ],

    'migration_profiles' => [
        'core' => [
            [
                'connection' => 'core',
                'paths' => [
                    base_path('database/migrations'),
                    base_path('Modules/LandingPage/Database/Migrations'),
                ],
            ],
        ],
        'approvals' => [
            [
                'connection' => 'approvals',
                'paths' => [$serviceMigrationDirectory.'/approvals'],
            ],
        ],
        'billing' => [
            [
                'connection' => 'billing',
                'paths' => [$serviceMigrationDirectory.'/billing'],
            ],
        ],
        'mrp' => [
            [
                'connection' => 'mrp',
                'paths' => [$serviceMigrationDirectory.'/mrp'],
            ],
        ],
        'quality' => [
            [
                'connection' => 'quality',
                'paths' => [$serviceMigrationDirectory.'/quality'],
            ],
        ],
        'maintenance' => [
            [
                'connection' => 'maintenance',
                'paths' => [$serviceMigrationDirectory.'/maintenance'],
            ],
        ],
        'chatgpt' => [
            [
                'connection' => 'chatgpt',
                'paths' => [$serviceMigrationDirectory.'/chatgpt'],
            ],
        ],
        'hotel' => [
            [
                'connection' => 'hotel',
                'paths' => [$serviceMigrationDirectory.'/hotel'],
            ],
        ],
        'traceability' => [
            [
                'connection' => 'traceability',
                'paths' => [$serviceMigrationDirectory.'/traceability'],
            ],
        ],
        'cropplanning' => [
            [
                'connection' => 'crop_planning',
                'paths' => [$serviceMigrationDirectory.'/cropplanning'],
            ],
        ],
        'cooperative' => [
            [
                'connection' => 'cooperative',
                'paths' => [$serviceMigrationDirectory.'/cooperative'],
            ],
        ],
        'hedging' => [
            [
                'connection' => 'hedging',
                'paths' => [$serviceMigrationDirectory.'/hedging'],
            ],
        ],
        'hrops' => [
            [
                'connection' => 'hr_ops',
                'paths' => [$serviceMigrationDirectory.'/hrops'],
            ],
        ],
        'operations' => [
            [
                'connection' => 'operations',
                'paths' => [$serviceMigrationDirectory.'/operations'],
            ],
        ],
        'platform' => [
            [
                'connection' => 'platform',
                'paths' => [$serviceMigrationDirectory.'/platform'],
            ],
        ],
        'industry' => [
            [
                'connection' => 'industry',
                'paths' => [$serviceMigrationDirectory.'/industry'],
            ],
        ],
        'btp' => [
            [
                'connection' => 'btp',
                'paths' => [$serviceMigrationDirectory.'/btp'],
            ],
        ],
        'integrations' => [
            [
                'connection' => 'integrations',
                'paths' => [$serviceMigrationDirectory.'/integrations'],
            ],
        ],
        'saas' => [
            [
                'connection' => 'saas',
                'paths' => [$serviceMigrationDirectory.'/saas'],
            ],
        ],
        'production' => [
            [
                'connection' => 'production',
                'paths' => [$serviceMigrationDirectory.'/production'],
            ],
        ],
    ],

    'migration_sources' => [
        'approvals' => [
            base_path('database/migrations/2026_03_01_000001_create_approval_tables.php'),
        ],
        'mrp' => [
            base_path('database/migrations/2026_02_14_000001_create_production_tables.php'),
        ],
        'quality' => [
            base_path('database/migrations/2026_02_14_000001_create_production_tables.php'),
        ],
        'production' => [
            base_path('database/migrations/2026_02_14_000001_create_production_tables.php'),
        ],
        'chatgpt' => [
            base_path('database/migrations/2023_06_06_043306_create_templates_table.php'),
        ],
        'hotel' => [
            base_path('database/migrations/2026_02_23_000000_create_hotel_management_tables.php'),
        ],
        'traceability' => [
            base_path('database/migrations/2026_02_23_000004_create_agri_modules_tables.php'),
        ],
        'cropplanning' => [
            base_path('database/migrations/2026_02_23_000004_create_agri_modules_tables.php'),
        ],
        'cooperative' => [
            base_path('database/migrations/2026_02_23_000004_create_agri_modules_tables.php'),
        ],
        'hedging' => [
            base_path('database/migrations/2026_02_23_000004_create_agri_modules_tables.php'),
        ],
        'btp' => [
            base_path('database/migrations/2026_02_23_120002_create_btp_tables.php'),
        ],
        'integrations' => [
            base_path('database/migrations/2023_04_24_073041_create_webhook_settings_table.php'),
        ],
    ],
];
