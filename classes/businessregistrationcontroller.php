<?php

namespace reliapost_registration;


class BusinessRegistrationController
{
    const FIRST_NAME_MISSING = "first_name_missing";
    const LAST_NAME_MISSING = "last_name_missing";
    const USERNAME_MISSING = "username_missing";
    const ERROR_EMAIL_EXISTS = "email_exists";
    const INVALID_EMAIL = "email";
	const INVALID_EMAIL_VERIFICATION = "email_verification_error";
	const INVALID_PASSWORD = "password";
	const INVALID_PASSWORD_VERIFICATION = "password_verification_error";
    const CLOSED = "closed";

    static function getSlug() {
        return "new-business-registration";
    }
	
    static function subscribeUser($customerId, $planID) {
        $subscription = \Stripe\Subscription::create([
            "customer" => $customerId,
            "items" => [["plan" => $planID]],
            'trial_period_days' => 15,
        ]);
        return $subscription;
    }

    /**
     * Adapted from https://code.tutsplus.com/tutorials/build-a-custom-wordpress-user-flow-part-2-new-user-registration--cms-23810
     * Redirects the user to the custom registration page instead
     * of wp-login.php?action=register.
     */
    static function redirect_to_custom_register() {
        Log::addEntry("redirect_to_custom_register()");
        if ( 'GET' == $_SERVER['REQUEST_METHOD'] ) {
            Log::addEntry("user is trying to request the registration form...");
            if ( is_user_logged_in() ) {
                LoginController::redirect_logged_in_user();
            } else {
                wp_redirect( home_url(self::getSlug() ) );
            }
            exit;
        } 
    }
    /**
     * Adapted from https://code.tutsplus.com/tutorials/build-a-custom-wordpress-user-flow-part-2-new-user-registration--cms-23810
     * Handles the registration of a new user.
     *
     * Used through the action hook "login_form_register" activated on wp-login.php
     * when accessed through the registration action.
     */
    static function registerBusiness() {
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
            Log::addEntry("do_register_user(): " . json_encode($_POST));
            $redirect_url = home_url(self::getSlug());
            $stripeController = new StripeController(); 
 
            if ( ! get_option( 'users_can_register' ) ) {
                // Registration closed, display error
                $redirect_url = add_query_arg( 'register-error', 'closed', $redirect_url );
            } else {
                $email = $_POST["email"];
                $businessname = sanitize_text_field($_POST["businessname"]);
                $username = sanitize_text_field($_POST["username"]);
                $firstName = sanitize_text_field($_POST['firstname']);
                $lastName = sanitize_text_field($_POST['lastname']);
                $password = $_POST["password"];
				$verifyEmail = sanitize_text_field($_POST['verify-email']);
                $verifyPassword = sanitize_text_field($_POST['verify-password']);
                // Token is created using Checkout or Elements!
                // Get the payment token ID submitted by the form:
                $token = $_POST['pmtToken'];
                $planID = get_option(StripeController::RELIAPOST_PLAN_ID);
                $customer = $stripeController->createCustomer($email, $token, $firstName, $lastName, $username);
                Log::addEntry("stripe customer created: " . json_encode($customer));

                if ($customer!==null && isset($customer["id"]) && strlen($customer["id"])>0) {

                    $subscription = self::subscribeUser($customer["id"], $planID);
                    Log::addEntry("customer subscribed on Stripe: " . json_encode($subscription));
                    $user = User::register_user($email, $password, $username, $lastName, $firstName, $verifyEmail, $verifyPassword, $businessname);

                    if ( User::is_error( $user ) ) {
                        // Parse errors into a string and append as parameter to redirect
                        $errorCode = "";
                        switch ($user) {
                            case User::BUSINESSNAME_MISSING:
                                $errorCode = self::BUSINESSNAME_MISSING;
                                break;
                            case User::FIRST_NAME_MISSING:
                                $errorCode = self::FIRST_NAME_MISSING;
                                break;
                            case User::LAST_NAME_MISSING:
                                $errorCode = self::LAST_NAME_MISSING;
                                break;
                            case User::USERNAME_MISSING:
                                $errorCode = self::USERNAME_MISSING;
                                break;
                            case User::ERROR_EMAIL_EXISTS:
                                $errorCode = self::ERROR_EMAIL_EXISTS;
                                break;
                            case User::ERROR_INVALID_EMAIL:
                                $errorCode = self::INVALID_EMAIL;
                                break;
							case User::INVALID_EMAIL_VERIFICATION:
                                $errorCode = self::INVALID_EMAIL_VERIFICATION;
                                break;
							case User::INVALID_PASSWORD:
                                $errorCode = self::INVALID_PASSWORD;
                                break;
							case User::INVALID_PASSWORD_VERIFICATION:
                                $errorCode = self::INVALID_PASSWORD_VERIFICATION;
                                break;
                        }

                        $errors = $user;
                        $redirect_url = add_query_arg( array(
                            'register-error' => $errorCode,
                            'businessname' => $businessname,
                            'firstname' => $firstName,
                            'lastname' => $lastName,
                            'username' => $username,
                            'email' => $email,
                            'verify-email' => $verifyEmail,
                        ), $redirect_url );

                        Log::addEntry("COULD NOT CREATE USER: " . json_encode($user));

                    } else {
                        // Success, redirect to login page.
                        $res = Billing::addTokenToUser($user->userId, $customer["id"]);
                        Log::addEntry("stripe details connected to customer in db: " . json_encode($res));
                        $redirect_url = home_url( LoginController::getSlug());
                        $redirect_url = add_query_arg( 'registered', $email, $redirect_url );
                    }
                }
                else {
                    Log::addEntry("COULD NOT CREATE STRIPE CUSTOMER");
                    wp_die("COULD NOT CREATE STRIPE CUSTOMER", 400);
                }

                $result = User::register_user($email, $password, $username, $lastName, $firstName, $verifyEmail, $verifyPassword);

            }

            wp_redirect( $redirect_url );
            exit;
        }
    }
}