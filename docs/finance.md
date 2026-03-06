# Finance & Ventes

## Comptabilité & Ventes
**Catégorie** : Finance  
**Priorité** : Must  
**Description courte** : Gérer devis, factures, achats et journaux.  
**Valeur business** : Accélère le cashflow et réduit les erreurs comptables.

**Spécifications fonctionnelles**
- User stories principales  
  - En tant que commercial, je crée un devis et le convertis en facture.  
  - En tant que comptable, je saisis une dépense et un achat.  
  - En tant que direction, je consulte le résultat mensuel.  
- Critères d’acceptation  
  - Devis converti conserve items et taxes.  
  - Paiement facture met à jour le statut.  
  - Journaux comptables équilibrés.

**Spécifications techniques**
- Architecture & pattern à utiliser : MVC + services de taxes et journaux  
- Schéma de données : quotations, invoices, invoice_items, bills, expenses, journal_entries  
- APIs exposées : POST /api/invoices, POST /api/payments  
- Événements : InvoiceCreated, PaymentReceived  
- Intégrations externes nécessaires : Stripe/PayPal/Mollie/Paystack  
- Considérations multi-tenant : created_by + index invoice_number unique par tenant  
- Sécurité & conformité associées : RBAC + journalisation  
- Gestion des erreurs & rollback : rollback sur création facture + items

**Implémentation recommandée**
- Tech stack précise : Laravel 11, PHPSpreadsheet  
- Code structure : app/Services/Accounting/InvoiceService.php  
- Exemple de code clé  
  ```typescript
  function applyPayment(invoice, amount) {
    invoice.paid += amount
    invoice.status = invoice.paid >= invoice.total ? "paid" : "partial"
  }
  ```
- Tests à écrire : unitaires taxes, intégration paiement gateway, E2E devis→facture

**Monitoring & KPIs**
- Métriques à tracker : DSO, taux impayés, marge brute  
- Alertes à configurer : facture échue > 15j  
- Dashboards Grafana à créer : Finance Cashflow

**Estimation d’effort**
- Story points : 20  
- Homme-jours : 16 + 4 + 2  
- Dépendances bloquantes : gateways paiement

---

## Paiements (Factures & Abonnements)
**Catégorie** : Finance  
**Priorité** : Must  
**Description courte** : Encaisser factures et abonnements SaaS.  
**Valeur business** : Assure le cashflow et les renouvellements.

**Spécifications fonctionnelles**
- User stories principales  
  - En tant que client, je paie une facture en ligne.  
  - En tant qu’entreprise, je règle un plan.  
  - En tant que finance, je réconcilie paiements.  
- Critères d’acceptation  
  - Paiement confirme et met à jour statut facture.  
  - Webhook valide l’état.  
  - Historique des paiements.

**Spécifications techniques**
- Architecture & pattern à utiliser : Controllers par gateway + Webhooks  
- Schéma de données : payments, payment_logs, orders  
- APIs exposées : POST /api/payments, POST /webhooks/stripe  
- Événements : PaymentSucceeded, PaymentFailed  
- Intégrations externes nécessaires : Stripe, PayPal, Mollie, Paystack, etc.  
- Considérations multi-tenant : created_by  
- Sécurité & conformité associées : signature webhook, idempotency key  
- Gestion des erreurs & rollback : retry webhook, compensation

**Implémentation recommandée**
- Tech stack précise : stripe/stripe-php 15.7, srmklive/paypal 3.0  
- Code structure : StripePaymentController, PaypalController  
- Exemple de code clé  
  ```typescript
  async function handleWebhook(event) {
    if (event.type === "payment_succeeded") return Payments.confirm(event.id)
  }
  ```
- Tests à écrire : intégration webhook, E2E paiement

**Monitoring & KPIs**
- Métriques à tracker : taux succès, temps confirmation  
- Alertes à configurer : échec webhook  
- Dashboards Grafana à créer : Payments

**Estimation d’effort**
- Story points : 13  
- Homme-jours : 12 + 3 + 2  
- Dépendances bloquantes : gateways actifs

---

## F10 — Relances & Scoring de risque
**Catégorie** : Finance  
**Priorité** : Must  
**Description courte** : Automatiser les relances et le scoring client.  
**Valeur business** : Réduit DSO et risques d’impayés.

**Spécifications fonctionnelles**
- User stories principales  
  - En tant que finance, je configure des séquences de relance.  
  - En tant que manager, je vois un score de risque.  
  - En tant que client, je peux opt-out SMS.  
- Critères d’acceptation  
  - Segmentation par historique paiement.  
  - Relance multicanal.  
  - Score recalculé quotidiennement.

**Spécifications techniques**
- Architecture & pattern à utiliser : DunningService + jobs planifiés  
- Schéma de données : dunning_rules, risk_scores, dunning_events  
- APIs exposées : POST /api/dunning/rules  
- Événements : DunningTriggered  
- Intégrations externes nécessaires : Email/SMS/WhatsApp  
- Considérations multi-tenant : created_by  
- Sécurité & conformité associées : consentement RGPD  
- Gestion des erreurs & rollback : retry notifications

**Implémentation recommandée**
- Tech stack précise : Laravel 11.44.1  
- Code structure : app/Services/Dunning  
- Exemple de code clé  
  ```typescript
  function computeRiskScore(history) {
    return history.latePayments * 10 + history.totalOverdue
  }
  ```
- Tests à écrire : intégration scoring, E2E relance

**Monitoring & KPIs**
- Métriques à tracker : DSO, taux recouvrement  
- Alertes à configurer : DSO > seuil  
- Dashboards Grafana à créer : Collections

**Estimation d’effort**
- Story points : 13  
- Homme-jours : 12 + 3 + 2  
- Dépendances bloquantes : paiements, notifications

---

## F11 — Exports & rapports programmés
**Catégorie** : Data  
**Priorité** : Must  
**Description courte** : Exports planifiés CSV/Excel.  
**Valeur business** : Automatisation du reporting.

**Spécifications fonctionnelles**
- User stories principales  
  - En tant que contrôleur, je programme un export hebdo.  
  - En tant que manager, je reçois un rapport.  
  - En tant qu’admin, je limite la taille.  
- Critères d’acceptation  
  - Exports récurrents configurables.  
  - Temps < 5 min sur 200k lignes.  
  - Permissions respectées.

**Spécifications techniques**
- Architecture & pattern à utiliser : Jobs + queue  
- Schéma de données : scheduled_exports, export_runs, export_files  
- APIs exposées : POST /api/exports/schedule  
- Événements : ExportGenerated, ExportFailed  
- Intégrations externes nécessaires : email, storage  
- Considérations multi-tenant : created_by  
- Sécurité & conformité associées : signed URLs  
- Gestion des erreurs & rollback : retry + notifications

**Implémentation recommandée**
- Tech stack précise : maatwebsite/excel 3.1  
- Code structure : app/Exports, app/Jobs  
- Exemple de code clé  
  ```typescript
  async function runExport(query, format) {
    const data = await query.execute()
    return Exporter.save(format, data)
  }
  ```
- Tests à écrire : intégration queue, E2E export

**Monitoring & KPIs**
- Métriques à tracker : temps export, taux succès  
- Alertes à configurer : export > 10 min  
- Dashboards Grafana à créer : Export Ops

**Estimation d’effort**
- Story points : 8  
- Homme-jours : 8 + 2 + 2  
- Dépendances bloquantes : queue, storage
