<?php

return array (
  'name' => 'Connecteurs e-commerce',
  'slug' => 'ecommerce-connectors',
  'description' => 'Connecteurs Shopify, WooCommerce et Prestashop pour produits, commandes et stock.',
  'priority' => 'P2',
  'features' => 
  array (
    0 => 'Synchronisation produits',
    1 => 'Synchronisation commandes',
    2 => 'Clients et coupons',
    3 => 'Stocks et prix',
    4 => 'Statuts d’expédition',
  ),
  'dependencies' => 
  array (
    0 => 'Sales',
    1 => 'Inventory',
    2 => 'Integrations',
  ),
  'entities' => 
  array (
    0 => 'ecommerce_connections',
    1 => 'channel_products',
    2 => 'channel_orders',
  ),
  'status' => 'blueprint',
  'delivery_mode' => 'module_scaffold',
);
