<?php
/**
 * Created by PhpStorm.
 * User: personal
 * Date: 1/3/19
 * Time: 9:40 AM
 */
$host = $_SERVER["HTTP_HOST"];
$local = strpos($host, ".test") > 0 || strpos($host, ".app") > 0;
//$facebookAppId = [
//    "dev"=> "368790380198448",
//    "prod"=>"196254564517827"
//];
$facebookAppId = [];
require_once $_SERVER['DOCUMENT_ROOT'].'/wp-content/themes/wprig-reliapost/reliapost-addon/artunlimited_plugin/databasecontroller_theme.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/wp-content/themes/wprig-reliapost/reliapost-addon/artunlimited_plugin/TwitterHelper.php';
$twitterUser = null;
$controller = new \ArtUnlimited\DatabaseController_Theme();
$settings = $controller->getSettings();
$facebookAppId["dev"] = $settings->facebookAppId;
$facebookAppId["prod"] = $settings->facebookAppId;


$userId = wp_get_current_user()->ID;
global $wpdb;
$tableName = "wp_" . "scheduled_posts";
$sql = "SELECT * FROM $tableName WHERE userId = '$userId' ORDER BY `id` DESC";
$data = $wpdb->get_results($sql);


define("RP_MAX_MESSAGE_LENGTH", 80);

class ScheduledPost {
    public $id;
    public $message_body;
    public $scheduled_time;
    public $post_type;
    public $userId;
    public $posted_at;
    public $status;
	public $link;
	public $page_name;
	public $post_id;
	public $image_url;
	public $category;
	public $source_type;
}

$categories = $pageData["categories"];

$scheduledPosts = [];

if (is_array($data)) foreach ($data as $obj) {
    $scheduledPost = new ScheduledPost();
    $scheduledPost->id = $obj->id;
    $body = $obj->message_body;
    if (strlen($body)>RP_MAX_MESSAGE_LENGTH) $body = substr($body, 0, RP_MAX_MESSAGE_LENGTH) . "...";
    $scheduledPost->message_body = $body;
    $scheduledPost->scheduled_time = $obj->scheduled_time;
    $scheduledPost->post_type = $obj->post_type;
    $scheduledPost->userId = $obj->userId;
	$scheduledPost->link = $obj->link;
	$scheduledPost->page_name = $obj->page_name;
    $scheduledPost->posted_at = $obj->posted_at;
	$scheduledPost->post_id = $obj->post_id;
    $scheduledPost->status = $obj->status;
	$scheduledPost->image_url = $obj->post_image_url;
	$scheduledPost->category = $obj->category;
	$scheduledPost->source_type = $obj->source_type;
    $scheduledPosts[] = $scheduledPost;
}

?> 
<div class="page-wrapper">
<!-- Compiled and minified CSS 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/css/materialize.min.css">-->


<!-- Compiled and minified JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/js/materialize.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.1/moment.min.js"></script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/jquery.simple-dtpicker.js"></script>
<link href="<?php echo get_stylesheet_directory_uri(); ?>/assets/jquery.simple-dtpicker.css" rel="stylesheet"/>

<!-- datetime picker-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-material-datetimepicker/2.7.1/js/bootstrap-material-datetimepicker.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-material-datetimepicker/2.7.1/css/bootstrap-material-datetimepicker.min.css" />

<script src="https://unpkg.com/@material-ui/core/umd/material-ui.production.min.js" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500">

<link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<style>
        .datepicker-modal {
            background-color:transparent !important;
            -webkit-box-shadow:none !important;
            box-shadow:none !important;
        }
        .timepicker-modal {
            background-color:transparent !important;
            -webkit-box-shadow:none !important;
            box-shadow:none !important;
        }
        .dropdown-toggle {
            -webkit-box-shadow:none !important;
            box-shadow:none !important;
        }
        .dropdown-menu {
            padding-left:8px;
            padding-right:8px;
        }
        
        @media only screen and (max-device-width: 700px) {
            
            .cboxContent {
                float:none;
                margin:0px auto;
            }
            .datepicker-container {
                flex-direction:column !important;
            }
        }
    </style>

