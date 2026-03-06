# Proposition ERP microservices — Architecture & Roadmap

## Finances & Trésorerie

### Gestion de la trésorerie multi-entités
- Fonctionnalité : consolidation temps réel des positions bancaires, cash pools et flux de trésorerie.
- Bénéfices : décisions de placement optimisées, réduction des soldes dormants.
- KPIs : cash balance accuracy, idle cash ratio, DSO.
- Exigences : microservices Treasury Position, Bank Connectivity, Cash Pooling ; événements TreasuryPositionUpdated, BankStatementImported ; APIs /treasury/positions, /banks/statements.
- Sécurité/Conformité : mTLS inter-services, OAuth2/JWT, RBAC par entité, audit immuable.
- Roadmap itérative : MVP par service Treasury Position + import MT940 ; itération 2 cash pooling + alertes.
- Critères de succès : consolidation < 5 min, disponibilité 99.9 %.

### Prévisions de trésorerie
- Fonctionnalité : prévision 13 semaines basée sur factures, commandes et saisonnalité.
- Bénéfices : planification liquidités fiable.
- KPIs : forecast accuracy, variance vs réalisé.
- Exigences : microservices Cash Forecasting, Invoices, Orders ; événements InvoiceIssued, OrderConfirmed ; APIs /treasury/forecast.
- Sécurité/Conformité : chiffrement au repos, contrôle d’accès par BU.
- Roadmap itérative : MVP prévision court terme ; itération 2 scénarios et stress tests.
- Critères de succès : précision > 85 % à 4 semaines.

### Paiements & approbations
- Fonctionnalité : workflows d’approbation, batch SEPA/Swift, paiements massifs.
- Bénéfices : réduction fraude, conformité interne.
- KPIs : payment cycle time, exception rate.
- Exigences : microservices Payments, Approval Workflow ; événements PaymentInitiated, PaymentApproved, PaymentRejected ; APIs /payments, /approvals.
- Sécurité/Conformité : MFA, segregation of duties, signature numérique.
- Roadmap itérative : MVP paiements internes ; itération 2 multi-banques.
- Critères de succès : 95 % paiements sans exception.

## Comptabilité & Reporting

### Comptabilité générale multi-référentiels
- Fonctionnalité : GL multi-IFRS/Local GAAP, règles automatiques.
- Bénéfices : conformité accélérée et fiable.
- KPIs : close cycle time, journal error rate.
- Exigences : microservices General Ledger, Accounting Rules, Journal ; événements JournalEntryPosted ; APIs /gl/entries.
- Sécurité/Conformité : audit immuable, contrôles SoX.
- Roadmap itérative : MVP GL + règles basiques ; itération 2 multi-référentiels.
- Critères de succès : clôture mensuelle < 5 jours.

### Comptes clients/fournisseurs
- Fonctionnalité : lettrage, relances, scoring.
- Bénéfices : amélioration cash et réduction litiges.
- KPIs : DSO, DPO, dispute rate.
- Exigences : microservices AR, AP, Collections ; événements InvoiceOverdue, PaymentReceived ; APIs /ar/invoices, /ap/bills.
- Sécurité/Conformité : contrôle d’accès par tiers, piste d’audit.
- Roadmap itérative : MVP AR/AP ; itération 2 relances automatisées.
- Critères de succès : DSO -10 %.

### Consolidation & reporting réglementaire
- Fonctionnalité : consolidation groupe, mapping intercos, reporting réglementaire.
- Bénéfices : reporting fiable et consolidé.
- KPIs : consolidation cycle time.
- Exigences : microservices Consolidation, Intercompany ; événements ConsolidationRunCompleted ; APIs /consolidation/runs.
- Sécurité/Conformité : contrôles d’intégrité, journalisation.
- Roadmap itérative : MVP consolidation basique ; itération 2 éliminations intercos.
- Critères de succès : consolidation < 48 h.

## Ressources Humaines (RH)

