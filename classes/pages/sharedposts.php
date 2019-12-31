<?php
/**
 * Created with NotePad++ by Jake Frasier.
 * User: AU
 * Date: 8/29/2019
 */

namespace reliapost_registration\Pages;

use reliapost_registration\UserCategoryController;
use reliapost_registration\View;

class sharedposts extends PageAbstract
{
    public function displayPage()
    {
        $this->configure();

        $pageData = [];

        $pageData["categories"] = get_categories();

        View::displayPage('shared_posts', $pageData);
    }

    protected function configure()
    {
        wp_enqueue_style("roboto", "https://fonts.googleapis.com/css?family=Roboto");
        wp_enqueue_style("quicksand", "https://fonts.googleapis.com/css?family=Quicksand");
        wp_enqueue_style("source_code_pro", "https://fonts.googleapis.com/css?family=Source+Code+Pro");
    }
}