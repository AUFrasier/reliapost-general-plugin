<?php
/**
 * Created by PhpStorm.
 * User: personal
 * Date: 6/7/18
 * Time: 10:20 PM
 */

namespace reliapost_registration;


class Like {
    public $pageId;
    public $likes;
    public $platform;
    public $updatedAt;
}

class DatabaseController
{
    const TABLE_USERS = "reliapost_users";
    const USERS_USER_ID = "user_id";
    const USERS_EMAIL = "email";
    const USERS_PASSWORD = "password";
    const USERS_UPDATED_AT = "updated_at";

    const TABLE_BILLING = "reliapost_billing";
    const BILLING_USER_ID = "user_id";
    const BILLING_STRIPE_TOKEN = "stripe_customer_id";
    const BILLING_UPDATED_AT = "updated_at";

    const TABLE_SUBSCRIPTIONS = "reliapost_subscriptions";
    const SUBSCRIPTIONS_STRIPE_ID = "stripe_id";
    const SUBSCRIPTIONS_ENABLED = "enabled";
    const SUBSCRIPTIONS_IS_USER_PLAN = "is_user_plan";

    const TABLE_USER_CATEGORIES = "reliapost_user_categories";
    const USERCATEGORIES_USER_ID = "user_id";
    const USERCATEGORIES_SLUG = "slug";
    const USERCATEGORIES_UPDATED_AT = "updated_at";
	
	const SCHEDULED_POSTS = "scheduled_posts";


    public $wpUserTable;
    public $tableUsers;
    public $tableBilling;
    public $tablePaymentSources;
    public $tableSubscriptions;
    public $tableUserCategories;

    /**
     * DatabaseController constructor.
     */
    public function __construct()
    {
        global $wpdb;
        $prefix = "wp_";

        $this->wpUserTable = $wpdb->prefix . "users";
        $this->tableUsers = $prefix . self::TABLE_USERS;
        $this->tableBilling = $prefix . self::TABLE_BILLING;
        $this->tableSubscriptions = $prefix . self::TABLE_SUBSCRIPTIONS;
        $this->tableUserCategories = $prefix . self::TABLE_USER_CATEGORIES;
    }

    private function getWpdb() {
        global $wpdb;
        if ($wpdb==null) {
            require_once ("../../../../wp-config.php");
        }
        return $wpdb;
    }
    
    public function getTables() {
        $wpdb = $this->getWpdb();
        $sql = "SHOW TABLES";
        $tables = $wpdb->get_results($sql);
        return $tables; 
    }

    public function query($sql) {
        global $wpdb;
        return $wpdb->get_results($sql);
    }
	
	function deleteScheduledMessage() { 
        global $wpdb;
		$messageId = $_POST["messageId"];
        $userId = wp_get_current_user()->ID;
        $wpdb->delete("wp_scheduled_posts", array('id'=>$messageId, 'userId'=>$userId));
		echo "success";
		return;
		
    }

    function rescheduleMessage() { 
        global $wpdb;
		$messageId = $_POST["messageId"];
		$scheduledTime = $_POST["scheduledTime"];
        $userId = wp_get_current_user()->ID;
        $wpdb->update("wp_scheduled_posts", array("scheduled_time" => $scheduledTime), array("id" => $messageId ));
		echo "success";
		return;
    }
	
	function editScheduledPost() {
		global $wpdb;
		$messageId = $_POST["messageId"];
		$pageId = $_POST["pageId"];
		$message = $_POST["message"];
		$pageName = $_POST["pageName"];
        $userId = wp_get_current_user()->ID;
        $wpdb->update("wp_scheduled_posts", array("message_body" => $message, "page_name" => $pageName, "pageId" => $pageId), array("id" => $messageId ));
		echo "success";
		return;
	}
	function editSharedPost() {
		global $wpdb;
		$title = $_POST["title"];
        $url = $_POST["url"];
        $description = $_POST["description"];
		$content = $_POST["content_field"];
        $category = $_POST["category"];
		$post_id = intval($_POST["post_id"]);
		$categoryId = UserCategoryController::getCategoryIdBySlug($category);
		if ($_FILES["image"] != "") {
			$imageResult = AddPostController::handleImageUpload($post_id, $_FILES["image"]);
		}
		//if (AddPostController::isError($imageResult)) {
            //wp_delete_post($post_id);
            //AddPostController::onError($imageResult);
        //}
		$featured_img_url = get_the_post_thumbnail_url($post_id);
		$update_post = array(
			'ID'           => $post_id,
			'post_title'   => $title,
			'post_content' => $content,
			'post_category' => array($categoryId),
			'post_excerpt' => $description
		);
		wp_update_post( $update_post );
		update_post_meta( $post_id, '_artunlimited_inner_srcUrl', $url );
		update_post_meta( $postId, '_artunlimited_inner_category', $category );
		$wpdb->update("wp_scheduled_posts", array("category" => $category, "post_image_url" => $featured_img_url), array("post_id" => $post_id ));
		
		$response = new \stdClass();
		$response->status = "success";
        $response->file = json_encode($_FILES[0]);
        echo json_encode($response);
        wp_die();
	}
	
	function deleteSharedPost() {
		global $wpdb;
		$postId = $_POST["postId"];
        $userId = wp_get_current_user()->ID;
        $wpdb->delete("wp_posts", array('id'=>$postId));
		echo "success";
		return;
	}
}