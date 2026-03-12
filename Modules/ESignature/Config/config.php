<?php

return array (
  'name' => 'Signature électronique',
  'slug' => 'e-signature',
  'description' => 'Signature électronique transverse pour contrats, devis, achats, RH et validations formelles.',
  'priority' => 'P1',
  'features' => 
  array (
    0 => 'Enveloppes de signature',
    1 => 'Validation avant signature',
    2 => 'Horodatage légal',
    3 => 'Historique de signature',
    4 => 'Suivi des signataires',
  ),
  'dependencies' => 
  array (
    0 => 'DocumentManagement',
    1 => 'Sales',
    2 => 'Hrm',
  ),
  'entities' => 
  array (
    0 => 'signature_envelopes',
    1 => 'signature_participants',
    2 => 'signature_events',
  ),
  'status' => 'blueprint',
  'delivery_mode' => 'module_scaffold',
);
