<?php
/**
 * Created by PhpStorm.
 * User: personal
 * Date: 10/20/18
 * Time: 10:24 PM
 */

namespace reliapost_registration;


use WP_Error;

class User
{
    public $userId;
    public $email;
    public $username;
    public $lastName;
    public $firstName;
    public $password;
    public $updatedAt;

    const ERROR_EMAIL_EXISTS = 100;
    const ERROR_INVALID_EMAIL = 101;
	const INVALID_EMAIL_VERIFICATION = 102;
	const INVALID_PASSWORD = 103;
    const INVALID_PASSWORD_VERIFICATION = 104;
    const FIRST_NAME_MISSING = 105;
	const LAST_NAME_MISSING = 106;
	const USERNAME_MISSING = 107;

    static function is_error($result) {
        return $result===self::FIRST_NAME_MISSING || 
        $result===self::LAST_NAME_MISSING || 
        $result===self::USERNAME_MISSING || 
        $result===self::ERROR_EMAIL_EXISTS || 
        $result===self::ERROR_INVALID_EMAIL || 
        $result===self::INVALID_EMAIL_VERIFICATION || 
        $result===self::INVALID_PASSWORD || 
        $result===self::INVALID_PASSWORD_VERIFICATION;
    }

    static function createUser($email, $password, $username, $lastName, $firstName) {
        $user = self::register_user($email, $password, $username, $lastName, $firstName);

        /*
        $user = new User();
        $user->email = $email;
        $user->password = hash("sha256",$password);
        $user->updatedAt = current_time("mysql");
        $user->userId = wp_generate_uuid4();

        $data = array([
            'user_id' => $user->userId,
            'email' => $user->email,
            'password' => $user->password,
            'updated_at' => $user->updatedAt
        ]);

        $query = $wpdb->prepare("INSERT INTO " . $dbController->tableUsers
            . " ("
            . DatabaseController::USERS_USER_ID . ","
            . DatabaseController::USERS_EMAIL . ","
            . DatabaseController::USERS_PASSWORD . ","
             . DatabaseController::USERS_UPDATED_AT . ") "
            . "VALUES (%s,%s,%s,%s)"
        , array($user->userId, $user->email, $user->password, $user->updatedAt));

        $result = $wpdb->query($query);

        self::wp_register_user($email, $password);
        */

        return $user;
    }

    static function login($email, $password) {
        global $wpdb;
        $dbController = new DatabaseController();

        $hashed = wp_hash_password($password);

        $queryString = $wpdb->prepare("SELECT " . DatabaseController::USERS_USER_ID . " FROM " . $dbController->tableUsers . " WHERE " . DatabaseController::USERS_EMAIL . " = %s AND " . DatabaseController::USERS_PASSWORD . " = %s",
            array($email, $hashed)
            );
        $results = $wpdb->get_results($queryString);
        if (count($results)>0) {
            $user = new User();
            $user->userId = $results[0];
            $user->email = $email;
            return $user;
        }
        else return null;
    }

    /**
     * Code adapted from https://code.tutsplus.com/tutorials/build-a-custom-wordpress-user-flow-part-2-new-user-registration--cms-23810
     * Validates and then completes the new user signup process if all went well.
     *
     * @param string $email         The new user's email address
     * @param string $password      The new user's password
     *
     * @return int|User         The the user that was created, or error code if failed.
     */
    static function register_user($email, $password, $username, $lastName, $firstName, $verifyEmail, $verifyPassword) {
        Log::addEntry("register_user($email)");

        //form validation
        if(empty($firstName)) {
			return self::FIRST_NAME_MISSING;
        }
        
        if(empty($lastName)) {
			return self::LAST_NAME_MISSING;
        }
        
        if(empty($username)) {
			return self::USERNAME_MISSING;
        }
        
        if ( ! is_email( $email ) ) {
            Log::addEntry("invalid email: " . $email);
            return self::ERROR_INVALID_EMAIL;
        }

        if ( username_exists( $email ) || email_exists( $email ) ) {
            Log::addEntry("email already exists: " . $email);
            return self::ERROR_EMAIL_EXISTS;
        }

		if($email != $verifyEmail || empty($verifyEmail) ) {
			return self::INVALID_EMAIL_VERIFICATION;
		}
		
        if(strlen($password) < '8' || 
        empty($password) || 
        !preg_match("#[0-9]+#",$password) ||
        !preg_match("#[A-Z]+#",$password) || 
        !preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/',$password)) {
			return self::INVALID_PASSWORD;
		}
		
		if($password != $verifyPassword || empty($verifyPassword)) {
			return self::INVALID_PASSWORD_VERIFICATION;
        }

		
        // Save the user's password
//        $password = wp_hash_password($password);

        $user_data = array(
            'user_login'    => $username,
            'first_name'    => $firstName,
            'last_name'     => $lastName,
            'user_email'    => $email,
            'user_pass'     => $password,
            'nickname'      => $firstName,
        );

        $user_id = wp_insert_user( $user_data );
        wp_new_user_notification( $user_id );

        $user = new User();
        $user->password = wp_hash_password($password);
        $user->email = $email;
        $user->userId = $user_id;

        return $user;
    }
}