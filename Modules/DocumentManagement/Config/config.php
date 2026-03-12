<?php

return array (
  'name' => 'GED / gestion documentaire',
  'slug' => 'document-management',
  'description' => 'Gestion électronique des documents avec classement, versioning, métadonnées et recherche.',
  'priority' => 'P1',
  'features' => 
  array (
    0 => 'Arborescence documentaire',
    1 => 'Versioning et check-in/out',
    2 => 'Recherche plein texte',
    3 => 'OCR et indexation',
    4 => 'Archivage et rétention',
  ),
  'dependencies' => 
  array (
    0 => 'Platform',
    1 => 'HrOps',
    2 => 'Quality',
  ),
  'entities' => 
  array (
    0 => 'documents',
    1 => 'document_versions',
    2 => 'document_folders',
  ),
  'status' => 'blueprint',
  'delivery_mode' => 'module_scaffold',
);
