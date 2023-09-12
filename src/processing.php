<?php
// add pre-processing logic based on URL here:
use FileCMS\Common\Stats\Clicks;
$click_fn  = $config['CLICK_CSV'] ?? BASE_DIR . '/logs/clicks.csv';
Clicks::add($uri, $click_fn);
