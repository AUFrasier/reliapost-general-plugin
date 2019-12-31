<?php
/**
 * Created by PhpStorm.
 * User: personal
 * Date: 10/20/18
 * Time: 11:51 PM
 */

namespace reliapost_registration;


use ArtUnlimited\BaseController;

class AdminController
{
    static function displayPage($page) {
        wp_enqueue_style('bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css',null, null);
        wp_enqueue_script('boostrapJs', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js', ['jquery'], null, true);
        wp_enqueue_style('admin_stripe_css', Link::getPathForCss("reliapost.admin.stripe"), null, Link::getVersionForCssFile("reliapost.admin.stripe"));
        include (RELIAPOST_REGISTRATION_DIR . "/views/$page.php");
    }

    static function display_page_stripe() {
        self::displayPage("stripe");
    }

    static function display_page_stripe_plans() {
        self::displayPage("stripe_plans");
    }
}