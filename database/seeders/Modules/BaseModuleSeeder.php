<?php

namespace Database\Seeders\Modules;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * =====================================================================
 * Base Module Seeder — Abstract Foundation for All Module Seeders
 * =====================================================================
 *
 * Provides:
 * - Dynamic database connection management
 * - Idempotent insert helpers (updateOrCreate pattern)
 * - Foreign key management during seeding
 * - Consistent logging
 *
 * All module seeders extend this class.
 * =====================================================================
 */
abstract class BaseModuleSeeder extends Seeder
{
    /** @var string The database connection name */
    protected string $connectionName = '';

    /** @var string Module display name */
    protected string $moduleName = '';

    /**
     * Run the seeder with optional connection override.
     */
    public function run(string $connection = ''): void
    {
        if ($connection) {
            $this->connectionName = $connection;
        }

        $this->command?->info("      🌱 Seeding {$this->moduleName}...");

        try {
            // Disable FK checks during seeding
            if ($this->connectionName) {
                DB::connection($this->connectionName)->statement('SET FOREIGN_KEY_CHECKS=0');
            }

            $this->seed();

            if ($this->connectionName) {
                DB::connection($this->connectionName)->statement('SET FOREIGN_KEY_CHECKS=1');
            }

        } catch (\Throwable $e) {
            // Re-enable FK checks even on failure
            if ($this->connectionName) {
                try {
                    DB::connection($this->connectionName)->statement('SET FOREIGN_KEY_CHECKS=1');
                } catch (\Throwable $ignored) {}
            }
            throw $e;
        }
    }

    /**
     * Implement the actual seeding logic in subclasses.
     */
    abstract protected function seed(): void;

    /**
     * Get the database connection for this module.
     */
    protected function db(): \Illuminate\Database\Connection
    {
        return $this->connectionName
            ? DB::connection($this->connectionName)
            : DB::connection();
    }

    /**
     * Idempotent insert: only insert if not exists (by unique key).
     */
    protected function insertIfNotExists(string $table, array $data, array $uniqueKeys): void
    {
        $query = $this->db()->table($table);

        foreach ($uniqueKeys as $key) {
            if (isset($data[$key])) {
                $query->where($key, $data[$key]);
            }
        }

        if ($query->count() === 0) {
            $data['created_at'] = $data['created_at'] ?? now();
            $data['updated_at'] = $data['updated_at'] ?? now();
            $this->db()->table($table)->insert($data);
        }
    }

    /**
     * Idempotent upsert: update or create by unique keys.
     */
    protected function upsert(string $table, array $uniqueData, array $updateData = []): void
    {
        $query = $this->db()->table($table);

        foreach ($uniqueData as $key => $value) {
            $query->where($key, $value);
        }

        $merged = array_merge($uniqueData, $updateData);
        $merged['updated_at'] = now();

        if ($query->count() === 0) {
            $merged['created_at'] = now();
            $this->db()->table($table)->insert($merged);
        } else {
            $query->update($updateData + ['updated_at' => now()]);
        }
    }

    /**
     * Check if a table exists on the module's connection.
     */
    protected function tableExists(string $table): bool
    {
        return Schema::connection($this->connectionName ?: config('database.default'))
            ->hasTable($table);
    }

    /**
     * Truncate a table safely.
     */
    protected function truncateIfExists(string $table): void
    {
        if ($this->tableExists($table)) {
            $this->db()->table($table)->truncate();
        }
    }

    /**
     * Get current timestamp string.
     */
    protected function now(): string
    {
        return now()->format('Y-m-d H:i:s');
    }
}
