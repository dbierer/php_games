<?php
require __DIR__ . '/../bootstrap.php';
echo 'Usage: php '
     . basename(__FILE__)
     . ' html_dir|backup_dir|login_info '
     . PHP_EOL;
$config = include __DIR__ . '/config/config.php';
$request = $argv[1] ?? '';
$info = 'ERROR';
switch ($request) {
    case 'html_dir' :
        $info = HTML_DIR;
        break;
    case 'backup_dir' :
        $info = $config['SUPER']['backup_dir'] ?? '';
        break;
    case 'login_info' :
        $info  = 'Username:   ' . $config['SUPER']['username'] ?? '';
        $info .= "\nPassword:   " . $config['SUPER']['password'] ?? '';
        $info .= "\nValidation: " . var_export($config['SUPER']['validation'], TRUE);
        break;
    default :
        $info = '';
}
echo $info;