<h3 id="sc-title">Scheduled Content</h3>
<br/>
<p>ReliaPost lets you schedule many posts in a matter of minutes. Using ReliaPost, you can also select which manufacturers to promote and how often you would like to showcase their products. It is also a great opportunity to add location specific information to optimize your feeds.</p>
<p>Here you can view and manage the scheduled posts you've selected to share from the "Available Posts" page.</p>
<p>To reschedule a post, you must first set your new date and time by selecting 'CHANGE'.  You will then be prompted to finalize your date and time change.</p>
<div id="datepicker" style="height:0px;"></div>
<div id="timepicker" style="height:0px;"></div>
	
    <?php
    /**
     * @var ScheduledPost $scheduledPost
     */
    $count = 0;
	
	echo "
	<p>To filter your scheduled posts, click the 'FILTERS' bar below:</p>
	<div id='postFilterToggle'>FILTERS<i id='accordianIcon' class='fas fa-angle-down'></i></div>
    <div id='post_filter_content'>";
	echo "
        <div id='post_filter_container'>
			<div class='sort_options_status'>
				<p style='margin-top: 45px; text-align: center;'>BY STATUS</p>
				<button class='filter_btn' style='color:white' data-filter='posted'>POSTED</button>
				<button class='filter_btn' style='color:white' data-filter='failed'>FAILED</button>
				<button class='filter_btn' style='color:white' data-filter='notYetScheduled'>NOT YET POSTED</button>
				<button class='filter_btn active' style='color:white' data-filter='sc-post-content'>SHOW ALL</button>
			</div>			
        </div>";
	echo "
        <div id='post_filter_container'>
			<div class='sort_options_categories'>
			<p style='margin-top: 45px; text-align: center;'>BY CATEGORY</p>";
	$catArray = $categories;
	foreach ($catArray as $cat) {
		$categoryFilterName = strtoupper(str_replace(",", " ", $cat->name));
		$categoryDataFilter = strtolower(preg_replace('/\s*/', '', $cat->name));
		echo "<button class='filter_btn_cat' style='color:white' data-filter='$categoryDataFilter'>$categoryFilterName</button>";
	}
	//for dubugging purposes
	function console_log($output, $with_script_tags = true) {
		$js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . ');';
		if ($with_script_tags) {
			$js_code = '<script>' . $js_code . '</script>';
		}
		echo $js_code;
	}
	console_log($catArray);
	echo "</div>";	
	echo "</div>";
	echo "</div>";
    echo "<div class='sc-post-container'>";
	$classOfState;
    foreach ($scheduledPosts as $scheduledPost) {
        $count++;
        $message = "<h4>MESSAGE</h4><span>" .  $scheduledPost->message_body . "</span><br>";
		$formMessage = $scheduledPost->message_body;
        $type = $scheduledPost->post_type;
		if ($count <= 1) {
			if ($type=="twitter" || $type=="fbUser" || $type=="fbPage") {
				include($_SERVER['DOCUMENT_ROOT'].'/wp-content/themes/avada-artunlimited-child/headers/twitter_head.php');
				include($_SERVER['DOCUMENT_ROOT'].'/wp-content/themes/avada-artunlimited-child/scripts/twitter_script.php');
				include($_SERVER['DOCUMENT_ROOT'].'/wp-content/themes/avada-artunlimited-child/scripts/facebook_script.php');
			} 
		}
        $id = $scheduledPost->id;
		$post_id = $scheduledPost->post_id;
		$status = $scheduledPost->status;
		$category_name = $scheduledPost->category;
		$categoryClasses = strtolower(preg_replace('/\s*/', '', $category_name));
		$categoryInfo = "<h4>CATEGORY</h4><span>" . $category_name . "</span><br>";
		$sourceType = strtoupper($scheduledPost->source_type);
		if ($sourceType == "") {$sourceType = "internal";}
		$sourceTypeInfo = "<h4>SOURCE TYPE</h4><span>" . $sourceType . "</span><br>";
		$sharedTo = "";
		if ($scheduledPost->page_name != "") {
			$sharedTo = "<h4>SHARED TO:</h4><span>" . $scheduledPost->page_name . "</span>";
		}
		$featuredImage = "<img style='text-align: center;' style='margin-top: 30px;'src='" .  $scheduledPost->image_url . "'/>";
		$failureNotice = "";
		$title = get_the_title($post_id);
		$titleInfo = "<h4>TITLE</h4><h4><a style='text-align: center;' href=" . $scheduledPost->link . ">" . $title . "</a></h4><br>";
        $socialIcon = "";
		$time = $scheduledPost->scheduled_time;
        $phpdate = strtotime( $time );
        $now = time();
        $statusAction = "";
        $rescheduleAction = "";
        $changeTime = "";
		$postedColor = "green";
        $posted = ""; 
		$postedInfo = "<h4>POSTED TIME</h4><span>$scheduledPost->posted_at</span><br>";	
		$scheduledInfo = "";
		//$previewButton = "";
		//$previewPost = "";
		$editButton = "";
		$editPost = "";
		$editPostInternal = "";
		$editPostExternal = "";
		$deleteButton = "<button style='color: white !important;' class='cancelButton' id='cancelButton$id' data-id='$id'>DELETE</button><br>";
		$socialSig = "";
		
        if ($type=="fbUser" || $type=="fbPage") {
            $type = "Facebook";
			$socialSig = "FB";
            $socialIcon = "<h4>Platform</h4><span><i class='fa-4x fab fa-facebook-square'></i></span><br>";
        }
        else if ($type=="twitter") {
            $type = "Twitter";
			$socialSig = "TW";
            $socialIcon = "<h4>Platform</h4><span><i class='fa-4x fab fa-twitter'></i></span><br>";
        }
        if ($scheduledPost->posted_at!=null) {
            if (!isset($status) || $status>=400) {
                $posted = "<span class='failedMessage' style='background: red;color: white;padding: 4px;margin: 5px;font-size: 25px;'>FAILED</span>";
				$postedInfo = "";
				$classOfState = "failed";
				
				if ($status==400) {
					$failureNotice = "<h4>FAILURE REASON</h4><span>The server did not completely understand the request.  Please try again, or contact our support for more information.</span><br>";
				} else if ($status==403) {
					$failureNotice = "<h4>FAILURE REASON</h4><span>The server understood the request but refused to fulfill the request.  Please try again, or contact our support for more information.</span><br>";;
				}
            }
            else {
                $posted = "<span class='successMessage'  style='background: green;color: white;padding: 4px;margin: 5px;font-size: 25px;'>POSTED</span><br>";
				$classOfState = "posted";
                $postedBool = true;
				$scheduledInfo = "";
            }
        }
		$statusAction = $posted;
        if ($phpdate > $now) {
			$classOfState = "notYetScheduled";
            $statusAction = "<span class='NY_Message'  style='background: #f7b429;color: white;padding: 4px;margin: 5px;font-size: 25px;'>NOT YET POSTED</span>";
            $changeTime = "<a style='color:red !important;' href='\"#\"' class='changeTimeButton' id=\"changeTimeButton$id\" onclick=\"changeTime('$id','$time');return false;\">CHANGE TIME</a>";
			$postedInfo = "";
			$scheduledInfo = "<h4>SCHEDULED TIME</h4><span class='changeTime$id' id='changeTimeContainer' style='display: grid;'>$time $changeTime</span>";
			//$previewButton = "<button class='previewButton' id='previewButton$id' data-filter='preview$id' data-id='$id'>PREVIEW</button><br>";
			$editButton = "<button class='editButton' id='editButton$id' data-type='$type' data-filter='edit$id' data-id='$id'>EDIT</button>";
			//$previewPost = "<div class='preview_container preview$id' id='preview$id' style='display:none; background-color:#FFFFFF; color:#000000; text-align:left; padding:12px'>$scheduledPost->message_body</div>";
			$editPost = '
				<div style="min-height: 300px; height: 100%; display:none; background-color:#FFFFFF; color:#000000; text-align:left; padding:12px" class="edit' . $id . ' update-scheduled-post" id="update-scheduled-post' . $id . '" name="">
					<div class="dropdown shareable_page_dropdown" style="margin-top:5px; text-align: center;">';
						if ($type=="Facebook") {
							$editPost .= ' 
							<!-- Dropdown menu for selecting pages to post to -->
							<div id="box_titleFB"></div>
							<div class="pageSelectorFB" id="pageSelectorFB">
								<div style="text-align: center;">SHARE ON PAGE:</div>
								<img src="" id="pageLogoImageFB">
								<div id="pageInfo">
									<span id="pageNameLabelFB"></span><br>
									<span id="pageLikeCountFB"></span>
								</div>
								<button class="btn btn-primary dropdown-toggle" id="selectPageButton" type="button" data-toggle="dropdown">SELECT PAGE</button>
								<div class="dropdown-menu" role="menu" style="max-height: 200px;overflow-y:scroll">
									<ul style="padding: 0;" aria-labelledby="menu1" id="thePageSelectorFB" style="list-style-type: none;"></ul>
								</div>
							</div>';
						} else {
							$editPost .= ' 
							<!-- Dropdown menu for selecting pages to post to -->
							<div id="box_titleTW"></div>
							<div style="text-align: center; margin-bottom: 20px;">YOU WILL BE SHARING ON YOUR TWITTER PAGE:</div>
							<div class="pageSelectorTW" id="pageSelectorTW">
								<img src="" id="pageLogoImageTW" style="float: none;">
								<span id="pageNameLabelTW"></span><br>
							</div>';
						}
							
					$editPost .= '
					
					</div>
					<button style="text-align: center;" id="social_logout_button" data-sig="'.$socialSig.'">LOGOUT</button>
					<div style="text-align: center;" class="update_message_container">
						<div style="margin-bottom: 20px;"margin-bottom: 20px;">NEW MESSAGE</div>
						<textarea name="message" id="updateMessage" type="text" rows="20" cols="120" value="">'.$scheduledPost->message_body.'</textarea>
					</div>
					<button class="btn btn-primary" data-sig="'.$socialSig.'" data-id="'.$id.'" id="updateScheduledPostButton" type="button">UPDATE POST</button>
				</div>';
		}
		
        echo "<div class='sc-post-content ".$classOfState." " . str_replace(",", " ",$categoryClasses) . "'>";
        echo "
            $socialIcon
			$statusAction
			$titleInfo
			$featuredImage
			$sharedTo
            $message
			$sourceTypeInfo
			$categoryInfo
            $scheduledInfo
			$postedInfo
			$failureNotice
			$editButton
			$deleteButton
            $editPost
			";
        echo "</div>";   

    }
    echo "</div>";
    ?>


