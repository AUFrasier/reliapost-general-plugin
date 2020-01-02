<?php

namespace reliapost_registration;

class FrontEndPluginHooks
{
	private static $title = null;
	private static $ruleToTest = null;

    const BRAND_REGISTRATION = "brandregistration";
    const BUSINESS_REGISTRATION = "businessregistration";
	const LOGIN = "login";
	const FORGOT_PASSWORD = "forgotpassword";
	const PROFILE = "myaccount";
	const ADD_POST = "addpost";
	const CONTENT_STREAM = "contentstream";
    const SHARED_POSTS = "sharedposts";
    const MAIN_PAGE = "mainpage";

	const THIS_CLASS = 'reliapost_registration\\FrontEndPluginHooks';
	
	public static function setTitle($title)
	{
		static::$title = $title;
	}
	
	public static function init()
	{
		add_shortcode('reliapostregistration', array(self::THIS_CLASS, 'shortcodeHandler'));
        add_shortcode( 'reliapostregistration-password-reset-form', array( LoginController::THIS_CLASS, 'render_password_reset_form' ) );
        add_action('wp_enqueue_scripts', array(self::THIS_CLASS, "onLoopReady"));
	}
	
	public static function shortcodeHandler(array $attributes)
	{
	    //Log::addEntry("shortcodeHandler() . json_encode($attributes)");

        $pageToDisplay = self::getPageToDisplay($attributes);
        self::enqueueScripts($pageToDisplay);
        self::enqueueStyles($pageToDisplay);

		if (static::isPageDisplayShortcode($attributes)) {
			return static::handlePageDisplay($attributes);
		}
	}
	
    static function onLoopReady() {
        if (LoginController::membershipRequired()) LoginController::requireLoggedInUser();
    }

    static function getStripePublicKey() {
        //localizing and enqueueing script here because Registration Page needs Stripe PK included for Stripe Element at #card-element in brand_registration.php
        $script = 'reliapost_registration';
        $fileUrl = Link::getURLForScript($script);
        $fileVersion = static::getVersionForFile($script);
        wp_enqueue_script($script, $fileUrl, null, $fileVersion, true);
        $script_params = array(
            'stripePk' => get_option(StripeHelper::STRIPE_PUBLIC_KEY)
        );
        wp_localize_script( $script, 'scriptParams', $script_params );
    }
	
	private static function enqueueScripts($pageToDisplay)
	{
	    switch ($pageToDisplay) {
            case self::BRAND_REGISTRATION : {
				self::getStripePublicKey();
                break;
            }
            case self::BUSINESS_REGISTRATION : {
				self::getStripePublicKey();
                break;
            }
            case self::LOGIN :{
                static::addScriptToQueue('login', ['jquery']);
                break;
            }
            case self::FORGOT_PASSWORD : {

                break;
            }
            case self::PROFILE: {
				static::addScriptToQueue('profile', null, false);
				break;
			}
            case self::ADD_POST: {
                static::addScriptToQueue('addpost', null, false);
                break;
            }
            case self::CONTENT_STREAM: {
                static::addScriptToQueue("scheduledposts", null, false);
                break;
            }
			case self::SHARED_POSTS: {
                static::addScriptToQueue("sharedposts", null, false);
                break;
            }
        }
	}

