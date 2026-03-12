<?php

return array (
  'name' => 'Gestion de flotte',
  'slug' => 'fleet-management',
  'description' => 'Gestion des véhicules, chauffeurs, entretien, carburant, assurances et sinistres.',
  'priority' => 'P3',
  'features' => 
  array (
    0 => 'Référentiel véhicules',
    1 => 'Affectations et chauffeurs',
    2 => 'Suivi carburant',
    3 => 'Entretien et assurance',
    4 => 'Kilométrage et incidents',
  ),
  'dependencies' => 
  array (
    0 => 'Maintenance',
    1 => 'Operations',
  ),
  'entities' => 
  array (
    0 => 'vehicles',
    1 => 'vehicle_assignments',
    2 => 'vehicle_expenses',
  ),
  'status' => 'blueprint',
  'delivery_mode' => 'module_scaffold',
);
