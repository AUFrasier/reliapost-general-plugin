<?php
/**
 * Created by Chris Ruddell.
 * Date: 11/20/18
 * Time: 6:57 AM
 */

use reliapost_registration\UserCategoryController;

/**
 * Purpose of this page is to allow user to update their profile info
 * Main elements of page:
 *
 * 1) Email
 * 2) Password
 * 3) Selected categories
 */

$userCategoryController = new UserCategoryController();
$userCategories = $userCategoryController->getUserCategories();
$categories = get_categories();

function getCategoryBySlug($slug, $categories) {
    foreach ($categories as $category) {
        if ($category->slug == $slug) return $category;
    }
    return null; 
}

$user = wp_get_current_user()->data;
$username = $user->user_login;
$email = $user->user_email;
$userID = $user->ID;

$user_info = get_userdata( $userID );
$type = $user_info->roles[0];
$firstName = $user_info->first_name;
$lastName = $user_info->last_name;
$name = $firstName. " ". $lastName;
if ($name == " ") {
    $name = "Your account does not have a name.";
}

$stripeController = new \reliapost_registration\StripeController();
$stripeToken = $stripeController->getStripeToken();
//$customer = $stripeController->getStripeAccount($stripeToken);
$cancelPeriodEnd = $stripeController->getCancelPeriodEndInfo($stripeToken);
$remainingDays = $stripeController->getRemainingDaysOnAccount($stripeToken);
$sub = $stripeController->getSubscriptionDetails($stripeToken);
$sub_id = $sub->id;
$status = $sub->status;
$planId = $sub->items->data[0]->plan->id;
$cancelledSubs = [];
if (is_page('homes') || is_page('my-profile')) {
    if ($type == "subscriber") {
        $json_data = json_encode($stripeController->getCancelledSubs($stripeToken));
        $decoded = json_decode($json_data);
        $cancelledSubsTotal = count($decoded->data);
        $cancelledSubs = $stripeController->getCancelledSubs($stripeToken);
    }
}


//$testMode = isset($_GET["test"]);

//if ($testMode) $remainingDays = $_GET["test"];

?>
<div class="page-wrapper">

<form>
    <h1 id="account_title">My Account</h1>

    <div id="account-text1">
    <p>Welcome and thank you for choosing Reliapost, where you can choose custom, approved vendor content to post on your social media platforms: Facebook and Twitter.</p>
   
    </div>
    <div id="name-redirect" style="display:none;">
        <p>Thank you for updating your username.  We have updated your information. You will be redirected to the login page shortly.</p>
    </div>
    <div id="email-redirect" style="display:none;">
        <p>Thank you for updating your email address.  We have updated your information. You will be redirected to the login page shortly. There you will be able to login with your new email address.</p>
    </div>
    <div id="password-redirect" style="display:none;">
        <p>Thank you for updating your password.  We have updated your information. You will be redirected to the login page shortly. There you will be able to login with your new password.</p>
    </div>

    <div class="field-container">
        <label id="name-label">Name</label>
        <div id="account-name-row">
            <div id="account-section">
                <span id="account_name"><?= $name ?></span>
            </div>
        </div>
    </div>

    <div class="field-container">
        <label id="name-label">Account Type</label>
        <div id="account-type-row">
            <div id="account-section">
                <span id="account_type"><?=ucfirst($type);?></span>
            </div>
        </div>
    </div>

    <div class="field-container">
        <div id="error-note-name" style="display:none;">
            <p>Your updated username needs to be different than your original username.  Please try again.</p>
        </div>
        <label id="name-label">Username</label>
        <div id="name-readonly">
            
            <div id="name-section">
                <span id="user_name"><?=$username;?></span>
            </div>
            <button class="edit-button" id="name-editButton" onclick="editField('name');return false;">EDIT</button>
        </div>
        <div id="name-edit" style="display:none;">
            <input type="text" name="name" value="<?=$username;?>" placeholder="Name" id="name-field">
            <div class="save-buttons" field="name"></div>
        </div>
    </div>

    <div class="field-container">
        <div id="error-note-email" style="display:none;">
            <p>Your updated email needs to be different than your original email.  Please try again.</p>
        </div>
        <label id="email-label">Email</label>
        <div id="email-readonly">
            <div id="email-section">
                <span id="user_email"><?=$email;?></span>
            </div>
            <button class="edit-button" id="email-editButton" onclick="editField('email');return false;">EDIT</button>
        </div> 
        <div id="email-edit" style="display:none;">
            <input type="text" name="email" value="<?=$email;?>" placeholder="Email Address" id="email-field">
            <div class="save-buttons" field="email"></div>
        </div>
    </div>

    <div class="field-container">  
        <label id="password-label">Password</label> 
        <div id="password-readonly">
            <div id="password-section">
                <span id="user_password">**********</span>
            </div>
            <button class="edit-button" id="password-editButton" onclick="editField('password');return false;">EDIT</button>
        </div>
        <div id="password-edit" style="display:none;">
            <input type="password" id="password-field" name="password" value="" placeholder="Enter Password"><br/><span class="error-hint" id="password-error-1"> </span>
            <input type="password" id="password-confirm-field" name="password-confirm" value="" placeholder="Confirm Password"><br/><span class="error-hint" id="password-error-2"> </span>
            <div class="save-buttons" field="password"></div>
        </div>
    </div>
    
