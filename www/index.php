<?php
require_once('../utm/CoreLoader.php');
$mvc = Utm\Core::instance();

// On enregistre les plugins
$mvc->registerPlugin('\Utm\Plugin\Config');
$mvc->registerPlugin('\Utm\Plugin\Debug');
$mvc->registerPlugin('\Utm\Plugin\Db');

$mvc->run() ;