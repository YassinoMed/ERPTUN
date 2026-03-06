<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for database operations. This is
    | the connection which will be utilized unless another connection
    | is explicitly specified when you execute a query / statement.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Below are all of the database connections defined for your application.
    | An example configuration is provided for each database system which
    | is supported by Laravel. You're free to add / remove connections.
    |
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

        'mysql' => [
            'driver' => 'mysql',
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
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        'projects' => [
            'driver' => env('PROJECTS_DB_DRIVER', 'mysql'),
            'url' => env('PROJECTS_DB_URL'),
            'host' => env('PROJECTS_DB_HOST', '127.0.0.1'),
            'port' => env('PROJECTS_DB_PORT', '3306'),
            'database' => env('PROJECTS_DB_DATABASE', 'projects'),
            'username' => env('PROJECTS_DB_USERNAME', 'root'),
            'password' => env('PROJECTS_DB_PASSWORD', ''),
            'unix_socket' => env('PROJECTS_DB_SOCKET', ''),
            'charset' => env('PROJECTS_DB_CHARSET', 'utf8mb4'),
            'collation' => env('PROJECTS_DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('PROJECTS_MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        'pos' => [
            'driver' => env('POS_DB_DRIVER', 'mysql'),
            'url' => env('POS_DB_URL'),
            'host' => env('POS_DB_HOST', '127.0.0.1'),
            'port' => env('POS_DB_PORT', '3306'),
            'database' => env('POS_DB_DATABASE', 'pos'),
            'username' => env('POS_DB_USERNAME', 'root'),
            'password' => env('POS_DB_PASSWORD', ''),
            'unix_socket' => env('POS_DB_SOCKET', ''),
            'charset' => env('POS_DB_CHARSET', 'utf8mb4'),
            'collation' => env('POS_DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('POS_MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        'inventory' => [
            'driver' => env('INVENTORY_DB_DRIVER', 'mysql'),
            'url' => env('INVENTORY_DB_URL'),
            'host' => env('INVENTORY_DB_HOST', '127.0.0.1'),
            'port' => env('INVENTORY_DB_PORT', '3306'),
            'database' => env('INVENTORY_DB_DATABASE', 'inventory'),
            'username' => env('INVENTORY_DB_USERNAME', 'root'),
            'password' => env('INVENTORY_DB_PASSWORD', ''),
            'unix_socket' => env('INVENTORY_DB_SOCKET', ''),
            'charset' => env('INVENTORY_DB_CHARSET', 'utf8mb4'),
            'collation' => env('INVENTORY_DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('INVENTORY_MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        'accounting' => [
            'driver' => env('ACCOUNTING_DB_DRIVER', 'mysql'),
            'url' => env('ACCOUNTING_DB_URL'),
            'host' => env('ACCOUNTING_DB_HOST', '127.0.0.1'),
            'port' => env('ACCOUNTING_DB_PORT', '3306'),
            'database' => env('ACCOUNTING_DB_DATABASE', 'accounting'),
            'username' => env('ACCOUNTING_DB_USERNAME', 'root'),
            'password' => env('ACCOUNTING_DB_PASSWORD', ''),
            'unix_socket' => env('ACCOUNTING_DB_SOCKET', ''),
            'charset' => env('ACCOUNTING_DB_CHARSET', 'utf8mb4'),
            'collation' => env('ACCOUNTING_DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('ACCOUNTING_MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        'sales' => [
            'driver' => env('SALES_DB_DRIVER', 'mysql'),
            'url' => env('SALES_DB_URL'),
            'host' => env('SALES_DB_HOST', '127.0.0.1'),
            'port' => env('SALES_DB_PORT', '3306'),
            'database' => env('SALES_DB_DATABASE', 'sales'),
            'username' => env('SALES_DB_USERNAME', 'root'),
            'password' => env('SALES_DB_PASSWORD', ''),
            'unix_socket' => env('SALES_DB_SOCKET', ''),
            'charset' => env('SALES_DB_CHARSET', 'utf8mb4'),
            'collation' => env('SALES_DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('SALES_MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        'wms' => [
            'driver' => env('WMS_DB_DRIVER', 'mysql'),
            'url' => env('WMS_DB_URL'),
            'host' => env('WMS_DB_HOST', '127.0.0.1'),
            'port' => env('WMS_DB_PORT', '3306'),
            'database' => env('WMS_DB_DATABASE', 'wms'),
            'username' => env('WMS_DB_USERNAME', 'root'),
            'password' => env('WMS_DB_PASSWORD', ''),
            'unix_socket' => env('WMS_DB_SOCKET', ''),
            'charset' => env('WMS_DB_CHARSET', 'utf8mb4'),
            'collation' => env('WMS_DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('WMS_MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        'production' => [
            'driver' => env('PRODUCTION_DB_DRIVER', 'mysql'),
            'url' => env('PRODUCTION_DB_URL'),
            'host' => env('PRODUCTION_DB_HOST', '127.0.0.1'),
            'port' => env('PRODUCTION_DB_PORT', '3306'),
            'database' => env('PRODUCTION_DB_DATABASE', 'production'),
            'username' => env('PRODUCTION_DB_USERNAME', 'root'),
            'password' => env('PRODUCTION_DB_PASSWORD', ''),
            'unix_socket' => env('PRODUCTION_DB_SOCKET', ''),
            'charset' => env('PRODUCTION_DB_CHARSET', 'utf8mb4'),
            'collation' => env('PRODUCTION_DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('PRODUCTION_MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        'approvals' => [
            'driver' => env('APPROVALS_DB_DRIVER', 'mysql'),
            'url' => env('APPROVALS_DB_URL'),
            'host' => env('APPROVALS_DB_HOST', '127.0.0.1'),
            'port' => env('APPROVALS_DB_PORT', '3306'),
            'database' => env('APPROVALS_DB_DATABASE', 'approvals'),
            'username' => env('APPROVALS_DB_USERNAME', 'root'),
            'password' => env('APPROVALS_DB_PASSWORD', ''),
            'unix_socket' => env('APPROVALS_DB_SOCKET', ''),
            'charset' => env('APPROVALS_DB_CHARSET', 'utf8mb4'),
            'collation' => env('APPROVALS_DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('APPROVALS_MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        'hr_ops' => [
            'driver' => env('HR_OPS_DB_DRIVER', 'mysql'),
            'url' => env('HR_OPS_DB_URL'),
            'host' => env('HR_OPS_DB_HOST', '127.0.0.1'),
            'port' => env('HR_OPS_DB_PORT', '3306'),
            'database' => env('HR_OPS_DB_DATABASE', 'hr_ops'),
            'username' => env('HR_OPS_DB_USERNAME', 'root'),
            'password' => env('HR_OPS_DB_PASSWORD', ''),
            'unix_socket' => env('HR_OPS_DB_SOCKET', ''),
            'charset' => env('HR_OPS_DB_CHARSET', 'utf8mb4'),
            'collation' => env('HR_OPS_DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('HR_OPS_MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        'operations' => [
            'driver' => env('OPERATIONS_DB_DRIVER', 'mysql'),
            'url' => env('OPERATIONS_DB_URL'),
            'host' => env('OPERATIONS_DB_HOST', '127.0.0.1'),
            'port' => env('OPERATIONS_DB_PORT', '3306'),
            'database' => env('OPERATIONS_DB_DATABASE', 'operations'),
            'username' => env('OPERATIONS_DB_USERNAME', 'root'),
            'password' => env('OPERATIONS_DB_PASSWORD', ''),
            'unix_socket' => env('OPERATIONS_DB_SOCKET', ''),
            'charset' => env('OPERATIONS_DB_CHARSET', 'utf8mb4'),
            'collation' => env('OPERATIONS_DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('OPERATIONS_MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        'platform' => [
            'driver' => env('PLATFORM_DB_DRIVER', 'mysql'),
            'url' => env('PLATFORM_DB_URL'),
            'host' => env('PLATFORM_DB_HOST', '127.0.0.1'),
            'port' => env('PLATFORM_DB_PORT', '3306'),
            'database' => env('PLATFORM_DB_DATABASE', 'platform'),
            'username' => env('PLATFORM_DB_USERNAME', 'root'),
            'password' => env('PLATFORM_DB_PASSWORD', ''),
            'unix_socket' => env('PLATFORM_DB_SOCKET', ''),
            'charset' => env('PLATFORM_DB_CHARSET', 'utf8mb4'),
            'collation' => env('PLATFORM_DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('PLATFORM_MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        'industry' => [
            'driver' => env('INDUSTRY_DB_DRIVER', 'mysql'),
            'url' => env('INDUSTRY_DB_URL'),
            'host' => env('INDUSTRY_DB_HOST', '127.0.0.1'),
            'port' => env('INDUSTRY_DB_PORT', '3306'),
            'database' => env('INDUSTRY_DB_DATABASE', 'industry'),
            'username' => env('INDUSTRY_DB_USERNAME', 'root'),
            'password' => env('INDUSTRY_DB_PASSWORD', ''),
            'unix_socket' => env('INDUSTRY_DB_SOCKET', ''),
            'charset' => env('INDUSTRY_DB_CHARSET', 'utf8mb4'),
            'collation' => env('INDUSTRY_DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('INDUSTRY_MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
        'btp' => [
            'driver' => env('BTP_DB_DRIVER', 'mysql'),
            'url' => env('BTP_DB_URL'),
            'host' => env('BTP_DB_HOST', '127.0.0.1'),
            'port' => env('BTP_DB_PORT', '3306'),
            'database' => env('BTP_DB_DATABASE', 'btp'),
            'username' => env('BTP_DB_USERNAME', 'root'),
            'password' => env('BTP_DB_PASSWORD', ''),
            'unix_socket' => env('BTP_DB_SOCKET', ''),
            'charset' => env('BTP_DB_CHARSET', 'utf8mb4'),
            'collation' => env('BTP_DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('BTP_MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

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
            // 'encrypt' => env('DB_ENCRYPT', 'yes'),
            // 'trust_server_certificate' => env('DB_TRUST_SERVER_CERTIFICATE', 'false'),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run on the database.
    |
    */

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as Memcached. You may define your connection settings here.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_') . '_database_'),
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
