# Portail Client

## F05 — Portail client self-service
**Catégorie** : Expérience client  
**Priorité** : Must  
**Description courte** : Donner accès aux factures, projets, support.  
**Valeur business** : Réduit le coût support et améliore la satisfaction.

**Spécifications fonctionnelles**
- User stories principales  
  - En tant que client, je consulte mes factures.  
  - En tant que client, je paye en ligne.  
  - En tant que client, je suis l’avancement projet.  
- Critères d’acceptation  
  - Accès restreint aux données client.  
  - Paiement possible depuis le portail.  
  - Téléchargement sécurisé documents.

**Spécifications techniques**
- Architecture & pattern à utiliser : Front dédié + policies  
- Schéma de données : customer_portal_settings  
- APIs exposées : GET /api/portal/invoices  
- Événements : PortalLogin  
- Intégrations externes nécessaires : paiement  
- Considérations multi-tenant : isolation client  
- Sécurité & conformité associées : scope client + 2FA optionnel  
- Gestion des erreurs & rollback : gestion sessions expirées

**Implémentation recommandée**
- Tech stack précise : Laravel 11.44.1  
- Code structure : app/Http/Controllers/Portal  
- Exemple de code clé  
  ```typescript
  function canAccessInvoice(customerId, invoice) {
    return invoice.customer_id === customerId
  }
  ```
- Tests à écrire : E2E accès client, paiement

**Monitoring & KPIs**
- Métriques à tracker : taux d’usage portail  
- Alertes à configurer : échec login massif  
- Dashboards Grafana à créer : Portal Engagement

**Estimation d’effort**
- Story points : 13  
- Homme-jours : 10 + 3 + 2  
- Dépendances bloquantes : paiement