### Dossier salarié & onboarding
- Fonctionnalité : gestion du cycle de vie employé, documents, onboarding.
- Bénéfices : conformité RH et onboarding accéléré.
- KPIs : onboarding time, document completeness.
- Exigences : microservices Employee Core, Document ; événements EmployeeCreated ; APIs /hr/employees.
- Sécurité/Conformité : PII chiffrées, RGPD, journal d’accès.
- Roadmap itérative : MVP dossier salarié ; itération 2 workflows onboarding.
- Critères de succès : 100 % dossiers complets.

### Paie & déclarations
- Fonctionnalité : calcul de paie, déclarations, multi-règles.
- Bénéfices : fiabilité de la paie, conformité locale.
- KPIs : payroll accuracy, correction rate.
- Exigences : microservices Payroll, Time Tracking, Benefits ; événements TimesheetApproved, PayrollRunCompleted ; APIs /payroll/runs.
- Sécurité/Conformité : séparation des rôles, chiffrement fort.
- Roadmap itérative : MVP paie standard ; itération 2 multi-pays.
- Critères de succès : erreurs < 0.5 %.

### Talent & performance
- Fonctionnalité : objectifs, évaluations, compétences.
- Bénéfices : alignement stratégique et montée en compétences.
- KPIs : review completion rate, skill gap index.
- Exigences : microservices Talent, Learning ; événements ReviewCompleted, SkillUpdated ; APIs /talent/reviews.
- Sécurité/Conformité : confidentialité renforcée, accès restreint.
- Roadmap itérative : MVP évaluations ; itération 2 plans de succession.
- Critères de succès : 95 % campagnes clôturées.

## Achats & Approvisionnements

### Sourcing & appels d’offres
- Fonctionnalité : RFQ, scoring, décision multi-critères.
- Bénéfices : réduction coûts et risques fournisseur.
- KPIs : savings %, cycle RFQ.
- Exigences : microservices Sourcing, Supplier ; événements RFQPublished, BidSubmitted ; APIs /procurement/rfqs.
- Sécurité/Conformité : traçabilité des décisions, audit.
- Roadmap itérative : MVP RFQ ; itération 2 scoring automatique.
- Critères de succès : savings > 5 %.

### Commandes fournisseurs & réception
- Fonctionnalité : PO, réception, 3-way match.
- Bénéfices : contrôle des dépenses.
- KPIs : match rate, PO cycle time.
- Exigences : microservices Purchase Orders, Receiving, AP ; événements POApproved, GoodsReceived ; APIs /procurement/pos.
- Sécurité/Conformité : contrôle budget et plafonds.
- Roadmap itérative : MVP PO ; itération 2 3-way match.
- Critères de succès : 98 % match automatique.

### Gestion fournisseurs & risques
- Fonctionnalité : qualification, conformité, scoring risques.
- Bénéfices : résilience supply chain.
- KPIs : supplier risk score, compliance rate.
- Exigences : microservices Supplier, Risk ; événements SupplierRiskUpdated ; APIs /suppliers.
- Sécurité/Conformité : KYC, sanctions screening.
- Roadmap itérative : MVP référentiel ; itération 2 monitoring continu.
- Critères de succès : 100 % fournisseurs validés.

## Ventes & CRM

### Prospection & pipeline
- Fonctionnalité : leads, opportunités, forecast.
- Bénéfices : visibilité revenus et prévision fiable.
- KPIs : win rate, pipeline coverage.
- Exigences : microservices CRM Core, Sales Forecast ; événements LeadQualified, OpportunityWon ; APIs /crm/leads, /crm/opportunities.
- Sécurité/Conformité : contrôle d’accès par équipe.
- Roadmap itérative : MVP pipeline ; itération 2 scoring IA.
- Critères de succès : forecast accuracy > 80 %.

### Devis & commande client
- Fonctionnalité : CPQ, conversion en commande.
- Bénéfices : réduction cycle vente.
- KPIs : quote-to-order time.
- Exigences : microservices CPQ, Sales Orders, Pricing ; événements QuoteAccepted, SalesOrderCreated ; APIs /sales/quotes, /sales/orders.
- Sécurité/Conformité : validation prix, plafonds de remise.
- Roadmap itérative : MVP devis ; itération 2 bundles complexes.
- Critères de succès : cycle devis -20 %.

