<?php

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