<?php

return array (
  'name' => 'WhatsApp Business / omnicanal',
  'slug' => 'whatsapp-omnichannel',
  'description' => 'Hub conversationnel WhatsApp et omnicanal lié aux objets CRM, factures, tickets et campagnes.',
  'priority' => 'P1',
  'features' => 
  array (
    0 => 'Inbox omnicanal',
    1 => 'Templates WhatsApp',
    2 => 'Messages transactionnels',
    3 => 'Campagnes ciblées',
    4 => 'Historique centralisé',
  ),
  'dependencies' => 
  array (
    0 => 'Crm',
    1 => 'Integrations',
    2 => 'Platform',
  ),
  'entities' => 
  array (
    0 => 'omnichannel_threads',
    1 => 'whatsapp_templates',
    2 => 'message_campaigns',
  ),
  'status' => 'blueprint',
  'delivery_mode' => 'module_scaffold',
);
