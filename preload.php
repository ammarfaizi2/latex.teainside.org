<?php

define("OPCACHE_PRELOAD", true);

require_once __DIR__."/../config.php";
require_once __DIR__."/../src/autoload.php";

var_dump(class_exists("TeaLatex\\TeaLatex"));
