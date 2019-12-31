<?php
/**
 * Created by PhpStorm.
 * User: personal
 * Date: 10/20/18
 * Time: 11:58 PM
 */

//query database for current subscription plans
$databaseController = new \reliapost_registration\DatabaseController();

$subscriptions = \reliapost_registration\Subscription::get();
$frequencies = ["day", "week", "month", "year"];

$stripeController = new \reliapost_registration\StripeController();
$product = $stripeController->getProduct();
$plans = $stripeController->getSubscriptions()["data"];
$subscriptions = $stripeController->getCustomerSubscriptions()["data"];
?>

<div class="container">
    <div class="row">
        <h3>Current Stripe Plan:</h3>
    </div>
    <div class="row">
        <div class="col-md-3 header">Stripe Id</div>
        <div class="col-md-3">Amount</div>
        <div class="col-md-3">Frequency</div>

    </div>
    <?php
    foreach ($plans as $plan) {
        $planId = $plan["id"];
        echo "\t<div class='row'>\n";
        echo "\t\t<div class='col-md-3'>" . $planId . "</div>\n";
        echo "\t\t<div class='col-md-3'>" . $plan["amount"]/100 . "</div>\n";
        echo "\t\t<div class='col-md-2'>" . $plan["interval"] . "</div>\n";
        echo "\t</div>\n";
    }
    ?>
</div>

<br/>
<div style="margin-left:50px;margin-right:50px;height:300px;overflow-y:scroll">
    <div class="row">
        <div class="col-md-3"><b>Customer</b></div>
        <div class="col-md-3"><b>Subscription Id</b></div>
        <div class="col-md-2"><b>Created</b></div>
        <div class="col-md-2"><b>Period End</b></div>
        <div class="col-md-2"><b>Plan Id</b></div>
    </div>
    <?php
    foreach ($subscriptions as $subscription) {
        $id = $subscription["id"];
        $customer = $subscription["customer"];
        $created = $subscription["created"];
        $daysUntilDue = $subscription["current_period_end"];
        $item = $subscription["items"]["data"][0];
        $planId = $item["plan"]["id"];

        $created = gmdate("Y-m-d\TH:i:s\Z", $created);
        $daysUntilDue = gmdate("Y-m-d\TH:i:s\Z", $daysUntilDue);

        echo <<<HTML
    <div class="row">
        <div class="col-md-3"><b>$customer</b></div>
        <div class="col-md-3"><b>$id</b></div>
        <div class="col-md-2"><b>$created</b></div>
        <div class="col-md-2"><b>$daysUntilDue</b></div>
        <div class="col-md-2"><b>$planId</b></div>
    </div>
HTML;

    }
    ?>

</div>

<br/>
<hr>
<br/>
<div class="container">
    <div class="row">
        <h3>Update Plan</h3>
    </div>
    <div class="row">
        <div class="col-md-3">Frequency</div>
        <div class="col-md-9">
            <select id="frequency">
                <option value="month">Monthly</option>
                <option value="day">Daily</option>
                <option value="week">Weekly</option>
                <option value="year">Annually</option>
                <option value="lifetime">Lifetime (not yet supported)</option>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">Amount (in USD)</div>
        <div class="col-md-9"><input type="number" id="cost" placeholder="$19.99"/></div>
    </div>
    <div class="row">
        <button onclick="createPlan();">Submit</button>
    </div>
</div>

<script>
    function createPlan() {
        var amount = jQuery("#cost").val();
        var frequency = jQuery("#frequency").val();

        var data = {
            action:"reliapost_createSubscription",
            cost:amount,
            frequency:frequency
        }

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
            //reload page
            location.reload(true);
        });

    }
</script>

<!--
<?=json_encode($subscriptions);?>
-->