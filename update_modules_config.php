<?php
$file = __DIR__ . '/config/modules.php';
$content = file_get_contents($file);
$content = str_replace("'app_folder' => 'app/',", "'app_folder' => '',", $content);
$content = preg_replace("/'path' => 'app\//", "'path' => '", $content);
file_put_contents($file, $content);
echo "config/modules.php updated.";
