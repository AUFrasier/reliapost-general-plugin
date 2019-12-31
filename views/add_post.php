<?php
/**
 * Created by Chris Ruddell.
 * Date: 11/20/18
 * Time: 6:57 AM
 */

/**
 * Purpose of this page is to allow contributor to create a new post
 */

$categories = $pageData["categories"];

$user = wp_get_current_user()->data;
$name = $user->user_nicename;
$email = $user->user_email;


?>
<div class="page-wrapper">
	<h3 id="addposttitle" style="text-align:center;">Create Post</h3>
	<br/>
	<p style="text-align: center;">Create your post here. When your post is created and submitted it will be displayed on the "Available Posts" page for all to see.</p>
	<div style="display: none;" class="notification-container">
		<div class="notification"></div>
	</div>
	<div class="source_options">
		<div class="external_option">
			<p>Click "Externally Sourced" below if the content you're adding is housed in another location on the web (such as a video, image, or a blog post)</p>
			<i style="font-size: 17px;" id="extsource_tooltip_icon" class="far fa-question-circle tooltip_icon"></i>
			<p id="tooltip_content" class="extsource_tooltip">This option is for those who have content located in another website.  If you are adding content located on a site outside of the ReliaPost platform, choose this option.</p>
			<button id="extsourceButton" class="post_type_filter" data-filter="external">Externally Sourced</button>
		</div>
		<div class="internal_option">
			<p>Click "Internally Sourced" below the content you're adding will be housed in the ReliaPost platform</p>
			<i style="font-size: 17px;" id="intsource_tooltip_icon" class="far fa-question-circle tooltip_icon"></i>
			<p id="tooltip_content" class="intsource_tooltip">This option is for those who are creating the content for the first time.  If you are creating the content to be stored directly on the ReliaPost platform, choose this option.</p>
			<button id="intsourceButton" class="post_type_filter" data-filter="internal">Internally Sourced</button>
		</div>
	</div>

	<form style="display: none;" class="add-post-form internal" name="add-post-form">

		<div style="margin-bottom: 30px" id="title-entry">
			<label for="title">Title:</label>
				<i style="font-size: 17px;" id="title_tooltip_icon" data-filter="title_tooltip" class="far fa-question-circle tooltip_icon"></i>
				<p id="tooltip_content" class="title_tooltip">This is the custom title of your content.</p>
			<input type="text" name="title" id="postTitle" onkeyup="generatePreview()">
		</div>
		<div style="margin-bottom: 30px" id="content-entry">	
			<label for="content_field">Content:</label>
				<i style="font-size: 17px;" id="content_tooltip_icon"  data-filter="content_tooltip" class="far fa-question-circle tooltip_icon"></i>
				<p id="tooltip_content" class="content_tooltip">This is where you will layout your content.</p>
			<textarea name="content_field" id="content_field" rows="7" cols="50" onkeyup="generatePreview()"></textarea>
		</div>
		<div style="margin-bottom: 30px" id="description-entry">
			<label for="description">Description:</label>
				<i style="font-size: 17px;" id="description_tooltip_icon"  data-filter="description_tooltip" class="far fa-question-circle tooltip_icon"></i>
				<p id="tooltip_content" class="description_tooltip">This is your custom description of the content.  Please add a few sentences describing what your content is about.  This will be displayed on social media.</p>
			<textarea name="description" id="description" rows="3" cols="50" onkeyup="generatePreview()"></textarea>
		</div>
		<div style="margin-bottom: 30px" id="image-entry">
			<label for="image">Add Image</label>
				<i style="font-size: 17px;" id="image_tooltip_icon"  data-filter="image_tooltip" class="far fa-question-circle tooltip_icon"></i>
				<p id="tooltip_content" class="image_tooltip">Here you can add an image to your post to be display on social media.</p>
				<p>For best results, use a landscape image 750px x 450px</p>
			<input type="file" name="image" accept='image/*' id="image" onchange='openFile(event)'>
			<img id='output'/>
		</div>
		<div style="margin-bottom: 30px" id="category-entry">
			<label for="category">Category</label>
				<i style="font-size: 17px;" id="category_tooltip_icon"  data-filter="category_tooltip" class="far fa-question-circle tooltip_icon"></i>
				<p id="tooltip_content" class="category_tooltip">This is where you choose the category your content falls under.  For example, if my content is about roofing, I would choose the "Roofing" category.</p>
				<select name="category" id="category" onchange="generatePreview()">
					<?php
						foreach ($categories as $category) {
							echo "\n<option value='" . $category->slug . "'>" . $category->name . "</option>";
						}
					?>
				</select>
		</div>		
		<!--<div style="margin-bottom: 30px" id="preview-entry">
			<label for="preview">Preview</label>
				<i style="font-size: 17px;" id="preview_tooltip_icon" data-filter="preview_tooltip" class="far fa-question-circle tooltip_icon"></i>
				<p id="tooltip_content" class="preview_tooltip">This is a preview of your post.</p>
			<div id="postpreview"></div>
			
		</div>-->
		<input name="checksource" id="checksource" type="hidden" value="">
		<input type="hidden" name="action" value="reliapost_addpost"/>
		<input id="addpostbutton" type="submit" value="Add Post">
		<br/><br/>

	</form>

	<form style="display: none;" class="add-post-form external" name="add-post-form">

		<div style="margin-bottom: 30px" id="title-entry">
			<label for="title">Title:</label>
				<i style="font-size: 17px;" id="title_tooltip_icon"  data-filter="title_tooltip" class="far fa-question-circle tooltip_icon"></i>
				<p id="tooltip_content" class="title_tooltip">This is the custom title of your content.</p>
			<input type="text" name="title" id="postTitle" onkeyup="generatePreview()">
		</div>
		<div style="margin-bottom: 30px" id="url-entry">
			<label for="url">SOURCE URL:</label>
				<i style="font-size: 17px;" id="url_tooltip_icon"  data-filter="url_tooltip" class="far fa-question-circle tooltip_icon"></i>
				<p id="tooltip_content" class="url_tooltip">This is the URL of your externally sourced content.</p>
			<input type="url" name="url" id="url" onkeyup="generatePreview()">
		</div>
		<div style="margin-bottom: 30px" id="description-entry">
			<label for="description">Description:</label>
				<i style="font-size: 17px;" id="description_tooltip_icon"  data-filter="description_tooltip" class="far fa-question-circle tooltip_icon"></i>
				<p id="tooltip_content" class="description_tooltip">This is your custom description of the content.  Please add a few sentences describing what your content is about.  This will be displayed on social media.</p>
			<textarea name="description" id="description" rows="3" cols="50" onkeyup="generatePreview()"></textarea>
		</div>
		<div style="margin-bottom: 30px" id="image-entry">
			<label for="image">Add Image</label>
				<i style="font-size: 17px;" id="image_tooltip_icon"  data-filter="image_tooltip" class="far fa-question-circle tooltip_icon"></i>
				<p id="tooltip_content" class="image_tooltip">Here you can add an image to your post to be display on social media.</p>
				<p>For best results, use a landscape image 750px x 450px</p>
			<input type="file" name="image" accept='image/*' id="image" onchange='openFile(event)'>
			<img id='output'/>
		</div>
		<div style="margin-bottom: 30px" id="category-entry">
			<label for="category">Category</label>
				<i style="font-size: 17px;" id="category_tooltip_icon"  data-filter="category_tooltip" class="far fa-question-circle tooltip_icon"></i>
				<p id="tooltip_content" class="category_tooltip">This is where you choose the category your content falls under.  For example, if my content is about roofing, I would choose the "Roofing" category.</p>
				<select name="category" id="category" onchange="generatePreview()">
					<?php
						foreach ($categories as $category) {
							echo "\n<option value='" . $category->slug . "'>" . $category->name . "</option>";
						}
					?>
				</select>
		</div>	
		<!--<div style="margin-bottom: 30px" id="preview-entry">
			<label for="preview">Preview</label>
				<i style="font-size: 17px;" id="preview_tooltip_icon" data-filter="preview_tooltip" class="far fa-question-circle tooltip_icon"></i>
				<p id="tooltip_content" class="preview_tooltip">This is a preview of your post.</p>
			<div id="postpreview"></div>
			
		</div>-->
		<input id="checksource" name="checksource" type="hidden" value="">
		<input type="hidden" name="action" value="reliapost_addpost"/>
		<input id="addpostbutton" type="submit" value="Add Post">
		
		<br/><br/>

	</form>
