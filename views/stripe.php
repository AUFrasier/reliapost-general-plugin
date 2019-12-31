<?php
/**
 * Created by PhpStorm.
 * User: personal
 * Date: 10/20/18
 * Time: 11:58 PM
 */

use \reliapost_registration\Settings;

$productId = get_option(\reliapost_registration\StripeController::OPTION_PRODUCT_ID, "");
$productName = get_option(\reliapost_registration\StripeController::OPTION_PRODUCT_NAME, "");
$settingsController = new Settings();
$stripeKey = $settingsController->stripeKey;
$stripePublicKey = $settingsController->stripePublicKey;
?>
<div class="container" style="margin-top:75px;">
    <div class="row">
        <div class="col-md-6">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">Setting</th>
                    <th scope="col">Value</th>
                    <th scope="col">Action</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <th>Stripe Public Key</th>
                    <td><input id="stripePublicKey" type="text" value="<?=$stripePublicKey;?>"></td>
                </tr>
                <tr>
                    <th>Stripe Secret Key</th>
                    <td><input id="stripeKey" type="text" value="<?=$stripeKey;?>"></td>
                </tr>
                <tr>
                    <th scope="row">Product</th>
                    <td><?=$productName;?></td>
                    <td><button id="createProduct" onclick="createProduct();">Create</button></td>

                </tr>
                </tbody>
            </table>
            <button onclick="save()">Save</button>
        </div>
    </div>
    <div class="row">
        <p style="margin-top:20px;">Log:</p>
        <pre id="formErrors"></pre>
    </div>
</div>

<script>
    function save() {
        var data = {
            action:'reliapost_saveSettings',
            stripeKey:jQuery("#stripeKey").val(),
            stripePublicKey:jQuery("#stripePublicKey").val()
        };

        var url = "/wp-admin/admin-ajax.php";

        var settings = {
            "async": true,
            "crossDomain": true,
            "url": url,
            "method": "POST",
            "headers": {
                "Content-Type": "application/x-www-form-urlencoded",
                "cache-control": "no-cache"
            },
            "data": data
        };

        jQuery.ajax(settings)
            .done(function(data, statusText, xhr) {

                var status = xhr.status;
                if (status==200) {
                    console.log("success posting to " + url);
                    console.log(statusText);
                    console.log(xhr);
                    console.log(data);
                    jQuery("#formErrors").html(data);
                }
                else {
                    console.log("Error (" + status + "): " + statusText);
                    alert("Error posting: " + statusText);
                }
            })
            .error(function(xhr, statusText, data) {
                console.log("data:");
                console.log(data);
                console.log("statusText:");
                console.log(statusText);
                console.log("xhr:");
                console.log(xhr);
                var status = xhr.status;
                if (status==200) {
                    console.log("success posting to " + url);
                    console.log(statusText);
                }
                else {
                    console.log("Error (" + status + "): " + statusText);
                    alert("Error posting: " + statusText);
                }
            });
    }
    function createProduct() {
        var name = prompt("What should be the name of the product?", "");

        if (name!=null && name.length>0) {
            var data = {
                action:"reliapost_createProduct",
                name:name
            };
            var url = "/wp-admin/admin-ajax.php";
            var settings = {
                "async": true,
                "crossDomain": true,
                "url": url,
                "method": "POST",
                "headers": {
                    "Content-Type": "application/x-www-form-urlencoded",
                    "cache-control": "no-cache"
                },
                "data": data
            }
            jQuery.ajax(settings).done(function(data){
                console.log(data);
            });
        }
    }
</script>
