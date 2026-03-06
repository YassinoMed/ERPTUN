# 🌱 ERPGo SaaS — Seed Orchestration Documentation

## Table of Contents

- [Overview](#overview)
- [Architecture](#architecture)
- [Quick Start](#quick-start)
- [CLI Reference](#cli-reference)
- [Service Dependency Graph](#service-dependency-graph)
- [Configuration](#configuration)
- [Seeders Reference](#seeders-reference)
- [Docker Integration](#docker-integration)
- [CI/CD Pipeline](#cicd-pipeline)
- [Security](#security)
- [Troubleshooting](#troubleshooting)

---

## Overview

The ERPGo Seed Orchestration system automates database population across all **19 microservices** with:

- ✅ **Dependency-aware execution** — Core is always seeded first
- ✅ **Idempotent operations** — Safe to run multiple times
- ✅ **Environment safety guards** — Production reset is blocked
- ✅ **Health checks with retries** — Databases are verified before seeding
- ✅ **Rollback on failure** — Previously seeded services are cleaned up
- ✅ **Centralized logging** — JSON reports for every execution
- ✅ **CI/CD ready** — GitHub Actions workflow included
- ✅ **Single command** — `./devops seed --env=dev --reset`

---

## Architecture

```
┌──────────────────────────────────────────────────────────────┐
│                     ./devops (CLI)                            │
│               ┌──────────┬──────────┐                        │
│               │  Local   │  Docker  │                        │
│               └────┬─────┴────┬─────┘                        │
│                    │          │                               │
│               ┌────▼──────────▼────┐                         │
│               │  SeedOrchestrator  │  (Artisan Command)      │
│               │  seed:orchestrate  │                         │
│               └────────┬───────────┘                         │
│                        │                                     │
│            ┌───────────▼───────────┐                         │
│            │   config/seeders.php  │  (Service Configuration)│
│            └───────────┬───────────┘                         │
│                        │                                     │
│  ┌─────────────────────▼─────────────────────────────┐       │
│  │              Execution Pipeline                    │       │
│  │  1. Health Check  →  2. Configure DB Connection   │       │
│  │  3. Reset (opt.)  →  4. Migrate                   │       │
│  │  5. Seed          →  6. Report                    │       │
│  └───────────────────────────────────────────────────┘       │
│                        │                                     │
│  ┌─────────┬───────────┼───────────┬─────────┐               │
│  ▼         ▼           ▼           ▼         ▼               │
│ Core    Billing     MRP         Hotel    Hedging  ... (×19)  │
│ (P:0)   (P:1)      (P:2)       (P:2)    (P:3)               │
└──────────────────────────────────────────────────────────────┘
```

---

## Quick Start

### 1. Full development seed with reset

```bash
./devops seed --env=dev --reset
```

### 2. Seed only core service

```bash
./devops seed --service=core
```

### 3. Preview execution plan

```bash
./devops seed --dry-run
```

### 4. Docker mode

```bash
./devops seed --env=dev --docker --reset
```

### 5. Via Artisan directly

```bash
php artisan seed:orchestrate --env=dev --reset --force
```

---

## CLI Reference

### `./devops` Commands

| Command | Description |
|---------|-------------|
| `seed` | Run database seeding orchestration |
| `status` | Show Docker container status |
| `health` | Run health checks on all databases |
| `logs` | Show recent execution logs |
| `report` | Display latest JSON report |
| `help` | Show help message |

### Seed Options

| Option | Default | Description |
|--------|---------|-------------|
| `--env=ENV` | `dev` | Target environment: `dev`, `staging`, `production` |
| `--reset` | false | Drop and recreate all tables before seeding |
| `--fresh` | false | Run `migrate:fresh` before seeding |
| `--service=NAME` | all | Seed only a specific service |
| `--dry-run` | false | Show execution plan without running |
| `--skip-health` | false | Skip database health checks |
| `--force` | false | Skip confirmation prompts |
| `--no-migrate` | false | Skip migrations, only run seeders |
| `--docker` | false | Run inside Docker containers |
| `--local` | - | Run locally with PHP (default) |

---

## Service Dependency Graph

```
Priority 0 (Foundation)
    └── core ──────────────────────────────────────┐
                                                    │
Priority 1 (Core Dependents)                       │
    ├── billing ◄──────────────────────────────────┤
    ├── saas ◄─────────────────────────────────────┤
    └── approvals ◄────────────────────────────────┤
                                                    │
Priority 2 (Domain Services)                       │
    ├── hrops ◄────────────────────────────────────┤
    ├── operations ◄───────────────────────────────┤
    ├── platform ◄─────────────────────────────────┤
    ├── mrp ◄──────────────────────────────────────┤
    ├── quality ◄──────────────────────────────────┤
    ├── maintenance ◄──────────────────────────────┤
    ├── chatgpt ◄──────────────────────────────────┤
    ├── integrations ◄─────────────────────────────┤
    ├── industry ◄─────────────────────────────────┤
    ├── hotel ◄────────────── billing ◄────────────┤
    └── btp ◄──────────────────────────────────────┤
                                                    │
Priority 3 (Specialized)                           │
    ├── traceability ◄─────────────────────────────┤
    ├── cropplanning ◄──── traceability ◄──────────┤
    ├── cooperative ◄───── traceability ◄──────────┤
    └── hedging ◄───────── cooperative ◄───────────┘
```

---

## Configuration

### `config/seeders.php`

Central configuration file controlling:

| Key | Description |
|-----|-------------|
| `timeout_per_service` | Max seconds per service (default: 300) |
| `retry_attempts` | DB health check retries (default: 3) |
| `retry_delay` | Seconds between retries (default: 5) |
| `environments` | Per-environment settings |
| `services` | Full service registry with DB credentials |

### Environment Settings

| Environment | Reset Allowed | Demo Data | Confirmation |
|-------------|:------------:|:---------:|:------------:|
| `dev` | ✅ | ✅ | ❌ |
| `staging` | ✅ | ✅ | ✅ |
| `production` | ❌ | ❌ | ✅ |

### Environment Variables

```bash
# Override via .env
SEED_TIMEOUT=300           # Timeout per service (seconds)
SEED_RETRIES=3             # Health check retry count
SEED_RETRY_DELAY=5         # Delay between retries (seconds)
SEED_LOG_CHANNEL=seed      # Log channel name

# Per-service DB overrides
CORE_DB_HOST=core-db
CORE_DB_DATABASE=core
BILLING_DB_HOST=billing-db
# ...etc for each service
```

---

## Seeders Reference

### Core Seeders (database/seeders/)

| Seeder | Purpose |
|--------|---------|
| `NotificationSeeder` | Email/push notification templates in 15 languages |
| `PlansTableSeeder` | 9 subscription plans (Free → Platinum) |
| `UsersTableSeeder` | Admin user, permissions, roles (300+ permissions) |
| `EducationSeeder` | Education reference data |
| `AiTemplateSeeder` | AI prompt templates |

### Module Seeders (database/seeders/Modules/)

| Seeder | Service | Data Seeded |
|--------|---------|-------------|
| `BillingSeeder` | billing | Payment methods, tax rates, billing settings |
| `SaasSeeder` | saas | SaaS settings, feature toggles |
| `ApprovalsSeeder` | approvals | Approval statuses, types, escalation rules |
| `HrOpsSeeder` | hrops | Departments, designations, leave types, employment types |
| `OperationsSeeder` | operations | Operation statuses, priorities, categories |
| `PlatformSeeder` | platform | Platform settings, notification channels |
| `MrpSeeder` | mrp | Production statuses, resource types, unit measures |
| `QualitySeeder` | quality | Inspection types, defect categories, quality standards |
| `MaintenanceSeeder` | maintenance | Maintenance types, equipment categories, schedules |
| `ChatGptSeeder` | chatgpt | AI models, prompt categories, settings |
| `IntegrationsSeeder` | integrations | Integration providers, settings |
| `IndustrySeeder` | industry | Industry categories, compliance frameworks |
| `HotelSeeder` | hotel | Room types, booking statuses, amenities |
| `BtpSeeder` | btp | Site statuses, equipment types, work phases |
| `TraceabilitySeeder` | traceability | Traceability statuses, trace event types |
| `CropPlanningSeeder` | cropplanning | Crop types, soil types, planning statuses |
| `CooperativeSeeder` | cooperative | Cooperative types, member roles, settings |
| `HedgingSeeder` | hedging | Commodity types, contract types, risk levels |

### Adding a New Seeder

1. Create a seeder class extending `BaseModuleSeeder`:

```php
<?php

namespace Database\Seeders\Modules;

class MyNewSeeder extends BaseModuleSeeder
{
    protected string $moduleName = 'MyModule';

    protected function seed(): void
    {
        if ($this->tableExists('my_table')) {
            $this->upsert('my_table', ['slug' => 'key'], [
                'name' => 'Value',
                'slug' => 'key',
            ]);
        }
    }
}
```

2. Register it in `config/seeders.php` under the appropriate service.

---

## Docker Integration

### Using docker/seed.sh

```bash
# Start DBs and seed
./docker/seed.sh --env=dev --reset

# Seed specific service
./docker/seed.sh --service=core --force

# Environment variables
SEED_ENV=staging SEED_RESET=true ./docker/seed.sh
```

### Docker Compose Integration

Add to your `docker-compose.yml`:

```yaml
services:
  seed-runner:
    <<: *laravel-service
    command: php artisan seed:orchestrate --env=dev --force --reset
    depends_on:
      core-db:
        condition: service_healthy
      billing-db:
        condition: service_healthy
      # ... other DBs
    profiles:
      - seed  # Only runs with: docker compose --profile seed up
```

Run with:

```bash
docker compose --profile seed up seed-runner
```

---

## CI/CD Pipeline

### GitHub Actions Workflow

Located at `.github/workflows/seed.yml`

#### Triggers

| Trigger | Environment | Reset |
|---------|-------------|:-----:|
| Manual dispatch | Configurable | Configurable |
| Push to main/develop (seed files) | dev | ❌ |
| Nightly schedule (2 AM UTC) | staging | ✅ |

#### Artifacts

Each run produces:
- `seed-report-{env}-{run}.json` — Detailed execution report
- `seed-logs-{run}/` — Full execution logs

#### Manual Dispatch

Go to **Actions** → **🌱 Database Seed Pipeline** → **Run workflow**

Configure:
- Environment (dev/staging)
- Reset databases
- Specific service
- Dry run mode

---

## Security

### Production Safeguards

1. **Reset blocked in production** — `--reset` and `--fresh` flags are rejected
2. **Confirmation required** — Staging and production require explicit confirmation
3. **No demo data in production** — `seed_demo_data` flag is false
4. **Credentials via env vars** — No hardcoded secrets in config
5. **Force flag required for CI** — Unattended execution requires `--force`

### Best Practices

- Never run `--reset` on production databases
- Use `--dry-run` to preview before staging seeds
- Review JSON reports after each execution
- Monitor the `seed.log` channel for anomalies
- Rotate credentials regularly via environment variables

---

## Troubleshooting

### Common Issues

| Issue | Solution |
|-------|----------|
| `Database unreachable` | Check container health: `./devops health` |
| `Table not found` | Run with migrations: remove `--no-migrate` flag |
| `Duplicate entry` | Seeders are idempotent, this is handled automatically |
| `Permission denied` | Check DB user grants: `GRANT ALL ON db.* TO user` |
| `Timeout` | Increase `SEED_TIMEOUT` env variable |
| `Class not found` | Run `composer dump-autoload` |

### Debugging Steps

```bash
# 1. Check service status
./devops status

# 2. Run health checks
./devops health

# 3. Preview execution plan
./devops seed --dry-run

# 4. Seed single service with logs
./devops seed --service=core --env=dev 2>&1 | tee debug.log

# 5. Check latest report
./devops report

# 6. View seed logs
tail -f storage/logs/seed.log
```

### Log Locations

| File | Purpose |
|------|---------|
| `storage/logs/seed.log` | Dedicated seed operations log (rotated daily, 30 days) |
| `storage/logs/seed-report-*.json` | JSON execution reports |
| `storage/logs/devops_*.log` | CLI script logs |

---

## File Structure

```
main-file/
├── devops                                  # 🔧 CLI entry point
├── config/
│   ├── seeders.php                         # 📋 Service registry & dependency graph
│   └── logging.php                         # 📝 Includes 'seed' log channel
├── app/Console/Commands/
│   └── SeedOrchestrator.php                # 🧠 Core orchestration engine
├── database/seeders/
│   ├── DatabaseSeeder.php                  # Original Laravel seeder
│   ├── UsersTableSeeder.php                # Permissions & users (existing)
│   ├── PlansTableSeeder.php                # Subscription plans (existing)
│   ├── NotificationSeeder.php              # Notification templates (existing)
│   ├── EducationSeeder.php                 # Education data (existing)
│   ├── AiTemplateSeeder.php                # AI templates (existing)
│   └── Modules/
│       ├── BaseModuleSeeder.php            # Abstract base class
│       ├── BillingSeeder.php               # Billing module
│       ├── SaasSeeder.php                  # SaaS module
│       ├── ApprovalsSeeder.php             # Approvals module
│       ├── HrOpsSeeder.php                 # HR Operations module
│       ├── OperationsSeeder.php            # Operations module
│       ├── PlatformSeeder.php              # Platform module
│       ├── MrpSeeder.php                   # MRP module
│       ├── QualitySeeder.php               # Quality module
│       ├── MaintenanceSeeder.php           # Maintenance module
│       ├── ChatGptSeeder.php               # ChatGPT module
│       ├── IntegrationsSeeder.php          # Integrations module
│       ├── IndustrySeeder.php              # Industry module
│       ├── HotelSeeder.php                 # Hotel module
│       ├── BtpSeeder.php                   # BTP module
│       ├── TraceabilitySeeder.php          # Traceability module
│       ├── CropPlanningSeeder.php          # CropPlanning module
│       ├── CooperativeSeeder.php           # Cooperative module
│       └── HedgingSeeder.php              # Hedging module
├── docker/
│   ├── gateway.conf                        # Nginx config (existing)
│   └── seed.sh                             # 🐳 Docker seed script
├── .github/workflows/
│   └── seed.yml                            # 🔄 CI/CD pipeline
└── docs/
    └── SEED_ORCHESTRATION.md               # 📚 This documentation
```
