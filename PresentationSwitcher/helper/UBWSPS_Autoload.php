<?php

foreach (glob(__DIR__ . '/*.php') as $filename) {
    if (basename($filename) != 'UBWSPS_Autoload.php') {
        include_once $filename;
    }
}