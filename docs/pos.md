# POS

## Point de vente
**Catégorie** : Retail  
**Priorité** : Must  
**Description courte** : Encaisser et gérer le stock en point de vente.  
**Valeur business** : Accélère les ventes et réduit les ruptures.

**Spécifications fonctionnelles**
- User stories principales  
  - En tant que vendeur, je scanne un produit et encaisse.  
  - En tant que manager, je clôture un shift caisse.  
  - En tant que back-office, je synchronise stock.  
- Critères d’acceptation  
  - Ticket généré avec taxes et remises.  
  - Stock décrémenté en temps réel.  
  - Remboursement tracé.

**Spécifications techniques**
- Architecture & pattern à utiliser : MVC + service caisse  
- Schéma de données : pos_sales, pos_items, cash_registers  
- APIs exposées : POST /api/pos/sale  
- Événements : PosSaleCompleted  
- Intégrations externes nécessaires : imprimante, lecteur code-barres  
- Considérations multi-tenant : created_by  
- Sécurité & conformité associées : rôle caisse limité  
- Gestion des erreurs & rollback : rollback si stock insuffisant

**Implémentation recommandée**
- Tech stack précise : Laravel 11, milon/barcode  
- Code structure : app/Services/POS/CheckoutService.php  
- Exemple de code clé  
  ```typescript
  function canSell(stock, qty) {
    return stock >= qty
  }
  ```
- Tests à écrire : intégration stock, E2E encaissement

**Monitoring & KPIs**
- Métriques à tracker : temps moyen encaissement, taux d’annulation  
- Alertes à configurer : stock négatif  
- Dashboards Grafana à créer : POS Performance

**Estimation d’effort**
- Story points : 8  
- Homme-jours : 7 + 2 + 1  
- Dépendances bloquantes : WMS
