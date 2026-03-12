<?php

return array (
  'name' => 'GRC / conformité / audit / risques',
  'slug' => 'grc',
  'description' => 'Gestion des risques, audits, incidents, plans d’action et conformité interne.',
  'priority' => 'P2',
  'features' => 
  array (
    0 => 'Registre des risques',
    1 => 'Audits et contrôles',
    2 => 'Incidents et non-conformités',
    3 => 'Plans d’action CAPA',
    4 => 'Preuves et traçabilité',
  ),
  'dependencies' => 
  array (
    0 => 'Quality',
    1 => 'Platform',
    2 => 'Approvals',
  ),
  'entities' => 
  array (
    0 => 'risk_registers',
    1 => 'audit_programs',
    2 => 'compliance_actions',
  ),
  'status' => 'blueprint',
  'delivery_mode' => 'module_scaffold',
);
