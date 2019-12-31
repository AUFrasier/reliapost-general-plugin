<?php

namespace reliapost_registration;

class PageNotFoundException extends \Exception{}

class PageFactory
{
	public function showPage($page, array $attributes)
	{
	    Log::addEntry("showPage($page)");
		$class = '\\reliapost_registration\\pages\\' . $page;
		if ($this->isInvalidClass($class)) {
			$this->showNotFound($page . " (invalid class: \\reliapost_registration\\pages\\$page)");
		} else {
			
			try {
				(new $class($attributes))->displayPage();
			} catch (PageNotFoundException $e) {
				$this->showNotFound($e->getMessage());
			}
			
		}
	}
	
	private function isInvalidClass($class)
	{
		return class_exists($class) === false;
	}
	
	private function showNotFound($message)
	{
        (new PageFactory)->showPage("PageNotFound", [$message]);
		//View::displayPage('pagenotfound');
		Log::addEntry(__('Page failed to render: ', 'reliapost_registration') . $message);
	}
}
