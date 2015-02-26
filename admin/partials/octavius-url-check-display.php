<?php

/**
 * template file for Octavius URL Check page
 */

?>

<div class="wrap octavius-url-checker">
	<h2>Octavius URL Checker</h2>
		<p>Status: <span class="octavius-status-display">inaktiv</span></p>
		<div class="progress-bar-wrapper"><div class="progress-bar"></div></div>
		<?php submit_button("URLs Check" ,"primary","ph_octavius_reload"); ?>

		<label for="url-migration-meta">Metafeld der alten URL</label><br>
		<select id="meta-key-list">
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
		</select>
		<table class="form-table">
			<tr>
				<th scope="row">Gefunden</th>
				<td><a id="octavius-found-link" href="#"><span id="octavius-found">.</span></a></td>
			</tr>
			<tr>
				<th scope="row">Nicht gefunden</th>
				<td><a id="octavius-lost-link" href="?page=ph-octavius_url_checker&amp;show_results=lost&amp;paged=1"><span id="octavius-lost">.</span></a></td>
			</tr>
		</table>


</div>