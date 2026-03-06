# Production

## BOM & Ordres de fabrication
**Catégorie** : Industrie  
**Priorité** : Must  
**Description courte** : Planifier et suivre la production (BOM, ordres).  
**Valeur business** : Améliore la maîtrise coûts et délais.

**Spécifications fonctionnelles**
- User stories principales  
  - En tant que responsable, je crée un BOM.  
  - En tant qu’atelier, je lance un ordre de fabrication.  
  - En tant que manager, je suis les rendements.  
- Critères d’acceptation  
  - BOM versionné.  
  - Consommation stock automatique.  
  - Rendement calculé par ordre.

**Spécifications techniques**
- Architecture & pattern à utiliser : ProductionService  
- Schéma de données : boms, bom_items, production_orders, work_centers  
- APIs exposées : POST /api/production/orders  
- Événements : ProductionOrderStarted, ProductionOrderCompleted  
- Intégrations externes nécessaires : WMS  
- Considérations multi-tenant : created_by  
- Sécurité & conformité associées : RBAC  
- Gestion des erreurs & rollback : rollback si stock insuffisant

**Implémentation recommandée**
- Tests : intégration stock, E2E ordre complet

**Monitoring & KPIs**
- Métriques à tracker : OEE, taux rebut  
- Alertes à configurer : ordre en retard  
- Dashboards Grafana à créer : Production KPIs

**Estimation d’effort**
- Story points : 13  
- Homme-jours : 12 + 3 + 1  
- Dépendances bloquantes : WMS
