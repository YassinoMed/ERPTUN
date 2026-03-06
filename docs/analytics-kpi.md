# Analytics & KPI

## F02 — Tableau de bord KPI configurable
**Catégorie** : Analytics  
**Priorité** : Must  
**Description courte** : Dashboard multi-modules personnalisable.  
**Valeur business** : Accélère la prise de décision.

**Spécifications fonctionnelles**
- User stories principales  
  - En tant que dirigeant, je personnalise mes KPIs.  
  - En tant que manager, je filtre par période.  
  - En tant qu’admin, je partage un template.  
- Critères d’acceptation  
  - Widgets configurables et persistés.  
  - Rafraîchissement < 5s pour 10 widgets.  
  - Export PDF.

**Spécifications techniques**
- Architecture & pattern à utiliser : service d’agrégation + cache  
- Schéma de données : dashboards, dashboard_widgets, widget_filters  
- APIs exposées : GET /api/kpi/widgets, POST /api/kpi/dashboards  
- Événements : WidgetConfigured  
- Intégrations externes nécessaires : Redis cache, PDF (spatie/browsershot)  
- Considérations multi-tenant : dashboards par tenant  
- Sécurité & conformité associées : RBAC  
- Gestion des erreurs & rollback : fallback sur cache stale

**Implémentation recommandée**
- Tech stack précise : Redis, spatie/browsershot 5.0  
- Code structure : app/Services/Kpi  
- Exemple de code clé  
  ```typescript
  async function computeWidget(metric, filters) {
    return Metrics.aggregate(metric, filters)
  }
  ```
- Tests à écrire : unitaires agrégations, intégration cache

**Monitoring & KPIs**
- Métriques à tracker : latence widgets, taux cache hit  
- Alertes à configurer : latence > 2s  
- Dashboards Grafana à créer : KPI Performance

**Estimation d’effort**
- Story points : 13  
- Homme-jours : 12 + 3 + 2  
- Dépendances bloquantes : modèles KPI
