<?php

class BackendHooks {
    const PREFIX_NOPRIV = "wp_ajax_nopriv_";
    const PREFIX_USER = "wp_ajax_";

    const BRAND_REGISTRATION_CONTROLLER = "\\reliapost_registration\\BrandRegistrationController";
    const BUSINESS_REGISTRATION_CONTROLLER = "\\reliapost_registration\\BusinessRegistrationController";
	const DATABASE_CONTROLLER = "\\reliapost_registration\\DatabaseController";
    const LOGIN_CONTROLLER = "\\reliapost_registration\\LoginController";
    const STRIPE_HELPER = "\\reliapost_registration\\StripeHelper";
    const STRIPE_CONTROLLER = "\\reliapost_registration\\StripeController";
    const USERCATEGORY_CONTROLLER = "\\reliapost_registration\\UserCategoryController";
    const PROFILE_CONTROLLER = "\\reliapost_registration\\ProfileController";
    const ADD_POST_CONTROLLER = "\\reliapost_registration\\AddPostController";
	
    function addHooks() {
        //hide the admin toolbar
        add_filter("show_admin_bar", "__return_false");

        $this->addHook("reliapost_createProduct", self::STRIPE_HELPER, "createProduct", true);
        $this->addHook("reliapost_createSubscription", self::STRIPE_HELPER, "createSubscription", true);
        $this->addHook("reliapost_login", self::LOGIN_CONTROLLER, "login");
        $this->addHook("reliapost_saveSettings", self::STRIPE_HELPER, "saveSettings", true);
        $this->addHook("reliapost_addCategoryToUser", self::USERCATEGORY_CONTROLLER, "addCategoryToUser", false);
        $this->addHook("reliapost_removeCategoryFromUser", self::USERCATEGORY_CONTROLLER, "removeCategoryFromUser", false);
        $this->addHook("reliapost_updateProfile", self::PROFILE_CONTROLLER, "updateProfile", false);
        $this->addHook("reliapost_addpost", self::ADD_POST_CONTROLLER, "addPost", false);
		$this->addHook("reliapost_deletePost", self::DATABASE_CONTROLLER, "deleteScheduledMessage", false);
		$this->addHook("reliapost_editScheduledPost", self::DATABASE_CONTROLLER, "editScheduledPost", false);
		$this->addHook("reliapost_updateSharedPost", self::DATABASE_CONTROLLER, "editSharedPost", false);
		$this->addHook("reliapost_deleteSharedPost", self::DATABASE_CONTROLLER, "deleteSharedPost", false);
		$this->addHook("reliapost_reschedulePost", self::DATABASE_CONTROLLER, "rescheduleMessage", false);
        $this->addHook('reliapost_cancel_subscription', self::LOGIN_CONTROLLER, "cancel_subscription", false);
        $this->addHook('reliapost_reactivate_subscription', self::LOGIN_CONTROLLER, "reactivateAccount", false);
        $this->addHook('reliapost_renew_subscription', self::LOGIN_CONTROLLER, "renewAccount", false);

        add_action('admin_menu', 'createAdminMenu');

        //override default Wordpress login/registration hooks
        add_action( 'login_form_register', array( self::BRAND_REGISTRATION_CONTROLLER, 'redirect_to_custom_register' ) );
        add_action( 'login_form_register', array( self::BUSINESS_REGISTRATION_CONTROLLER, 'redirect_to_custom_register' ) );
        add_action( 'login_form_register', self::BRAND_REGISTRATION_CONTROLLER, "registerBrand");
        add_action( 'login_form_register', self::BUSINESS_REGISTRATION_CONTROLLER, "registerBusiness");
        add_action( 'login_form_login', array( self::LOGIN_CONTROLLER, 'redirect_to_custom_login' ) );
        add_filter( 'login_redirect', array( self::LOGIN_CONTROLLER, 'redirect_after_login' ), 10, 3 );
        add_filter( 'authenticate', array( self::LOGIN_CONTROLLER, 'maybe_redirect_at_authenticate' ), 101, 3 );
        add_action( 'wp_logout', array( self::LOGIN_CONTROLLER, 'redirect_after_logout' ) );
        add_action( 'login_form_lostpassword', array( self::LOGIN_CONTROLLER, 'redirect_to_custom_lostpassword' ) );
        add_action( 'login_form_lostpassword', array( self::LOGIN_CONTROLLER, 'do_password_lost' ) );
        add_filter( 'retrieve_password_message', array( self::LOGIN_CONTROLLER, 'replace_retrieve_password_message' ), 10, 4 );
        add_action( 'login_form_rp', array( self::LOGIN_CONTROLLER, 'redirect_to_custom_password_reset' ) );
        add_action( 'login_form_resetpass', array( self::LOGIN_CONTROLLER, 'redirect_to_custom_password_reset' ) );
        add_action( 'login_form_rp', array( self::LOGIN_CONTROLLER, 'do_password_reset' ) );
        add_action( 'login_form_resetpass', array( self::LOGIN_CONTROLLER, 'do_password_reset' ) );
        add_action( 'check_admin_referer', array(self::LOGIN_CONTROLLER, 'logout_without_confirm'), 10, 2);

        //add option to the quick edit for pages
        add_action( 'add_meta_boxes', array(self::LOGIN_CONTROLLER, 'addMetaBoxes') );
        add_action('save_post', array(self::LOGIN_CONTROLLER, 'save_postdata'));
    }

    function addHook($key, $class, $functionName, $adminRequired = false) {
        add_action( self::PREFIX_NOPRIV . $key, array($class, $functionName) );
        add_action( self::PREFIX_USER . $key, array($class, $functionName) );

    }


}

function createAdminMenu() {
    add_menu_page( 'Stripe', 'Stripe', 'manage_options', 'reliapost_admin_stripe', array("\\reliapost_registration\\AdminController", "display_page_stripe") );
    add_submenu_page( 'reliapost_admin_stripe', 'Plans', 'Plans', 'manage_options', 'reliapost_admin_stripe_plans', array("\\reliapost_registration\\AdminController", "display_page_stripe_plans") );
}