<p id="sc_text2">NOTE: Date format is Year-Month-Day and the Time format is based on the 24-hour clock (military time) standard.</p>

<?php
    if ($scheduledPost == "") {
            echo "<p style='color:red !important;'>You have no scheduled content.</p>";
            echo "<p>Please visit the <a style='color:#f7b429;' href='https://app.reliapost.com/'>Available Posts</a> page to share and schedule content!</p>";
            echo "
            <script type=\"text/javascript\">
            var table = document.getElementById('sc_table'); 
            table.style.display = 'none';
            var text1 = document.getElementById('sc_text1'); 
            text1.style.display = 'none';
            var text2 = document.getElementById('sc_text2'); 
            text2.style.display = 'none';
            </script>
            ";
        }

    ?>
<script>
	jQuery("#social_logout_button").on("click", {param: jQuery(this).attr("data-sig") }, function(event){
        event.preventDefault();

        Swal.fire({
            title: 'Are you sure?',
            text: "This will log you out of your social media account and refresh the page.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, I want to log out',
            cancelButtonText: 'Dismiss'
        }).then((result) => {
            if (result.value) {
                logoutSocialAccount(event);
            }
        })
    });
	function logoutSocialAccount(event) {
		if (event.data.param == "FB") {
			logoutFB();
		} else {
			logoutTW();
		}
		location.reload();
		
	}
	jQuery("#postFilterToggle").click(function(){
		jQuery("#post_filter_content").slideToggle("slow");
		jQuery("#accordianIcon").toggleClass('fa-angle-down fa-angle-up');
	});
		
	var boxs = jQuery(".sc-post-content");
	var btns = jQuery(".filter_btn").on("click", function() {
	  jQuery(".filter_btn_cat").removeClass("active");
	  var active = 
		btns.removeClass("active")
		  .filter(this)
		  .addClass("active")
		  .data("filter");
	  
	  boxs
		.hide()
		.filter( "." + active )
		.fadeIn("slow");

	});
	
	var cat_btns = jQuery(".filter_btn_cat").on("click", function() {
	  jQuery(".filter_btn").removeClass("active");
	  var active = 
		cat_btns.removeClass("active")
		  .filter(this)
		  .addClass("active")
		  .data("filter");
	  
	  boxs
		.hide()
		.filter( "." + active )
		.fadeIn("slow");

	});

	
	jQuery(".cancelButton").on("click", function(e){
        e.preventDefault();

        Swal.fire({
            title: 'Are you sure?',
            text: "This will delete this scheduled post from our database.  This post will no longer be posted at its scheduled time.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, I want to delete',
            cancelButtonText: 'Dismiss'
        }).then((result) => {
            if (result.value) {
                //handle account deactivation
				var id = jQuery(this).attr("data-id"); 
                deletePost(parseInt(id));
            }
        })
    });
    function deletePost(id) {
        var data = {
            action:"reliapost_deletePost",
            messageId: id
        };
 
        postDataToBackend(data, function(response) {
            console.log(response, data);
            if (response=="success" || response=="success0") {
                Swal.fire({
                   title:'Success',
                   text:'Thank you. Your post has been deleted from our database.  This page will automatically reload.'
                }).then((result) => {
                    if (result.value) {
						location.reload();
                    }
                })
            }
            else {
                Swal.fire({
                    title:'Error deleting post',
                    text:'Please contact support'
                });
            }
        });
    }

    window.ajaxurl = "<?php echo admin_url( 'admin-ajax.php' ); ?>";
    var myDatepicker;
    var myTimepicker;
 
        /**
         * @var Date selectedDate
         */
    var selectedDate;
    var windowSize = "unset";

    function formatTime(hours, minutes) {
        var isPm = hours>12;
        var amOrPm = (isPm ? "pm" : "am");
        if (isPm) hours -= 12;

        var ret = hours;
        ret += ":";
        if (minutes<10) ret += "0" + minutes;
        else ret += minutes;


        ret += " " + amOrPm;

        return ret;
    }

