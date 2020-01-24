<?php

function teaLatexAutoloader($class)
{
	if (file_exists($f = __DIR__."/classes/".str_replace("\\", "/", $class).".php")) {
		require $f;
	}
}

spl_autoload_register("teaLatexAutoloader");
