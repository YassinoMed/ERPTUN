<?php

return array (
  'name' => 'BI / tableaux de bord avancés',
  'slug' => 'business-intelligence',
  'description' => 'Couche analytique avancée avec dashboards dynamiques, KPIs et exports planifiés.',
  'priority' => 'P1',
  'features' => 
  array (
    0 => 'Dashboards dynamiques',
    1 => 'Drill-down et filtres',
    2 => 'KPIs personnalisés',
    3 => 'Exports PDF Excel',
    4 => 'Envois planifiés',
  ),
  'dependencies' => 
  array (
    0 => 'Platform',
    1 => 'Accounting',
    2 => 'Sales',
    3 => 'Projects',
  ),
  'entities' => 
  array (
    0 => 'bi_dashboards',
    1 => 'bi_widgets',
    2 => 'bi_snapshots',
  ),
  'status' => 'blueprint',
  'delivery_mode' => 'module_scaffold',
);
