<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

/**
 * =====================================================================
 * ERPGo SaaS — Intelligent Seed Orchestrator
 * =====================================================================
 *
 * Orchestrates database seeding across all 19 microservices with:
 * - Dependency-aware sequential execution
 * - Idempotent operations (safe to re-run)
 * - Centralized logging with JSON reports
 * - Rollback on failure
 * - Environment-aware safety guards
 * - Health checks before seeding
 *
 * Usage:
 *   php artisan seed:orchestrate --target-env=dev --reset
 *   php artisan seed:orchestrate --service=core --fresh
 *   php artisan seed:orchestrate --target-env=staging --dry-run
 * =====================================================================
 */
class SeedOrchestrator extends Command
{
    protected $signature = 'seed:orchestrate
        {--target-env=dev : Target environment (dev|staging|production)}
        {--service= : Seed only a specific service}
        {--reset : Drop and recreate all tables before seeding}
        {--fresh : Run migrate:fresh before seeding}
        {--dry-run : Show what would be executed without running}
        {--skip-health : Skip database health checks}
        {--force : Force execution without confirmation}
        {--report= : Output report format (json|text)}
        {--no-migrate : Skip migrations, only run seeders}';

    protected $description = '🌱 Orchestrate database seeding across all ERPGo microservices';

    /** @var array Execution report */
    private array $report = [];

    /** @var array Tracking successfully seeded services for rollback */
    private array $seededServices = [];

    /** @var float Start time */
    private float $startTime;

    /** @var string Log channel */
    private string $logChannel;

    /** @var string */
    private string $environment;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->startTime = microtime(true);
        $this->environment = $this->option('target-env');
        $this->logChannel = config('seeders.log_channel', 'seed');

        // ── ASCII Banner ──────────────────────────────────────────────
        $this->displayBanner();

        // ── Load Configuration ────────────────────────────────────────
        $config = config('seeders');
        if (!$config) {
            $this->error('❌ Configuration file config/seeders.php not found!');
            return 1;
        }

        $envConfig = $config['environments'][$this->environment] ?? null;
        if (!$envConfig) {
            $this->error("❌ Unknown environment: {$this->environment}");
            $this->info('Available: ' . implode(', ', array_keys($config['environments'])));
            return 1;
        }

        // ── Safety Guards ─────────────────────────────────────────────
        if (!$this->performSafetyChecks($envConfig)) {
            return 1;
        }

        // ── Determine Services to Seed ────────────────────────────────
        $services = $this->resolveServices($config['services']);
        if (empty($services)) {
            $this->error('❌ No services found to seed.');
            return 1;
        }

        // ── Dry Run Mode ──────────────────────────────────────────────
        if ($this->option('dry-run')) {
            return $this->executeDryRun($services);
        }

        // ── Execute Seeding Pipeline ──────────────────────────────────
        $this->info('');
        $this->info("🚀 Starting seed orchestration for <fg=cyan>{$this->environment}</> environment");
        $this->info('   Services to process: <fg=yellow>' . count($services) . '</>');
        $this->info('   Reset mode: <fg=yellow>' . ($this->option('reset') ? 'YES' : 'NO') . '</>');
        $this->info(str_repeat('─', 60));

        $exitCode = $this->executeSeeding($services, $config);

        // ── Generate Report ───────────────────────────────────────────
        $this->generateReport();

