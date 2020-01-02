<?php

namespace reliapost_registration;


use WP_Error;
use WP_User;

class LoginController
{
   
    const LOGIN = "login";
    const FORGOT_PASSWORD = "forgot_password";
    const RESET_PASSWORD = "reset-password";
    const THIS_CLASS = "\\reliapost_registration\\LoginController";
    const MEMBERSHIP_META_TAG = "reliapostregistration_membershiprequired";

    static function getSlug($page = self::LOGIN) {
        switch ($page) {
            case self::LOGIN:
                return "login";
            case self::FORGOT_PASSWORD:
                return "forgot-password";
            case self::RESET_PASSWORD:
                return "reset-password";
        }
    }

    static function getSlugForgotPassword() {

    }

    static function Login() {
        Log::addEntry("login()");
        $email = $_POST["email"];
        $password = $_POST["password"];

        $user = User::login($email, $password);

        if ($user!==null) {
            //logged in!
            //set a cookie

        }
    }

    /**
     * Adapted from https://code.tutsplus.com/tutorials/build-a-custom-wordpress-user-flow-part-1-replace-the-login-page--cms-23627
     * Redirect the user to the custom login page instead of wp-login.php.
     */
    static function redirect_to_custom_login() {
        Log::addEntry("redirect_to_custom_login()");
        if ( $_SERVER['REQUEST_METHOD'] == 'GET' ) {
            $redirect_to = isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : null;

            if ( is_user_logged_in() ) {
                Log::addEntry("user already logged in...");
                self::redirect_logged_in_user( $redirect_to );
                exit;
            }

            // The rest are redirected to the login page
            $login_url = home_url( self::getSlug() );

            if ( ! empty( $redirect_to ) ) {
                $login_url = add_query_arg( 'redirect_to', $redirect_to, $login_url );
            }

            wp_redirect( $login_url );
            exit;
        }
    }


    /**
     * Redirects the user to the correct page depending on whether he / she
     * is an admin or not.
     *
     * @param string $redirect_to   An optional redirect_to URL for admin users
     */
    static function redirect_logged_in_user( $redirect_to = null ) {
        Log::addEntry("redirect_logged_in_user()");
        $user = wp_get_current_user();
        if ( user_can( $user, 'manage_options' ) ) {
            if ( $redirect_to ) {
                wp_safe_redirect( $redirect_to );
            } else {
                wp_redirect( admin_url() );
            }
        } else {
            wp_redirect( home_url( "houses" ) );
        }
    }

    /**
     * adapted from https://code.tutsplus.com/tutorials/build-a-custom-wordpress-user-flow-part-1-replace-the-login-page--cms-23627
     * Redirect the user after authentication if there were any errors.
     *
     * @param Wp_User|Wp_Error  $user       The signed in user, or the errors that have occurred during login.
     * @param string            $username   The user name used to log in.
     * @param string            $password   The password used to log in.
     *
     * @return Wp_User|Wp_Error The logged in user, or error information if there were errors.
     */
    static function maybe_redirect_at_authenticate( $user, $username, $password ) {
        Log::addEntry("maybe_redirect_at_authenticate()");
        // Check if the earlier authenticate filter (most likely,
        // the default WordPress authentication) functions have found errors
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            if ( is_wp_error( $user ) ) {
                Log::addEntry("error logging in with $username...");
                Log::addEntry(json_encode($_POST));
                $error_codes = join( ',', $user->get_error_codes() );

                $login_url = home_url( self::getSlug() );
                $login_url = add_query_arg( 'login', $error_codes, $login_url );

                wp_redirect( $login_url );
                exit;
            }
        }

