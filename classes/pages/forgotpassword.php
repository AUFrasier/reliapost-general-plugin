<?php
/**
 * Created by PhpStorm.
 * User: christopherruddell
 * Date: 2/15/18
 * Time: 9:12 PM
 */

namespace reliapost_registration\Pages;

use reliapost_registration\LoginController;
use reliapost_registration\View;

class Forgotpassword extends PageAbstract
{

    public function displayPage()
    {
        $this->configure();
        $pageData = [];
        View::displayPage('forgotpassword', $pageData);
    }

    protected function configure()
    {
        wp_enqueue_style("roboto", "https://fonts.googleapis.com/css?family=Roboto");
        wp_enqueue_style("quicksand", "https://fonts.googleapis.com/css?family=Quicksand");
        wp_enqueue_style("source_code_pro", "https://fonts.googleapis.com/css?family=Source+Code+Pro");

        // Retrieve possible errors from request parameters
        $attributes['errors'] = array();
        if ( isset( $_REQUEST['errors'] ) ) {
            $error_codes = explode( ',', $_REQUEST['errors'] );

            foreach ( $error_codes as $error_code ) {
                $attributes['errors'] []= $this->get_error_message( $error_code );
            }
        }
    }

    function get_error_message($error_code) {
        switch ($error_code) {
            // Lost password

            case 'empty_username':
                return __( 'You need to enter your email address to continue.', 'personalize-login' );

            case 'invalid_email':
            case 'invalidcombo':
                return __( 'There are no users registered with this email address.', 'personalize-login' );
        }
    }
}