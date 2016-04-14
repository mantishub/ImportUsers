<?php
class ImportUsersPlugin extends MantisPlugin
{
	function register() {
		$this->name = plugin_lang_get( 'title' );
		$this->description = plugin_lang_get( 'description' );
		$this->version = '1.0';
		$this->requires = array( 'MantisCore' => '1.3.0' );
		$this->author = 'MantisHub';
		$this->contact = 'http://www.mantishub.com';
		$this->url = 'http://www.mantishub.com';
	}

	function hooks() {
		return array(
			'EVENT_MENU_MANAGE' => 'csv_import_menu',
		);
	}

	function csv_import_menu() {
		return array(
			'<a href="' . plugin_page( 'import_users_page_init' ) . '">' . plugin_lang_get( 'import_users' ) . '</a>',
		);
	}
}
