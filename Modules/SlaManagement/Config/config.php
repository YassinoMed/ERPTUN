<?php

return array (
  'name' => 'Gestion des SLA',
  'slug' => 'sla-management',
  'description' => 'Gestion des SLA, engagements de service, priorités, escalades et suivi des dépassements.',
  'priority' => 'P1',
  'features' => 
  array (
    0 => 'Politiques SLA',
    1 => 'Priorités de service',
    2 => 'Escalades automatiques',
    3 => 'Calendriers ouvrés',
    4 => 'Mesure des dépassements',
  ),
  'dependencies' => 
  array (
    0 => 'Support',
    1 => 'WorkflowDesigner',
    2 => 'FieldService',
  ),
  'entities' => 
  array (
    0 => 'sla_policies',
    1 => 'sla_targets',
    2 => 'sla_breaches',
  ),
  'status' => 'blueprint',
  'delivery_mode' => 'module_scaffold',
);
