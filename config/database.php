<?php

use Illuminate\Support\Str;

$mysqlConnection = static function (
    string $hostEnv,
    string $portEnv,
    string $databaseEnv,
    string $usernameEnv,
    string $passwordEnv,
    string $defaultHost = '127.0.0.1',
    string $defaultPort = '3306',
    string $defaultDatabase = 'laravel',
    string $defaultUsername = 'root',
    string $defaultPassword = ''
): array {
    return [
        'driver' => 'mysql',
        'url' => env('DB_URL'),
        'host' => env($hostEnv, $defaultHost),
        'port' => env($portEnv, $defaultPort),
        'database' => env($databaseEnv, $defaultDatabase),
        'username' => env($usernameEnv, $defaultUsername),
        'password' => env($passwordEnv, $defaultPassword),
        'unix_socket' => env('DB_SOCKET', ''),
        'charset' => env('DB_CHARSET', 'utf8mb4'),
        'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
        'prefix' => '',
        'prefix_indexes' => true,
        'strict' => false,
        'engine' => null,
        'options' => extension_loaded('pdo_mysql') ? array_filter([
            PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
        ]) : [],
    ];
};

$serviceConnection = static function (string $prefix, string $defaultDatabase) use ($mysqlConnection): array {
    return $mysqlConnection(
        $prefix.'_DB_HOST',
        $prefix.'_DB_PORT',
        $prefix.'_DB_DATABASE',
        $prefix.'_DB_USERNAME',
        $prefix.'_DB_PASSWORD',
        env('DB_HOST', '127.0.0.1'),
        env('DB_PORT', '3306'),
        $defaultDatabase,
        env('DB_USERNAME', 'root'),
        env('DB_PASSWORD', '')
    );
};

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    */
    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    */
    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DB_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
            'busy_timeout' => null,
            'journal_mode' => null,
            'synchronous' => null,
        ],

        'mysql' => $mysqlConnection(
            'DB_HOST',
            'DB_PORT',
            'DB_DATABASE',
            'DB_USERNAME',
            'DB_PASSWORD'
        ),

        'core' => $serviceConnection('CORE', env('DB_DATABASE', 'erpgo')),
        'projects' => $serviceConnection('PROJECTS', 'erpgo_projects'),
        'pos' => $serviceConnection('POS', 'erpgo_pos'),
        'inventory' => $serviceConnection('INVENTORY', 'erpgo_inventory'),
        'accounting' => $serviceConnection('ACCOUNTING', 'erpgo_accounting'),
        'sales' => $serviceConnection('SALES', 'erpgo_sales'),
        'wms' => $serviceConnection('WMS', 'erpgo_wms'),
        'production' => $serviceConnection('PRODUCTION', 'erpgo_production'),
        'billing' => $serviceConnection('BILLING', 'erpgo_billing'),
        'approvals' => $serviceConnection('APPROVALS', 'erpgo_approvals'),
        'hr_ops' => $serviceConnection('HROPS', 'erpgo_hrops'),
        'hrops' => $serviceConnection('HROPS', 'erpgo_hrops'),
        'operations' => $serviceConnection('OPERATIONS', 'erpgo_operations'),
        'platform' => $serviceConnection('PLATFORM', 'erpgo_platform'),
        'industry' => $serviceConnection('INDUSTRY', 'erpgo_industry'),
        'btp' => $serviceConnection('BTP', 'erpgo_btp'),
        'mrp' => $serviceConnection('MRP', 'erpgo_mrp'),
        'quality' => $serviceConnection('QUALITY', 'erpgo_quality'),
        'maintenance' => $serviceConnection('MAINTENANCE', 'erpgo_maintenance'),
        'chatgpt' => $serviceConnection('CHATGPT', 'erpgo_chatgpt'),
        'integrations' => $serviceConnection('INTEGRATIONS', 'erpgo_integrations'),
        'saas' => $serviceConnection('SAAS', 'erpgo_saas'),
        'hotel' => $serviceConnection('HOTEL', 'erpgo_hotel'),
        'traceability' => $serviceConnection('TRACEABILITY', 'erpgo_traceability'),
        'crop_planning' => $serviceConnection('CROP_PLANNING', 'erpgo_cropplanning'),
        'cropplanning' => $serviceConnection('CROP_PLANNING', 'erpgo_cropplanning'),
        'cooperative' => $serviceConnection('COOPERATIVE', 'erpgo_cooperative'),
        'hedging' => $serviceConnection('HEDGING', 'erpgo_hedging'),

        'mariadb' => [
            'driver' => 'mariadb',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    */
    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    */
    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],

    ],

];