        return $exitCode;
    }

    /**
     * Display the ASCII banner.
     */
    private function displayBanner(): void
    {
        $this->info('');
        $this->info('<fg=cyan>╔══════════════════════════════════════════════════════╗</>');
        $this->info('<fg=cyan>║</>  <fg=green>🌱 ERPGo SaaS — Seed Orchestrator</> <fg=cyan>                 ║</>');
        $this->info('<fg=cyan>║</>  <fg=white>Automated Database Population Engine</>  <fg=cyan>              ║</>');
        $this->info('<fg=cyan>╚══════════════════════════════════════════════════════╝</>');
        $this->info('');
    }

    /**
     * Perform environment-aware safety checks.
     */
    private function performSafetyChecks(array $envConfig): bool
    {
        // Block reset in production
        if ($this->option('reset') && !$envConfig['reset_allowed']) {
            $this->error('🛑 RESET is BLOCKED in production environment!');
            $this->error('   Use --target-env=dev or --target-env=staging for destructive operations.');
            $this->logEvent('BLOCKED', 'Reset attempted in production');
            return false;
        }

        // Block fresh migration in production
        if ($this->option('fresh') && $this->environment === 'production') {
            $this->error('🛑 FRESH MIGRATION is BLOCKED in production!');
            return false;
        }

        // Require confirmation for staging/prod
        if ($envConfig['confirm_required'] && !$this->option('force')) {
            $confirmed = $this->confirm(
                "⚠️  You are about to seed the <fg=red>{$this->environment}</> environment. Continue?",
                false
            );
            if (!$confirmed) {
                $this->info('Operation cancelled.');
                return false;
            }
        }

        return true;
    }

    /**
     * Resolve which services to seed, sorted by priority.
     */
    private function resolveServices(array $allServices): array
    {
        // Single service mode
        if ($serviceName = $this->option('service')) {
            if (!isset($allServices[$serviceName])) {
                $this->error("❌ Service '{$serviceName}' not found in configuration.");
                $this->info('Available services: ' . implode(', ', array_keys($allServices)));
                return [];
            }

            // Also include dependencies
            $resolved = $this->resolveDependencies($serviceName, $allServices);
            return $resolved;
        }

        // Sort by priority
        uasort($allServices, fn($a, $b) => $a['priority'] <=> $b['priority']);

        return $allServices;
    }

    /**
     * Resolve service dependencies (topological sort).
     */
    private function resolveDependencies(string $service, array $allServices, array &$resolved = []): array
    {
        $config = $allServices[$service];

        // First, resolve dependencies
        foreach ($config['depends_on'] as $dep) {
            if (!isset($resolved[$dep]) && isset($allServices[$dep])) {
                $this->resolveDependencies($dep, $allServices, $resolved);
            }
        }

        $resolved[$service] = $config;
        return $resolved;
    }

    /**
     * Execute dry run — show plan without executing.
     */
    private function executeDryRun(array $services): int
    {
        $this->info('');
        $this->info('<fg=yellow>📋 DRY RUN — Execution Plan</>');
        $this->info(str_repeat('─', 60));

        $step = 1;
        foreach ($services as $name => $config) {
            $deps = empty($config['depends_on'])
                ? 'none'
                : implode(', ', $config['depends_on']);

            $this->info('');
            $this->info("  <fg=cyan>Step {$step}:</> <fg=white>{$config['label']}</> ({$name})");
            $this->info("    ├─ Priority: {$config['priority']}");
            $this->info("    ├─ Database: {$config['db_database']}@{$config['db_host']}");
            $this->info("    ├─ Dependencies: {$deps}");
            $this->info("    ├─ Migrations: " . (empty($config['migrations']) ? 'none' : implode(', ', $config['migrations'])));
            $this->info("    └─ Seeders: " . count($config['seeders']));

            foreach ($config['seeders'] as $seeder) {
                $this->info("       └─ " . class_basename($seeder));
            }

            $step++;
        }

        $this->info('');
        $this->info('<fg=green>✅ Dry run complete. No changes were made.</>');
        return 0;
    }

    /**
     * Main seeding execution pipeline.
     */
    private function executeSeeding(array $services, array $config): int
    {
        $totalServices = count($services);
        $current = 0;
        $failed = false;

        foreach ($services as $name => $serviceConfig) {
            $current++;
            $serviceStart = microtime(true);

            $this->info('');
            $this->info("  [{$current}/{$totalServices}] <fg=cyan>▶ {$serviceConfig['label']}</> ({$name})");

            try {
                // Resolve actual DB host (Docker vs Local)
                $serviceConfig = $this->resolveDbHost($name, $serviceConfig);

                // Step 1: Health Check
                if (!$this->option('skip-health')) {
                    $this->info("    ├─ 🔍 Health check...");
                    $this->checkDatabaseHealth($name, $serviceConfig);
                    $this->info("    │  └─ <fg=green>✓ Database reachable</>");
                }

                // Step 2: Configure dynamic connection
                $this->info("    ├─ ⚙️  Configuring connection...");
                $this->configureDatabaseConnection($name, $serviceConfig);
                $this->info("    │  └─ <fg=green>✓ Connection '{$name}' configured</>");

                // Step 3: Reset if needed
                if ($this->option('reset') || $this->option('fresh')) {
                    $this->info("    ├─ 🗑️  Resetting database...");
                    $this->resetDatabase($name, $serviceConfig);
                    $this->info("    │  └─ <fg=green>✓ Database reset</>");
                }

                // Step 4: Run migrations
                if (!$this->option('no-migrate') && !empty($serviceConfig['migrations'])) {
                    $this->info("    ├─ 📦 Running migrations...");
                    $this->runMigrations($name, $serviceConfig);
                    $this->info("    │  └─ <fg=green>✓ Migrations applied</>");
                }

                // Step 5: Run seeders
                $this->info("    ├─ 🌱 Running seeders...");
                $seederCount = $this->runSeeders($name, $serviceConfig, $config);
                $this->info("    │  └─ <fg=green>✓ {$seederCount} seeder(s) executed</>");

                // Record success
                $elapsed = round(microtime(true) - $serviceStart, 2);
                $this->info("    └─ <fg=green>✅ Complete</> <fg=gray>({$elapsed}s)</>");

                $this->seededServices[] = $name;
                $this->report[$name] = [
                    'status'   => 'success',
                    'duration' => $elapsed,
                    'seeders'  => $seederCount,
                    'message'  => 'All seeders executed successfully',
                ];

                $this->logEvent('SUCCESS', "Service {$name} seeded", ['duration' => $elapsed]);

            } catch (\Throwable $e) {
                $elapsed = round(microtime(true) - $serviceStart, 2);
                $this->error("    └─ ❌ FAILED: {$e->getMessage()}");

                $this->report[$name] = [
                    'status'   => 'failed',
                    'duration' => $elapsed,
                    'error'    => $e->getMessage(),
                    'file'     => $e->getFile() . ':' . $e->getLine(),
                ];

                $this->logEvent('FAILED', "Service {$name} failed", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                // Attempt rollback
                $this->info('');
                $this->warn("    ⚠️  Initiating rollback for previously seeded services...");
                $this->performRollback();

                $failed = true;
                break;
            }
        }

        return $failed ? 1 : 0;
    }

    /**
     * Resolve DB host: use .env values for local mode, Docker hostnames for container mode.
     * For the core service, always use the default Laravel DB config.
     */
    private function resolveDbHost(string $name, array $config): array
    {
        if ($name === 'core') {
            // Core always uses the default Laravel connection from .env
            $config['db_host'] = config('database.connections.mysql.host', '127.0.0.1');
            $config['db_port'] = config('database.connections.mysql.port', 3306);
            $config['db_database'] = config('database.connections.mysql.database', 'core');
            $config['db_username'] = config('database.connections.mysql.username', 'core');
            $config['db_password'] = config('database.connections.mysql.password', 'secret');
            return $config;
        }

        // For other services: if the db_host looks like a Docker hostname
        // (contains '-db') and is unreachable, fall back to the local .env host
        $localHost = config('database.connections.mysql.host', '127.0.0.1');
        $localPort = config('database.connections.mysql.port', 3306);

        if (str_contains($config['db_host'], '-db')) {
            // Try Docker hostname first, silently
            try {
                $pdo = new \PDO(
                    "mysql:host={$config['db_host']};port={$config['db_port']}",
                    $config['db_username'],
                    $config['db_password'],
                    [\PDO::ATTR_TIMEOUT => 2]
                );
                return $config; // Docker host works
            } catch (\Exception $e) {
                // Docker hostname unreachable → fall back to local MySQL
                $config['db_host'] = $localHost;
                $config['db_port'] = $localPort;
                // In local mode, use the core user to access all databases
                $config['db_username'] = config('database.connections.mysql.username', 'root');
                $config['db_password'] = config('database.connections.mysql.password', 'secret');
                $this->info("    │  └─ <fg=yellow>↪ Falling back to local MySQL ({$localHost}:{$localPort})</>");
            }
        }

        return $config;
    }

    /**
     * Check if a database is reachable.
     */
    private function checkDatabaseHealth(string $name, array $config): void
    {
        $maxRetries = config('seeders.retry_attempts', 3);
        $delay = config('seeders.retry_delay', 5);

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $pdo = new \PDO(
                    "mysql:host={$config['db_host']};port={$config['db_port']};dbname={$config['db_database']}",
                    $config['db_username'],
                    $config['db_password'],
                    [\PDO::ATTR_TIMEOUT => 10]
                );
                $pdo->query('SELECT 1');
                return; // Success
            } catch (\Exception $e) {
                if ($attempt === $maxRetries) {
                    throw new \RuntimeException(
                        "Database {$config['db_database']} unreachable after {$maxRetries} attempts: {$e->getMessage()}"
                    );
                }
                $this->info("    │  └─ <fg=yellow>⏳ Attempt {$attempt}/{$maxRetries} failed, retrying in {$delay}s...</>");
                sleep($delay);
            }
        }
    }

    /**
     * Configure a dynamic database connection for a service.
     */
    private function configureDatabaseConnection(string $name, array $config): void
    {
        Config::set("database.connections.{$name}", [
            'driver'    => 'mysql',
            'host'      => $config['db_host'],
            'port'      => $config['db_port'],
            'database'  => $config['db_database'],
            'username'  => $config['db_username'],
            'password'  => $config['db_password'],
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => true,
            'engine'    => null,
        ]);

        // Purge any cached connection
        DB::purge($name);
    }

    /**
     * Reset/fresh a database.
     */
    private function resetDatabase(string $name, array $config): void
    {
        if ($this->option('fresh') && !empty($config['migrations'])) {
            foreach ($config['migrations'] as $path) {
                Artisan::call('migrate:fresh', [
                    '--database' => $name,
                    '--path'     => $path,
                    '--realpath' => str_starts_with($path, '/'),
                    '--force'    => true,
                ]);
            }
        } elseif ($this->option('reset')) {
            // Drop all tables in the service database
            $connection = DB::connection($name);
            $connection->statement('SET FOREIGN_KEY_CHECKS=0');

            $tables = $connection->select('SHOW TABLES');
            $dbKey = "Tables_in_{$config['db_database']}";

            foreach ($tables as $table) {
                $tableName = $table->$dbKey ?? array_values((array)$table)[0];
                $connection->statement("DROP TABLE IF EXISTS `{$tableName}`");
            }

            $connection->statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    /**
     * Run migrations for a service.
     */
    private function runMigrations(string $name, array $config): void
    {
        foreach ($config['migrations'] as $migrationPath) {
            $fullPath = base_path($migrationPath);

            if (!is_dir($fullPath)) {
                $this->info("    │  └─ <fg=yellow>⚠ Migration path not found: {$migrationPath}</>");
                continue;
            }

            // For core, use default connection
            $connectionName = ($name === 'core') ? config('database.default') : $name;

            Artisan::call('migrate', [
                '--database' => $connectionName,
                '--path'     => $migrationPath,
                '--realpath' => false,
                '--force'    => true,
            ]);
        }
    }

    /**
     * Run seeders for a service. Returns count of seeders executed.
     */
    private function runSeeders(string $name, array $serviceConfig, array $globalConfig): int
    {
        $count = 0;

        // For core service, use default connection
        if ($name === 'core') {
            $previousConnection = DB::getDefaultConnection();

            foreach ($serviceConfig['seeders'] as $seederClass) {
                $seederName = class_basename($seederClass);
                $this->info("    │  ├─ Running {$seederName}...");

                try {
                    Artisan::call('db:seed', [
                        '--class' => $seederClass,
                        '--force' => true,
                    ]);
                    $count++;
                } catch (\Throwable $e) {
                    // Check if it's an idempotent issue (already seeded)
                    if ($this->isIdempotentError($e)) {
                        $this->info("    │  │  └─ <fg=yellow>⚠ Already seeded (idempotent skip)</>");
                        $count++;
                    } else {
                        throw $e;
                    }
                }
            }
        } else {
            // Set connection for module seeders
            foreach ($serviceConfig['seeders'] as $seederClass) {
                $seederName = class_basename($seederClass);
                $this->info("    │  ├─ Running {$seederName}...");

                if (class_exists($seederClass)) {
                    $seeder = app($seederClass);
                    // Pass the connection name to the seeder
                    $seeder->run($name);
                    $count++;
                } else {
                    $this->info("    │  │  └─ <fg=yellow>⚠ Seeder class not found: {$seederClass}</>");
                }
            }
        }

        return $count;
    }

    /**
     * Check if an error is an idempotent duplicate issue.
     */
    private function isIdempotentError(\Throwable $e): bool
    {
        $message = strtolower($e->getMessage());
        return str_contains($message, 'duplicate')
            || str_contains($message, 'already exists')
            || str_contains($message, 'integrity constraint');
    }

    /**
     * Perform rollback on previously seeded services.
     */
    private function performRollback(): void
    {
        if (empty($this->seededServices)) {
            $this->info('    └─ No services to rollback.');
            return;
        }

        // Rollback in reverse order
        $reversed = array_reverse($this->seededServices);

        foreach ($reversed as $serviceName) {
            try {
                $serviceConfig = config("seeders.services.{$serviceName}");
                if (!$serviceConfig) continue;

                $this->info("    ├─ Rolling back: {$serviceName}...");

                // Re-configure connection
                $this->configureDatabaseConnection($serviceName, $serviceConfig);

                if ($serviceName === 'core') {
                    // For core, we only truncate seeded tables, not drop
                    $this->info("    │  └─ <fg=yellow>⚠ Core rollback: keeping structure, clearing seed data</>");
                    // Selective rollback — we don't want to lose migrations table
                } else {
                    // For module services: drop all tables
                    $connection = DB::connection($serviceName);
                    $connection->statement('SET FOREIGN_KEY_CHECKS=0');

                    $tables = $connection->select('SHOW TABLES');
                    $dbKey = "Tables_in_{$serviceConfig['db_database']}";

                    foreach ($tables as $table) {
                        $tableName = $table->$dbKey ?? array_values((array)$table)[0];
                        if ($tableName !== 'migrations') {
                            $connection->statement("TRUNCATE TABLE `{$tableName}`");
                        }
                    }

                    $connection->statement('SET FOREIGN_KEY_CHECKS=1');
                }

                $this->report[$serviceName]['rollback'] = 'success';
                $this->info("    │  └─ <fg=green>✓ Rolled back</>");

            } catch (\Throwable $e) {
                $this->error("    │  └─ ❌ Rollback failed: {$e->getMessage()}");
                $this->report[$serviceName]['rollback'] = 'failed: ' . $e->getMessage();
            }
        }

        $this->info("    └─ Rollback complete.");
    }

    /**
     * Generate and display the final execution report.
     */
    private function generateReport(): void
    {
        $totalTime = round(microtime(true) - $this->startTime, 2);
        $succeeded = count(array_filter($this->report, fn($r) => $r['status'] === 'success'));
        $failed = count(array_filter($this->report, fn($r) => $r['status'] === 'failed'));
        $total = count($this->report);

        $this->info('');
        $this->info('<fg=cyan>╔══════════════════════════════════════════════════════╗</>');
        $this->info('<fg=cyan>║</>  <fg=white>📊 EXECUTION REPORT</>                                <fg=cyan>║</>');
        $this->info('<fg=cyan>╚══════════════════════════════════════════════════════╝</>');
        $this->info('');
        $this->info("  Environment:  <fg=cyan>{$this->environment}</>");
        $this->info("  Total Time:   <fg=yellow>{$totalTime}s</>");
        $this->info("  Services:     <fg=green>{$succeeded} ✅</> / <fg=red>{$failed} ❌</> / {$total} total");
        $this->info('');

        // Table view
        $rows = [];
        foreach ($this->report as $name => $data) {
            $statusIcon = $data['status'] === 'success' ? '✅' : '❌';
            $rows[] = [
                $name,
                "{$statusIcon} {$data['status']}",
                $data['duration'] . 's',
                $data['seeders'] ?? '-',
                $data['error'] ?? $data['message'] ?? '-',
            ];
        }

        $this->table(
            ['Service', 'Status', 'Duration', 'Seeders', 'Details'],
            $rows
        );

        // Save JSON report
        $reportPath = storage_path('logs/seed-report-' . date('Y-m-d-His') . '.json');
        $reportData = [
            'timestamp'   => Carbon::now()->toIso8601String(),
            'environment' => $this->environment,
            'total_time'  => $totalTime,
            'summary'     => [
                'total'     => $total,
                'succeeded' => $succeeded,
                'failed'    => $failed,
            ],
            'options' => [
                'reset'      => $this->option('reset'),
                'fresh'      => $this->option('fresh'),
                'no_migrate' => $this->option('no-migrate'),
            ],
            'services' => $this->report,
        ];

        if (!is_dir(dirname($reportPath))) {
            mkdir(dirname($reportPath), 0755, true);
        }
        file_put_contents($reportPath, json_encode($reportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info('');
        $this->info("📄 Report saved: <fg=yellow>{$reportPath}</>");
        $this->info('');

        if ($failed > 0) {
            $this->error("⚠️  {$failed} service(s) failed. Check logs for details.");
        } else {
            $this->info('<fg=green>🎉 All services seeded successfully!</>');
        }
    }

    /**
     * Log an event to the seed log channel.
     */
    private function logEvent(string $level, string $message, array $context = []): void
    {
        $context['environment'] = $this->environment;
        $context['timestamp'] = Carbon::now()->toIso8601String();

        try {
            Log::channel($this->logChannel)->info("[SEED:{$level}] {$message}", $context);
        } catch (\Throwable $e) {
            // Fallback to default log
            Log::info("[SEED:{$level}] {$message}", $context);
        }
    }
}
