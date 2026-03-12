<?php

return array (
  'name' => 'Gestion des abonnements clients',
  'slug' => 'customer-subscriptions',
  'description' => 'Gestion des abonnements clients métier, cycles, options, renouvellements et usage.',
  'priority' => 'P1',
  'features' => 
  array (
    0 => 'Souscriptions clients',
    1 => 'Cycles et renouvellements',
    2 => 'Options et add-ons',
    3 => 'Suivi d’usage',
    4 => 'Suspension et reprise',
  ),
  'dependencies' => 
  array (
    0 => 'RecurringBilling',
    1 => 'ContractManagement',
    2 => 'Saas',
  ),
  'entities' => 
  array (
    0 => 'customer_subscriptions',
    1 => 'subscription_usage',
    2 => 'subscription_changes',
  ),
  'status' => 'blueprint',
  'delivery_mode' => 'module_scaffold',
);
