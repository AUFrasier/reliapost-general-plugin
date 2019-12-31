<?php
/**
 * Created with NotePad++ by Jake Frasier.
 * User: AU
 * Date: 8/29/2019
 */


$userId = wp_get_current_user()->ID;
global $wpdb;
$tableName = "wp_posts";
$sql = "SELECT * FROM $tableName WHERE post_author = '$userId' AND post_type='post' ORDER BY `id` DESC";
$data = $wpdb->get_results($sql);

define("RP_MAX_MESSAGE_LENGTH", 80);

class SharedPosts {
	public $id;
    public $title;
	public $excerpt;
	public $content;
	public $link; 
}

$categories = $pageData["categories"];

$sharedPosts = [];

if (is_array($data)) foreach ($data as $obj) {
    $sharedPost = new SharedPosts();
    $sharedPost->id = $obj->ID;
	$sharedPost->title = $obj->post_title;
	$sharedPost->excerpt = $obj->post_excerpt;
	$sharedPost->content = $obj->post_content;
	$sharedPost->link = $obj->guid;
    $sharedPosts[] = $sharedPost;
}

?>

<!-- Compiled and minified CSS 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/css/materialize.min.css">-->


<script src="https://unpkg.com/@material-ui/core/umd/material-ui.production.min.js" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500">

<link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<div class="page-wrapper">
<h3 id="sc-title">Contributed Posts</h3>
<br/>
<p></p>
<div style="display: none;" class="notification-container">
	<div class="notification"></div>
