<?php

namespace reliapost_registration\Pages;

interface PageInterface
{
	public function __construct(array $attributes);
	public function displayPage();
}
