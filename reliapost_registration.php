<?php
/**
 * @package Reliapost User Registration
 */
/*
 Plugin Name: ReliaPost General
 Description: The ReliaPost plugin holding the general functionality.
 Version: 0.0.1
 Author: Chris Ruddell
 Author URI: https://exciteddragon.com
 Text Domain: reliapost_registration
 */

define("RELIAPOST_REGISTRATION_FILE", __FILE__);
define("RELIAPOST_REGISTRATION_DIR", __DIR__);

define("RELIAPOST_ADMIN_URL", admin_url( 'admin-ajax.php' ));

require_once('autoloader.php');
require_once("stripe/init.php");
require_once("backendhooks.php");

\reliapost_registration\Log::setupLog();

if ( !function_exists( 'add_action' ) ) {
    exit;
}

setlocale(LC_MONETARY, 'en_US.UTF-8');

if (is_admin()) {
    //\ArtUnlimited\Log::addEntry("is_admin()");
    //wp_die("is admin...");
}

add_action('init', array('reliapost_registration\\FrontEndPluginHooks', 'init'));

(new BackendHooks())->addHooks();


/**
 * Override default fusion_cached_query method from Avada's functions.php file.
 * This is so we can limit the recent_posts results to only those selected for the current user
 */
/*if ( ! function_exists( 'fusion_cached_query' ) ) {
    
    function fusion_cached_query( $args ) {

        if (is_array($args) && array_key_exists("posts_per_page", $args)) {
            $currentQuery = "";
            if (array_key_exists("cat", $args)) $currentQuery = $args["cat"];
            $allowedCategories = explode(",",$currentQuery);
            $controller = new \reliapost_registration\UserCategoryController();
            $userCategories = $controller->getUserCategoryIds();
            $queriedCategories = [];
            foreach ($userCategories as $categoryId) {
                if (in_array($categoryId, $allowedCategories) || count($allowedCategories)==0 || strlen($currentQuery)==0) $queriedCategories[] = $categoryId;
            }
            $args["cat"] = implode(",", $queriedCategories);
        }
        // Make sure cached queries are not language agnostic.
        if ( is_array( $args ) ) {
            $args['fusion_lang'] = Fusion_Multilingual::get_active_language();
        } else {
            $args .= '&fusion_lang=' . Fusion_Multilingual::get_active_language();
        }

        $query_id   = md5( maybe_serialize( $args ) );
        $query = wp_cache_get( $query_id, 'fusion_library' );
        if ( false === $query ) {
            $query = new WP_Query( $args );
            wp_cache_set( $query_id, $query, 'fusion_library' );
        }
        return $query;
    }
}*/