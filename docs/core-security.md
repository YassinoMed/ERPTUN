# Core sécurité & identité

## Authentification & Sessions
**Catégorie** : Sécurité  
**Priorité** : Must  
**Description courte** : Authentifier les utilisateurs et sécuriser les sessions.  
**Valeur business** : Réduit le risque d’accès non autorisé.

**Spécifications fonctionnelles**
- User stories principales  
  - En tant qu’utilisateur, je me connecte avec email/mot de passe.  
  - En tant qu’utilisateur, je réinitialise mon mot de passe.  
  - En tant qu’admin, j’impose la vérification email.
- Critères d’acceptation  
  - Connexion avec throttling et blocage après N échecs.  
  - Réinitialisation via token expirant.  
  - Sessions invalidées lors du logout.

**Spécifications techniques**
- Architecture & pattern à utiliser : Laravel Auth + middleware  
- Schéma de données : users(id, email, password, email_verified_at, created_by, type)  
- APIs exposées : POST /login, POST /forgot-password, POST /logout  
- Événements : UserLoggedIn, UserLoggedOut  
- Intégrations externes nécessaires : none  
- Considérations multi-tenant : creatorId() pour contexte  
- Sécurité & conformité associées : hash bcrypt, CSRF, throttle  
- Gestion des erreurs & rollback : messages neutres, pas de détails sur existence compte

**Implémentation recommandée**
- Tech stack précise : Laravel 11.44.1, PHP 8.2  
- Code structure : app/Http/Controllers/Auth  
- Exemple de code clé  
  ```typescript
  async function login(email: string, password: string) {
    const user = await Users.findByEmail(email)
    if (!user || !user.verifyPassword(password)) throw new Error("unauthorized")
    return Sessions.create(user.id)
  }
  ```
- Tests à écrire : unitaires hash, intégration login, E2E reset password

**Monitoring & KPIs**
- Métriques à tracker : taux d’échec login, durée session  
- Alertes à configurer : spike d’échecs login  
- Dashboards Grafana à créer : Security Overview

**Estimation d’effort**
- Story points : 5  
- Homme-jours : 4 (dev) + 1 (QA)  
- Dépendances bloquantes : none

---

## Multi-entreprises (Tenancy)
**Catégorie** : Architecture  
**Priorité** : Must  
**Description courte** : Cloisonner les données par entreprise.  
**Valeur business** : Garantit l’isolation et la conformité.

**Spécifications fonctionnelles**
- User stories principales  
  - En tant qu’utilisateur, je ne vois que les données de mon entreprise.  
  - En tant qu’admin SaaS, je crée un tenant.  
  - En tant que support, j’impersonate un client.
- Critères d’acceptation  
  - Toutes les requêtes filtrées par created_by.  
  - Impersonation tracée.  
  - Aucun accès cross-tenant.

**Spécifications techniques**
- Architecture & pattern à utiliser : Global scopes + creatorId()  
- Schéma de données : ajout created_by sur tables métier  
- APIs exposées : POST /api/tenants  
- Événements : TenantCreated  
- Intégrations externes nécessaires : none  
- Considérations multi-tenant : index (created_by, id)  
- Sécurité & conformité associées : policies par tenant  
- Gestion des erreurs & rollback : fallback creatorId si contexte manquant

**Implémentation recommandée**
- Tech stack précise : Laravel 11.44.1  
- Code structure : app/Models/User.php, scopes sur modèles  
- Exemple de code clé  
  ```typescript
  function scopeTenant(query, creatorId) {
    return query.where("created_by", creatorId)
  }
  ```
- Tests à écrire : unitaires scope, intégration permissions, E2E impersonation

**Monitoring & KPIs**
- Métriques à tracker : tentatives cross-tenant  
- Alertes à configurer : accès interdit répété  
- Dashboards Grafana à créer : Tenant Isolation

**Estimation d’effort**
- Story points : 8  
- Homme-jours : 6 + 2  
- Dépendances bloquantes : RBAC

---

## Rôles & Permissions (RBAC)
**Catégorie** : Sécurité  
**Priorité** : Must  
**Description courte** : Contrôler l’accès aux actions et données.  
**Valeur business** : Réduit les erreurs et fuites de données.

**Spécifications fonctionnelles**
- User stories principales  
  - En tant qu’admin, je crée un rôle avec permissions.  
  - En tant que manager, j’assigne un rôle à un utilisateur.  
  - En tant qu’auditeur, je vérifie les accès.
- Critères d’acceptation  
  - Permissions vérifiées sur chaque action sensible.  
  - Traçabilité des changements de rôles.  
  - Export des rôles.

**Spécifications techniques**
- Architecture & pattern à utiliser : spatie/laravel-permission  
- Schéma de données : roles, permissions, model_has_roles, role_has_permissions  
- APIs exposées : POST /api/roles, POST /api/permissions  
- Événements : RoleAssigned, PermissionUpdated  
- Intégrations externes nécessaires : none  
- Considérations multi-tenant : rôles par tenant  
- Sécurité & conformité associées : least privilege  
- Gestion des erreurs & rollback : transaction sur assignations multiples

**Implémentation recommandée**
- Tech stack précise : spatie/laravel-permission 6.9  
- Code structure : RoleController, PermissionController  
- Exemple de code clé  
  ```typescript
  async function assignRole(userId, roleId) {
    await RoleAssignments.create({ userId, roleId })
  }
  ```
- Tests à écrire : unitaires policies, intégration assignation

**Monitoring & KPIs**
- Métriques à tracker : changements de rôles  
- Alertes à configurer : ajout rôle admin  
- Dashboards Grafana à créer : RBAC Changes

**Estimation d’effort**
- Story points : 5  
- Homme-jours : 4 + 1  
- Dépendances bloquantes : Auth

---

## F07 — Journal d’audit centralisé
**Catégorie** : Sécurité/Conformité  
**Priorité** : Should  
**Description courte** : Traçabilité complète des actions sensibles.  
**Valeur business** : Facilite audits et conformité.

**Spécifications fonctionnelles**
- User stories principales  
  - En tant qu’auditeur, je recherche une action.  
  - En tant que DSI, je configure la rétention.  
  - En tant que manager, j’exporte le journal.
- Critères d’acceptation  
  - 100% des actions sensibles loggées.  
  - Export CSV possible.  
  - Rétention configurable.

**Spécifications techniques**
- Architecture & pattern à utiliser : Event Listeners + AuditLogService  
- Schéma de données : audit_logs(id, tenant_id, actor_id, action, entity, payload, ip)  
- APIs exposées : GET /api/audit/logs  
- Événements : AuditLogged  
- Intégrations externes nécessaires : SIEM optionnel  
- Considérations multi-tenant : partition par tenant  
- Sécurité & conformité associées : accès restreint  
- Gestion des erreurs & rollback : fallback sync si queue échoue

**Implémentation recommandée**
- Tech stack précise : Laravel 11.44.1  
- Code structure : app/Listeners/AuditLogger  
- Exemple de code clé  
  ```typescript
  async function logAction(entry) {
    return AuditLogs.create(entry)
  }
  ```
- Tests à écrire : intégration logs, E2E export

**Monitoring & KPIs**
- Métriques à tracker : volume logs, erreurs d’écriture  
- Alertes à configurer : drop logs  
- Dashboards Grafana à créer : Audit Activity

**Estimation d’effort**
- Story points : 8  
- Homme-jours : 8 + 2  
- Dépendances bloquantes : RBAC
