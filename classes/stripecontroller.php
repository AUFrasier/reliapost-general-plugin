<?php
/**
 * Created by PhpStorm.
 * User: personal
 * Date: 10/21/18
 * Time: 9:42 AM
 */

namespace reliapost_registration;


use Stripe\Error\Api;

class StripeController
{
    const OPTION_PRODUCT_ID = "reliapoost_product_id";
    const OPTION_PRODUCT_NAME = "reliapost_product_name";
    const STANDARD_RELIAPOST_PLAN_ID = "standard_subscription_plan_id";
    const ADVANCED_RELIAPOST_PLAN_ID = "advanced_subscription_plan_id";

    /**
     * StripeController constructor.
     */
    public function __construct() 
    {
        // Set your secret key: remember to change this to your live secret key in production
        // See your keys here: https://dashboard.stripe.com/account/apikeys
        $settings = new Settings();
        \Stripe\Stripe::setApiKey($settings->stripeKey);
    }

    public function createCustomer($email, $token, $firstName, $lastName, $username) {
        $customer = \Stripe\Customer::create(array(
            "email" => $email,
            "name" => $firstName." ".$lastName,
            "description" => $username,
            "source" => $token,
        ));
        
        return $customer;
    }

    public function createProduct($name, $productId) {
        $product = \Stripe\Product::create([
            'id' => $productId,
            'name' => $name,
            'type' => 'service',
        ]);

        $this->deleteProduct();
        add_option(self::OPTION_PRODUCT_ID, $product["id"]);
        add_option(self::OPTION_PRODUCT_NAME, $name);

        return $product;
    }

    public function deleteProduct() {
        delete_option(self::OPTION_PRODUCT_ID);
        delete_option(self::OPTION_PRODUCT_NAME);
    }

    public function getProduct() {
        $productId = get_option(self::OPTION_PRODUCT_ID);
        if ($productId!=null && strlen($productId)>0) {
            $product = \Stripe\Product::retrieve($productId);
            return $product;
        }
        else {
            return null;
        }
    }

    public function createSubscription($frequency, $costInCents) {
        $productId = get_option(self::OPTION_PRODUCT_ID);
        $productName = get_option(self::OPTION_PRODUCT_NAME);

        $plan = \Stripe\Plan::create([
            'product' => $productId,
            'nickname' => "Subscription for product: $productName",
            'interval' => $frequency,
            'currency' => 'usd',
            'amount' => $costInCents,
        ]);

        return $plan;
    }

    public function getSubscriptions() {
        $productId = get_option(self::OPTION_PRODUCT_ID);
        $productName = get_option(self::OPTION_PRODUCT_NAME);

        try {
            $plans = \Stripe\Plan::all();
            return $plans;
        } catch (Api $e) {
            return $e;
        }
    }

    public function getCustomerSubscriptions() {
        $subscriptions = [];
        try {
            $subscriptions = \Stripe\Subscription::all();
        } catch (Api $e) {
        }

        return $subscriptions;
    }

    public function getStripeToken() {
        $user = wp_get_current_user();
        $userId = $user->ID;
        $dbController = new DatabaseController();
        $res = $dbController->query("SELECT " . DatabaseController::BILLING_STRIPE_TOKEN . " FROM " . $dbController->tableBilling . " WHERE " . DatabaseController::BILLING_USER_ID . " = '$userId'");
        if ($res!==null && count($res)>0) {
            return $res[0]->stripe_customer_id;
        }
        return $res;
    }

    public function getStripeAccount($token) {
        $res = \Stripe\Customer::retrieve($token);
        return $res;
    }

    public function getSubscriptionDetails($token) {
        if ($token==null) return null;
        $customer = \Stripe\Customer::retrieve($token);
        if ($customer==null) return null;
        $subs = $customer->subscriptions->data;
        if ($subs!=null && count($subs)>0) return $subs[0];
        else return null;
    }

    public function getCancelledSubs($token) {
        $canceledSubs = [];
        $customer = \Stripe\Customer::retrieve($token);
        $cust_id = $customer->id;
        $canceledSubs = \Stripe\Subscription::all(["status" => "canceled", "customer" => $cust_id]);
        return $canceledSubs;
    }
    
    public function getRemainingDaysOnAccount($token) {
        $subscription = $this->getSubscriptionDetails($token);
        if ($subscription==null) return 0;
        $periodEnd = $subscription->current_period_end;
        $now = time();
        $remainingDays = ($periodEnd - $now)/(60*60*24);
        return ceil($remainingDays);
    }

    public function getCancelPeriodEndInfo($token) {
        $subscription = $this->getSubscriptionDetails($token);
        if ($subscription==null) return 0;
        $cancelPeriodEnd = $subscription->cancel_at_period_end;
        return $cancelPeriodEnd;
    }

    
}