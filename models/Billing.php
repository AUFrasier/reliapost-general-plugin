<?php
/**
 * Created by PhpStorm.
 * User: personal
 * Date: 10/20/18
 * Time: 10:24 PM
 */

namespace reliapost_registration;


class Billing
{
    public $userId;
    public $stripeToken;
    public $updatedAt;

    public static function addTokenToUser($userId, $token) {
        global $wpdb;
        $dbController = new DatabaseController();

        $query = $wpdb->prepare("INSERT INTO " . $dbController->tableBilling . " ("
            . DatabaseController::BILLING_USER_ID . ","
            . DatabaseController::BILLING_STRIPE_TOKEN . ","
            . DatabaseController::BILLING_UPDATED_AT . ") "
            . " VALUES (%s,%s,%s)",
        array($userId, $token, current_time("mysql")));
        return $wpdb->query($query);
    }
}