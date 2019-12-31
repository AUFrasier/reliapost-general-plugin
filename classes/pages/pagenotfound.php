<?php
/**
 * Created by PhpStorm.
 * User: christopherruddell
 * Date: 2/15/18
 * Time: 9:12 PM
 */

namespace reliapost_registration\Pages;

use reliapost_registration\View;

class PageNotFound extends PageAbstract
{

    public function displayPage()
    {
        // TODO: Implement displayPage() method.
        $this->configure();
        $pageData = $this->attributes;
        View::displayPage('pagenotfound', $pageData);
    }

    protected function configure()
    {
        // TODO: Implement configure() method.
    }
}