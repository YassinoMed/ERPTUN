# Stock & WMS

## Stock / WMS (Base)
**Catégorie** : Supply Chain  
**Priorité** : Must  
**Description courte** : Suivre les quantités et emplacements.  
**Valeur business** : Réduit les ruptures et erreurs d’inventaire.

**Spécifications fonctionnelles**
- User stories principales  
  - En tant que logisticien, je gère les stocks par entrepôt.  
  - En tant que vendeur, je vois la disponibilité.  
  - En tant qu’admin, je réalise un inventaire.  
- Critères d’acceptation  
  - Mouvements stock tracés.  
  - Disponibilité par entrepôt.  
  - Inventaire exportable.

**Spécifications techniques**
- Architecture & pattern à utiliser : StockService  
- Schéma de données : warehouses, product_stocks, stock_movements  
- APIs exposées : POST /api/stock/move  
- Événements : StockAdjusted  
- Intégrations externes nécessaires : scanners code-barres  
- Considérations multi-tenant : created_by  
- Sécurité & conformité associées : RBAC “stock.*”  
- Gestion des erreurs & rollback : compensation sur mouvement invalide

**Implémentation recommandée**
- Tech stack précise : Laravel 11  
- Tests : unitaires mouvements, intégration inventaire

**Monitoring & KPIs**
- Métriques à tracker : taux de rupture, variance inventaire  
- Alertes à configurer : seuil mini dépassé  
- Dashboards Grafana à créer : Stock Health

**Estimation d’effort**
- Story points : 8  
- Homme-jours : 7 + 2 + 1  
- Dépendances bloquantes : POS

---

## F08 — WMS Avancé (lots, FEFO, barcode)
**Catégorie** : Supply Chain  
**Priorité** : Should  
**Description courte** : Gestion avancée des lots et expirations.  
**Valeur business** : Réduit pertes et améliore la traçabilité.

**Spécifications fonctionnelles**
- User stories principales  
  - En tant que logisticien, je gère les lots et dates.  
  - En tant qu’agent, je scanne pour picking.  
  - En tant que manager, je fais un inventaire cyclique.  
- Critères d’acceptation  
  - FEFO/FIFO automatique.  
  - Scans validés.  
  - Inventaire variance < 2%.

**Spécifications techniques**
- Architecture & pattern à utiliser : WMSService + barcode  
- Schéma de données : lots, lot_movements, bin_locations, cycle_counts  
- APIs exposées : POST /api/wms/pick  
- Événements : LotExpired, PickCompleted  
- Intégrations externes nécessaires : scanners, barcode  
- Considérations multi-tenant : created_by  
- Sécurité & conformité associées : RBAC  
- Gestion des erreurs & rollback : rollback si lot insuffisant

**Implémentation recommandée**
- Tests : intégration picking, E2E cycle count

**Monitoring & KPIs**
- Métriques à tracker : erreurs picking, pertes expirations  
- Alertes à configurer : lot expiré  
- Dashboards Grafana à créer : WMS Control

**Estimation d’effort**
- Story points : 21  
- Homme-jours : 20 + 6 + 3  
- Dépendances bloquantes : stock de base