### Facturation & recouvrement
- Fonctionnalité : génération factures, relances.
- Bénéfices : cash accéléré.
- KPIs : DSO, invoice dispute rate.
- Exigences : microservices Billing, AR, Collections ; événements InvoiceIssued ; APIs /billing/invoices.
- Sécurité/Conformité : intégrité factures, audit.
- Roadmap itérative : MVP facturation ; itération 2 relances.
- Critères de succès : DSO -10 %.

## Stocks & Logistique

### Gestion des stocks multi-sites
- Fonctionnalité : stock temps réel, lots/séries.
- Bénéfices : réduction ruptures.
- KPIs : inventory accuracy, stockout rate.
- Exigences : microservices Inventory, Warehouse ; événements StockAdjusted, StockReserved ; APIs /inventory/items.
- Sécurité/Conformité : contrôle d’accès par site.
- Roadmap itérative : MVP stock central ; itération 2 multi-sites.
- Critères de succès : accuracy > 98 %.

### Préparation & expédition
- Fonctionnalité : picking, packing, expédition multi-transporteurs.
- Bénéfices : expéditions rapides.
- KPIs : order cycle time, OTIF.
- Exigences : microservices Fulfillment, Shipping ; événements ShipmentCreated, ShipmentDelivered ; APIs /logistics/shipments.
- Sécurité/Conformité : traçabilité scans, audit.
- Roadmap itérative : MVP picking ; itération 2 transporteurs multiples.
- Critères de succès : OTIF > 95 %.

### Planification des approvisionnements
- Fonctionnalité : MRP, réapprovisionnement.
- Bénéfices : réduction coûts stocks.
- KPIs : inventory turns.
- Exigences : microservices Supply Planning, Inventory ; événements ReplenishmentSuggested ; APIs /supply/plan.
- Sécurité/Conformité : règles de sécurité stock.
- Roadmap itérative : MVP réappro auto ; itération 2 MRP avancé.
- Critères de succès : turns +15 %.

## Production & Maintenance

### Ordonnancement de production
- Fonctionnalité : planification capacités, ordres.
- Bénéfices : utilisation optimisée.
- KPIs : OEE, schedule adherence.
- Exigences : microservices Manufacturing Orders, Capacity ; événements WorkOrderReleased ; APIs /manufacturing/orders.
- Sécurité/Conformité : contrôle modifications planning.
- Roadmap itérative : MVP ordonnancement ; itération 2 APS.
- Critères de succès : OEE +5 %.

### Suivi atelier & qualité
- Fonctionnalité : traçabilité, non-conformités.
- Bénéfices : réduction rebuts.
- KPIs : scrap rate.
- Exigences : microservices Shopfloor, Quality ; événements NonConformityDetected ; APIs /quality/nonconformities.
- Sécurité/Conformité : traçabilité lots.
- Roadmap itérative : MVP suivi atelier ; itération 2 SPC.
- Critères de succès : scrap -10 %.

### Maintenance préventive
- Fonctionnalité : plans maintenance, tickets, historique.
- Bénéfices : réduction arrêts non planifiés.
- KPIs : MTBF, downtime.
- Exigences : microservices Maintenance, Asset ; événements MaintenanceScheduled, AssetDown ; APIs /maintenance/workorders.
- Sécurité/Conformité : historique inviolable.
- Roadmap itérative : MVP préventif ; itération 2 prédictif.
- Critères de succès : downtime -15 %.

## Projets & Services

### Gestion de projets
- Fonctionnalité : planning, coûts, ressources.
- Bénéfices : maîtrise marges.
- KPIs : project margin, schedule variance.
- Exigences : microservices Project, Resource ; événements ProjectCreated, TimeBooked ; APIs /projects.
- Sécurité/Conformité : contrôle budget projet.
- Roadmap itérative : MVP planning ; itération 2 EVM.
- Critères de succès : marge ±5 %.

