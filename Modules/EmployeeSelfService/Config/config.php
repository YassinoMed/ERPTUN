<?php

return array (
  'name' => 'Portail employé self-service',
  'slug' => 'employee-self-service',
  'description' => 'Self-service RH pour congés, documents, bulletins, attestations, notes de frais et onboarding.',
  'priority' => 'P1',
  'features' => 
  array (
    0 => 'Demandes RH',
    1 => 'Documents employés',
    2 => 'Bulletins et attestations',
    3 => 'Objectifs et pointage',
    4 => 'Onboarding salarié',
  ),
  'dependencies' => 
  array (
    0 => 'Hrm',
    1 => 'HrOps',
  ),
  'entities' => 
  array (
    0 => 'employee_requests',
    1 => 'employee_documents',
    2 => 'employee_portal_widgets',
  ),
  'status' => 'blueprint',
  'delivery_mode' => 'module_scaffold',
);
