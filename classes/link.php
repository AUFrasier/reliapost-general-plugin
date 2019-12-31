<?php

namespace reliapost_registration;

class Link
{
	const WITHOUT_SEARCH = false;
	
	public static function getPageUrlBySlug($slug)
	{
		return get_permalink(get_page_by_path($slug));
	}
	
	public static function getPageSlug($page, $exclusiveMode = true)
	{
		$pageOptBaseName = "reliapost_registration-" . $page . "-page-slug";
		return $exclusiveMode ? get_option($pageOptBaseName): get_option($pageOptBaseName . "-non-exclusive");
	}
	
	public static function slugToTitle($slug)
	{
		$title = str_replace('-', ' ', $slug);
		$title = ucwords($title);
		return $title;
	}
	
	public static function getURLForScript($script)
	{
		$url = plugins_url('assets/js/' . $script . '.js', RELIAPOST_REGISTRATION_FILE);
		if (!file_exists(self::getPathForScript(RELIAPOST_REGISTRATION_DIR . "/assets/js/" . $script . '.js'))) $url = plugins_url("assets/js/" . $script . ".js", RELIAPOST_REGISTRATION_FILE);
		return $url;
	}
	
	public static function getPathForScript($script)
	{
		$url = RELIAPOST_REGISTRATION_DIR . "/assets/js/" . $script . '.js';
        if (!file_exists($url)) $url = RELIAPOST_REGISTRATION_DIR . "/assets/js/" . $script . ".js";
		return $url;
	}
	

	
	public static function getImageUrl($filename)
	{
		return plugins_url("assets/images/" . $filename, dirname(__FILE__));
	}
	
	public static function currentUrl()
	{
		$protocol = static::getProtocol();
		return $protocol . '://' . $_SERVER['SERVER_NAME']. $_SERVER['REQUEST_URI'];
	}
	
	private static function getProtocol()
	{
		$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
		return $isHttps ? "https" : "http";
	}

    public static function getFilePathForCss($filename) {
        if (strpos($filename, ".php")>0) {
            return RELIAPOST_REGISTRATION_DIR . "/assets/css/" . $filename;
        }
        else {
            return RELIAPOST_REGISTRATION_DIR . "/assets/css/" . $filename . '.css';
        }
    }

    public static function getPathForCss($filename) {
        if (strpos($filename, ".php")>0) {
            return plugins_url("/assets/css/$filename", dirname(__FILE__));
        }
        return plugins_url("/assets/css/" . $filename . ".css", dirname(__FILE__));
    }

    public static function getVersionForCssFile($file) {
        $filePath = Link::getFilePathForCss($file);
        if (!file_exists($filePath)) return 0;
        return date("ymd-Gis", filemtime($filePath));
    }
}
