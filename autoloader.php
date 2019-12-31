<?php

spl_autoload_register(function ($className)
{
	$ns = 'reliapost_registration\\';
	if (strpos($className, $ns) !== false) {

		$noNamespace = str_replace($ns, '', $className);
		$normalizedClassName = str_replace('\\', '/', $noNamespace);
		$lowerClass = strtolower($normalizedClassName);
		$upperClass = ucfirst($lowerClass);
		$fullPathFile = __DIR__ . '/classes/' . $lowerClass . '.php';
		if (($realPath = realpath($fullPathFile)) !== false) {
			require_once($realPath);
		}

        $fullPathFile = __DIR__ . '/models/' . $upperClass . '.php';
        if (($realPath = realpath($fullPathFile)) !== false) {
            require_once($realPath);
        }
	}
});

require_once("classes/stripehelper.php");
