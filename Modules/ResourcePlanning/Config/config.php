<?php

return array (
  'name' => 'Planification avancée des ressources',
  'slug' => 'resource-planning',
  'description' => 'Planification avancée des ressources humaines, techniques et matérielles avec charge, capacité et arbitrage.',
  'priority' => 'P1',
  'features' => 
  array (
    0 => 'Charge et capacité',
    1 => 'Allocation multi-ressources',
    2 => 'Arbitrage et conflits',
    3 => 'Prévision de disponibilité',
    4 => 'Planning transverse',
  ),
  'dependencies' => 
  array (
    0 => 'Projects',
    1 => 'Hrm',
    2 => 'Operations',
  ),
  'entities' => 
  array (
    0 => 'resource_plans',
    1 => 'resource_allocations',
    2 => 'capacity_snapshots',
  ),
  'status' => 'blueprint',
  'delivery_mode' => 'module_scaffold',
);