### Gestion des temps & activités
- Fonctionnalité : timesheets, approbations.
- Bénéfices : facturation fiable.
- KPIs : timesheet compliance.
- Exigences : microservices Time Tracking, Approvals ; événements TimesheetSubmitted ; APIs /time/entries.
- Sécurité/Conformité : audit complet.
- Roadmap itérative : MVP saisie temps ; itération 2 mobile offline.
- Critères de succès : conformité > 95 %.

### Facturation projets
- Fonctionnalité : T&M, forfait, jalons.
- Bénéfices : cash accéléré.
- KPIs : billing cycle time.
- Exigences : microservices Project Billing, Billing ; événements MilestoneReached ; APIs /projects/billing.
- Sécurité/Conformité : contrôle des taux.
- Roadmap itérative : MVP T&M ; itération 2 jalons.
- Critères de succès : cycle -20 %.

## Immobilisations

### Gestion des actifs
- Fonctionnalité : registre, amortissements.
- Bénéfices : conformité fiscale.
- KPIs : asset accuracy.
- Exigences : microservices Asset Register, Depreciation ; événements AssetCapitalized ; APIs /assets.
- Sécurité/Conformité : audit.
- Roadmap itérative : MVP registre ; itération 2 amortissements complexes.
- Critères de succès : écarts inventaire < 1 %.

### Cessions & transferts
- Fonctionnalité : disposition, transferts internes.
- Bénéfices : contrôle valeur nette.
- KPIs : disposal cycle time.
- Exigences : microservices Asset, GL ; événements AssetDisposed ; APIs /assets/disposals.
- Sécurité/Conformité : validations multi-niveaux.
- Roadmap itérative : MVP cessions ; itération 2 transferts automatisés.
- Critères de succès : 100 % traçabilité.

### Inventaires périodiques
- Fonctionnalité : campagnes, rapprochements.
- Bénéfices : fiabilité comptable.
- KPIs : reconciliation rate.
- Exigences : microservices Asset Inventory ; événements AssetCounted ; APIs /assets/inventories.
- Sécurité/Conformité : contrôles d’intégrité.
- Roadmap itérative : MVP inventaire ; itération 2 mobilité.
- Critères de succès : écarts < 0.5 %.

## Qualité & Conformité

### Gestion documentaire
- Fonctionnalité : politiques, versions, approbations.
- Bénéfices : conformité renforcée.
- KPIs : document compliance.
- Exigences : microservices Document, Approval ; événements DocumentApproved ; APIs /docs.
- Sécurité/Conformité : rétention, signature.
- Roadmap itérative : MVP GED ; itération 2 workflows.
- Critères de succès : 100 % docs critiques validés.

### Audits & contrôles internes
- Fonctionnalité : plans d’audit, actions correctives.
- Bénéfices : réduction risques.
- KPIs : audit completion rate.
- Exigences : microservices Audit, Risk ; événements AuditFindingRaised ; APIs /audit.
- Sécurité/Conformité : traçabilité complète.
- Roadmap itérative : MVP audits ; itération 2 plans correctifs.
- Critères de succès : 95 % actions clôturées.

### Conformité réglementaire
- Fonctionnalité : contrôles automatiques, alertes.
- Bénéfices : réduction pénalités.
- KPIs : compliance incidents.
- Exigences : microservices Compliance Rules ; événements ComplianceBreachDetected ; APIs /compliance/checks.
- Sécurité/Conformité : journalisation inviolable.
- Roadmap itérative : MVP règles clés ; itération 2 extension sectorielle.
- Critères de succès : incidents < seuil défini.

## Data & BI

### Tableaux de bord exécutifs
- Fonctionnalité : KPIs consolidés CFO/COO/DSI.
- Bénéfices : pilotage rapide.
- KPIs : refresh latency.
- Exigences : microservices Metrics, Data Aggregation ; événements KPIComputed ; APIs /bi/kpis.
- Sécurité/Conformité : accès par profil.
- Roadmap itérative : MVP dashboards exécutifs ; itération 2 drill-down.
- Critères de succès : rafraîchissement < 15 min.

