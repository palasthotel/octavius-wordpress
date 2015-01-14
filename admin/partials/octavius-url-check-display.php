<?php

/**
 * template file for Octavius URL Check page
 */

?>

<div class="wrap">
	<h2>Octavius URL Checker</h2>
	<form method="post" action="<?php echo $_SERVER["PHP_SELF"]."?page=".$this->settings_page; ?>">

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

		<?php submit_button("Reload" ,"primary","ph_octavius_reload"); ?>

	</form>
</div>