<?php

return array (
  'name' => 'Gestion des contrats',
  'slug' => 'contract-management',
  'description' => 'Gestion des contrats clients et fournisseurs avec clauses, annexes, échéances et renouvellements.',
  'priority' => 'P1',
  'features' => 
  array (
    0 => 'Référentiel de contrats',
    1 => 'Clauses et annexes',
    2 => 'Renouvellements automatiques',
    3 => 'Alertes d’échéance',
    4 => 'Indexation et pénalités',
  ),
  'dependencies' => 
  array (
    0 => 'Sales',
    1 => 'Billing',
    2 => 'DocumentManagement',
  ),
  'entities' => 
  array (
    0 => 'contracts',
    1 => 'contract_terms',
    2 => 'contract_renewals',
  ),
  'status' => 'blueprint',
  'delivery_mode' => 'module_scaffold',
);
