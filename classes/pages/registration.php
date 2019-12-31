<?php
/**
 * Created by PhpStorm.
 * User: christopherruddell
 * Date: 2/15/18
 * Time: 9:12 PM
 */

namespace reliapost_registration\Pages;

use reliapost_registration\Link;
use reliapost_registration\StripeController;
use reliapost_registration\View;
use reliapost_registration\StripeHelper;

class Registration extends PageAbstract
{

    public function displayPage()
    {
        // TODO: Implement displayPage() method.
        $this->configure();
        $pageData = [];

        //calculate current plan cost
        $stripeController = new StripeController();
        $plans = $stripeController->getSubscriptions()["data"];
        $plan = $plans[0];
        $planAmount = $plan["amount"]/100;
        $planInterval = $plan["interval"];
        $pageData["planAmount"] = $planAmount;
        $pageData["planInterval"] = $planInterval;
        $pageData["planId"] = $plan["id"];


        View::displayPage('registration_form', $pageData);
        //echo "<script src='" . plugins_url("../../assets/js/stripe_main.js?i=" . time(), __FILE__) . "' data-rel-js></script>\n";
    }

    private static function getVersionForFile($file)
    {
        $filePath = Link::getPathForScript($file);
        return date("ymd-Gis", filemtime($filePath));
    }

    protected function configure()
    {
        wp_enqueue_style("roboto", "https://fonts.googleapis.com/css?family=Roboto");
        wp_enqueue_style("quicksand", "https://fonts.googleapis.com/css?family=Quicksand");
        wp_enqueue_style("source_code_pro", "https://fonts.googleapis.com/css?family=Source+Code+Pro");
        //wp_enqueue_style("registration", plugins_url("../../assets/css/stripe_form3.css", __FILE__));
    }
}