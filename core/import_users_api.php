<?php
#
# Copyright (c) 2016 MantisHub
# Licensed under the MIT license
#

require_api( 'user_api.php' );

# Column Names
define( 'COLUMN_USER_NAME',     0 );
define( 'COLUMN_REAL_NAME',     1 );
define( 'COLUMN_EMAIL_ADDRESS', 2 );
define( 'COLUMN_ACCESS_LEVEL',  3 );
define( 'COLUMN_PASSWORD',      4 );
define( 'COLUMN_PROTECTED',     5 );
define( 'COLUMN_ENABLED',       6 );

# --------------------
function csv_string_unescape( $p_string ) {
	$t_wo_quotes = preg_replace( '/\A"(.*)"\z/sm', '${1}', $p_string );
	if( $t_wo_quotes !== $p_string ) {
		$t_wo_quotes = str_replace( '""', '"', $t_wo_quotes );
	}

	return $t_wo_quotes;
}

# --------------------
function read_csv_file( $p_filename ) {
	$t_regexp = '/\G((?:[^"\r\n]+|"[^"]*")+)[\r|\n]*/sm';
	$t_file_content = file_get_contents( $p_filename );
	preg_match_all( $t_regexp, $t_file_content, $t_file_rows );
	return $t_file_rows[1];
}

# --------------------
function read_csv_row( $p_file_row, $p_separator ) {
	$t_regexp = '/\G(?:\A|\\' . $p_separator . ')([^"\\' . $p_separator . ']+|(?:"[^"]*")*)/sm';
	preg_match_all( $t_regexp, $p_file_row, $t_row_element );
	return array_map( 'csv_string_unescape', $t_row_element[1] );
}

# --------------------
function prepare_output( $t_string ) {
	return string_html_specialchars( utf8_encode( $t_string ) );
}

# --------------------
function csv_user_create(
	$p_username, $p_password, $p_email = '', $p_access_level = null, $p_protected = false,
	$p_enabled = true, $p_realname = '', $p_invite_emails = false, $p_admin_name = '' ) {

	$t_password = auth_process_plain_password( $p_password );

	$c_enabled = (bool)$p_enabled;

	$t_cookie_string = auth_generate_unique_cookie_string();

	$t_query = 'INSERT INTO {user}
				    ( username, email, password, date_created, last_visit,
				     enabled, access_level, login_count, cookie_string, realname )
				  VALUES
				    ( ' . db_param() . ', ' . db_param() . ', ' . db_param() . ', ' . db_param() . ', ' . db_param()  . ',
				     ' . db_param() . ',' . db_param() . ',' . db_param() . ',' . db_param() . ', ' . db_param() . ')';
	db_query(
		$t_query, array( $p_username, $p_email, $t_password, db_now(), db_now(), $c_enabled,
			(int)$p_access_level, 0, $t_cookie_string, $p_realname ) );

	# Create preferences for the user
	$t_user_id = db_insert_id( db_get_table( 'user' ) );

	# Users are added with protected set to FALSE in order to be able to update
	# preferences.  Now set the real value of protected.
	if( $p_protected ) {
		user_set_field( $t_user_id, 'protected', (bool)$p_protected );
	}

	# Send notification email
	if( $c_enabled ) {
		if( $p_invite_emails ) {
			$t_confirm_hash = auth_generate_confirm_hash( $t_user_id );
			email_signup( $t_user_id, $t_confirm_hash, $p_admin_name );
		}
	}

	event_signal( 'EVENT_MANAGE_USER_CREATE', array( $t_user_id ) );

	return $t_cookie_string;
}

