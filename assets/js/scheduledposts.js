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
	var previews = jQuery(".preview_container");
    var previewbtns = jQuery(".previewButton").on("click", function() {
		var active = 
			previewbtns
				.removeClass("active")
				.filter(this)
				.addClass("active")
				.data("filter");
			previews
				.removeClass("activePreview")
				.hide()
				.filter( "." + active )
				.addClass("activePreview")
				.fadeIn("slow");
	});			
	
});