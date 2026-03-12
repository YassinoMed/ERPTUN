<?php

return array (
  'name' => 'Réservation / booking',
  'slug' => 'booking-engine',
  'description' => 'Moteur de réservation transverse pour agents, salles, équipements, médecins ou techniciens.',
  'priority' => 'P2',
  'features' => 
  array (
    0 => 'Disponibilités par ressource',
    1 => 'Réservation en ligne',
    2 => 'Rappels automatiques',
    3 => 'Annulation et replanification',
    4 => 'File d’attente',
  ),
  'dependencies' => 
  array (
    0 => 'Integrations',
    1 => 'Platform',
  ),
  'entities' => 
  array (
    0 => 'bookings',
    1 => 'booking_resources',
    2 => 'booking_slots',
  ),
  'status' => 'blueprint',
  'delivery_mode' => 'module_scaffold',
);
