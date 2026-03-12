<?php

return array (
  'name' => 'Notes de frais',
  'slug' => 'expense-management',
  'description' => 'Gestion des dépenses collaborateurs avec reçus, politiques et remboursements.',
  'priority' => 'P1',
  'features' => 
  array (
    0 => 'Saisie et scan des dépenses',
    1 => 'Kilométrage',
    2 => 'Politiques de dépenses',
    3 => 'Workflow de validation',
    4 => 'Remboursements',
  ),
  'dependencies' => 
  array (
    0 => 'Hrm',
    1 => 'Accounting',
    2 => 'Approvals',
  ),
  'entities' => 
  array (
    0 => 'expense_reports',
    1 => 'expense_items',
    2 => 'expense_policies',
  ),
  'status' => 'blueprint',
  'delivery_mode' => 'module_scaffold',
);
