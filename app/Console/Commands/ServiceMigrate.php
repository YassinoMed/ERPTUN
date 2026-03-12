<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ServiceMigrate extends Command
{
    protected $signature = 'service:migrate {service?} {--shared} {--fresh} {--seed}';

    protected $description = 'Run migrations for a service boundary profile.';

    public function handle(): int
    {
        $service = $this->argument('service') ?: env('APP_SERVICE', 'core');
        $profiles = config('service_boundaries.migration_profiles', []);

        if (! array_key_exists($service, $profiles)) {
            $this->error(sprintf('Unknown service migration profile: %s', $service));

            return self::FAILURE;
        }

        $targets = $profiles[$service];

        if ($this->option('shared') && $service !== 'core') {
            $targets = array_merge(config('service_boundaries.migration_profiles.core', []), $targets);
        }

        foreach ($targets as $target) {
            foreach ($target['paths'] as $path) {
                if (! is_dir($path) && ! is_file($path)) {
                    $this->warn(sprintf('Skipped missing migration path: %s', $path));
                    continue;
                }

                $command = $this->option('fresh') ? 'migrate:fresh' : 'migrate';
                $arguments = [
                    '--database' => $target['connection'],
                    '--path' => $path,
                    '--realpath' => true,
                    '--force' => true,
                ];

                if ($command === 'migrate:fresh') {
                    unset($arguments['--path']);
                    unset($arguments['--realpath']);
                }

                $this->line(sprintf(
                    '%s [%s] using %s',
                    $command,
                    $target['connection'],
                    $path
                ));

                $exitCode = Artisan::call($command, $arguments);
                $this->output->write(Artisan::output());

                if ($exitCode !== 0) {
                    return $exitCode;
                }
            }
        }

        if ($this->option('seed')) {
            $exitCode = Artisan::call('db:seed', ['--force' => true]);
            $this->output->write(Artisan::output());

            if ($exitCode !== 0) {
                return $exitCode;
            }
        }

        return self::SUCCESS;
    }
}