jQuery(document).ready(function() {
        console.log("document ready - initializing...");

        jQuery("#datepicker").datepicker();
        var options = {autoClose : true};
        myDatepicker = (M.Datepicker.init(jQuery("#datepicker"), 
        {
            autoClose : true, 
            onSelect: function(date) {
                selectedDate = date;
                myTimepicker.open();
            },
            onClose: function() {

                //jQuery('head > link[href$="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/css/materialize.min.css"]').remove();
                //jQuery('#side-header').show();
            }
        }))[0];
        myTimepicker = (M.Timepicker.init(jQuery("#timepicker"), 
        {
            autoClose: true, 
            onSelect: function(hour, minute) {
                var amPm = myTimepicker.amOrPm;
                var hours24 = hour + (amPm.toUpperCase()==="PM" ? 12 : 0);
                selectedDate.setHours(hours24, minute, 0);
                window.scheduled = true;
                var month = selectedDate.getMonth() + 1;
                var day = selectedDate.getDate();
                var minutes = selectedDate.getMinutes();
                console.log("selected " + hour + "," + minute + " for time");
                var timeVal = month + "/" + day + " " + formatTime(hours24, minutes);
                console.log("setting value to " + timeVal);
                parent.selectedTime = moment(selectedDate);
            }, 
            onCloseEnd: function() {
                var ts = Math.round((new Date()).getTime() / 1000);
                ts = moment.unix(ts).format("YYYY-MM-DD HH:mm");

                if (parent.selectedTime!=null) {
                    ts = parent.selectedTime.format("YYYY-MM-DD HH:mm");
                }
                console.log("date scheduled: " + ts);
                
                jQuery('head > link[href$="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/css/materialize.min.css"]').remove();
                jQuery('#side-header').show();
                Swal.fire({
                   title:'Success',
                   text:'Thank you for rescheduling.  Your new scheduled date will be ' + ts + '. Please set your new time by selecting "SET NEW TIME"'
                }).then((result) => {
                    if (result.value) {
                        var today = new Date();
                        var dd = String(today.getDate()).padStart(2, '0');
                        var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
                        var yyyy = today.getFullYear();
                        var h = today.getHours();
                        var m = today.getMinutes();
                        var s = today.getSeconds();
                        today = yyyy + '-' + mm + '-' + dd + " " + h + ":" + m;
                        var post_id = sessionStorage.getItem('post_id');
                        var post_time = sessionStorage.getItem('post_time');
                        console.log(today);
                        jQuery('#changeTimeButton'+String(post_id)).hide();
                        //jQuery('#cancelButton'+String(post_id)).hide();
                        //new time validation
                        if (post_time != ts) {
                            jQuery('.newTime'+String(post_id)).show();
                            jQuery('.changeTime'+String(post_id)).text(ts);
                            jQuery('.changeTime'+String(post_id)).append("<p>** Please double check your new time **</p>");
                        } else {
                            jQuery('.changeTime'+String(post_id)).text("Your new time was the same as your original scheduled time. Please enter a new time.  Thank you.");
                        }
                        if (today == ts) {
                            jQuery('.changeTime'+String(post_id)).append("<p>** Your new time is set for today.  Please be aware that your post will publish as soon as you set your new time.  Thank you. **</p>");
                            jQuery('.changeTime'+String(post_id)).append("<p>** If you would like to change your time again, please do so by clicking 'CHANGE TIME' below. **</p>");
                            jQuery("<span class='newTime$id'>NEW TIME</span><br/> ").insertBefore(".changeTime"+String(post_id));
                        }
                        
                        //sessionStorage.clear();
                    }
                })
            }
        }))[0];
    
});

