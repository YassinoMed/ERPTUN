<?php

return array (
  'name' => 'Base de connaissances',
  'slug' => 'knowledge-base',
  'description' => 'FAQ, articles, procédures et documentation interne avec recherche intelligente.',
  'priority' => 'P2',
  'features' => 
  array (
    0 => 'Articles et FAQ',
    1 => 'Catégorisation',
    2 => 'Recherche intelligente',
    3 => 'Suggestions depuis les tickets',
    4 => 'Documentation interne',
  ),
  'dependencies' => 
  array (
    0 => 'Platform',
    1 => 'Support',
  ),
  'entities' => 
  array (
    0 => 'knowledge_articles',
    1 => 'knowledge_categories',
    2 => 'knowledge_feedback',
  ),
  'status' => 'blueprint',
  'delivery_mode' => 'module_scaffold',
);
