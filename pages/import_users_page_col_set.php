 <?php
#
# Copyright (c) 2016 MantisHub
# Licensed under the MIT license
#

require_once( 'core.php' );
plugin_require_api( 'core/import_users_api.php' );

access_ensure_global_level( ADMINISTRATOR );

layout_page_header( plugin_lang_get( 'import_users' ) );
layout_page_begin();

$t_import_it = plugin_page( 'import_users' );
$f_invite_emails = gpc_get_bool( 'invite_emails' );

// Look if the import file name is in the posted data
$f_import_file = gpc_get_file( 'import_file', - 1 );

// Check fields are set
if ( is_blank ( $f_import_file ['tmp_name'] ) || ( $f_import_file ['size'] == 0 ) ) {
	plugin_error( 'ERROR_FILE_UPLOAD', ERROR );
}

// File analysis
$t_file_content = read_csv_file( $f_import_file ['tmp_name'] );
$t_separator = gpc_get_string( 'edt_cell_separator' );

$t_column_count = -1;
$t_column_title = array();

foreach( $t_file_content as $t_key => &$t_file_line ) {
	$t_elements = read_csv_row( $t_file_line, $t_separator );

	// First line
	if( $t_column_count < 0 ) {
		for( $i = 0; $i < count ( $t_elements ); $i++ ) {
			if( trim ( $t_elements [$i] ) == '') {
				$t_elements = array_slice( $t_elements, 0, $i );
				break 1;
			}
		}
		$t_column_count = count( $t_elements );
		$t_column_title = $t_elements;
	}	

	// Other lines
	elseif( $t_column_count != count ( $t_elements ) ) {
		$t_row = explode( $t_separator, $t_file_line );
		$t_row = array_slice( $t_row, 0, $t_column_count );
		$t_file_line = implode( $t_separator, $t_row );
	}
}

if( is_writable( $f_import_file ['tmp_name'] ) ) {
	if( $handle = fopen ( $f_import_file ['tmp_name'], "wb" ) ) {
		foreach( $t_file_content as &$t_file_line ) {
			$t_written = fwrite( $handle, $t_file_line . "\n" );
		}
		fclose( $handle );
	} else {
		error_parameters( plugin_lang_get ( 'error_file_not_opened' ) );
		plugin_error( 'ERROR_FILE_FORMAT', ERROR );
	}
} else {
	error_parameters( plugin_lang_get ( 'error_file_not_writable' ) );
	plugin_error( 'ERROR_FILE_FORMAT', ERROR );
}

// Move file
$t_file_name = tempnam( dirname ( $f_import_file ['tmp_name'] ), 'tmp' );
move_uploaded_file( $f_import_file ['tmp_name'], $t_file_name );
?>

<!-- File extraction -->
<div class="col-md-12 col-xs-12">

	<div class="space-10"></div>
	<div class="form-container">

	<div class="widget-box widget-color-blue2">
		<div class="widget-header widget-header-small">
			<h4 class="widget-title lighter">
				<?php echo $f_import_file['name']?>	
		    </h4>
		</div>

		<div class="widget-body">
			<div class="widget-main no-padding">
				<div class="table-responsive">
<table class="table table-striped table-bordered table-condensed table-hover">

<tr class="row-category">
	<?php
	// Write columns labels
	for($i = 0; $i < $t_column_count; $i ++) {
		echo '<td>';
		echo prepare_output ( $t_column_title [$i] );
		echo '</td>';
	}
	?>
	</tr>

<?php
// Display file lines
$t_first_run = true;
$t_display_max = 10000;

foreach ( $t_file_content as &$t_file_line ) {
	// Ignore columns labels
 	if ($t_first_run) {
		$t_first_run = false;
		continue;
	} 
	echo '<tr>';
	// Still more lines (add "...")
	if ( -- $t_display_max < 0 ) {
		echo str_repeat ( '<td>&hellip;</td>', $t_column_count );
		echo '</tr>';
		break;
	} else {
		$t_skip_row = false;
		// Write values
		foreach ( read_csv_row ( $t_file_line, $t_separator ) as $t_key => $t_element ) {
			if( $t_key == COLUMN_USER_NAME && is_blank( $t_element ) ) {
				$t_skip_row = true;
			}

			if( $t_skip_row ) {
				continue;
			}

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
					echo '<td>' . prepare_bool_output( $t_element, false ) . '</td>';
				}

				continue;
			}

			if( $t_key == COLUMN_ENABLED ) {
				if( is_blank( $t_element ) ) {
					echo '<td><font color="green">true</font></td>';
				} else {
					echo '<td>' . prepare_bool_output( $t_element, true ) . '</td>';
				}

				continue;
			}

			echo '<td>' . prepare_output( $t_element ) . '</td>';
		}		
	}
	echo '</tr>';
}

?>
</table>
				</div>
			</div>

			<div class="widget-toolbox padding-8 clearfix">
			<form method="post" action="<?php echo $t_import_it ?>">
				<input type="hidden" name="edt_cell_separator" value="<?php echo $t_separator ?>" /> 
				<input type="hidden" name="import_file" value="<?php echo $t_file_name ?>" /> 
				<input type="hidden" name="import_column_count" value="<?php echo $t_column_count ?>" /> 
				<input type="hidden" name="invite_emails" value="<?php echo $f_invite_emails ?>" /> 
				<input type="submit" id="importForm" class="btn btn-primary btn-white btn-sm btn-round" value="<?php echo plugin_lang_get( 'file_button' ) ?>" />
			</form>
			</div>
		</div>
	</div>
	</div>
</div>
<?php
layout_page_end( __FILE__ );
