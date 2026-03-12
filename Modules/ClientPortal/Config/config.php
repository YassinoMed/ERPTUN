<?php

return array (
  'name' => 'Portail client avancé',
  'slug' => 'client-portal',
  'description' => 'Portail client self-service pour devis, factures, paiements, documents, tickets et suivi des demandes.',
  'priority' => 'P1',
  'features' => 
  array (
    0 => 'Espace client self-service',
    1 => 'Validation en ligne',
    2 => 'Suivi devis, factures et paiements',
    3 => 'Documents partagés',
    4 => 'Suivi tickets et demandes',
  ),
  'dependencies' => 
  array (
    0 => 'Crm',
    1 => 'Sales',
    2 => 'Billing',
    3 => 'Support',
  ),
  'entities' => 
  array (
    0 => 'portal_accounts',
    1 => 'portal_sessions',
    2 => 'portal_shares',
  ),
  'status' => 'blueprint',
  'delivery_mode' => 'module_scaffold',
);
