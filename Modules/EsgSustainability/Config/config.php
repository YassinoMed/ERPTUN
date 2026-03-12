<?php

return array (
  'name' => 'ESG / carbone / durabilité',
  'slug' => 'esg-sustainability',
  'description' => 'Suivi énergie, eau, déchets, émissions carbone et objectifs RSE.',
  'priority' => 'P3',
  'features' => 
  array (
    0 => 'Indicateurs ESG',
    1 => 'Suivi carbone',
    2 => 'Consommations énergie eau',
    3 => 'Objectifs RSE',
    4 => 'Rapports de durabilité',
  ),
  'dependencies' => 
  array (
    0 => 'Platform',
    1 => 'BusinessIntelligence',
  ),
  'entities' => 
  array (
    0 => 'esg_metrics',
    1 => 'carbon_entries',
    2 => 'sustainability_targets',
  ),
  'status' => 'blueprint',
  'delivery_mode' => 'module_scaffold',
);
