# Workflows d’Approbation

## F01 — Workflows d’approbation multi-niveaux
**Catégorie** : Gouvernance  
**Priorité** : Must  
**Description courte** : Configurer des validations multi-niveaux par règles.  
**Valeur business** : Réduit les risques et améliore la conformité.

**Spécifications fonctionnelles**
- User stories principales  
  - En tant que DAF, je définis un workflow par seuil.  
  - En tant que manager, je délègue une approbation.  
  - En tant qu’admin, je vois l’historique des décisions.  
- Critères d’acceptation  
  - Règles par montant, département, type document.  
  - Escalade automatique en cas de délai dépassé.  
  - Refus avec commentaire obligatoire.

**Spécifications techniques**
- Architecture & pattern à utiliser : moteur de règles + orchestrateur  
- Schéma de données : approval_flows, approval_steps, approval_requests, approval_actions  
- APIs exposées : POST /api/approvals/submit, POST /api/approvals/{id}/action  
- Événements : ApprovalRequested, ApprovalApproved, ApprovalRejected  
- Intégrations externes nécessaires : notifications (email/Slack)  
- Considérations multi-tenant : flows par tenant  
- Sécurité & conformité associées : RBAC + audit log  
- Gestion des erreurs & rollback : rollback sur action invalide

**Implémentation recommandée**
- Tech stack précise : Laravel 11.44.1, Redis  
- Code structure : app/Services/Approvals/ApprovalEngine.php  
- Exemple de code clé  
  ```typescript
  function nextApprover(flow, context) {
    return flow.steps.find(s => s.rule.matches(context))
  }
  ```
- Tests à écrire : unitaires règles, intégration escalade, E2E achat→validation

**Monitoring & KPIs**
- Métriques à tracker : délai moyen d’approbation  
- Alertes à configurer : approbation > SLA  
- Dashboards Grafana à créer : Approval Flow

**Estimation d’effort**
- Story points : 21  
- Homme-jours : 22 + 6 + 3  
- Dépendances bloquantes : audit log, notifications
