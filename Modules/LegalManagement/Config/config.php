<?php

return array (
  'name' => 'Module juridique',
  'slug' => 'legal-management',
  'description' => 'Suivi des contrats, litiges, échéances légales, contentieux et dossiers juridiques.',
  'priority' => 'P3',
  'features' => 
  array (
    0 => 'Dossiers juridiques',
    1 => 'Litiges et contentieux',
    2 => 'Échéances légales',
    3 => 'Bibliothèque de modèles',
    4 => 'Pouvoirs et délégations',
  ),
  'dependencies' => 
  array (
    0 => 'DocumentManagement',
    1 => 'ESignature',
    2 => 'ContractManagement',
  ),
  'entities' => 
  array (
    0 => 'legal_cases',
    1 => 'legal_deadlines',
    2 => 'legal_templates',
  ),
  'status' => 'blueprint',
  'delivery_mode' => 'module_scaffold',
);