	private static function enqueueStyles($pageToDisplay) {
	    switch ($pageToDisplay) {
            case self::BRAND_REGISTRATION: {
                wp_enqueue_style('reliapost_general', Link::getPathForCss("reliapost_general"), null, static::getVersionForCssFile("reliapost_general"));
                wp_enqueue_style('reliapost_stripe', Link::getPathForCss("stripe_form3"), null, static::getVersionForCssFile("stripe_form3"));
                wp_enqueue_style('reliapost_registration', Link::getPathForCss("reliapost_registration"), null, static::getVersionForCssFile("reliapost_registration"));
                break;
            }
            case self::BUSINESS_REGISTRATION: {
                wp_enqueue_style('reliapost_general', Link::getPathForCss("reliapost_general"), null, static::getVersionForCssFile("reliapost_general"));
                wp_enqueue_style('reliapost_stripe', Link::getPathForCss("stripe_form3"), null, static::getVersionForCssFile("stripe_form3"));
                wp_enqueue_style('reliapost_registration', Link::getPathForCss("reliapost_registration"), null, static::getVersionForCssFile("reliapost_registration"));
                break;
            }
            case self::LOGIN: {
                wp_enqueue_style('reliapost_general', Link::getPathForCss("reliapost_general"), null, static::getVersionForCssFile("reliapost_general"));
                wp_enqueue_style('reliapost_login', Link::getPathForCss("reliapost_login"), null, static::getVersionForCssFile("reliapost_login"));
                break;
            }
            case self::FORGOT_PASSWORD: {
                wp_enqueue_style('reliapost_general', Link::getPathForCss("reliapost_general"), null, static::getVersionForCssFile("reliapost_general"));
                wp_enqueue_style('reliapost_login', Link::getPathForCss("reliapost_login"), null, static::getVersionForCssFile("reliapost_login"));
                break;
            }
            case self::MAIN_PAGE: {
                wp_enqueue_style('reliapost_general', Link::getPathForCss("reliapost_general"), null, static::getVersionForCssFile("reliapost_general"));
                wp_enqueue_style('reliapost_mainpage', Link::getPathForCss("reliapost_mainpage"), null, static::getVersionForCssFile("reliapost_mainpage"));
                break;
            }
            case self::PROFILE: {
                wp_enqueue_style("google_material_design_icons", "https://fonts.googleapis.com/icon?family=Material+Icons", null);
                wp_enqueue_style('reliapost_profile', Link::getPathForCss("reliapost_profile"), null, static::getVersionForCssFile("reliapost_profile"));
                break;
            }
            case self::ADD_POST: {
                wp_enqueue_style("google_material_design_icons", "https://fonts.googleapis.com/icon?family=Material+Icons", null);
                wp_enqueue_style('reliapost_addpost', Link::getPathForCss("reliapost_addpost"), null, static::getVersionForCssFile("reliapost_addpost"));
                break;
            }
            case self::CONTENT_STREAM: {
                wp_enqueue_style("google_material_design_icons", "https://fonts.googleapis.com/icon?family=Material+Icons", null);
                wp_enqueue_style('reliapost_contentstream', Link::getPathForCss("reliapost_contentstream"), null, static::getVersionForCssFile("reliapost_contentstream"));
                break;
            }
			case self::SHARED_POSTS: {
                wp_enqueue_style("google_material_design_icons", "https://fonts.googleapis.com/icon?family=Material+Icons", null);
                wp_enqueue_style('reliapost_sharedposts', Link::getPathForCss("reliapost_sharedposts"), null, static::getVersionForCssFile("reliapost_sharedposts"));
                break;
            }

        }
    }
		
	private static function addScriptToQueue($script, $dependencies, $inFooter = true)
	{
		$fileUrl = Link::getURLForScript($script);
		$fileVersion = static::getVersionForFile($script);
		wp_enqueue_script($script, $fileUrl, $dependencies, $fileVersion, $inFooter);
	}
		
	private static function getVersionForFile($file)
	{
		$filePath = Link::getPathForScript($file);
		return date("ymd-Gis", filemtime($filePath));
	}

    private static function getVersionForCssFile($file) {
        $filePath = Link::getFilePathForCss($file);
        if (!file_exists($filePath)) return 0;
        return date("ymd-Gis", filemtime($filePath));
    }



    private static function isPageDisplayShortcode(array $attributes)
	{
		return array_key_exists('displaytype', $attributes);
	}
	
	private static function handlePageDisplay(array $attributes)
	{
        Log::addEntry("handlePageDisplay()");
		$callback = function () use ($attributes) {
		    $pageToDisplay = self::getPageToDisplay($attributes);
		    Log::addEntry("handlePageDisplay($pageToDisplay)");
			(new PageFactory)->showPage($pageToDisplay, $attributes);
		};
		
		return Utility::collectOutput($callback);
	}

	private static function getPageToDisplay($attributes) {
	    $displayCode = "";
	    if (isset($attributes["displaytype"])) {
            $displayCode = $attributes["displaytype"];
            switch ($displayCode) {
                case "profile":
                    return "myaccount";
                default:
                    return $displayCode;
            }
        }
	    else {
	        Log::addEntry("getPageToDisplay - no displaytype specified!");
            return null;
        }
    }

}
