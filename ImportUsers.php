<?php
#
# Copyright (c) 2016 MantisHub
# Licensed under the MIT license
#

class ImportUsersPlugin extends MantisPlugin
{
	function register() {
		$this->name = plugin_lang_get( 'title' );
		$this->description = plugin_lang_get( 'description' );
		$this->page = 'import_users_page_init';
		$this->version = '1.0';
		$this->requires = array( 'MantisCore' => '2.0.0' );
		$this->author = 'MantisHub';
		$this->contact = 'support@mantishub.com';
		$this->url = 'https://www.mantishub.com';
	}
}
