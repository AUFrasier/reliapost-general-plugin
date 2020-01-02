var $ = jQuery;

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