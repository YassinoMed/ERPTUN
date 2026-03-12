<?php

return array (
  'name' => 'IoT / capteurs / télémétrie',
  'slug' => 'iot-telemetry',
  'description' => 'Collecte télémétrique, alertes seuils et dashboards temps réel pour capteurs et équipements.',
  'priority' => 'P3',
  'features' => 
  array (
    0 => 'Collecte capteurs',
    1 => 'Alertes de seuil',
    2 => 'Temps réel',
    3 => 'Historisation télémétrique',
    4 => 'Intégration maintenance et qualité',
  ),
  'dependencies' => 
  array (
    0 => 'Integrations',
    1 => 'Maintenance',
    2 => 'Quality',
  ),
  'entities' => 
  array (
    0 => 'iot_devices',
    1 => 'iot_measurements',
    2 => 'iot_alerts',
  ),
  'status' => 'blueprint',
  'delivery_mode' => 'module_scaffold',
);
