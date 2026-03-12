# Roadmap d’extension ERP

Ce document regroupe les modules à ajouter au produit sans dupliquer les capacités déjà présentes.

## Priorité P1

| Module | Valeur business | Dépendances | Livrables techniques |
|---|---|---|---|
| Portail client / fournisseur | Self-service, baisse du support manuel, accélération devis/factures | CRM, ventes, achats, support, documents, paiements | Portails web, ACL externes, documents partagés, validations en ligne |
| GED / DMS | Conformité, audit, gestion documentaire transverse | RH, finance, qualité, projets, support | Référentiel documentaire, versioning, métadonnées, recherche |
| Signature électronique | Réduction des cycles de validation et contractualisation | GED, contrats, devis, RH, achats | Enveloppes de signature, statut, journal légal, horodatage |
| Workflow designer visuel | Différenciation SaaS forte, adaptabilité multi-secteurs | approvals, achats, RH, compta, support | Builder visuel, règles métiers, moteur d’exécution, SLA |
| Notes de frais | Adoption rapide, automatisation comptable RH | RH, compta, workflow, paiements | Dépenses, reçus, kilométrage, remboursement, politiques |
| Procurement avancé | Maturité achats, contrôle des dépenses | achats, fournisseurs, stock, workflow | DA internes, RFQ, scoring, consultations, contrats-cadres |
| Portail employé ESS MSS | Adoption interne, réduction charge RH | RH, paie, documents, congés, pointage | Self-service salarié, documents, demandes, onboarding |
| BI avancée | Forte valeur direction, différenciation produit | dashboards, finance, ventes, stock, RH, projets | Modèle analytique, KPI configurables, drill-down, exports |
| Contrats et revenus récurrents | Monétisation et gestion service/maintenance | ventes, achats, billing, abonnements | Contrats, échéanciers, renouvellements, facturation récurrente |
| WhatsApp omnicanal | Très forte adoption commerciale et support | CRM, support, notifications, factures | Inbox omnicanal, templates, logs, campagnes, messages transactionnels |

## Priorité P2

| Module | Valeur business | Dépendances | Livrables techniques |
|---|---|---|---|
| Field Service / SAV | Très utile pour maintenance et interventions terrain | maintenance, support, planning, stock | Ordres d’intervention, planning mobile, signature client |
| Immobilisations / actifs | Complément ERP classique finance-maintenance | compta, maintenance, achats | Registre d’actifs, amortissements, inventaires, transferts |
| Knowledge base | Réduction du volume support, capitalisation interne | support, RH, GED | FAQ, articles, recherche, suggestions en ticket |
| Booking transverse | Réutilisable dans clinique, support, SAV, RH | calendrier, clinique, RH, support | Réservation ressource, rappels, disponibilité, replanification |
| LMS / eLearning | Bon levier RH et conformité formation | RH, formation, portail employé | Cours, quiz, progression, certifications |
| GRC / audit / conformité | Très bon pour groupes, industrie, santé | qualité, GED, workflows | Risques, incidents, CAPA, audits, contrôles |
| Connecteurs e-commerce | Besoin marché fort | ventes, stock, CRM, livraison | Shopify, WooCommerce, Prestashop, sync produits/commandes |
| Logistique / livraison | Prolonge WMS au dernier kilomètre | WMS, stock, ventes | BL, tournées, transporteurs, tracking, POD |

## Priorité P3

| Module | Valeur business | Dépendances | Livrables techniques |
|---|---|---|---|
| Flotte / véhicules | Utile pour logistique, BTP, agriculture | maintenance, opérations, BTP | Véhicules, carburant, entretien, sinistres, affectations |
| CMMS avancée | Verticalisation industrielle forte | maintenance, stock, IoT, actifs | Préventif, conditionnel, pièces, MTBF, MTTR |
| IoT / télémétrie | Différenciation premium | maintenance, agriculture, qualité, BI | Collecte capteurs, alertes, dashboards temps réel |
| ESG / carbone | Positionnement moderne et appels d’offres | BI, achats, flotte, énergie | Indicateurs ESG, émissions, objectifs RSE |
| Franchise / multi-site | Très pertinent pour retail et réseaux | POS, SaaS, stock, ventes | Référentiels centraux, consolidation, redevances |
| Loyalty / fidélité | Très vendeur côté retail | POS, CRM, ventes | Points, coupons, cashback, cartes cadeaux |
| Module juridique | Différenciation B2B | GED, signature, contrats | Dossiers, litiges, échéances, modèles |

## Ordre d’implémentation recommandé

1. Portail client / fournisseur
2. GED / DMS
3. Signature électronique
4. Workflow designer visuel
5. Notes de frais
6. Procurement avancé
7. Portail employé
8. BI avancée
9. Contrats et revenus récurrents
10. WhatsApp omnicanal

## Règle d’exécution

- Commencer par les modules transverses avant les verticales.
- Mutualiser permissions, fichiers, notifications, workflows et reporting.
- Ne pas lancer plus de 2 modules P1 en parallèle sur cette base Laravel.
