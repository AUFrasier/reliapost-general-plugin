<?php

namespace reliapost_registration;

class Utility
{
	public static function collectOutput(Callable $callback)
	{
		$oldBuffer = ob_get_contents();
		ob_clean();
		$callback();
		$newContent = ob_get_contents();
		ob_clean();
		echo $oldBuffer;
		return $newContent;
	}
	

	public static function diagnostics()
	{
		$url = Link::currentUrl();
		$protocol	= $_SERVER['SERVER_PROTOCOL'];
		$method		= $_SERVER['REQUEST_METHOD'];
		
		return $protocol . ' ' . $method . ' ' . $url;
	}
	
	public static function trimArray(array &$array)
	{
		array_walk($array, function (&$item, $key) {
			$item = trim($item);
		});
	}
	
	public static function getNestedItem($item, $array)
	{
		$keys = explode(':', $item);
		
		if (!is_array($array)) {
			return "";
		}
		
		foreach ($keys as $key) {
			
			if (array_key_exists($key, $array) !== true) {
				return "";
			}
			
			$array = $array[$key];
			
		}
		
		return $array;
	}
}
