<?php

namespace reliapost_registration;

?>

<?php if ( count( $attributes['errors'] ) > 0 ) : ?>
    <?php foreach ( $attributes['errors'] as $error ) : ?>
        <p>
            <?php echo $error; ?>
        </p>
    <?php endforeach; ?>
<?php endif; ?>

<?php
// Check if user just logged out
$attributes['logged_out'] = isset( $_REQUEST['logged_out'] ) && $_REQUEST['logged_out'] == true;
?>
<div id="password-lost-form" class="widecolumn tan-bg white center_horizontal" style=" max-width: 100% !important; padding:20px;">
<img id="logo" src="https://app.reliapost.com/wp-content/uploads/2018/09/ReliaPost_LOGO_HORIZONTAL_COLOR_HORIZONTAL_COLOR.png" alt="ReliaPost Logo">
    <?php if ( $attributes['show_title'] ) : ?>
        <h3><?php _e( 'Forgot Your Password?', 'personalize-login' ); ?></h3>
    <?php endif; ?>

    <p id="password-lost-text">
        <?php
        _e(
            "Enter your email address and we'll send you a link you can use to pick a new password.",
            'personalize_login'
        );
        ?>
    </p>

    <form id="lostpasswordform" action="<?php echo wp_lostpassword_url(); ?>" method="post">
        <p class="form-row">
            <label for="user_login"><?php _e( 'Email', 'personalize-login' ); ?>
                <input type="text" name="user_login" id="user_login">
        </p>

        <p class="lostpassword-submit">
            <input type="submit" name="submit" class="lostpassword-button"
                   value="<?php _e( 'Reset Password', 'personalize-login' ); ?>"/>
        </p>
    </form>
</div>

<style>
   #main {
        width: 100% !important;
        height: 100% !important;
        margin: 0 !important;
   }
   #side-header {
        display: none !important; 
   }
   #content {
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;

   }
   .site-footer {
        display: none !important;
   }
</style>
