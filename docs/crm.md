# CRM

## Gestion des Leads, Deals, Pipelines
**Catégorie** : Business  
**Priorité** : Must  
**Description courte** : Gérer le cycle commercial de la prospection à la signature.  
**Valeur business** : Augmente le taux de conversion et la visibilité pipeline.

**Spécifications fonctionnelles**
- User stories principales  
  - En tant que commercial, je crée un lead avec source et statut.  
  - En tant que manager, je visualise les deals par pipeline et étape.  
  - En tant qu’équipe, je partage notes et activités d’un deal.  
  - En tant que direction, je mesure le taux de conversion et valeur prévisionnelle.
- Critères d’acceptation  
  - Création/édition/suppression lead et deal auditables.  
  - Pipeline personnalisable avec étapes ordonnées.  
  - Conversion lead → deal conserve l’historique.  
  - KPIs pipeline (taux, cycle, valeur) calculés sur période.

**Spécifications techniques**
- Architecture & pattern à utiliser : MVC Laravel + services métiers (DealService, PipelineService)  
- Schéma de données :  
  - leads(id, company_id/created_by, name, email, source, status, owner_id, created_at)  
  - deals(id, company_id, name, amount, stage_id, pipeline_id, probability, close_date)  
  - pipelines(id, company_id, name, is_default)  
  - stages(id, pipeline_id, name, order, is_won, is_lost)  
  - indexes : (company_id), (pipeline_id, order), (owner_id)  
- APIs exposées :  
  - REST: GET /api/crm/leads, POST /api/crm/deals  
  - Payload exemple  
    ```json
    { "name": "ACME Renewal", "amount": 12000, "stage_id": 3, "pipeline_id": 1 }
    ```
- Événements : LeadCreated, DealStageChanged, DealWon  
- Intégrations externes nécessaires : Email/Slack/Telegram pour notifications, Google Calendar pour tâches  
- Considérations multi-tenant : filtrage created_by, indexation par company_id  
- Sécurité & conformité associées : RBAC par permissions “lead.*” / “deal.*”  
- Gestion des erreurs & rollback : transactions DB sur conversion lead→deal

**Implémentation recommandée**
- Tech stack précise : PHP 8.2, Laravel 11.44.1, Sanctum 4.0, MySQL 8  
- Code structure : app/Http/Controllers/LeadController.php, app/Services/CRM/DealService.php  
- Exemple de code clé  
  ```typescript
  async function convertLead(leadId: string, input: DealInput) {
    const lead = await Leads.findById(leadId)
    const deal = await Deals.create({ ...input, leadId })
    await Leads.update(leadId, { status: "converted" })
    return deal
  }
  ```
- Tests à écrire : unitaires KPI conversion, intégration conversion lead→deal, E2E pipeline complet, chaos suppression d’étape

**Monitoring & KPIs**
- Métriques à tracker : taux conversion, cycle moyen, valeur pipeline  
- Alertes à configurer : chute conversion > 20% sur 7j  
- Dashboards Grafana à créer : CRM Overview, Pipeline Health

**Estimation d’effort**
- Story points : 13  
- Homme-jours : 12 (dev) + 3 (QA) + 1 (DevOps)  
- Dépendances bloquantes : RBAC, notifications
