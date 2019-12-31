var $ = jQuery;

function postDataToBackend(data, callbackFunction = null) {
    var url = "/wp-admin/admin-ajax.php";

    var settings = {
        "async": true,
        "crossDomain": true,
		"contentType": 'JSON',
        "url": url,
        "method": "POST",
		"beforeSend": function(){
			console.log('happened');
		},
		"success": function(result) {
			console.log(result);
		},
        "headers": {
            "Content-Type": "application/x-www-form-urlencoded",
            "cache-control": "no-cache"
        },
        "data": data
    }

    jQuery.ajax(settings)
        .done(function(data, statusText, xhr) {

            var status = xhr.status;
            if (status==200) {
                console.log("success posting to " + url);
                console.log(data);
				console.log(statusText);
				console.log(xhr);
                if (callbackFunction!=null) callbackFunction(data);
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
                if (callbackFunction!=null) callbackFunction(statusText);
            }
            else {
                console.log("Error (" + status + "): " + statusText);
                alert("Error posting: " + statusText);
            }
        });
		return false;
}

$(document).ready(function(){

	var editforms = jQuery(".update-post-form");
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
				.addClass("activeForm")
				.fadeIn("slow");
		if (editforms.hasClass('activeForm')) {
			jQuery('.activeForm').attr("name","active_form");
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
									//alert("Your post has been successfully edited");
								}
								else {
									errorCode = jsonBody["errorCode"];
									errorMessage = jsonBody["message"];
									//alert("An error has occurred (" + errorCode + ") - please check your data and try again.\n\n" + errorMessage);
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
	
	function appendNotificationText(status, errorCode, errorMessage) {
		jQuery("html, body").animate({ scrollTop: 0 }, "slow");
		jQuery(".activeForm").show("slow");
		jQuery(".notification-container").show();
		var notifcationItems = $('.notification').children().size();
		if (status=="success") {
			if($('.notification').has('.success').length == 0) {
				var success = "<div class='success' style='color: white;'><p><strong style='color:green'>SUCCESS!</strong> Your post has been successfully edited. Please visit the <a href='/'>Available Posts</a> page to view/schedule your content.</p></div>";
				$(".notification").append(success);
			}
			if ($('.fail').css('display') == 'block') {
				jQuery(".fail").remove();
			}
		} else {
			var fail;
			if (errorCode == 400) {
				if ($('.fail').css('display') == 'block') {
					jQuery(".fail").remove();
				}
				fail = "<div class='fail' style='color: white;'><p>Your post has <strong style='color:red'>FAILED</strong>. " + errorMessage + "  Please contact <a href='/support/'>Support</a> or try again below.</p></div>";
				$(".notification").append(fail);
			} else if (errorCode == 401) {
				if ($('.fail').css('display') == 'block') {
					jQuery(".fail").remove();
				}
				fail = "<div class='fail' style='color: white;'><p>Your post has <strong style='color:red'>FAILED</strong>. " + errorMessage + " Please contact <a href='/support/'>Support</a> or try again below.</p></div>";
				$(".notification").append(fail);
			} else if (errorCode == 402) {
				if ($('.fail').css('display') == 'block') {
					jQuery(".fail").remove();
				}
				fail = "<div class='fail' style='color: white;'><p>Your post has <strong style='color:red'>FAILED</strong>. " + errorMessage + " Please contact <a href='/support/'>Support</a> or try again below.</p></div>";
				$(".notification").append(fail);
			} else if (errorCode == 403) {
				if ($('.fail').css('display') == 'block') {
					jQuery(".fail").remove();
				}
				fail = "<div class='fail' style='color: white;'><p>Your post has <strong style='color:red'>FAILED</strong>. " + errorMessage + " Please contact <a href='/support/'>Support</a> or try again below.</p></div>";
				$(".notification").append(fail);
			} else if (errorCode == 404) {
				if ($('.fail').css('display') == 'block') {
					jQuery(".fail").remove();
				}
				fail = "<div class='fail' style='color: white;'><p>Your post has <strong style='color:red'>FAILED</strong>. " + errorMessage + " Please contact <a href='/support/'>Support</a> or try again below.</p></div>";
				$(".notification").append(fail);
			}
		}
	}
});