</div>
<script>
var $ = jQuery;
jQuery(document).ready(function() {
	
	jQuery("#intsource_tooltip_icon").click(function() {
		jQuery(".intsource_tooltip").toggle("slow");
	})
	jQuery("#extsource_tooltip_icon").click(function() {
		jQuery(".extsource_tooltip").toggle("slow");
	})

	
});
    
    /*function getURLhtml(previewurl){
        alert(previewurl);
        var preview = $.get(previewurl, function(data, status){
            alert($(data).find('p:first').text());
            alert($(data).find('img:first').attr("src"));
        }, "html");
    }
    
	function generatePreview(){
		//var previewhtml = "Title: " + $("#postTitle").val();
		//previewhtml += "<br>" + $( "#category option:selected" ).text();
		var previewhtml = $(".activeForm #description").val() + "<br><a href='"+ $(".activeForm #url").val() + "'>" + $(".activeForm #url").val() + "</a>";
		$(".activeForm #postpreview").html(previewhtml);
        getURLhtml($(".activeForm #url").val());
	}*/   

	var openFile = function(event) {
		var input = event.target;
		var reader = new FileReader();
		reader.onload = function(){
		  var dataURL = reader.result;
		  var output = document.querySelector('.activeForm #image-entry #output');
		  output.src = dataURL;
		};
		reader.readAsDataURL(input.files[0]);
	};
    	
	
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>