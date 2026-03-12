<?php

return array (
  'name' => 'Facturation récurrente',
  'slug' => 'recurring-billing',
  'description' => 'Facturation récurrente métier distincte du SaaS avec échéanciers, cycles et relances.',
  'priority' => 'P1',
  'features' => 
  array (
    0 => 'Cycles de facturation',
    1 => 'Échéanciers',
    2 => 'Prorata et indexation',
    3 => 'Alertes de renouvellement',
    4 => 'Relances automatiques',
  ),
  'dependencies' => 
  array (
    0 => 'Billing',
    1 => 'ContractManagement',
    2 => 'Saas',
  ),
  'entities' => 
  array (
    0 => 'billing_cycles',
    1 => 'recurring_invoices',
    2 => 'subscription_events',
  ),
  'status' => 'blueprint',
  'delivery_mode' => 'module_scaffold',
);
