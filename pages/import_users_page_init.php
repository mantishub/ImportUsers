<?php
# Mantis - a php based bugtracking system
require_once( 'core.php' );
plugin_require_api( 'core/import_users_api.php' );
access_ensure_global_level( ADMINISTRATOR );

layout_page_header( plugin_lang_get( 'manage_users' ) );
layout_page_begin();

$import_page = plugin_page('import_users_page_col_set');

$t_max_file_size = (int)min( ini_get_number( 'upload_max_filesize' ), ini_get_number( 'post_max_size' ), config_get( 'max_file_size' ) );
?>
<div class="col-md-12 col-xs-12">
	<div class="space-10"></div>
	<div class="form-container">
		<form method="post" enctype="multipart/form-data" action="<?php echo $import_page ?>">

		<div class="widget-box widget-color-blue2">
			<div class="widget-header widget-header-small">
				<h4 class="widget-title lighter">
					<?php echo plugin_lang_get( 'users_file' ) ?>
				</h4>
			</div>

			<div class="widget-body">
				<div class="widget-main no-padding">
					<div class="table-responsive">

			<table class="table table-striped table-bordered table-condensed table-hover">
				<tr>
					<td class="category" style="text-align:center">
						<input id="edt_cell_separator" name="edt_cell_separator" type="text" size="15" maxlength="1" value="<?php echo config_get( 'csv_separator' )?>" style="text-align:center"/>
						</td>
					<td>
					<?php echo plugin_lang_get( 'file_format_col_spacer' ) ?> -
					<a href="#" onclick="javascript:document.getElementById('edt_cell_separator').value=String.fromCharCode(9);">[<?php echo plugin_lang_get( 'tab_csv_separator' ) ?>]</a>
					</td>
				</tr>
	
				<tr>
					<td class="category" colspan="1" style="text-align:center">
					<?php echo plugin_lang_get( 'user_name' ) ?>
					</td>
					<td>
					<?php echo plugin_lang_get( 'user_name_description' ) ?>
					</td>
				</tr>
	
				<tr>
					<td class="category" colspan="1" style="text-align:center">
					<?php echo plugin_lang_get( 'real_name' ) ?>
					</td>
					<td>
					<?php echo plugin_lang_get( 'real_name_description' ) ?>
					</td>
				</tr>
	
				<tr>
					<td class="category" colspan="1" style="text-align:center">
					<?php echo plugin_lang_get( 'email_address' ) ?>
					</td>
					<td>
					<?php echo plugin_lang_get( 'email_address_description' ) ?>
					</td>
				</tr>
	
				<tr>
					<td class="category" colspan="1" style="text-align:center">
					<?php echo plugin_lang_get( 'access_level' ) ?>
					</td>
					<td>
					<?php echo plugin_lang_get( 'access_level_description' ) ?>
					</td>
				</tr>
	
				<tr>
					<td class="category" colspan="1" style="text-align:center">
					 <?php echo plugin_lang_get( 'user_password' ) ?>
					</td>
					<td>
					<?php echo plugin_lang_get( 'user_password_description' ) ?>
					</td>
				</tr>
	
				<tr>
					<td class="category" colspan="1" style="text-align:center">
					<?php echo plugin_lang_get( 'user_protected' ) ?>
					</td>
					<td>
					<?php echo plugin_lang_get( 'user_protected_description' ) ?>
					</td>
				</tr>
	
				<tr>
					<td class="category" colspan="1" style="text-align:center">
					<?php echo plugin_lang_get( 'user_enabled' ) ?>
					</td>
					<td>
					<?php echo plugin_lang_get( 'user_enabled_description' ) ?>
					</td>
				</tr>
	
				<tr>
					<td class="category" colspan="1" style="text-align:center">
					<input type="checkbox" class="ace" checked="checked" name="invite_emails" />
			        <span class="lbl"> </span>
					</td>
					<td>
					<?php echo plugin_lang_get( 'invite_emails_description' );?> 
					</td>
				</tr>
	
				<tr>
					<td class="category" width="15%" style="text-align:center">
					<?php echo lang_get( 'select_file' ) ?><br />
					<?php echo '<span class="small">(' . plugin_lang_get( 'max_file_size_label' ) . ': ' . number_format( $t_max_file_size/1000 ) . 'k)</span>'?>
					</td>
					<td width="85%" colspan="2">
					<input type="hidden" name="max_file_size" value="<?php echo $t_max_file_size ?>" />
					<input type="file" name="import_file" size="40" />
					</td>
				</tr>
			</table>
					</div>
				</div>
	
				<div class="widget-toolbox padding-8 clearfix">
					<input type="submit" class="btn btn-primary btn-white btn-sm btn-round" value="<?php echo lang_get( 'upload_file_button' ) ?>" />
				</div>
			</div>
		</div>

		</form>
	</div>
</div>

<?php
layout_page_end( __FILE__ );
    