### Data Lakehouse & analytics
- Fonctionnalité : data marts, self-service BI.
- Bénéfices : autonomie data.
- KPIs : data availability.
- Exigences : CDC Outbox, event streams ; APIs /data/catalog.
- Sécurité/Conformité : gouvernance et qualité des données.
- Roadmap itérative : MVP lakehouse ; itération 2 catalog.
- Critères de succès : 90 % datasets catalogués.

### Détection d’anomalies
- Fonctionnalité : alertes fraude, dérives.
- Bénéfices : réduction risques.
- KPIs : anomaly detection precision.
- Exigences : microservices Analytics, Risk ; événements AnomalyDetected ; APIs /analytics/anomalies.
- Sécurité/Conformité : respect RGPD.
- Roadmap itérative : MVP règles ; itération 2 modèles.
- Critères de succès : faux positifs < 5 %.

## Gouvernance & Administration

### Gestion des rôles & habilitations
- Fonctionnalité : RBAC/ABAC centralisé.
- Bénéfices : sécurité accrue.
- KPIs : access violation rate.
- Exigences : microservices IAM, Policy ; événements RoleAssigned ; APIs /iam/roles.
- Sécurité/Conformité : MFA, Zero Trust, audit.
- Roadmap itérative : MVP RBAC ; itération 2 ABAC.
- Critères de succès : 0 accès non autorisé.

### Paramétrage multi-entités
- Fonctionnalité : chart of accounts, taxes, calendriers.
- Bénéfices : flexibilité groupe.
- KPIs : setup time.
- Exigences : microservices Tenant Config ; événements ConfigUpdated ; APIs /config.
- Sécurité/Conformité : isolation tenant.
- Roadmap itérative : MVP multi-entités ; itération 2 templates.
- Critères de succès : création entité < 1 jour.

### Monitoring & alerting métiers
- Fonctionnalité : alertes SLA, exceptions.
- Bénéfices : continuité opérationnelle.
- KPIs : incident response time.
- Exigences : microservices Observability, Alerting ; événements BusinessSLABreached ; APIs /alerts.
- Sécurité/Conformité : audit.
- Roadmap itérative : MVP alertes critiques ; itération 2 SLA avancés.
- Critères de succès : MTTR -30 %.

## Intégrations & Écosystème

### API Partner & Marketplace
- Fonctionnalité : portail développeurs, catalog APIs.
- Bénéfices : écosystème partenaires.
- KPIs : API adoption.
- Exigences : microservices API Gateway, Developer Portal ; événements ApiKeyIssued ; APIs /api/keys.
- Sécurité/Conformité : rate limiting, API security, WAF.
- Roadmap itérative : MVP portail API ; itération 2 marketplace.
- Critères de succès : 20 partenaires actifs.

### Connecteurs standards
- Fonctionnalité : banques, e-commerce, CRM externes.
- Bénéfices : intégration rapide.
- KPIs : time-to-integrate.
- Exigences : microservices Integration Hub ; événements ExternalSyncCompleted ; APIs /integrations.
- Sécurité/Conformité : mTLS, OAuth2.
- Roadmap itérative : MVP banques/CRM ; itération 2 e-commerce.
- Critères de succès : intégration < 2 semaines.

### EDI & B2B
- Fonctionnalité : EDIFACT/UBL, mapping.
- Bénéfices : automatisation B2B.
- KPIs : EDI success rate.
- Exigences : microservices EDI Gateway ; événements EDIMessageReceived ; APIs /edi/messages.
- Sécurité/Conformité : validation schémas, audit.
- Roadmap itérative : MVP EDI ventes ; itération 2 achats.
- Critères de succès : taux succès > 98 %.

