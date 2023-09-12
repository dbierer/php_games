<?php
use PhpTraining\Maze\Constants;
return [
    Constants::DEF_NAME => function () : array {
        $wall = [];
        for ($x = 1; $x < 9; $x++) $wall[] = [$x,4];
        for ($x = 4; $x < 12; $x++) $wall[] = [$x,8];
        return $wall;
    },
    '15' => function () : array {
        $max = 15;
        $wall = [];
        for ($x = 1; $x < ($max - 3); $x++) $wall[] = [$x,3];
        for ($x = 4; $x < $max; $x++) $wall[] = [$x,6];
        for ($x = 1; $x < ($max - 3); $x++) $wall[] = [$x,9];
        for ($x = 4; $x < $max; $x++) $wall[] = [$x,12];
        return $wall;
    },
];
