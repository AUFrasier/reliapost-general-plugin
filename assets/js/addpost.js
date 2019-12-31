var $ = jQuery;


$(document).ready(function(){
	
	console.log("HI");
	var activeForm;
	
	var tooltipsInternal = jQuery(".internal #tooltip_content");
	var iconsInternal = jQuery(".internal .tooltip_icon").on("click", function() {
		var active = 
			iconsInternal
				.removeClass("active")
				.filter(this)
				.addClass("active") 
				.data("filter");
			tooltipsInternal
				.hide()
				.filter( "." + active )
				.fadeIn("slow");
	})
	
	var tooltipsExternal= jQuery(".external #tooltip_content");
	var iconsExternal = jQuery(".external .tooltip_icon").on("click", function() {
		var active = 
			iconsExternal
				.removeClass("active")
				.filter(this)
				.addClass("active")
				.data("filter");
			tooltipsExternal
				.hide()
				.filter( "." + active )
				.fadeIn("slow");
	})
	
	var forms = jQuery(".add-post-form");

	var btns = jQuery(".post_type_filter").on("click", function() {
		var active = 
			btns
				.removeClass("active")
				.filter(this)
				.addClass("active")
				.data("filter");
			forms
				.removeClass("activeForm")
				.attr("name","")
				.hide()
				.filter( "." + active )
				.addClass("activeForm")
				.fadeIn("slow");
		if (forms.hasClass('activeForm')) {
			jQuery('.activeForm').attr("name","active_form");
			jQuery('#addpostbutton').css("display","block");
			jQuery('.activeForm #checksource').val(jQuery(this).data("filter"));
			
			var url = "../wp-admin/admin-ajax.php";
			activeForm = document.forms.namedItem("active_form");
			console.log(activeForm);
			
			activeForm.addEventListener('submit', function(ev) {
				ev.preventDefault();
				
				Swal.fire({
					title: 'Are you sure?',
					text: "This will create a post for all to view and share.  You can always go back and edit this post, before it is shared, on the Scheduled Posts page.",
					type: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Confirm',
					cancelButtonText: 'Dismiss'
				}).then((result) => {
					if (result.value) {
						var oData = new FormData(activeForm);
						console.log(oData);

						var oReq = new XMLHttpRequest();
						oReq.open("POST", url, true);
						oReq.onload = function(oEvent) {
							console.log(oReq.status);
							console.log(JSON.parse(oReq.responseText));
							var status;
							var errorCode;
							var errorMessage;
							if (oReq.status==200) {
								var jsonBody = JSON.parse(oReq.responseText);
								status = jsonBody["status"];
								
								if (status=="success") {
									//success - clear form	
									//alert("Your post has been successfully added");
									clearForm();
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
});

function clearForm() {
    $(".activeForm #postTitle").val("");
    $(".activeForm #url").val("");
    $(".activeForm #description").val("");
    $(".activeForm #content").val("");
}

function appendNotificationText(status, errorCode, errorMessage) {
	jQuery("html, body").animate({ scrollTop: 0 }, "slow");
	jQuery(".activeForm").show("slow");
	jQuery(".notification-container").show();
	var notifcationItems = $('.notification').children().size();
	if ($('.fail').css('display') == 'block') {
		jQuery(".fail").remove();
	}
	if ($('.success').css('display') == 'block') {
		jQuery(".success").remove();
	}
	if (status=="success") {
		$(".notification").append("<div class='success' style='color: white;'><p><strong style='color:green'>SUCCESS!</strong> Your post has been added to the " + jQuery(".activeForm #category option:selected").text() + " category. Please visit the <a href='/'>Available Posts</a> page to view/schedule your content.</p></div>");
	} else {
		$(".notification").append("<div class='fail' style='color: white;'><p>Your post has <strong style='color:red'>FAILED</strong>. " + errorMessage + "  Please contact <a href='/support/'>Support</a> or try again below.</p></div>");
	}
	jQuery(".source_options").show("slow");
}