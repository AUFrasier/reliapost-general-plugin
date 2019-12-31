<?php
/**
 * Created by PhpStorm.
 * User: personal
 * Date: 11/20/18
 * Time: 6:59 AM
 */

namespace reliapost_registration\Pages;

use reliapost_registration\UserCategoryController;
use reliapost_registration\View;

class myaccount extends PageAbstract
{
    public function displayPage()
    {
        $this->configure();
        View::displayPage('my_account', []);
    }

    protected function configure()
    {
        wp_enqueue_style("roboto", "https://fonts.googleapis.com/css?family=Roboto");
        wp_enqueue_style("quicksand", "https://fonts.googleapis.com/css?family=Quicksand");
        wp_enqueue_style("source_code_pro", "https://fonts.googleapis.com/css?family=Source+Code+Pro");
    }
}