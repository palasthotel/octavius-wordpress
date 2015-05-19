<?php

/**
 * template file for Octavius URL Check page
 */

?>

<div class="wrap octavius-url-checker">
	<h2>Octavius URL Checker</h2>
		<?php
		$options = get_option('ph_octavius_ga_url_attributes', array());
		$page = (isset($options->page))? $options->page: 0;
		$pages = (isset($options->pages))? $options->pages: "...";
		// var_dump( $options );
		?>
		<input type="hidden" id="last-page-loaded" value="<?php echo $page; ?>">
		<p>Status: <span class="octavius-status-display"><?php echo $page."/".$pages; ?></span></p>
		<div class="progress-bar-wrapper"><div class="progress-bar"></div></div>
		<p><?php submit_button("Load URLs" ,"primary","ph_octavius_load", false); ?> 
		<?php submit_button("Reload URLs" ,"primary","ph_octavius_reload", false); ?></p>
		
		<hr />

		<table class="form-table">
			<tr>
				<th><label for="url-migration-meta">Metafeld der alten URL</label></th>
				<td><select id="meta-key-list">
			<?php 
			global $wpdb;
			$metas = $wpdb->get_results("SELECT DISTINCT meta_key FROM ".$wpdb->prefix."postmeta");
			foreach ($metas as $meta) {
				$selected="";
				if( get_option("octavius_url_checker_meta_key","") == $meta->meta_key)
				{
					$selected = "selected='selected'";
				}
				echo "<option $selected value='".$meta->meta_key."'>".$meta->meta_key."</option>";
			}
			?>
		</select></td>
			</tr>

			<tr>
				<th><label for="url-migration-regex">Regex</label></th>
				<td><input type="text" id="url-migration-regex" value="^.*/article(\d+)(\.ece|/).*$" /></td>
			</tr>
			<tr>
				<th scope="row">Gefunden</th>
				<td><span id="octavius-found">.</span></td>
			</tr>
			<tr>
				<th scope="row">Nicht gefunden</th>
				<td><span id="octavius-lost">.</span></td>
			</tr>
			<tr>
				<th scope="row">Status</th>
				<td><span id="octavius-loading">.</span></td>
			</tr>
			<tr>
				<th></th>
				<td><?php submit_button("Reload page to check again" ,"primary","ph_octavius_check"); ?></td>
			</tr>
		</table>


</div>