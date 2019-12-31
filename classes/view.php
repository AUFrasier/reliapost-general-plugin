<?php

namespace reliapost_registration;

class View
{
	public static function displayPage($page, array $pageData = array())
	{
		$file = static::getFilenameForPage($page);
		if (!file_exists($file)) {
			echo "Template not found:$file";
		} else {
			require($file);
		}
	}
	
	public static function parseTemplate($page, array $pageData = array())
	{
		$file = static::getFilenameForPage($page);
		$page = file_get_contents($file);
		
		$template = array_keys($pageData);
		$replacements = array_values($pageData);
		$page = str_replace($template, $replacements, $page);
		
		return $page;
	}
	
	private static function getViewPath()
	{
		return realpath(__DIR__ . '/../views/');
	}
	
	private static function getFilenameForPage($page)
	{
		$root = static::getViewPath();
		return $root . '/' . $page . '.php';
	}
}
