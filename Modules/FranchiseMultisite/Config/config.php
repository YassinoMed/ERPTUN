<?php

return array (
  'name' => 'Franchise / multi-site',
  'slug' => 'franchise-multisite',
  'description' => 'Pilotage des réseaux multi-sites, franchises, consolidations, redevances et performances par point de vente.',
  'priority' => 'P3',
  'features' => 
  array (
    0 => 'Référentiels centraux',
    1 => 'Consolidation multi-site',
    2 => 'Tarification par site',
    3 => 'Redevances franchise',
    4 => 'Performance par point de vente',
  ),
  'dependencies' => 
  array (
    0 => 'Saas',
    1 => 'Pos',
    2 => 'Inventory',
  ),
  'entities' => 
  array (
    0 => 'franchise_networks',
    1 => 'retail_sites',
    2 => 'royalty_rules',
  ),
  'status' => 'blueprint',
  'delivery_mode' => 'module_scaffold',
);
