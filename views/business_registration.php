<?php

namespace reliapost_registration;

function getErrorMessage($code) {
    $resetPasswordLink = "/forgot-password";
    $email = $_POST["email"];
    switch ($code) {
        // Registration errors
        case BusinessRegistrationController::FIRST_NAME_MISSING:
            return __( '<p>First name is missing. This field is required.</p>', 'personalize-login' );
		case BusinessRegistrationController::LAST_NAME_MISSING:
            return __( '<p>Last name is missing. This field is required.</p>', 'personalize-login' );
		case BusinessRegistrationController::USERNAME_MISSING:
            return __( '<p>Username is missing. This field is required.</p>', 'personalize-login' );
        case BusinessRegistrationController::INVALID_EMAIL:
            return __( '<p>The email address you entered is not valid or is missing.</p>', 'personalize-login' );
		case BusinessRegistrationController::INVALID_EMAIL_VERIFICATION:
            return __( '<p>Email verification failed. Make sure your emails match.</p>', 'personalize-login' );
		case BusinessRegistrationController::INVALID_PASSWORD:
            return __( '<p>The password you entered does not meet the requirements or is missing.</p>', 'personalize-login' );
		case BusinessRegistrationController::INVALID_PASSWORD_VERIFICATION:
            return __( '<p>Password verification failed. Make sure your passwords match.</p>', 'personalize-login' );
        case BusinessRegistrationController::ERROR_EMAIL_EXISTS:
            return __( "<p>An account exists with that email address.</p>To reset password <a href='$resetPasswordLink'>click here</a>", 'personalize-login' );
        case BusinessRegistrationController::CLOSED:
            return __( '<p>Registering new users is currently not allowed.</p>', 'personalize-login' );
    }
} 

$pageData['firstname'] = null;
$pageData['businessname'] = null;
$pageData['lastname'] = null;
$pageData['username'] = null;
$pageData['email'] = null;
$pageData['verify-email'] = null;
$pageData['error_message'] = null;
if ( isset( $_REQUEST['register-error'] ) ) {
    $pageData['error_message'] = getErrorMessage( $_REQUEST["register-error"] );
    //populating form fields
    $pageData['businessname'] = $_REQUEST["businessname"];
    $pageData['firstname'] = $_REQUEST["firstname"];
    $pageData['lastname'] = $_REQUEST["lastname"];
    $pageData['username'] = $_REQUEST["username"];
    $pageData['email'] = $_REQUEST["email"];
    $pageData['verify-email'] = $_REQUEST["verify-email"];
}


?>
<div class="page-wrapper">
<div class="container">
    <div class="row"> 
        <div class="registration_container">
        <img id="logo" src="https://app.reliapost.com/wp-content/uploads/2019/02/reliapost_logo.png" alt="ReliaPost Logo">
        <div class="center_horizontal">
            <h2 id="user_registration_title">Register</h2>
            <h4 id="alreadyRegistered">Already have an account? <a href="<?=wp_login_url(); ?>">Login Here</a></h4>
        </div>
            <div id="registration-form-container">
                <!--Any registration errors here -->
                <?php if ( $pageData['error_message'] !== null ) : ?>
                    <div class="registration-error">
                        <?php echo $pageData['error_message']; ?>
                    </div>
                <?php endif; ?>
                <!--End any registration errors -->
                <form action="<?php echo wp_registration_url(); ?>" method="post" id="registration-form">
                    
                    <div class="form-row">
                        <label for="businessname">
                            Business Name
                        </label>
                        <div>
                            <input type="text" name="businessname" id="businessname" value="<?php if($pageData['businessname'] !== null) echo $pageData['businessname'];?>"/>
                        </div>
                    </div>
                    <div class="form-row">
                        <label for="firstname">
                            First Name
                        </label>
                        <div>
                            <input type="text" name="firstname" id="firstname" value="<?php if($pageData['firstname'] !== null) echo $pageData['firstname'];?>"/>
                        </div>
                    </div>
                    <div class="form-row">
                        <label for="lastname">
                            Last Name
                        </label>
                        <div>
                            <input type="text" name="lastname" id="lastname" value="<?php if($pageData['lastname'] !== null) echo $pageData['lastname'];?>"/>
                        </div>
                    </div>
                    <div class="form-row">
                        <label for="username">
                            Username
                        </label>
                        <div>
                            <input type="text" name="username" id="username" value="<?php if($pageData['username'] !== null) echo $pageData['username'];?>"/>
                        </div>
                    </div>
                    <div class="form-row">
                        <label for="email">
                            Email
                        </label>
                        <div>
                            <input type="email" name="email" id="email" value="<?php if($pageData['email'] !== null) echo $pageData['email'];?>"/>
                        </div>
                    </div>
                    <div class="form-row">
                        <label for="verify-email">
                            Verify Email
                        </label>
                        <div>
                            <input type="email" name="verify-email" id="verify-email" value="<?php if($pageData['verify-email'] !== null) echo $pageData['verify-email'];?>"/>
                        </div>
                    </div>
                    <div class="form-row">
                        <label for="password">
                            Password
                        </label>
                        <ul class="password-requirements">
                            <li>Must be at least 8 characters long</li>
                            <li>Must contain at least one number</li>
                            <li>Must contain at least one uppercase character</li>
                            <li>Must contain at least one special character (i.e. !, $, &, #, etc.)</li>
                        </ul>
                        <div>
                            <input type="password" name="password" id="password"/>
                        </div>
                    </div>
                    <div class="form-row">
                        <label for="verify-password">
                            Verify Password
                        </label>
                        <div>
                            <input type="password" name="verify-password" id="verify-password"/>
                        </div>
                    </div>
                    <div id="register-text1">
                        <p>Click the "Sign Up!" button below to begin sharing!</p>
                    </div>
                    <button class="signup_button">Sign-up!</button>
                    <div class="center_horizontal" id="formErrors"></div>
                </form>
                
            </div>
        </div>
        <!-- end registration_container -->
    </div>
</div>
</div>

<!-- Register page styling-->

<style>
   #main {
        width: 100% !important;
        height: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
   }
   #side-header {
        display: none !important; 
   }
   #content {
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;

   }
   .entry {
        margin: 0 !important;
   }
   .site-footer {
       display: none !important;
   }
   .page-wrapper {
        padding: 0 !important;
    }
</style>