        return $user;
    }


    /**
     * Adapted from https://code.tutsplus.com/tutorials/build-a-custom-wordpress-user-flow-part-1-replace-the-login-page--cms-23627
     * Finds and returns a matching error message for the given error code.
     *
     * @param string $error_code    The error code to look up.
     *
     * @return string               An error message.
     */
    static function get_error_message( $error_code ) {
        Log::addEntry("get_error_message($error_code)");
        switch ( $error_code ) {
            case 'empty_username':
                return __( 'You do have an email address, right?', 'personalize-login' );

            case 'empty_password':
                return __( 'You need to enter a password to login.', 'personalize-login' );

            case 'invalid_username':
                return __(
                    "We don't have any users with that email address. Maybe you used a different one when signing up?",
                    'personalize-login'
                );

            case 'incorrect_password':
                $err = __(
                    "The password you entered wasn't quite right. <a href='%s'>Did you forget your password</a>?",
                    'personalize-login'
                );
                return sprintf( $err, wp_lostpassword_url() );

            case 'expiredkey':
            case 'invalidkey':
                return __( 'The password reset link you used is not valid anymore.', 'personalize-login' );

            case 'password_reset_mismatch':
                return __( "The two passwords you entered don't match.", 'personalize-login' );

            case 'password_reset_empty':
                return __( "Sorry, we don't accept empty passwords.", 'personalize-login' );

            case 'invalid_email':
            case 'invalidcombo':
                return __( 'There are no users registered with this email address.', 'personalize-login' );
            default:
                break;
        }

        return __( 'An unknown error occurred. Please try again later.', 'personalize-login' );
    }

    /**
     * Redirect to custom login page after the user has been logged out.
     */
    static function redirect_after_logout() {
        Log::addEntry("redirect_after_logout()");
        $redirect_url = home_url( self::getSlug() . '?logged_out=true' );
        wp_safe_redirect( $redirect_url );
        exit;
    }

    /**
     * Adapted from https://code.tutsplus.com/tutorials/build-a-custom-wordpress-user-flow-part-1-replace-the-login-page--cms-23627
     * Returns the URL to which the user should be redirected after the (successful) login.
     *
     * @param string           $redirect_to           The redirect destination URL.
     * @param string           $requested_redirect_to The requested redirect destination URL passed as a parameter.
     * @param WP_User|WP_Error $user                  WP_User object if login was successful, WP_Error object otherwise.
     *
     * @return string Redirect URL
     */
    static function redirect_after_login( $redirect_to, $requested_redirect_to, $user ) {
        Log::addEntry("redirect_after_login()");
        $redirect_url = home_url();

        if ( ! isset( $user->ID ) ) {
            return $redirect_url;
        }

        if ( user_can( $user, 'manage_options' ) ) {
            // Use the redirect_to parameter if one is set, otherwise redirect to custom profile page.
            if ( $requested_redirect_to == '' ) {
                $redirect_url = admin_url();
            } else {
                $redirect_url = $requested_redirect_to;
            }
        } else {
            // Optional: Non-admin users always go to their account page after login
            // $redirect_url = home_url( 'member-account' );
            $redirect_url = home_url('my-profile');
        }

        return wp_validate_redirect( $redirect_url, home_url() );
    }

    /**
     * Adapted from https://code.tutsplus.com/tutorials/build-a-custom-wordpress-user-flow-part-3-password-reset--cms-23811
     * Redirects the user to the custom "Forgot your password?" page instead of
     * wp-login.php?action=lostpassword.
     */
    static function redirect_to_custom_lostpassword() {
        Log::addEntry("redirect_to_custom_lostpassword");
        if ( 'GET' == $_SERVER['REQUEST_METHOD'] ) {
            if ( is_user_logged_in() ) {
                self::redirect_logged_in_user();
                exit;
            }

            wp_redirect( home_url( self::getSlug(self::FORGOT_PASSWORD )) );
            exit;
        }
    }

    /**
     * Adapted from https://code.tutsplus.com/tutorials/build-a-custom-wordpress-user-flow-part-3-password-reset--cms-23811
     * Initiates password reset.
     */
    static function do_password_lost() {
        Log::addEntry("do_password_lost");
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
            $errors = retrieve_password();
            if ( is_wp_error( $errors ) ) {
                // Errors found
                $redirect_url = home_url( self::getSlug(self::FORGOT_PASSWORD) );
                $redirect_url = add_query_arg( 'errors', join( ',', $errors->get_error_codes() ), $redirect_url );
            } else {
                // Email sent
                $redirect_url = home_url( self::getSlug() );
                $redirect_url = add_query_arg( 'checkemail', 'confirm', $redirect_url );
            }

            wp_redirect( $redirect_url );
            exit;
        }
    }

    /**
     * Adapted from https://code.tutsplus.com/tutorials/build-a-custom-wordpress-user-flow-part-3-password-reset--cms-23811
     * Returns the message body for the password reset mail.
     * Called through the retrieve_password_message filter.
     *
     * @param string  $message    Default mail message.
     * @param string  $key        The activation key.
     * @param string  $user_login The username for the user.
     * @param WP_User $user_data  WP_User object.
     *
     * @return string   The mail message to send.
     */
    static function replace_retrieve_password_message( $message, $key, $user_login, $user_data ) {
        Log::addEntry("replace_retrieve_password_message");
        // Create new message
        $msg  = __( 'Hello!', 'personalize-login' ) . "\r\n\r\n";
        $msg .= sprintf( __( 'You asked us to reset your password for your account using the email address %s.', 'personalize-login' ), $user_data->user_email) . "\r\n\r\n";
        $msg .= __( "If this was a mistake, or you didn't ask for a password reset, just ignore this email and nothing will happen.", 'personalize-login' ) . "\r\n\r\n";
        $msg .= __( 'To reset your password, visit the following address:', 'personalize-login' ) . "\r\n\r\n";
        $msg .= site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ) . "\r\n\r\n";
        $msg .= __( 'Thanks!', 'personalize-login' ) . "\r\n";

        return $msg;
    }

    /**
     * Adapted from https://code.tutsplus.com/tutorials/build-a-custom-wordpress-user-flow-part-3-password-reset--cms-23811
     * Redirects to the custom password reset page, or the login page
     * if there are errors.
     */
    static function redirect_to_custom_password_reset() {
        Log::addEntry("redirect_to_custom_password_reset");
        if ( 'GET' == $_SERVER['REQUEST_METHOD'] ) {
            // Verify key / login combo
            $user = check_password_reset_key( $_REQUEST['key'], $_REQUEST['login'] );
            Log::addEntry("redirect_to_custom_password_reset(" . $_REQUEST['key'] . "," . $_REQUEST["login"] . ")");
            if ( ! $user || is_wp_error( $user ) ) {
                Log::addEntry("User clicked on INVALID forgot password link in their email!!! ERROR_CODE: " . $user->get_error_code() . " : " . $user->get_error_message());
                if ( $user && $user->get_error_code() === 'expired_key' ) {
                    wp_redirect( home_url( self::getSlug() . '?login=expiredkey' ) );
                } else {
                    wp_redirect( home_url( self::getSlug() . '?login=invalidkey' ) );
                }
                exit;
            }

            Log::addEntry("User clicked on forgot password link in their email - displaying custom reset password page");

            $redirect_url = home_url(self::getSlug(self::RESET_PASSWORD));
            $redirect_url = add_query_arg( 'login', esc_attr( $_REQUEST['login'] ), $redirect_url );
            $redirect_url = add_query_arg( 'key', esc_attr( $_REQUEST['key'] ), $redirect_url );

            Log::addEntry($redirect_url);
            wp_redirect( $redirect_url );
            exit;
        }
    }

    /**
     * A shortcode for rendering the form used to reset a user's password.
     *
     * @param  array   $attributes  Shortcode attributes.
     * @param  string  $content     The text content for shortcode. Not used.
     *
     * @return string  The shortcode output
     */
    static function render_password_reset_form( $attributes, $content = null ) {
        // Parse shortcode attributes
        $default_attributes = array( 'show_title' => false );
        $attributes = shortcode_atts( $default_attributes, $attributes );

        if ( is_user_logged_in() ) {
            return __( 'You are already signed in.', 'personalize-login' );
        } else {
            if ( isset( $_REQUEST['login'] ) && isset( $_REQUEST['key'] ) ) {
                $attributes['login'] = $_REQUEST['login'];
                $attributes['key'] = $_REQUEST['key'];
                Log::addEntry("render_password_reset_form(" . $_REQUEST['key'] . "," . $_REQUEST['login'] . ")");

                // Error messages
                $errors = array();
                if ( isset( $_REQUEST['error'] ) ) {
                    $error_codes = explode( ',', $_REQUEST['error'] );

                    foreach ( $error_codes as $code ) {
                        $errors []= self::get_error_message( $code );
                    }
                }
                $attributes['errors'] = $errors;

                $callback = function () use ($attributes) {
                    (new PageFactory)->showPage("passwordreset", $attributes);
                };

                return Utility::collectOutput($callback);

            } else {
                return __( 'Invalid password reset link.', 'personalize-login' );
            }
        }
    }

    static function logAllResetKeys() {
        global $wpdb;
        $results = $wpdb->get_results("SELECT ID, user_activation_key, user_login FROM " . $wpdb->users);
        foreach ($results as $obj) {
            Log::addEntry("rp: " . json_encode($obj));
        }
    }

    /**
     * Resets the user's password if the password reset form was submitted.
     */
    static function do_password_reset() {
        Log::addEntry("do_password_reset");
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
            $rp_key = $_REQUEST['rp_key'];
            $rp_login = $_REQUEST['rp_login'];
            Log::addEntry("do_password_reset(" . $rp_key . "," . $rp_login . ")");

            $user = check_password_reset_key( $rp_key, $rp_login );

            if ( ! $user || is_wp_error( $user ) ) {
                Log::addEntry("do_password_reset - error with key $rp_key for user $rp_login: " . $user->get_error_code() . " : " . $user->get_error_message());
                Log::addEntry("REQUEST: " . json_encode($_REQUEST));
                if ( $user && $user->get_error_code() === 'expired_key' ) {
                    wp_redirect( home_url( self::getSlug() . '?login=expiredkey' ) );
                } else {
                    self::logAllResetKeys();
                    wp_redirect( home_url( self::getSlug() . '?login=invalidkey' ) );
                }
                exit;
            }

            if ( isset( $_POST['pass1'] ) ) {
                if ( $_POST['pass1'] != $_POST['pass2'] ) {
                    // Passwords don't match
                    Log::addEntry("passwords don't match");
                    $redirect_url = home_url(self::getSlug(self::RESET_PASSWORD));

                    $redirect_url = add_query_arg( 'key', $rp_key, $redirect_url );
                    $redirect_url = add_query_arg( 'login', $rp_login, $redirect_url );
                $redirect_url = add_query_arg( 'error', 'password_reset_mismatch', $redirect_url );

                wp_redirect( $redirect_url );
                exit;
            }

                if ( empty( $_POST['pass1'] ) ) {
                    // Password is empty
                    Log::addEntry("password was empty");
                    $redirect_url = home_url( self::getSlug(self::RESET_PASSWORD));

                    $redirect_url = add_query_arg( 'key', $rp_key, $redirect_url );
                    $redirect_url = add_query_arg( 'login', $rp_login, $redirect_url );
                    $redirect_url = add_query_arg( 'error', 'password_reset_empty', $redirect_url );

                    wp_redirect( $redirect_url );
                    exit;
                }

                // Parameter checks OK, reset password
                Log::addEntry("reset_password()");
                reset_password( $user, $_POST['pass1'] );
                wp_redirect( home_url(self::getSlug() . '?password=changed' ) );
            } else {
                echo "Invalid request.";
            }

            exit;
        }
    }

    /**
     * Requires the user to be logged in.  If not, the user is redirected to the login page.
     * Adapted from https://premium.wpmudev.org/forums/topic/restricting-page-to-logged-in-users-only
     */
    static function requireLoggedInUser() {
        $userLoggedIn = is_user_logged_in();
        Log::addEntry("requireLoggedInUser(" . ($userLoggedIn ? "true" : "false") . ")");
        if (!$userLoggedIn){
            wp_redirect( home_url(self::getSlug()) );
            exit;
        }
    }

    static function addMetaBoxes() {
        add_meta_box( self::MEMBERSHIP_META_TAG, "Membership", array( LoginController::THIS_CLASS, 'addMembershipRequiredOption' ), "page", 'advanced', 'high' );
    }

    static function membershipRequired() {
        global $post;
        $value = get_post_meta($post->ID, self::MEMBERSHIP_META_TAG . '_meta_key', true);
        Log::addEntry(json_encode($post));
        Log::addEntry("membershipRequired($value)");
        $required = $value=='1';
        Log::addEntry("membershipRequired(" . ($required ? "true" : "false") . ")");
        return $required;
    }

    static function addMembershipRequiredOption() {
        global $post;
        $value = get_post_meta($post->ID, self::MEMBERSHIP_META_TAG . '_meta_key', true);
        ?>
        <label for="<?=self::MEMBERSHIP_META_TAG;?>_field">Membership Required?</label>
        <select name="<?=self::MEMBERSHIP_META_TAG;?>_field" id="<?=self::MEMBERSHIP_META_TAG;?>_field" class="postbox">
            <option value="0" <?php if ($value=='0') echo 'selected="selected"';?>>No</option>
            <option value="1" <?php if ($value=='1') echo 'selected="selected"';?>>Yes</option>
        </select>
        <?php
    }

    static function save_postdata($post_id)
    {
        if (array_key_exists(self::MEMBERSHIP_META_TAG . '_field', $_POST)) {
            update_post_meta(
                $post_id,
                self::MEMBERSHIP_META_TAG . '_meta_key',
                $_POST[self::MEMBERSHIP_META_TAG . '_field']
            );
        }
    }

    static function logout_without_confirm($action, $result) {
        /**
         * Allow logout without confirmation
         */
        Log::addEntry("logout_without_confirm()");
        if (($action == "log-out") && !isset($_GET['_wpnonce'])) {
            $redirect_to = isset($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : '/';
            $location = str_replace('&amp;', '&', wp_logout_url($redirect_to));
            Log::addEntry("logging out immediately ... $location");
            header("Location: $location");
            die;
        }
        else {
            Log::addEntry("Error logging out immediately - action: $action");
            Log::addEntry("request: " . json_encode($_GET));
        }
    }

    static function renewAccount() {
        
        // get customer
        $stripeController = new StripeController();
        $token = $stripeController->getStripeToken();
        $customer = $stripeController->getStripeAccount($token);
        
        // re-subscribe user
        $planID = get_option(StripeController::RELIAPOST_PLAN_ID);
        $subscription = BrandRegistrationController::subscribeUser($customer["id"], $planID);
        //Log::addEntry("customer subscribed on Stripe: " . json_encode($subscription));

        if (true) return self::onError("success");
    }

    static function cancel_subscription() {
        //if (true) return self::onError("success");
        $stripeController = new StripeController();
        $token = $stripeController->getStripeToken();
        //if ($token==null) return self::onError("INVALID_TOKEN");
        $sub = $stripeController->getSubscriptionDetails($token);
        //if ($sub==null) return self::onError("INVALID_SUBSCRIPTION");
        $sub->cancel_at_period_end = true;
        $sub->save();
        return self::onError("success");
    }

    static function reactivateAccount() {
        //if (true) return self::onError("success");
        $stripeController = new StripeController();
        $token = $stripeController->getStripeToken();
        //if ($token==null) return self::onError("INVALID_TOKEN");
        $sub = $stripeController->getSubscriptionDetails($token);
        //if ($sub==null) return self::onError("INVALID_SUBSCRIPTION");
        $sub->cancel_at_period_end = false;
        $sub->save();
        return self::onError("success");
    }

    private static function onError($msg) {
        $json = new \stdClass();
        $json->status = $msg;
        echo json_encode($json);
        wp_die();
        return null;
    }
}