## Roadmap globale itérative
- Phase 1 : MVP cœur par service (Finance/GL, AR/AP, CRM basique, Inventory, IAM, API Gateway, observabilité minimale).
- Phase 2 : Industrialisation (Procurement, Manufacturing, Projects, BI exécutif, Outbox + CDC, service mesh mTLS).
- Phase 3 : Optimisation (Forecasting avancé, analytics anomalies, consolidation groupe, APS, extensions partenaires).
- Phase 4 : Excellence (prédictif, auto-scaling avancé, canary/blue-green global, SLO/SLI matures).

## Architecture Microservices & Exigences Techniques Transverses (détaillée)

### Schéma global d’architecture
- Canaux Web/Mobile/Externe via API Gateway.
- BFF par canal pour orchestration et agrégation.
- Microservices par bounded context DDD.
- Communication synchrone REST/gRPC via Gateway.
- Communication asynchrone Kafka/Pulsar + Schema Registry.
- Outbox + CDC (Debezium) vers Lakehouse partagé.

### Tableau de découpage microservices
| Service | Domaine | Base de données | Événements clés | APIs publiques |
| --- | --- | --- | --- | --- |
| Treasury Position | Finances | PostgreSQL | TreasuryPositionUpdated | /treasury/positions |
| Cash Forecasting | Finances | TimescaleDB | ForecastComputed | /treasury/forecast |
| Payments | Finances | PostgreSQL | PaymentInitiated | /payments |
| General Ledger | Comptabilité | PostgreSQL + Event Store | JournalEntryPosted | /gl/entries |
| AR/AP | Comptabilité | PostgreSQL | InvoiceIssued, PaymentReceived | /ar/invoices, /ap/bills |
| Consolidation | Comptabilité | PostgreSQL | ConsolidationRunCompleted | /consolidation/runs |
| Employee Core | RH | PostgreSQL | EmployeeCreated | /hr/employees |
| Payroll | RH | PostgreSQL | PayrollRunCompleted | /payroll/runs |
| Procurement | Achats | PostgreSQL | POApproved | /procurement/pos |
| Inventory | Stocks | Cassandra | StockReserved | /inventory/items |
| Fulfillment | Logistique | PostgreSQL | ShipmentCreated | /logistics/shipments |
| Manufacturing | Production | PostgreSQL | WorkOrderReleased | /manufacturing/orders |
| Projects | Projets | PostgreSQL | ProjectCreated | /projects |
| Asset Register | Immobilisations | PostgreSQL | AssetCapitalized | /assets |
| Quality | Qualité | MongoDB | NonConformityDetected | /quality/nonconformities |
| CRM Core | Ventes | PostgreSQL | OpportunityWon | /crm/opportunities |
| Billing | Ventes | PostgreSQL | InvoiceIssued | /billing/invoices |
| IAM/Policy | Gouvernance | PostgreSQL | RoleAssigned | /iam/roles |
| Integration Hub | Intégrations | MongoDB | ExternalSyncCompleted | /integrations |

### Stratégie de communication & intégration
- APIs contractuelles versionnées, gRPC interne pour performance.
- Événements pour propagation d’état et découplage.
- Sagas pour transactions distribuées Order-to-Cash, Procure-to-Pay, Hire-to-Retire.

### Stratégie de données
- Database per service, cohérence éventuelle.
- Read models CQRS pour reporting.
- CDC vers Data Lakehouse, catalog data products.

### DevOps / GitOps / CI-CD / MLOps
- GitOps avec Helm/Argo, environnements éphemeres.
- Tests contractuels, tests E2E orientés événements.
- Pipelines MLOps pour modèles de prévision et détection d’anomalies.

### Sécurité & Zero Trust
- mTLS via service mesh, OAuth2/JWT, RBAC/ABAC.
- Secrets en vault, rotation automatique, WAF.
- Rate limiting, API security, audit inviolable.

### Stratégie de déploiement, scaling, blue-green/canary
- Kubernetes HPA/VPA, autoscaling par métriques métiers.
- Déploiements blue-green et canary avec rollback automatique.
- Circuit breaker, bulkhead, retry contrôlé au niveau mesh.

### Stratégie de migration legacy
- Strangler Fig par domaine et par flux.
- Coexistence via API Gateway.
- Migration progressive des données par CDC.