function changeTime(id, time) {
        //this is a little hacky, I know
        sessionStorage.setItem('post_id', id);
        sessionStorage.setItem('post_time', time);
        jQuery('#side-header').hide();
		
        Swal.fire({
            title: 'Are you sure?',
            text: "You are about to change your scheduled time. When complete, please finalize your time by selecting 'SET NEW TIME' for your desired post.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, I want to change the date and time of this post.',
            cancelButtonText: 'Dismiss'
        }).then((result) => {
            if (result.value) {
                //handle getting date and time picker
                console.log("reschedule button a clicked...");
                myDatepicker.open();
                jQuery('head').append('<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/css/materialize.min.css">');
                console.log(jQuery(".datepicker-modal").css("display"));
				jQuery("<span class='newTime$id'>NEW TIME</span><br/> ").insertBefore(".changeTime"+id);
				jQuery("<span style='display: block;' class='rescheduleData$"+id+"' ><a href='#' style='color: white !important;' class='rescheduleButton' id='rescheduleButton"+id+"' onclick='setReschedule("+id+");return false;'>SET NEW TIME</a></span>").insertAfter(".changeTime"+id);
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                jQuery('head > link[href$="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/css/materialize.min.css"]').remove();
                jQuery('#side-header').show();

                console.log(jQuery(".datepicker-modal").css("display"));
            }
        })
    }

