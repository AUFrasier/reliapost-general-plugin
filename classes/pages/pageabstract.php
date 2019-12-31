<?php

namespace reliapost_registration\Pages;


abstract class PageAbstract implements PageInterface
{
	public $extraPageData = [];
	
	protected $attributes = [];
	protected $client = null;
	protected $search = null;
	
	public function __construct(array $attributes)
	{
		$this->attributes = $attributes;
	}
	
	public function hasAttribute($attribute)
	{
		return array_key_exists($attribute, $this->attributes);
	}
	
	public function getSearch()
	{
		return $this->search;
	}

	
	abstract public function displayPage();
	abstract protected function configure();
}
