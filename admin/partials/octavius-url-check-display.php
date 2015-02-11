<?php

/**
 * template file for Octavius URL Check page
 */

?>

<div class="wrap octavius-url-checker">
	<h2>Octavius URL Checker</h2>

		<table class="form-table">
			<tr>
				<th scope="row">Gefunden</th>
				<td>200 <a href="#">Liste</a></td>
			</tr>
			<tr>
				<th scope="row">Nicht gefunden</th>
				<td>10 <a href="#">Liste</a></td>
			</tr>
			<tr>
				<th scope="row">Über</th>
				<td>20 <a href="#">Liste</a></td>
			</tr>
		</table>
		<p>Status: <span class="octavius-status-display">inaktiv</span></p>
		<div class="progress-bar-wrapper"><div class="progress-bar"></div></div>
		<?php submit_button("Reload" ,"primary","ph_octavius_reload"); ?>



</div>