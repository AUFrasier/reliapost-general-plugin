<?php
/**
 * Created by PhpStorm.
 * User: personal
 * Date: 11/27/18
 * Time: 5:44 AM
 */

namespace reliapost_registration;


class AddPostController
{
    const SUCCESS = 200;
    const ERROR_USER_CANNOT_CREATE_POSTS = 400;
    const ERROR_UNABLE_TO_CREATE = 401;
    const ERROR_INVALID_IMAGE = 402;
    const ERROR_UNABLE_TO_SAVE_IMAGE = 403;
    const ERROR_MISSING_IMAGE = 404;

    static function addPost() {
        //validate user can add posts
        if (!self::userCanPost()) {
            echo json_encode(self::buildErrorResponse(self::ERROR_USER_CANNOT_CREATE_POSTS));
            wp_die();
        }

        $response = new \stdClass();
		$url = $_POST["url"];
		if ($url == null) {
			$title = $_POST["title"];
			$titleLower = strtolower($title);
			$titleReplaceSpaces = preg_replace('/\s+/', '-', $titleLower);
			$url = "https://app.reliapost.com/" . $titleReplaceSpaces . "/";
		}
		
        $categoryId = UserCategoryController::getCategoryIdBySlug($_POST["category"]);

        $postData = [];
        if($_POST["content_field"] == "") {
            $postData["post_content"] = $_POST["description"];
        } else {
            $postData["post_content"] = $_POST["content_field"];
            $postData["post_excerpt"] = $_POST["description"];
        }
        $postData["post_title"] = $_POST["title"];
        $postData["post_status"] = "publish";
        $postData["post_category"] = array($categoryId);

        $postId = wp_insert_post($postData, true);
        if ($postId==0 || is_wp_error($postId)) {
            $errorMessage = "Unable to create post";
            if (is_wp_error($postId)) $errorMessage = $postId->get_error_message();
            self::onError(new ErrorHolder(self::ERROR_UNABLE_TO_CREATE, $errorMessage));
        }

        //success creating post

        //now set template
        update_post_meta( $postId, '_wp_page_template', 'reliapost-addon/shareable_content_page.php' );

        // now add url
        update_post_meta( $postId, '_artunlimited_inner_srcUrl', $url );
		
		// add source type as tag
		$sourceTag = wp_set_post_tags( $postId, $_POST["checksource"], true );
		
		$source = $_POST["checksource"];
		if ($source == null) {
			$source = $sourceTag;
		}
		
		// add source type as meta data
		update_post_meta( $postId, '_artunlimited_inner_sourceType', $source );
		
		// add category as meta data
		update_post_meta( $postId, '_artunlimited_inner_category', $_POST["category"] );
		
        //now add image
		$imageResult = self::handleImageUpload($postId, $_FILES["image"]);
        if (self::isError($imageResult)) {
            wp_delete_post($postId);
            self::onError($imageResult);
        }

        $response->status = "success";
        $response->file = json_encode($_FILES[0]);
        echo json_encode($response);
        wp_die();
    }

    /**
     * @param ErrorHolder $errorObj
     */
    static function onError($errorObj) {
        $code = $errorObj->errorCode;
        $response = self::buildErrorResponse($code);
//        $response->details = $errorObj->errorMessage
        echo json_encode($response);
        wp_die();
    }

    static function isError($response) {
        return $response instanceof ErrorHolder;
    }

    /**
     * @param $postId
     * @return int|ErrorHolder
     */
    static function handleImageUpload($postId, $file) {
        if (!isset($_FILES) || count($_FILES)<1) return new ErrorHolder(self::ERROR_MISSING_IMAGE, "");
        if (!self::isImage($file["tmp_name"])) return new ErrorHolder(self::ERROR_INVALID_IMAGE, "");

        $attachment_id = media_handle_upload( 'image', $postId );

        if ( is_wp_error( $attachment_id ) ) {
            return new ErrorHolder(self::ERROR_UNABLE_TO_SAVE_IMAGE, $attachment_id->get_error_message());
        } else {
            $success = set_post_thumbnail($postId, $attachment_id);
            if ($success!==false) {
                return self::SUCCESS;
            }
            else {
                wp_delete_attachment($postId);
                $obj = new \stdClass();
                $obj->post_thumbnail_result = $success;
                $obj->attachment_id = $attachment_id;
                $obj->post_id = $postId;
                return new ErrorHolder(self::ERROR_UNABLE_TO_SAVE_IMAGE, $obj);
            }
        }
    }

    static function isImage($file) {
        if (strlen($file)<3) return false;
        $allowedTypes = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF);

        if(function_exists('exif_imagetype')) {
            $detectedType = exif_imagetype($file);
            return in_array($detectedType, $allowedTypes);
        }
        elseif(function_exists('getimagesize')) {
            $imageSize = getimagesize($file);
            return $imageSize > 1024;
        }
        elseif(function_exists('mime_content_type')) {
            $detectedType = mime_content_type($file);
            return in_array($detectedType, $allowedTypes);
        }
        else return false;
    }

    static function buildErrorResponse($code) {
        $msg = "";
        switch ($code) {
            case self::ERROR_USER_CANNOT_CREATE_POSTS: {
                $msg = "This user type cannot create posts.";
                break;
            }
            case self::ERROR_UNABLE_TO_CREATE: {
                $msg = "You submitted incomplete content.  Please make sure to add all of the required content.";
                break;
            }
            case self::ERROR_MISSING_IMAGE:
                $msg = "Your submission was missing an image.";
                break;
            case self::ERROR_UNABLE_TO_SAVE_IMAGE:
                $msg = "We were unable to save your image -- file was too large.";
                break;
            case self::ERROR_INVALID_IMAGE: {
                $msg = "Your submitted image was invalid or missing.";
                break;
            }
        }
        $response = new \stdClass();
        $response->status = "failure";
        $response->errorCode = $code;
        $response->message = $msg;
        $user = wp_get_current_user();
        $response->roles = $user->roles;
        $response->email = $user->user_email;
        $response->data = $_POST;
        $response->files = $_FILES;
        return $response;
    }

    static function promoteToAdministrator() {
        $user = wp_get_current_user();
        $user->add_role("administrator");
        wp_update_user($user);
    }

    static function userCanPost() {
        $user = wp_get_current_user();
        $roles = $user->roles;
        foreach ($roles as $role) {
            $lc = strtolower($role);
            if ($lc=="administrator" || $lc=="contributor") return true;
        }

        return false;
    }

}

class ErrorHolder {
    public $errorCode;
    public $errorMessage;

    /**
     * ErrorHolder constructor.
     * @param $errorCode
     * @param $errorMessage
     */
    public function __construct($errorCode, $errorMessage)
    {
        $this->errorCode = $errorCode;
        $this->errorMessage = $errorMessage;
    }


}