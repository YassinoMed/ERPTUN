<?php

return array (
  'name' => 'LMS / e-learning',
  'slug' => 'learning-management',
  'description' => 'Plateforme de formation avec cours, modules, quiz, certifications et suivi de progression.',
  'priority' => 'P2',
  'features' => 
  array (
    0 => 'Cours et parcours',
    1 => 'Modules vidéo PDF quiz',
    2 => 'Certifications',
    3 => 'Suivi de progression',
    4 => 'Obligations de formation',
  ),
  'dependencies' => 
  array (
    0 => 'Hrm',
    1 => 'HrOps',
  ),
  'entities' => 
  array (
    0 => 'courses',
    1 => 'learning_modules',
    2 => 'learning_enrollments',
  ),
  'status' => 'blueprint',
  'delivery_mode' => 'module_scaffold',
);
