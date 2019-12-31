<?php
/**
 * Created by PhpStorm.
 * User: personal
 * Date: 11/21/18
 * Time: 2:48 PM
 */

namespace reliapost_registration;


class ProfileController
{
    static function updateProfile() {
        global $wpdb;

        $field = $_POST["fieldName"];
        $value = $_POST["value"];

        $user = wp_get_current_user();

        $response = new \stdClass();
        $result = null;
        $stripeController = new StripeController();
        $stripeToken = $stripeController->getStripeToken();
        $customer = $stripeController->getStripeAccount($stripeToken);

        switch ($field) {
            case "name": {
                $user->user_login = $value;
                if ($customer !== null) {
                    \Stripe\Customer::update(
                        $customer["id"],
                        [
                        'description' => $value,
                        ]
                    );
                }
                $wpdb->update($wpdb->users, array('user_login' => $value), array('ID' => $user->ID));
                break;
            }
            case "email": {
                if ($customer !== null) {
                    \Stripe\Customer::update(
                        $customer["id"],
                        [
                        'email' => $value,
                        ]
                    );
                }
                //make sure is a valid email and does not already exist
                if ($user->user_email == $value) self::handleError("Email not changed... please use a new email address.");
                if (!is_email($value)) self::handleError("You entered an invalid email ($value) - please check and try again");
                if (self::userExists($value)) self::handleError("Another account already exists with that email - please check and try again");
                //$wpdb->update($wpdb->users, array('user_email' => $value), array('ID' => $user->ID));
                $user->user_email = $value;
                break;
            }
            case "password": {
                wp_set_password($value, $user->ID);
                $result = $user->ID;    //set result now so we don't try to call wp_update_user() below using the old password (user's new password is already updated at this point)
                break;
            }
            default: {
                break;
            }
        }

        //save the values
        if ($result===null) $result = wp_update_user( $user );
        if ($result instanceof \WP_Error) {
            self::handleError($result->get_error_message());
        }

        $response->status = "success";
        $response->field = $field;
        $response->value = $value;

        echo json_encode($response);
        wp_die();


    }

    static function handleError($errorMessage) {
        $result = new \stdClass();
        $result->status = "error";
        $result->error = $errorMessage;
        echo json_encode($result);
        wp_die();
    }

    static function userExists($email) {
        global $wpdb;
        $users = $wpdb->get_results("SELECT * FROM " . $wpdb->users . " WHERE user_email = '$email'");
        return count($users)>0;
    }

}