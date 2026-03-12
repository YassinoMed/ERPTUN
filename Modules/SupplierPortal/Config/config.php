<?php

return array (
  'name' => 'Portail fournisseur avancé',
  'slug' => 'supplier-portal',
  'description' => 'Portail fournisseur pour commandes, factures, litiges, paiements et collaboration achat.',
  'priority' => 'P1',
  'features' => 
  array (
    0 => 'Espace fournisseur dédié',
    1 => 'Bons de commande et factures',
    2 => 'Litiges et réclamations',
    3 => 'Suivi paiements fournisseur',
    4 => 'Partage documentaire achat',
  ),
  'dependencies' => 
  array (
    0 => 'Accounting',
    1 => 'Inventory',
    2 => 'Integrations',
  ),
  'entities' => 
  array (
    0 => 'supplier_portal_accounts',
    1 => 'supplier_cases',
    2 => 'supplier_documents',
  ),
  'status' => 'blueprint',
  'delivery_mode' => 'module_scaffold',
);