</form>

<p id="cat-text">Choose your categories by clicking the down arrow. This allows you to filter the content that is displayed on the "Available Posts" page.</p>

<div id="categoryAccordian">Categories<i id="accordianIcon" class="fas fa-angle-down"></i></div>
    <div id="category_content">
        <div id="category-container">
            <label id="categoryLabel">Filter your categories here:</label>
            <?php
            if ($cancelledSubsTotal >= 0 && $status != null || $type == "administrator") {
                foreach ($categories as $category) {
                    $slug = $category->slug;
                    $name = $category->name;
                    echo
                    "<div class='row' style='margin-left:20px;'>
                    <div id='categoryName' class=''>$name</div>
                    <div class='checkboxContainer'>
                        <input class='categoryCheckbox' name='$slug' id='$slug' type='checkbox'>
                    </div>
                    </div>";
                }
            } else {
                echo "<p style='margin-left:20px; color:red;'>Please renew your subscription</p>";
            }

            ?>
        </div>
    </div>

    <br/><br/>

    <?php
    if ($type == "subscriber" && $status == "trialing" && $cancelPeriodEnd === false) {
    ?>
    <p id="account-text2" class="trialtext" style="text-align:center;">
    You are currently in a trial period.  You have a total of 15 days.  You have <?=$remainingDays;?>  day(s) remaining in your trial period.<br>
    If you would like to continue using ReliaPost after the trial period, no action is needed.  You will be automatically billed and will be entered into your first billing period.<br>
    If you do not want to continue using ReliaPost, please be sure to cancel your account (by clicking the <strong style="color: #f7b429">button</strong> below) before your trial period ends.
    </p>
    <?php
    }

    if ($type == "subscriber" && $status == "active"  && $cancelPeriodEnd === false) {
    ?>
    <p id="account-text2" class="activetext" style="text-align:center;">
    Your account is currently ACTIVE.<br>
    </p>

    <?php
    }

    if ($type == "subscriber" && $status !== null && $cancelPeriodEnd === true) {
    ?>
    <p id="account-text2" class="deactivatetext" style="text-align:center;">
    Your account is currently DEACTIVATED.<br>
    </p>

    <?php
    }

    if ($type == "subscriber" && $cancelledSubsTotal >= 1 && $status == null) {
    ?>
    <p id="account-text2" class="renewtext" style="text-align:center;">
    Your account is currently CANCELLED.<br>
    </p>

    <?php
    }

    if ($type == "subscriber" && $cancelledSubsTotal >= 1 && $status == null) {
    ?>
    <p id="account-text2" class="renewtext" style="text-align:center;">
    If you would like to renew your account, please click the <strong style="color: #f7b429">button</strong> below. This will enter you into a 15 day trial period.<br>
    </p>
    <a id="renew" class="rp_profile_button" href="#" id="cancelAccountButton">RENEW ACCOUNT</a>

    <?php
    }

    if ($type == "subscriber" && $status !== 'trialing' && $cancelPeriodEnd === false) {
    ?>
    <p id="account-text2" class="canceltext" style="text-align:center;">
    If you do not want to continue using ReliaPost, click the <strong style="color: #f7b429">button</strong> below.  Your account will close when your current period is complete.
    </p>
    <?php
    }

    if ($type == "subscriber" && $cancelPeriodEnd === false) {
    ?>
    <a id="cancel" class="rp_profile_button" href="#" id="cancelAccountButton">CANCEL ACCOUNT</a>
    <?php
    }

    if ($type == "subscriber" && $cancelPeriodEnd === true) {
    ?>
    <p id="account-text2" class="reactivatetext" style="text-align:center;">You have <?=$remainingDays;?> day(s) until your account is closed completely.  By clicking the <strong style="color: #f7b429">button</strong> below, you can reactivate your account before your account is closed.</p>
    <a id="reactivate" class="rp_profile_button" href="#" id="cancelAccountButton">REACTIVATE ACCOUNT</a>
    <?php
    }

    if ($type == "contributor") {
    ?>
    <p id="account-text2" class="contributortext" style="text-align:center;">
    If you would like to cancel your account, please contact Art Unlimited at <a style="color: #f7b429 !important;" href="tel:+12186662512">(218) 666-2512</a>.<br>
    Or, send us an email at <a style="color: #f7b429 !important;" href="mailto:info@artunlimitedusa.com">info@artunlimitedusa.com</a>.
    </p>
    <?php
    }
    ?>
    <a id="logout" class="rp_profile_button" href="<?php echo wp_logout_url(home_url()) ?>">LOG OUT</a><br/>

    <script>
        var $ = jQuery;

        $("#categoryAccordian").click(function(){
                $("#category_content").toggle();
                $("#accordianIcon").toggleClass('fa-angle-down fa-angle-up');
            });

        $("#cancel").on("click", function(e){
            e.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: "This will cancel your account. Your account will close after your current period.",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, I want to cancel',
                cancelButtonText: 'Dismiss'
            }).then((result) => {
                if (result.value) {
                    //handle account deactivation
                    deactivateAccount();
                }
            })
        });

        function deactivateAccount() {
            var data = {
                action:'reliapost_cancel_subscription'
            };
            
            postDataToBackend(data, function(response) {
                console.log("postDataToBackend()");
                console.log(response);
                var status = response["status"];
                if (status=="success" || status=="success0") {
                    Swal.fire({
                    title:'Success',
                    text:'Thank you. Your account will close in <?=$remainingDays;?> day(s).'
                    }).then((result) => {
                        if (result.value) {
                            location.href="/my-profile";
                        }
                    })
                }
                else {
                    Swal.fire({
                        title:'Error cancelling account',
                        text:'Please contact support'
                    });
                }
            });
        }

        $("#reactivate").on("click", function(e){
            e.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: "This will reactivate your account.  You have <?=$remainingDays;?> day(s) remaining in your current period.",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, I want to reactivate',
                cancelButtonText: 'Dismiss'
            }).then((result) => {
                if (result.value) {
                    //handle account reactivation
                    reactivateAccount();
                }
            })
        });

        function reactivateAccount() {
            var data = {
                action:'reliapost_reactivate_subscription'
            };

            postDataToBackend(data, function(response) {
                console.log("postDataToBackend()");
                console.log(response);
                var status = response["status"];
                if (status=="success" || status=="success0") {
                    Swal.fire({
                    title:'Success',
                    text:'Thank you. Your account account has been reactivated.'
                    }).then((result) => {
                        if (result.value) {
                            location.href="/my-profile";
                        }
                    })
                }
                else {
                    Swal.fire({
                        title:'Error reactivating account',
                        text:'Please contact support'
                    });
                }
            });
        }

        $("#renew").on("click", function(e){
            e.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: "This will renew your account and restart billing after your 15 day trial period.",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, I want to renew',
                cancelButtonText: 'Dismiss'
            }).then((result) => {
                if (result.value) {
                    //handle account reactivation
                    renewAccount();
                }
            })
        });

        function renewAccount() {
            var data = {
                action:'reliapost_renew_subscription'
            };

            postDataToBackend(data, function(response) {
                console.log("postDataToBackend()");
                console.log(response);
                var status = response["status"];
                if (status=="success" || status=="success0") {
                    Swal.fire({
                    title:'Success',
                    text:'Thank you. Your account account has been renewed.'
                    }).then((result) => {
                        if (result.value) {
                            location.href="/my-profile";
                        }
                    })
                }
                else {
                    Swal.fire({
                        title:'Error renewing account',
                        text:'Please contact support'
                    });
                }
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
</div>