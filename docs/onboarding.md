# Onboarding Entreprises

## F12 — Industrialiser l’onboarding des entreprises
**Catégorie** : SaaS  
**Priorité** : Should  
**Description courte** : Templates d’entreprise, import assisté, checklists d’activation et données de démo.  
**Valeur business** : Réduit le time-to-value.

**Spécifications fonctionnelles**
- User stories principales  
  - En tant qu’admin SaaS, je crée rapidement une entreprise prête à l’emploi.  
  - En tant qu’utilisateur, j’importe mes données avec mapping guidé.  
  - En tant que support, je consulte les erreurs d’import.  
- Critères d’acceptation  
  - Temps d’onboarding réduit de 40 %.  
  - 95 % des imports réussis du premier coup.  
  - Rollback complet si import échoue.

**Spécifications techniques**
- Architecture & pattern à utiliser : ImportService + Templates  
- Schéma de données : tenant_templates, import_jobs, import_errors  
- APIs exposées : POST /api/onboarding/import  
- Événements : ImportCompleted, ImportFailed  
- Intégrations externes nécessaires : CSV/Excel  
- Considérations multi-tenant : isolation stricte  
- Sécurité & conformité associées : validation forte  
- Gestion des erreurs & rollback : transaction + rollback

**Implémentation recommandée**
- Tech stack précise : maatwebsite/excel 3.1  
- Code structure : app/Services/Onboarding  
- Exemple de code clé  
  ```typescript
  async function importRows(rows, mapper) {
    for (const row of rows) await mapper(row)
  }
  ```
- Tests à écrire : intégration import, E2E onboarding

**Monitoring & KPIs**
- Métriques à tracker : taux succès import  
- Alertes à configurer : échecs > 5%  
- Dashboards Grafana à créer : Onboarding Funnel

**Estimation d’effort**
- Story points : 13  
- Homme-jours : 12 + 3 + 2  
- Dépendances bloquantes : templates
