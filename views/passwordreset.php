<?php
/**
 * Created by PhpStorm.
 * User: christopherruddell
 * Date: 2/15/18
 * Time: 9:18 PM
 */
namespace reliapost_registration;

if (!isset($pageData)) $pageData = [];
?>

<div id="password-reset-form" class="widecolumn">
    <?php if ( $pageData['show_title'] ) : ?>
        <h3><?php _e( 'Pick a New Password', 'personalize-login' ); ?></h3>
    <?php endif; ?>

    <form name="resetpassform" id="resetpassform" action="<?php echo site_url( 'wp-login.php?action=resetpass' ); ?>" method="post" autocomplete="off">
        <input type="hidden" id="user_login" name="rp_login" value="<?php echo esc_attr( $pageData['login'] ); ?>" autocomplete="off" />
        <input type="hidden" name="rp_key" value="<?php echo esc_attr( $pageData['key'] ); ?>" />

        <?php if ( count( $pageData['errors'] ) > 0 ) : ?>
            <?php foreach ( $pageData['errors'] as $error ) : ?>
                <p>
                    <?php echo $error; ?>
                </p>
            <?php endforeach; ?>
        <?php endif; ?>

        <ul style="margin: 0;" class="password-requirements">
            <li>Must be at least 8 characters long</li>
            <li>Must contain at least one number</li>
            <li>Must contain at least one uppercase character</li>
            <li>Must contain at least one special character (i.e. !, $, &, #, etc.)</li>
        </ul>
        <p>
            <label for="pass1"><?php _e( 'New password', 'personalize-login' ) ?></label>
            <input type="password" name="pass1" id="pass1" class="input" size="20" value="" autocomplete="off" />
        </p>
        <p>
            <label for="pass2"><?php _e( 'Repeat new password', 'personalize-login' ) ?></label>
            <input type="password" name="pass2" id="pass2" class="input" size="20" value="" autocomplete="off" />
        </p> 

        <p class="resetpass-submit">
            <input type="submit" name="submit" id="resetpass-button"
                   class="button" value="<?php _e( 'Reset Password', 'personalize-login' ); ?>" />
        </p>
    </form>
</div>

<style>
    #resetpass-button {
        width: 55%;
        height: 40px;
        margin: 0 auto;
        margin-top: 10px;
        font-size: 22px;
        background-color: white !important;
        color: #0D3151 !important;
        display: block;
        border-radius: 30px;
        padding-top: 3px;
        border: none;
        font-family: 'Muli', sans-serif;
    }
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
        padding: 40px !important;

   }
   .entry {
        margin: 0 !important;
   }
   .site-footer {
       display: none !important;
   }
</style>
