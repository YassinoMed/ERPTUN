<?php

return array (
  'name' => 'Workflow designer visuel',
  'slug' => 'workflow-designer',
  'description' => 'Moteur BPM et designer visuel pour approbations, escalades, SLA et automatisations métier.',
  'priority' => 'P1',
  'features' => 
  array (
    0 => 'Designer drag and drop',
    1 => 'Conditions métier',
    2 => 'Escalades et rappels',
    3 => 'SLA et journal d’exécution',
    4 => 'Templates de workflow',
  ),
  'dependencies' => 
  array (
    0 => 'Approvals',
    1 => 'Platform',
  ),
  'entities' => 
  array (
    0 => 'workflow_definitions',
    1 => 'workflow_nodes',
    2 => 'workflow_runs',
  ),
  'status' => 'blueprint',
  'delivery_mode' => 'module_scaffold',
);
