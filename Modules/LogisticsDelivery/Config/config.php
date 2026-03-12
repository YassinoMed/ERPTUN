<?php

return array (
  'name' => 'Logistique / expédition / livraison',
  'slug' => 'logistics-delivery',
  'description' => 'Pilotage expédition, livraison, tournées, transporteurs, étiquettes et preuve de livraison.',
  'priority' => 'P2',
  'features' => 
  array (
    0 => 'Bons de livraison',
    1 => 'Tournées et transporteurs',
    2 => 'Tracking colis',
    3 => 'Preuve de livraison',
    4 => 'Retours marchandises',
  ),
  'dependencies' => 
  array (
    0 => 'Wms',
    1 => 'Inventory',
    2 => 'Sales',
  ),
  'entities' => 
  array (
    0 => 'shipments',
    1 => 'delivery_routes',
    2 => 'delivery_proofs',
  ),
  'status' => 'blueprint',
  'delivery_mode' => 'module_scaffold',
);
