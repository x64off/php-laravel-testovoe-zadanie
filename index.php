<?php
session_start();
define("ROOT_PATH",__DIR__);
require_once ROOT_PATH.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'System.php';
System::run();
