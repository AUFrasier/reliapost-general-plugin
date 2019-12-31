<?php
/**
 * Created by PhpStorm.
 * User: personal
 * Date: 11/21/18
 * Time: 8:41 AM
 */

namespace reliapost_registration;


class UserCategoryController
{
    const INVALID_CATEGORY = -1;

    static function addCategoryToUser() {
        global $wpdb;

        $user = wp_get_current_user();
        $userId = $user->ID;
        $slug = $_POST["slug"];

        $categoryId = self::getCategoryIdBySlug($slug);

        $results = new \stdClass();

        if ($categoryId != self::INVALID_CATEGORY) {
            $dbController = new DatabaseController();
            $table = $dbController->tableUserCategories;

            //make sure we don't already have this one added to the user
            $queryResults = $wpdb->get_results("SELECT * FROM " . $dbController->tableUserCategories . " WHERE " . DatabaseController::USERCATEGORIES_USER_ID . " = " . $userId . " AND " . DatabaseController::USERCATEGORIES_SLUG . " = " . $categoryId);
            if (count($queryResults)>0) {
                //already exists
                $results->status = "exists";
            }
            else {
                $wpdb->insert($table, [
                    DatabaseController::USERCATEGORIES_USER_ID => $userId,
                    DatabaseController::USERCATEGORIES_SLUG => $categoryId,
                    DatabaseController::USERCATEGORIES_UPDATED_AT => current_time("mysql")
                ]);

                $results->status = "success";
                $results->slug = $slug;

                $controller = new UserCategoryController();
                $results->categories = $controller->getUserCategories();
            }
        }
        else {
            $results->status = "failure";
        }
        echo json_encode($results);

        wp_die();
    }

    static function removeCategoryFromUser() {
        global $wpdb;

        $user = wp_get_current_user();
        $userId = $user->ID;
        $slug = $_POST["slug"];

        $dbController = new DatabaseController();

        $categoryId = self::getCategoryIdBySlug($slug);
        $results = new \stdClass();

        if ($categoryId != self::INVALID_CATEGORY) {
            $delResults = $wpdb->delete($dbController->tableUserCategories, [DatabaseController::USERCATEGORIES_USER_ID => $userId, DatabaseController::USERCATEGORIES_SLUG => $categoryId]);
            if ($delResults === false) {
                $results->status = "failure";
            }
            else {
                $results->status = "success";
                //query updated list
                $controller = new UserCategoryController();
                $results->categories = $controller->getUserCategories();
            }
        }
        else {
            $results->status = "invalidCategory";
        }
        echo json_encode($results);
        wp_die();
    }

    static function getCategoryIdBySlug($slug) {
        //validate slug
        $categories = get_categories();
        $categoryId = -1;

        $results = new \stdClass();

        foreach ($categories as $category) {
            if ($category->slug == $slug) {
                return $category->cat_ID;
            }
        }

        return self::INVALID_CATEGORY;
    }

    function getUserCategoriesByUser($user) {
        global $wpdb;
        $userId = $user->ID;
        $dbController = new DatabaseController();

        $allCategories = get_categories();
        $entries = $wpdb->get_results("SELECT slug FROM " . $dbController->tableUserCategories . " WHERE " . DatabaseController::USERCATEGORIES_USER_ID . " = " . $userId);

        $userCategories = [];
        foreach ($entries as $entry) {
            $categoryId = $entry->slug;
            foreach ($allCategories as $category) {
                if ($category->cat_ID == $categoryId) $userCategories[] = $category->slug;
            }
        }

        return $userCategories;
    }

    function getUserCategories() {
        global $wpdb;
        $user = wp_get_current_user();
        return $this->getUserCategoriesByUser($user);
    }

    function getUserCategoryIds() {
        global $wpdb;
        $user = wp_get_current_user();
        $userId = $user->ID;
        $dbController = new DatabaseController();

        $entries = $wpdb->get_results("SELECT slug FROM " . $dbController->tableUserCategories . " WHERE " . DatabaseController::USERCATEGORIES_USER_ID . " = " . $userId);

        $userCategories = [];
        foreach ($entries as $entry) {
            $categoryId = $entry->slug;
            $userCategories[] = $categoryId;
        }

        return $userCategories;

    }
}