<?php
/**
 * Created by PhpStorm.
 * User: personal
 * Date: 10/21/18
 * Time: 10:02 AM
 */

namespace reliapost_registration;


class StripeHelper
{
    public static function createProduct() {
        $controller = new StripeController();
        $user = wp_get_current_user();
        $name = "Reliapost Subscription";
        $productId = $user->ID;
        $product = $controller->createProduct($name,$productId);
        echo json_encode($product);
    }

    public static function createSubscription() {
        $controller = new StripeController();
        $frequency = $_POST["frequency"];
        $cost = "$20";
        $cost = str_replace("$", "", $cost);
        $cost = $cost * 100;

        $subscription = $controller->createSubscription($frequency, $cost);
        echo json_encode($subscription);
    }

    const STRIPE_KEY = "reliapost_stripeKey";
    const STRIPE_PUBLIC_KEY = "reliapost_stripePublicKey";
    public static function saveSettings() {
        $stripeKey = $_POST["stripeKey"];
        $stripePk = $_POST["stripePublicKey"];
        update_option(self::STRIPE_KEY, $stripeKey);
        update_option(self::STRIPE_PUBLIC_KEY, $stripePk);
    }
}

class Settings {
    public $stripeKey;
    public $stripePublicKey;

    /**
     * Settings constructor.
     */
    public function __construct()
    {
        $this->stripeKey = get_option(StripeHelper::STRIPE_KEY);
        $this->stripePublicKey = get_option(StripeHelper::STRIPE_PUBLIC_KEY);
    }


}