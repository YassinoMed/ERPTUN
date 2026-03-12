<?php

return array (
  'name' => 'Fidélité / cartes cadeaux',
  'slug' => 'loyalty-rewards',
  'description' => 'Programme de fidélité, cashback, coupons, points et cartes cadeaux.',
  'priority' => 'P3',
  'features' => 
  array (
    0 => 'Points fidélité',
    1 => 'Segmentation et niveaux',
    2 => 'Cashback',
    3 => 'Cartes cadeaux',
    4 => 'Campagnes ciblées',
  ),
  'dependencies' => 
  array (
    0 => 'Crm',
    1 => 'Pos',
    2 => 'Sales',
  ),
  'entities' => 
  array (
    0 => 'loyalty_accounts',
    1 => 'reward_transactions',
    2 => 'gift_cards',
  ),
  'status' => 'blueprint',
  'delivery_mode' => 'module_scaffold',
);
