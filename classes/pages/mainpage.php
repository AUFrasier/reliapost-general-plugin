<?php
/**
 * Created by PhpStorm.
 * User: christopherruddell
 * Date: 2/15/18
 * Time: 9:12 PM
 */

namespace reliapost_registration\Pages;

use reliapost_registration\View;

class MainPage extends PageAbstract
{

    public function displayPage()
    {
        // TODO: Implement displayPage() method.
        $this->configure();
        $pageData = []; 
        View::displayPage('main_view', $pageData);
    }

    protected function configure()
    {
        // TODO: Implement configure() method.
    }
}