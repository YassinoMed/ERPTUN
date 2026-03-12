<?php

return array (
  'name' => 'Field Service / interventions terrain',
  'slug' => 'field-service',
  'description' => 'Gestion des interventions terrain, affectations techniciens, rapports et signatures mobiles.',
  'priority' => 'P2',
  'features' => 
  array (
    0 => 'Ordres d’intervention',
    1 => 'Planning terrain',
    2 => 'Checklists et photos',
    3 => 'Pièces consommées',
    4 => 'Signature client mobile',
  ),
  'dependencies' => 
  array (
    0 => 'Maintenance',
    1 => 'Projects',
    2 => 'Inventory',
  ),
  'entities' => 
  array (
    0 => 'service_orders',
    1 => 'service_visits',
    2 => 'service_reports',
  ),
  'status' => 'blueprint',
  'delivery_mode' => 'module_scaffold',
);
