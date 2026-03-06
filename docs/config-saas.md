# Configuration & SaaS

## Paramètres système & Branding
**Catégorie** : Configuration  
**Priorité** : Must  
**Description courte** : Gérer la configuration par tenant.  
**Valeur business** : Permet la personnalisation et l’autonomie.

**Spécifications fonctionnelles**
- User stories principales  
  - En tant qu’admin, je configure SMTP.  
  - En tant qu’admin, je change logo/couleurs.  
  - En tant que DSI, j’active/désactive la landing page.
- Critères d’acceptation  
  - Settings persistés par tenant.  
  - Modifications visibles en < 1 min.  
  - Validation des champs critiques.

**Spécifications techniques**
- Architecture & pattern à utiliser : Utility::settings + cache  
- Schéma de données : settings(id, name, value, created_by)  
- APIs exposées : PUT /api/settings  
- Événements : SettingUpdated  
- Intégrations externes nécessaires : none  
- Considérations multi-tenant : settings par created_by  
- Sécurité & conformité associées : validation stricte  
- Gestion des erreurs & rollback : rollback si upload logo échoue

**Implémentation recommandée**
- Tech stack précise : Laravel 11.44.1  
- Code structure : SystemController, Utility.php  
- Exemple de code clé  
  ```typescript
  async function updateSetting(name, value, tenantId) {
    return Settings.upsert({ name, value, created_by: tenantId })
  }
  ```
- Tests à écrire : unitaires validation, intégration SMTP

**Monitoring & KPIs**
- Métriques à tracker : changements settings  
- Alertes à configurer : SMTP invalides  
- Dashboards Grafana à créer : Settings Activity

**Estimation d’effort**
- Story points : 5  
- Homme-jours : 4 + 1  
- Dépendances bloquantes : Auth

---

## Plans & Abonnements
**Catégorie** : SaaS  
**Priorité** : Must  
**Description courte** : Monétiser et limiter les fonctionnalités.  
**Valeur business** : Génère le revenu récurrent.

**Spécifications fonctionnelles**
- User stories principales  
  - En tant qu’admin, je crée un plan et ses modules.  
  - En tant qu’entreprise, je change de plan.  
  - En tant que finance, je vois les renouvellements.
- Critères d’acceptation  
  - Limites users et storage appliquées.  
  - Modules visibles selon plan.  
  - Downgrade bloque si dépassement.

**Spécifications techniques**
- Architecture & pattern à utiliser : PlanService + gating User::show_*  
- Schéma de données : plans, plan_features, orders, subscriptions  
- APIs exposées : POST /api/plans, POST /api/subscriptions  
- Événements : PlanUpgraded, PlanDowngraded  
- Intégrations externes nécessaires : gateways paiement  
- Considérations multi-tenant : plan par tenant  
- Sécurité & conformité associées : RBAC admin  
- Gestion des erreurs & rollback : transaction sur upgrade

**Implémentation recommandée**
- Tech stack précise : Laravel 11.44.1  
- Code structure : PlanController, User.php  
- Exemple de code clé  
  ```typescript
  function canAccessModule(user, module) {
    return user.plan?.features?.includes(module)
  }
  ```
- Tests à écrire : unitaires gating, intégration upgrade

**Monitoring & KPIs**
- Métriques à tracker : MRR, churn  
- Alertes à configurer : échecs renouvellement  
- Dashboards Grafana à créer : SaaS Revenue

**Estimation d’effort**
- Story points : 8  
- Homme-jours : 6 + 2  
- Dépendances bloquantes : paiements

---

## Stockage fichiers & Limites
**Catégorie** : Infrastructure  
**Priorité** : Must  
**Description courte** : Gérer uploads et quotas.  
**Valeur business** : Contrôle coûts et sécurité.

**Spécifications fonctionnelles**
- User stories principales  
  - En tant qu’utilisateur, je téléverse un document.  
  - En tant qu’admin, je configure S3.  
  - En tant que finance, je vois l’usage.
- Critères d’acceptation  
  - Quota appliqué par tenant.  
  - Téléchargement sécurisé.  
  - Suppression libère l’espace.

**Spécifications techniques**
- Architecture & pattern à utiliser : StorageService + Flysystem  
- Schéma de données : files, storage_usage  
- APIs exposées : POST /api/files, GET /storage/uploads/{path}  
- Événements : FileUploaded, QuotaExceeded  
- Intégrations externes nécessaires : S3/Wasabi  
- Considérations multi-tenant : quota par created_by  
- Sécurité & conformité associées : signed URLs  
- Gestion des erreurs & rollback : rollback upload partiel

**Implémentation recommandée**
- Tech stack précise : league/flysystem-aws-s3-v3 3.28  
- Code structure : Utility.php  
- Exemple de code clé  
  ```typescript
  async function ensureQuota(used, add, limit) {
    if (used + add > limit) throw new Error("quota_exceeded")
  }
  ```
- Tests à écrire : intégration upload, E2E quota

**Monitoring & KPIs**
- Métriques à tracker : stockage utilisé  
- Alertes à configurer : > 90% quota  
- Dashboards Grafana à créer : Storage Usage

**Estimation d’effort**
- Story points : 5  
- Homme-jours : 4 + 1  
- Dépendances bloquantes : storage externe
