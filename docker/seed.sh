#!/usr/bin/env bash
# =====================================================================
# ERPGo SaaS — Docker Seed Script (BD unique)
# =====================================================================
# Script pour seeder la base de données centralisée.
#
# Usage:
#   docker/seed.sh [--env=dev] [--reset] [--force]
#
# Environment Variables:
#   SEED_ENV          - Target environment (default: dev)
#   SEED_RESET        - Set to "true" to reset databases
#   SEED_SERVICE      - Specific service to seed
#   SEED_SKIP_HEALTH  - Skip health checks
#   SEED_NO_MIGRATE   - Skip migrations
# =====================================================================

set -euo pipefail

# ── Configuration ──────────────────────────────────────────────────────
SEED_ENV="${SEED_ENV:-dev}"
SEED_RESET="${SEED_RESET:-false}"
SEED_SERVICE="${SEED_SERVICE:-}"
SEED_SKIP_HEALTH="${SEED_SKIP_HEALTH:-false}"
SEED_NO_MIGRATE="${SEED_NO_MIGRATE:-false}"
SEED_FORCE="${SEED_FORCE:-false}"

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="${SCRIPT_DIR}/.."
COMPOSE_FILE="${PROJECT_ROOT}/docker-compose.yml"

# ── Color Codes ────────────────────────────────────────────────────────
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m'

info()    { echo -e "${CYAN}[SEED]${NC} $*"; }
success() { echo -e "${GREEN}[SEED]${NC} ✅ $*"; }
warn()    { echo -e "${YELLOW}[SEED]${NC} ⚠️  $*"; }
error()   { echo -e "${RED}[SEED]${NC} ❌ $*"; }

# ── Parse CLI Arguments ────────────────────────────────────────────────
for arg in "$@"; do
    case $arg in
        --env=*)       SEED_ENV="${arg#*=}" ;;
        --reset)       SEED_RESET="true" ;;
        --force)       SEED_FORCE="true" ;;
        --service=*)   SEED_SERVICE="${arg#*=}" ;;
        --skip-health) SEED_SKIP_HEALTH="true" ;;
        --no-migrate)  SEED_NO_MIGRATE="true" ;;
    esac
done

# ── Docker Compose Command ────────────────────────────────────────────
COMPOSE_CMD=""
if docker compose version &> /dev/null 2>&1; then
    COMPOSE_CMD="docker compose -f ${COMPOSE_FILE}"
elif command -v docker-compose &> /dev/null; then
    COMPOSE_CMD="docker-compose -f ${COMPOSE_FILE}"
else
    error "Neither 'docker compose' nor 'docker-compose' found!"
    exit 1
fi

# ── Main Execution ────────────────────────────────────────────────────
echo -e ""
echo -e "${CYAN}╔═══════════════════════════════════════════════════════╗${NC}"
echo -e "${CYAN}║${NC}  ${GREEN}🐳 ERPGo SaaS — Docker Seed Script${NC}                 ${CYAN}║${NC}"
echo -e "${CYAN}║${NC}  ${GREEN}   BD unique centralisée${NC}                            ${CYAN}║${NC}"
echo -e "${CYAN}╚═══════════════════════════════════════════════════════╝${NC}"
echo -e ""
info "Environment: ${SEED_ENV}"
info "Reset: ${SEED_RESET}"
info "Service: ${SEED_SERVICE:-all}"

# Step 1: Start MySQL + Redis
info "Starting MySQL and Redis..."
${COMPOSE_CMD} up -d mysql redis 2>&1

# Step 2: Wait for MySQL to be healthy
info "Waiting for MySQL to become healthy..."
MAX_WAIT=120
WAITED=0
INTERVAL=5

while [[ $WAITED -lt $MAX_WAIT ]]; do
    if ${COMPOSE_CMD} ps mysql 2>/dev/null | grep -q "healthy"; then
        success "MySQL is healthy!"
        break
    fi
    echo -ne "\r  ⏳ Waiting for MySQL... (${WAITED}s / ${MAX_WAIT}s)  "
    sleep $INTERVAL
    WAITED=$((WAITED + INTERVAL))
done
echo -e ""

if [[ $WAITED -ge $MAX_WAIT ]]; then
    error "Timeout! MySQL is not ready after ${MAX_WAIT}s."
    exit 1
fi

# Step 3: Start the core service
info "Ensuring core service is running..."
${COMPOSE_CMD} up -d core 2>&1

# Wait for core to be ready
sleep 10

# Step 4: Build artisan command
ARTISAN_ARGS="seed:orchestrate --env=${SEED_ENV}"
[[ "$SEED_RESET" == "true" ]]       && ARTISAN_ARGS+=" --reset"
[[ -n "$SEED_SERVICE" ]]            && ARTISAN_ARGS+=" --service=${SEED_SERVICE}"
[[ "$SEED_SKIP_HEALTH" == "true" ]] && ARTISAN_ARGS+=" --skip-health"
[[ "$SEED_NO_MIGRATE" == "true" ]]  && ARTISAN_ARGS+=" --no-migrate"
[[ "$SEED_FORCE" == "true" ]]       && ARTISAN_ARGS+=" --force"

# Step 5: Execute seeding inside core container
info "Executing seed orchestrator inside 'core' container..."
echo -e ""

${COMPOSE_CMD} exec -T core php artisan ${ARTISAN_ARGS}
EXIT_CODE=$?

echo -e ""

# Step 6: Report
if [[ $EXIT_CODE -eq 0 ]]; then
    success "🎉 Database seeding completed successfully!"
else
    error "Database seeding FAILED with exit code: ${EXIT_CODE}"
    warn "Check logs with: ${COMPOSE_CMD} logs core"
fi

exit $EXIT_CODE
