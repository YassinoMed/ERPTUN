<?php

return array (
  'name' => 'Procurement avancé',
  'slug' => 'advanced-procurement',
  'description' => 'Achats avancés avec demandes internes, RFQ, scoring fournisseurs et contrats-cadres.',
  'priority' => 'P1',
  'features' => 
  array (
    0 => 'Demandes d’achat',
    1 => 'Appels d’offres',
    2 => 'Comparaison et scoring',
    3 => 'Catalogue fournisseur',
    4 => 'Réceptions partielles',
  ),
  'dependencies' => 
  array (
    0 => 'Accounting',
    1 => 'Inventory',
    2 => 'Approvals',
  ),
  'entities' => 
  array (
    0 => 'purchase_requests',
    1 => 'rfqs',
    2 => 'supplier_bids',
  ),
  'status' => 'blueprint',
  'delivery_mode' => 'module_scaffold',
);
