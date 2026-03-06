# HRM

## Employés, Présence, Congés, Paie
**Catégorie** : Business  
**Priorité** : Must  
**Description courte** : Centraliser la gestion RH et la conformité interne.  
**Valeur business** : Réduit les erreurs de paie et améliore la planification RH.

**Spécifications fonctionnelles**
- User stories principales  
  - En tant que RH, je crée un employé et son contrat.  
  - En tant que manager, je valide des congés.  
  - En tant que RH, je génère les bulletins mensuels.  
  - En tant que salarié, je consulte ma présence.
- Critères d’acceptation  
  - Workflow congés validé par manager.  
  - Présence exportable en CSV.  
  - Paie calculée avec règles d’allocations/déductions.  
  - Historique RH auditable.

**Spécifications techniques**
- Architecture & pattern à utiliser : MVC + services Paie  
- Schéma de données : employees, attendances, leaves, payslips, allowances, deductions  
- APIs exposées : POST /api/hrm/leave, GET /api/hrm/payslips  
- Événements : LeaveRequested, LeaveApproved, PayslipGenerated  
- Intégrations externes nécessaires : Email/SMS pour notifications  
- Considérations multi-tenant : created_by sur employés et paie  
- Sécurité & conformité associées : accès salarié limité à ses données  
- Gestion des erreurs & rollback : transaction lors génération paie

**Implémentation recommandée**
- Tech stack précise : Laravel 11, MySQL 8  
- Code structure : app/Services/HRM/PayrollService.php  
- Exemple de code clé  
  ```python
  def compute_net_pay(gross, allowances, deductions):
      return gross + sum(allowances) - sum(deductions)
  ```
- Tests à écrire : unitaires calcul paie, intégration validation congés, E2E création employé

**Monitoring & KPIs**
- Métriques à tracker : taux d’absentéisme, délai validation congés  
- Alertes à configurer : paie non générée J-2  
- Dashboards Grafana à créer : HRM Overview, Leave SLA

**Estimation d’effort**
- Story points : 21  
- Homme-jours : 18 + 4 + 2  
- Dépendances bloquantes : RBAC

---

## F09 — Planifier les ressources RH et la charge
**Catégorie** : RH  
**Priorité** : Should  
**Description courte** : Ajouter un planning de capacité, gestion de disponibilités et affectations multi-projets.  
**Valeur business** : Optimise l’utilisation des équipes.

**Spécifications fonctionnelles**
- User stories principales  
  - En tant que RH, je définis disponibilités.  
  - En tant que manager, j’affecte à projets.  
  - En tant qu’employé, je consulte mon planning.  
- Critères d’acceptation  
  - Conflits de charge détectés.  
  - Planning multi-projets.  
  - Congés pris en compte.

**Spécifications techniques**
- Architecture & pattern à utiliser : PlanningService + calendrier  
- Schéma de données : resource_plans, allocations, availability_slots  
- APIs exposées : POST /api/hrm/allocations  
- Événements : AllocationConflictDetected  
- Intégrations externes nécessaires : Google Calendar  
- Considérations multi-tenant : created_by  
- Sécurité & conformité associées : RBAC  
- Gestion des erreurs & rollback : rollback si conflit

**Implémentation recommandée**
- Tech stack précise : Laravel 11  
- Code structure : app/Services/HRM/PlanningService  
- Exemple de code clé  
  ```typescript
  function detectConflict(slots, allocation) {
    return slots.some(s => s.overlaps(allocation))
  }
  ```
- Tests à écrire : intégration conflits, E2E affectation

**Monitoring & KPIs**
- Métriques à tracker : taux d’occupation  
- Alertes à configurer : surcharge > 120%  
- Dashboards Grafana à créer : Resource Planning

**Estimation d’effort**
- Story points : 13  
- Homme-jours : 12 + 3 + 2  
- Dépendances bloquantes : HRM
