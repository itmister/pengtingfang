<?php
define('PATH_LIB', dirname(__FILE__) . '/');
define('HOST_NAME', gethostname());
require PATH_LIB . 'core.class.php';
\Lib\Core::start();