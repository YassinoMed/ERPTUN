#!/usr/bin/env bash
# =====================================================================
# ERPGo SaaS — Migration vers MySQL Docker
# =====================================================================
# Ce script migre les données depuis la base externe (192.168.1.195)
# vers le MySQL Docker conteneurisé.
#
# Usage:
#   chmod +x docker/migrate-to-docker.sh
#   ./docker/migrate-to-docker.sh
#
# Prérequis:
#   - mysql et mysqldump installés localement
#   - Accès à la base source (192.168.1.195:8889)
#   - Docker Compose lancé avec le service mysql healthy
# =====================================================================

set -euo pipefail

# ── Configuration ──────────────────────────────────────────────────────
# Source (base existante)
SRC_HOST="${SRC_HOST:-192.168.1.195}"
SRC_PORT="${SRC_PORT:-8889}"
SRC_DB="${SRC_DB:-ERP_TEST}"
SRC_USER="${SRC_USER:-root}"
SRC_PASS="${SRC_PASS:-root}"

# Destination (MySQL Docker)
DST_CONTAINER="${DST_CONTAINER:-erp-mysql}"
DST_DB="${DST_DB:-erpgo}"
DST_USER="${DST_USER:-erpgo_user}"
DST_PASS="${DST_PASS:-erpgo_secret_2026}"

# ── Colors ─────────────────────────────────────────────────────────────
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m'

info()    { echo -e "${CYAN}[MIGRATE]${NC} $*"; }
success() { echo -e "${GREEN}[MIGRATE]${NC} ✅ $*"; }
warn()    { echo -e "${YELLOW}[MIGRATE]${NC} ⚠️  $*"; }
error()   { echo -e "${RED}[MIGRATE]${NC} ❌ $*"; }

# ── Banner ─────────────────────────────────────────────────────────────
echo -e ""
echo -e "${CYAN}╔═══════════════════════════════════════════════════════╗${NC}"
echo -e "${CYAN}║${NC}  ${GREEN}🐳 ERPGo — Migration vers MySQL Docker${NC}             ${CYAN}║${NC}"
echo -e "${CYAN}╚═══════════════════════════════════════════════════════╝${NC}"
echo -e ""
info "Source  : ${SRC_USER}@${SRC_HOST}:${SRC_PORT}/${SRC_DB}"
info "Dest    : ${DST_USER}@${DST_CONTAINER}/${DST_DB}"
echo -e ""

# ── Step 1: Vérifier que le MySQL Docker est prêt ──────────────────────
info "Vérification du MySQL Docker..."
if ! docker exec "${DST_CONTAINER}" mysqladmin ping -h 127.0.0.1 -u"${DST_USER}" -p"${DST_PASS}" --silent 2>/dev/null; then
    error "MySQL Docker n'est pas prêt. Lancez d'abord :"
    echo -e "  docker compose up -d mysql"
    echo -e "  # Attendez que le healthcheck soit 'healthy'"
    exit 1
fi
success "MySQL Docker est prêt"

# ── Step 2: Dump de la base source ────────────────────────────────────
DUMP_FILE="/tmp/erp_migration_$(date +%Y%m%d_%H%M%S).sql"
info "Export de ${SRC_DB} depuis ${SRC_HOST}..."
info "Fichier : ${DUMP_FILE}"

mysqldump \
    -h "${SRC_HOST}" \
    -P "${SRC_PORT}" \
    -u "${SRC_USER}" \
    -p"${SRC_PASS}" \
    --single-transaction \
    --quick \
    --lock-tables=false \
    --set-gtid-purged=OFF \
    --routines \
    --triggers \
    --events \
    "${SRC_DB}" > "${DUMP_FILE}" 2>/dev/null

DUMP_SIZE=$(du -sh "${DUMP_FILE}" | cut -f1)
success "Export terminé (${DUMP_SIZE})"

# ── Step 3: Import dans MySQL Docker ──────────────────────────────────
info "Import dans ${DST_CONTAINER}/${DST_DB}..."

docker exec -i "${DST_CONTAINER}" mysql \
    -u"${DST_USER}" \
    -p"${DST_PASS}" \
    "${DST_DB}" < "${DUMP_FILE}"

success "Import terminé"

# ── Step 4: Vérification ──────────────────────────────────────────────
info "Vérification des tables..."
TABLE_COUNT=$(docker exec "${DST_CONTAINER}" mysql \
    -u"${DST_USER}" -p"${DST_PASS}" \
    -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='${DST_DB}';" \
    -sN 2>/dev/null)

success "✅ ${TABLE_COUNT} tables importées dans ${DST_DB}"

# ── Step 5: Nettoyage ────────────────────────────────────────────────
info "Nettoyage du dump temporaire..."
rm -f "${DUMP_FILE}"
success "Dump supprimé"

echo -e ""
echo -e "${GREEN}╔═══════════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║  🎉 Migration terminée avec succès !                 ║${NC}"
echo -e "${GREEN}║                                                       ║${NC}"
echo -e "${GREEN}║  Prochaines étapes :                                  ║${NC}"
echo -e "${GREEN}║  1. docker compose up -d                              ║${NC}"
echo -e "${GREEN}║  2. docker compose exec core php artisan migrate      ║${NC}"
echo -e "${GREEN}║  3. Tester sur http://localhost:8080                   ║${NC}"
echo -e "${GREEN}╚═══════════════════════════════════════════════════════╝${NC}"
echo -e ""
