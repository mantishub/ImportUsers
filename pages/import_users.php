<?php
require_once( 'core.php' );
require_api( 'category_api.php' );
require_api( 'database_api.php' );
require_api( 'user_api.php' );
require_api( 'bug_api.php' );

plugin_require_api( 'core/import_users_api.php' );
access_ensure_project_level( config_get ( 'manage_site_threshold' ) );

layout_page_header( plugin_lang_get( 'import_users' ) );
layout_page_begin();

$f_import_file = gpc_get_string( 'import_file' );
$f_separator = gpc_get_string( 'edt_cell_separator' );
$f_column_count = gpc_get_string( 'import_column_count' ) + 1;
$f_invite_emails = gpc_get_bool( 'invite_emails' );
$t_file_content = read_csv_file( $f_import_file );

$t_columns_lables = array();
$users_info = array();

# Display first file lines
$t_first_run = true;

# Import status
$status = array();

error_reporting( 0 );
$error_message = array();

foreach( $t_file_content as &$t_file_line ) {
	# Ignore columns labels
	if( $t_first_run ) {
		$t_first_run = false;

		foreach( read_csv_row ( $t_file_line, $f_separator ) as $t_element ) {
			$t_columns_lables[] = trim( $t_element );
		}

		continue;
	} else {
		$users_info = array ();
		foreach( read_csv_row ( $t_file_line, $f_separator ) as $t_element ) {
			$users_info[] = trim( $t_element );
		}

		# default access_level
		$users_info[COLUMN_ACCESS_LEVEL] = MantisEnum::getValue( config_get( 'access_levels_enum_string' ), $users_info[COLUMN_ACCESS_LEVEL] );
		if( is_blank( $users_info[COLUMN_ACCESS_LEVEL] ) ) {
			$users_info[COLUMN_ACCESS_LEVEL] = REPORTER;
		}

		# default protected
		if( is_blank($users_info[COLUMN_PROTECTED] ) ) {
			$users_info[COLUMN_PROTECTED] = false;
		} else {
			if( strtolower( $users_info[COLUMN_PROTECTED] ) == 'false' ) {
				$users_info[COLUMN_PROTECTED] = false;
			}
		}
		
		# default enabled
		if( is_blank($users_info[COLUMN_ENABLED] ) ) {
			$users_info[COLUMN_ENABLED] = true;
		} else {
			if( strtolower( $users_info[COLUMN_ENABLED] ) == 'false' ) {
				$users_info[COLUMN_ENABLED] = false;
			}
		}

		# error message
		if( !user_is_name_valid( $users_info[COLUMN_USER_NAME] ) ) {
			$error_message[] = plugin_lang_get( 'username_vaild_error' );
		}

		if( !user_is_name_unique( $users_info[COLUMN_USER_NAME] ) ) {
			$error_message[] = plugin_lang_get( 'username_unique_error' );
		}

		if( 1 > user_is_realname_unique( $users_info[COLUMN_USER_NAME], $users_info[COLUMN_REAL_NAME] ) ) {
			$error_message[] = plugin_lang_get( 'realname_unique_error' );
		}

		if( !is_blank( $users_info[COLUMN_EMAIL_ADDRESS] ) ) {
				if( !email_is_valid( $users_info[COLUMN_EMAIL_ADDRESS] ) ) {
					$error_message[] = plugin_lang_get( 'email_vaild_error' );
				}

				if( !user_is_email_unique( $users_info[COLUMN_EMAIL_ADDRESS] ) ) {
					$error_message[] = plugin_lang_get( 'email_unique_error' );
				}
		}

		if ( is_blank( $users_info[COLUMN_PASSWORD] ) ) {
			$users_info[COLUMN_PASSWORD] = auth_generate_random_password();
		}

		if( !empty( $error_message ) ) {
			$status [] = $error_message;
			$error_message = array();
		} else {
			csv_user_create(
				$users_info[COLUMN_USER_NAME], $users_info[COLUMN_PASSWORD], $users_info[COLUMN_EMAIL_ADDRESS],
				$users_info[COLUMN_ACCESS_LEVEL], $users_info[COLUMN_PROTECTED], $users_info[COLUMN_ENABLED],
				$users_info[COLUMN_REAL_NAME], $f_invite_emails );
			$status[] = plugin_lang_get( 'import_success' );
		}
	}
}
?>

<div class="col-md-12 col-xs-12">

	<div class="space-10"></div>
	<div class="form-container">

	<div class="widget-box widget-color-blue2">
		<div class="widget-header widget-header-small">
			<h4 class="widget-title lighter">
			<?php echo 'Import Status'?>
			</h4>
		</div>

        <div class="widget-body">
			<div class="widget-main no-padding">
				<div class="table-responsive">

<table class="table table-striped table-bordered table-condensed table-hover">	
	<tr class="row-category">
	<?php
	// Write columns labels
	foreach( $t_columns_lables as $columns ) {
		echo '<td>' . prepare_output( $columns ) . '</td>';
	}
	echo '<td>', plugin_lang_get( 'import_status' ), '</td>';
	?>
	</tr>

<?php
// Display file lines
$firstline_skip = true;
$t_display_max = 20;
$i = -2;

foreach( $t_file_content as &$t_file_line ) {

	$i++;

	// Ignore columns labels
	if( $firstline_skip ) {
		$firstline_skip = false;
		continue;
	}
	echo '<tr>';
	// Still more lines (add "...")
	if( --$t_display_max < 0 ) {
		echo str_repeat( '<td>&hellip;</td>', $f_column_count );
		echo '</tr>';
		break;
	} else {
		// Write values
		foreach( read_csv_row( $t_file_line, $f_separator ) as $t_key => $t_element ) {

			if( $t_key == COLUMN_PASSWORD ) {
				if( is_blank( $t_element ) ) {
					echo '<td><font color="green">' . plugin_lang_get( 'auto_generate' ) . '</font></td>';
				} else {
					echo '<td>' . prepare_output( $t_element ) . '</td>';
				}
				continue;
			}

			if( $t_key == COLUMN_PROTECTED ) {
				if( is_blank( $t_element ) ) {
					echo '<td><font color="green">false</font></td>';
				} else {
					echo '<td>' . prepare_output( $t_element ) . '</td>';
				}
				continue;
			}

			if( $t_key == COLUMN_ENABLED ) {
				if( is_blank( $t_element ) ) {
					echo '<td><font color="green">true</font></td>';
				} else {
					echo '<td>' . prepare_output( $t_element ) . '</td>';
				}
				continue;
			}

			echo '<td>' . prepare_output( $t_element ) . '</td>';
		}
	}

	if( $i >= 0 ) {
		if( $status[$i] == 'success' ) {
			echo '<td><font color="green">' . prepare_output( $status[$i] ) . '</font></td>';
		} else {
			echo '<td>';
			foreach( $status[$i] as $err ) {
				echo '<font color="red">'. prepare_output( $err ) . '</font><br/>';
			}
			echo '</td>';
	    }
	}
	echo '</tr>';
}
?>
</table>
				</div>
      		</div>
	  	</div>
	</div> 
	</div>
</div>
<?php
layout_page_end( __FILE__ );
