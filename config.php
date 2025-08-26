<?php

// Http Url
$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
define('HTTP_URL', '/'. substr_replace(trim($_SERVER['REQUEST_URI'], '/'), '', 0, strlen($scriptName)));

// Define Path Application
define('DIR', str_replace('\\', '/', rtrim(__DIR__, '/')) . '/');
define('Framework', DIR . 'framework/');
define('MODELS', DIR . 'app/Models/');

