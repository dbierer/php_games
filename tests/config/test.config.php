<?php
$config = include BASE_DIR . '/src/config/config.php';
use PhpTraining\Maze\Constants;
$inner = function () {
    $wall = [];
    for ($y = 33; $y < 36; $y++)
        for ($x = 1; $x < 66; $x++) $wall[] = [$x,$y];
    for ($y = 66; $y < 69; $y++)
        for ($x = 33; $x < 99; $x++) $wall[] = [$x,$y];
    return $wall;
};
$config['PHP_TRAINING']['maze']['maps']['TEST'] = [
    'name' => 'TEST',
    'size' => 99,
    'render' => 'table',
    'doors' => [
        Constants::SOUTH => [[1,0],[2,0],[3,0]],
        Constants::NORTH => [[99,100],[98,100],[97,100]],
    ],
    'inner' => $inner(),
];
return $config;
