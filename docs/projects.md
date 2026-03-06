# Projets & Budgets

## Projets, Tâches, Temps
**Catégorie** : Delivery  
**Priorité** : Must  
**Description courte** : Suivre la progression projet et la charge.  
**Valeur business** : Améliore le pilotage et la rentabilité.

**Spécifications fonctionnelles**
- User stories principales  
  - En tant que chef de projet, je crée un projet et ses tâches.  
  - En tant que consultant, je saisis mon temps.  
  - En tant que client, je vois l’avancement.  
- Critères d’acceptation  
  - Calcul d’avancement basé sur étapes de tâches.  
  - Timesheet validé par manager.  
  - Export des temps par projet.

**Spécifications techniques**
- Architecture & pattern à utiliser : MVC + service de progression  
- Schéma de données : projects, project_tasks, task_stages, timesheets, time_trackers  
- APIs exposées : POST /api/projects, POST /api/timesheets  
- Événements : TaskCompleted, TimesheetSubmitted  
- Intégrations externes nécessaires : Google Calendar pour planning  
- Considérations multi-tenant : created_by  
- Sécurité & conformité associées : RBAC “project.*”, “timesheet.*”  
- Gestion des erreurs & rollback : rollback lors assignation bulk

**Implémentation recommandée**
- Tech stack précise : Laravel 11  
- Code structure : app/Services/Project/ProgressService.php  
- Exemple de code clé  
  ```python
  def project_progress(done, total):
      return 0 if total == 0 else round(done / total * 100, 2)
  ```
- Tests à écrire : unitaires progression, intégration timesheet, E2E projet complet

**Monitoring & KPIs**
- Métriques à tracker : taux respect délais, charge vs capacité  
- Alertes à configurer : retard > 10 %  
- Dashboards Grafana à créer : Project Delivery

**Estimation d’effort**
- Story points : 13  
- Homme-jours : 10 + 3 + 1  
- Dépendances bloquantes : planning RH

---

## F04 — Budgets & Forecasts Projet
**Catégorie** : Finance/Projet  
**Priorité** : Must  
**Description courte** : Suivre budget vs réel par projet.  
**Valeur business** : Maîtrise la marge et prévient les dérives.

**Spécifications fonctionnelles**
- User stories principales  
  - En tant que chef de projet, je définis un budget.  
  - En tant que finance, je vois l’écart réel/budget.  
  - En tant que direction, je prévois la marge.  
- Critères d’acceptation  
  - Écart > 10% déclenche alerte.  
  - Budgets par phase.  
  - Multi-devises supportées.

**Spécifications techniques**
- Architecture & pattern à utiliser : BudgetService + reporting  
- Schéma de données : project_budgets, budget_lines, cost_actuals  
- APIs exposées : POST /api/projects/{id}/budget  
- Événements : BudgetExceeded  
- Intégrations externes nécessaires : compta  
- Considérations multi-tenant : created_by  
- Sécurité & conformité associées : RBAC  
- Gestion des erreurs & rollback : rollback calculs

**Implémentation recommandée**
- Tech stack précise : Laravel 11  
- Code structure : app/Services/Project/BudgetService.php  
- Exemple de code clé  
  ```typescript
  function variance(actual, budget) {
    return budget === 0 ? 0 : ((actual - budget) / budget) * 100
  }
  ```
- Tests à écrire : unitaires calculs, intégration alertes

**Monitoring & KPIs**
- Métriques à tracker : variance budget, marge prévue  
- Alertes à configurer : dépassement > 10%  
- Dashboards Grafana à créer : Project Finance

**Estimation d’effort**
- Story points : 13  
- Homme-jours : 12 + 3 + 2  
- Dépendances bloquantes : timesheets + factures
