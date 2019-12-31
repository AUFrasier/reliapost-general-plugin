<?php
/**
 * Created by PhpStorm.
 * User: personal
 * Date: 10/21/18
 * Time: 9:16 AM
 */

namespace reliapost_registration;


class Subscription
{
    public $stripeId;
    public $enabled;
    public $isUserPlan;

    public static function get($stripeId = null) {
        global $wpdb;
        $controller = new DatabaseController();

        $sql = "SELECT * FROM " . $controller->tableSubscriptions;
        if (!is_null($stripeId)) $sql .= " WHERE " . DatabaseController::SUBSCRIPTIONS_STRIPE_ID . " = '$stripeId'";
        $results = $wpdb->get_results($sql);
        return $results;
    }
}