function setReschedule(id) {
        //if we haven't scheduled a future date, schedule it for now
        var ts = Math.round((new Date()).getTime() / 1000);
        ts = moment.unix(ts).format("YYYY-MM-DD HH:mm:ss");

        if (parent.selectedTime!=null) {
            ts = parent.selectedTime.format("YYYY-MM-DD HH:mm:ss");
        }

        console.log("date scheduled: " + ts);

        
        var data = {
            action: "reliapost_reschedulePost",
            messageId: id,
            scheduledTime: ts
        };

        console.log(data);


    jQuery.ajax({
        url:window.ajaxurl,
        type:"post",
        data: data
    }).done(function(data, statusText, xhr) {
        var status = xhr.status;
        if (status==200) {
            console.log("success posting to " + window.ajaxurl);
            console.log(statusText);
            window.location.reload(true);
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
                console.log("success posting to " + window.ajaxurl);
                console.log(statusText);
                //window.location.reload(true);
            }
            else {
                console.log("Error (" + status + "): " + statusText);
                alert("Error posting: " + statusText);
            }
        });
    }
	
	
	
	
	
	(function(jQuery){
		//  inspired by DISQUS
		jQuery.oauthpopup = function(options) {
			if (!options || !options.path) {
				throw new Error("options.path must not be empty");
			}
			options = jQuery.extend({
				windowName: 'ConnectWithOAuth' // should not include space for IE
				, windowOptions: 'location=0,status=0,width=800,height=400'
				, callback: function(){ window.location.reload(); }
			}, options);

			var oauthWindow   = window.open(options.path, options.windowName, options.windowOptions);
			var oauthInterval = window.setInterval(function(){
				if (oauthWindow.closed) {
					window.clearInterval(oauthInterval);
					options.callback();
				}
			}, 1000);
		};
	})(jQuery);
	
	
	var editforms = jQuery(".update-scheduled-post");
    var editbtns = jQuery(".editButton").on("click", function() {
		
		var active = 
			editbtns
				.removeClass("active")
				.filter(this)
				.addClass("active")
				.data("filter");
			editforms
				.removeClass("activeForm")
				.attr("name","")
				.hide()
				.filter( "." + active )
				.addClass("activeForm");
					
		Swal.fire({
            title: 'Editing Your Scheduled Post',
            text: "This option allows you to change the message that will be shared along with your post.  To do this we need to make sure your are logged into your social media account.",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, I want to edit',
            cancelButtonText: 'Dismiss'
        }).then((result) => {
            if (result.value) {
				var type = jQuery(this).data('type');
				checkSocial(type);
            }
        })
		
		function checkSocial(type) {
			if (type == "Facebook") {
				attemptToLogin();
			} else {
				<?php if (!$isLoggedIn) { ?>
				jQuery.oauthpopup({
					path: '/wp-content/plugins/artunlimited/twitter_oauth/TwitterLogin.php',
					callback: function(){
						Swal.fire({
						   title:'Success',
						   text:'Thank you. Your Twitter account has been authorized.  This page will automatically reload.  You can continue editing once the page has fully reloaded.'
						}).then((result) => {
							if (result.value) {
								window.location.reload(true);
							}
						})
						console.log("authorized!!!");
					}
				});
				<?php } else { ?>
				jQuery('.activeForm').show();
				jQuery('html, body').animate({ 
					scrollTop: jQuery('.activeForm').offset().top 
				}, 1000);
				<?php } ?>
			}
		}
		if (editforms.hasClass('activeForm')) {
			jQuery('.activeForm').attr("name","active_form");
			jQuery('.activeForm #updateScheduledPostButton').addClass("activeUpdateButton");
			if (jQuery('.activeForm #updateScheduledPostButton').hasClass("activeUpdateButton")) {
				jQuery(".activeUpdateButton").bind("click", function(e){
					console.log("click");
					e.preventDefault();

					Swal.fire({
						title: 'Are you sure?',
						text: "This will save the changes you made to your scheduled post.  Don't worry, you can always go back to change your scheduled post again before it is posted.",
						type: 'warning',
						showCancelButton: true,
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						confirmButtonText: 'Yes, I want to save my changes',
						cancelButtonText: 'Dismiss'
					}).then((result) => {
						if (result.value) {
							//handle scheduled post message and page changes
							var id = jQuery(this).attr("data-id"); 
							var sig = jQuery(this).attr("data-sig");
							updateSchedulePost(parseInt(id), sig);
						}
					})
				});
			
				function updateSchedulePost(id, sig) {
					var pageId = window.selectedPageId;
					var pageNameSelector = "";
					if (sig == "FB") {
						pageNameSelector = jQuery(".activeForm #pageNameLabelFB").text();
					} else {
						pageNameSelector = jQuery(".activeForm #pageNameLabelTW").text();
					}
					console.log(pageId);
					console.log(sig);
					var data = {
						action:"reliapost_editScheduledPost",
						messageId: id,
						message: jQuery('.activeForm .update_message_container #updateMessage').val(),
						pageId: pageId,
						pageName: pageNameSelector
					};
			 
					postDataToBackend(data, function(response) {
						console.log(response, data);
						if (response=="success" || response=="success0") {
							Swal.fire({
							   title:'Success',
							   text:'Thank you. Your scheduled post has been updated.  This page will automatically reload.'
							}).then((result) => {
								if (result.value) {
									location.reload();
								}
							})
						}
						else {
							Swal.fire({
								title:'Error editing scheduled post',
								text:'Please contact support'
							});
						}
					});
				}
			}
			var url = "../wp-admin/admin-ajax.php";
			var activeForm = jQuery('.activeForm');
			console.log(activeForm);
			
			activeForm[0].addEventListener('submit', function(ev) {
				ev.preventDefault();
				
				Swal.fire({
					title: 'Are you sure?',
					text: "This will save your edits.  Keep in mind, you can always edit your post again.",
					type: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Yes, I want to edit this post',
					cancelButtonText: 'Dismiss'
				}).then((result) => {
					if (result.value) {
						var oData = new FormData(activeForm[0]);
						console.log(oData);

						var oReq = new XMLHttpRequest();
						oReq.open("POST", url, true);
						oReq.onload = function(oEvent) {
							console.log(oReq.status);
							console.log(oReq.responseText);
							var status;
							var errorCode;
							var errorMessage;
							if (oReq.status==200) {
								var jsonBody = JSON.parse(oReq.responseText);
								status = jsonBody["status"];
								
								if (status=="success") {
									//success - clear form	
									alert("Your post has been successfully edited");
								}
								else {
									errorCode = jsonBody["errorCode"];
									errorMessage = jsonBody["message"];
									alert("An error has occurred (" + errorCode + ") - please check your data and try again.\n\n" + errorMessage);
								}
								appendNotificationText(status, errorCode, errorMessage);
							}
							else {
								//unknown error - wordpress should always return a 200
								//alert("An unknown error has occurred (" + oReq.status + ")");
							}
						};

						oReq.send(oData);
						
					}
				})
			}, false);
		}
	});
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
</div>