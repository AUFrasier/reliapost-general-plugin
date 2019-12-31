<?php
/**
 * Created by PhpStorm.
 * User: christopherruddell
 * Date: 2/15/18
 * Time: 9:18 PM
 */
namespace reliapost_registration;

$attributes['lost_password_sent'] = isset( $_REQUEST['checkemail'] ) && $_REQUEST['checkemail'] == 'confirm';
// Retrieve possible errors from request parameters
$attributes['errors'] = array();
if ( isset( $_REQUEST['errors'] ) ) {
    $error_codes = explode( ',', $_REQUEST['errors'] );

    foreach ( $error_codes as $error_code ) {
        $attributes['errors'] []= $this->get_error_message( $error_code );
    }
}

?>
<div class="page-wrapper">
<div class="container">
    <div class="row">
        <div class="login_container" style="padding:20px;">
            <div class="sub_container">
                <div id="whitebg1">
                    <img id="logo" src="https://app.reliapost.com/wp-content/uploads/2019/02/reliapost_logo.png" alt="ReliaPost Logo">

                    <!-- Show errors if there are any -->
                    <?php if ( count( $attributes['errors'] ) > 0 ) : ?>
                        <?php foreach ( $attributes['errors'] as $error ) : ?>
                            <div class="row">
                                <p class="login-error">
                                    <?php echo $error; ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if ( $attributes['password_updated'] ) : ?>
                        <div class="row">
                            <p class="login-info">
                                <?php _e( 'Your password has been changed. You can sign in now.', 'personalize-login' ); ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <?php if ( $attributes['lost_password_sent'] ) : ?>
                        <div class="row">
                            <p class="login-info">
                                <?php _e( 'Check your email for a link to reset your password.', 'personalize-login' ); ?>
                            </p>
                        </div>
                    <?php endif; ?>
                    <!-- End showing errors -->

                    <form action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>" method="post" id="login-form">
                        <div class="form-row">
                            <div>
                                <input placeholder="E-mail" type="email" name="log" id="user_login"/>
                            </div>
                        </div>
                        <div class="form-row">
                            <div>
                                <input placeholder="Password" type="password" name="pwd" id="user_pass"/>
                            </div>
                        </div>
                        <button class="login_button">Login</button>
                        <!--<div class="forgetmenot">
                            <label for="rememberme">
                                <?php //esc_html_e( 'Remember Me' ); ?>
                            </label>
                            <div>
                                <input name="rememberme" type="checkbox" id="rememberme" value="forever" <?php checked( $rememberme ); ?> />
                            </div>
                        </div>-->
                        <a href="/forgot-password" class="forgotpassword" style="color: #0D3151 !important;">Forgot your password?</a>
                    </form>
                </div>   
                <div id="whitebg2" id="registerLink" class="center_horizontal">
                    <p class="register_label">Need an account?</p>
                    <button class="register_button"><a href="/register" style="color: white !important;">Register</a></button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
        
<!--end of container-->
<!-- Login page styling-->

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
