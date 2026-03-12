<?php

return array (
  'name' => 'CMMS / maintenance avancée',
  'slug' => 'advanced-cmms',
  'description' => 'Maintenance industrielle avancée avec préventif, conditionnel, pièces et indicateurs MTBF MTTR.',
  'priority' => 'P3',
  'features' => 
  array (
    0 => 'Maintenance préventive',
    1 => 'Maintenance conditionnelle',
    2 => 'Pièces de rechange',
    3 => 'MTBF et MTTR',
    4 => 'Coûts de maintenance',
  ),
  'dependencies' => 
  array (
    0 => 'Maintenance',
    1 => 'Inventory',
  ),
  'entities' => 
  array (
    0 => 'cmms_plans',
    1 => 'cmms_work_orders',
    2 => 'cmms_readings',
  ),
  'status' => 'blueprint',
  'delivery_mode' => 'module_scaffold',
);
