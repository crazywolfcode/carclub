<?php

session_start();

define("WP_SDK_ROOT", dirname(__FILE__) . "/");
define("WP_SDK_CLASS_PATH", WP_SDK_ROOT . "class/");
require_once(QC_SDK_CLASS_PATH . "WP.class.php");
