<?php

return array (
  'name' => 'Gestion des immobilisations',
  'slug' => 'asset-management',
  'description' => 'Gestion des actifs et immobilisations, amortissements, transferts et inventaires.',
  'priority' => 'P2',
  'features' => 
  array (
    0 => 'Registre des immobilisations',
    1 => 'Amortissement linéaire et dégressif',
    2 => 'Affectation à employé ou site',
    3 => 'Transferts et cessions',
    4 => 'Inventaire physique',
  ),
  'dependencies' => 
  array (
    0 => 'Accounting',
    1 => 'Maintenance',
  ),
  'entities' => 
  array (
    0 => 'fixed_assets',
    1 => 'asset_depreciations',
    2 => 'asset_transfers',
  ),
  'status' => 'blueprint',
  'delivery_mode' => 'module_scaffold',
);