</div>
<?php
echo "<div class='sc-post-container'>";
foreach ($sharedPosts as $sharedPost) {
	$editPost;
	$id = $sharedPost->id;
	$title = $sharedPost->title;
	$excerpt = $sharedPost->excerpt;
	$content = $sharedPost->content;
	$link = get_permalink($id);
	$sourceURL = get_post_meta($id, '_artunlimited_inner_srcUrl', true );
	$cat = get_post_meta($id, '_artunlimited_inner_category', true ); 
	$sourceType = strtoupper(get_post_meta($id, '_artunlimited_inner_sourceType', true )); 
	$featuredImage = get_the_post_thumbnail_url($id);
	$editButton = "<button class='editButton' id='editButton$id' data-filter='edit$id' data-id='$id' onclick='editPost($id)'>EDIT</button>";
	if ($sourceType == "internal") {
		$editPostInternal = '
			<form style="display:none; background-color:#FFFFFF; color:#000000; text-align:left; padding:12px" class="edit' . $id . ' update-post-form" id="update-post-form' . $id . '" name="">
				<div style="margin-bottom: 30px" id="title-entry">
					<label for="title">Title:</label>
						<i style="font-size: 17px;" id="title_tooltip_icon" class="far fa-question-circle tooltip_icon"></i>
						<p id="tooltip_content" class="title_tooltip">This is the custom title of your content.</p>
					<input type="text" name="title" id="postTitle" value="' . $title . '">
				</div>
				<div style="margin-bottom: 30px" id="content-entry">	
					<label for="content_field">Content:</label>
						<i style="font-size: 17px;" id="content_tooltip_icon" class="far fa-question-circle tooltip_icon"></i>
						<p id="tooltip_content" class="content_tooltip">This is where you will layout your content.</p>
					<textarea name="content_field" id="content_field" rows="7" cols="50">' . $content . '</textarea>
				</div>
				<div style="margin-bottom: 30px" id="description-entry">
					<label for="description">Description:</label>
						<i style="font-size: 17px;" id="description_tooltip_icon" class="far fa-question-circle tooltip_icon"></i>
						<p id="tooltip_content" class="description_tooltip">This is your custom description of the content.  Please add a few sentences describing what your content is about.  This will be displayed on social media.</p>
					<textarea name="description" id="description" rows="3" cols="50">' . $excerpt . '</textarea>
				</div>
				<div style="margin-bottom: 30px" id="image-entry">
					<label for="image">Add Image</label>
						<i style="font-size: 17px;" id="image_tooltip_icon" class="far fa-question-circle tooltip_icon"></i>
						<p id="tooltip_content" class="image_tooltip">Here you can add an image to your post to be display on social media.</p>
						<p>For best results, use a landscape image 750px x 450px</p>
					<input type="file" name="image" accept="image/*" id="image" onchange="openFile(event, ' . $id . ')">
					<img src="' .  $featuredImage . '"/>
				</div>
				<div style="margin-bottom: 30px" id="category-entry">
					<label for="category">Category</label>
					<i style="font-size: 17px;" id="category_tooltip_icon" class="far fa-question-circle tooltip_icon"></i>
					<p id="tooltip_content" class="category_tooltip">This is where you choose the category your content falls under.  For example, if my content is about roofing, I would choose the "Roofing" category.</p>
					<select name="category" id="category">';
							foreach ($categories as $category) {
								$editPostInternal .= "\n<option value='$category->slug'>" . $category->name . "</option>";
							}
		$editPostInternal .= '            
					</select>
				</div>
				<input name="post_id" id="post_id" type="hidden" value="' . $id  . '">
				<input type="hidden" name="action" value="reliapost_updateSharedPost"/>
				<input id="updatepostbutton" type="submit" value="Update Post">
			</form>';
		$editPost = $editPostInternal;
	} else {
		$editPostExternal = '
			<form style="display:none; background-color:#FFFFFF; color:#000000; text-align:left; padding:12px" class="edit' . $id . ' update-post-form" id="update-post-form' . $id . '" name="">
				<div style="margin-bottom: 30px" id="title-entry">
					<label for="title">Title:</label>
						<i style="font-size: 17px;" id="title_tooltip_icon" class="far fa-question-circle tooltip_icon"></i>
						<p id="tooltip_content" class="title_tooltip">This is the custom title of your content.</p>
					<input type="text" name="title" id="postTitle" value="' . $title . '">
				</div>
				<div style="margin-bottom: 30px" id="url-entry">
					<label for="url">SOURCE URL:</label>
						<i style="font-size: 17px;" id="url_tooltip_icon" class="far fa-question-circle tooltip_icon"></i>
						<p id="tooltip_content" class="url_tooltip">This is the URL of your externally sourced content.</p>
					<input type="url" name="url" id="url" onkeyup="generatePreview()" value="' . $sourceURL . '">
				</div>
				<div style="margin-bottom: 30px" id="description-entry">
					<label for="description">Description:</label>
						<i style="font-size: 17px;" id="description_tooltip_icon" class="far fa-question-circle tooltip_icon"></i>
						<p id="tooltip_content" class="description_tooltip">This is your custom description of the content.  Please add a few sentences describing what your content is about.  This will be displayed on social media.</p>
					<textarea name="description" id="description" rows="3" cols="50">' . $excerpt . '</textarea>
				</div>
				<div style="margin-bottom: 30px" id="image-entry">
					<label for="image">Add Image</label>
						<i style="font-size: 17px;" id="image_tooltip_icon" class="far fa-question-circle tooltip_icon"></i>
						<p id="tooltip_content" class="image_tooltip">Here you can add an image to your post to be display on social media.</p>
						<p>For best results, use a landscape image 750px x 450px</p>
					<input type="file" name="image" accept="image/*" id="image" onchange="openFile(event, ' . $id . ')">
					<img src="' .  $featuredImage . '"/>
				</div>
				<div style="margin-bottom: 30px" id="category-entry">
					<label for="category">Category</label>
					<i style="font-size: 17px;" id="category_tooltip_icon" class="far fa-question-circle tooltip_icon"></i>
					<p id="tooltip_content" class="category_tooltip">This is where you choose the category your content falls under.  For example, if my content is about roofing, I would choose the "Roofing" category.</p>
					<select name="category" id="category">';
							foreach ($categories as $category) {
								$editPostExternal .= "\n<option value='$category->slug'>" . $category->name . "</option>";
							}
		$editPostExternal .= '            
					</select>
				</div>
				<input name="post_id" id="post_id" type="hidden" value="' . $id . '">
				<input type="hidden" name="action" value="reliapost_updateSharedPost"/>
				<input id="updatepostbutton" type="submit" value="Update Post">
			</form>';
		$editPost = $editPostExternal;
		}
	
	
	echo "<div class='sc-post-content'>";
	echo "
		<h4>TITLE</h4>
		<h4><a style='text-align: center;' href=" . $link . ">$title</a></h4><br>
		<img style='text-align: center;' style='margin-top: 30px;'src='" .  $featuredImage . "'/>
		<h4>EXCERPT</h4>
		<span>$excerpt</span>
		<h4>SOURCE TYPE</h4>
		<span>$sourceType</span>
		<h4>CATEGORY</h4>
		<span>" . $cat . "</span><br>
		<button style='color: white !important;' class='cancelButton' id='cancelButton$id' data-id='$id'>DELETE</button><br>
		$editButton
		$editPost
		";
	echo "</div>";   
}

echo "</div>";
?>
</div>
<script>

function editPost(id) {
		jQuery('.edit'+id).toggle( "slow" );
		jQuery('.edit'+id).parent().toggleClass('editActive');
		jQuery('html, body').animate({ 
			scrollTop: jQuery('.edit'+id).offset().top 
		}, 1000);
	}

var openFile = function(event, id) {
		var input = event.target;
		var reader = new FileReader();
		reader.onload = function(){
		  var dataURL = reader.result;
		  var output = document.querySelector('.edit' + id + ' #image-entry img');
		  output.src = dataURL;
		};
		reader.readAsDataURL(input.files[0]);
	};
	
jQuery(".cancelButton").on("click", function(e){
	e.preventDefault();

	Swal.fire({
		title: 'Are you sure?',
		text: "This will delete your created post from our database.",
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
		action:"reliapost_deleteSharedPost",
		postId: id
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
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
