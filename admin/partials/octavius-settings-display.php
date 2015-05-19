<?php

/**
 * template file for Octavius Settings Page
 *
 * $submit_button_text		Text for submit button
 * $submit_button 			Submit button identifier
 */

?>

<div class="wrap">
	<h2>Octavius Settings</h2>
	<form method="post" action="<?php echo $_SERVER["PHP_SELF"]."?page=".$this->settings_page; ?>">

		<table class="form-table">
			<tr>
				<th scope="row"><label for="ph_octavius_client">Client</label></th>
				<td><input type="text" id="ph_octavius_client" name="ph_octavius_client" value="<?php echo $options->client; ?>" class="regular-text" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="ph_octavius_pw">Passwort</label></th>
				<td><input type="text" id="ph_octavius_pw" name="ph_octavius_pw" value="<?php echo $options->pw; ?>" class="regular-text" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="ph_octavius_doimain">Domain</label></th>
				<td><input type="text" id="ph_octavius_domain" name="ph_octavius_domain" value="<?php echo $options->domain; ?>" class="regular-text" /></td>
			</tr>
		</table>

		<?php submit_button($submit_button_text ,"primary",$submit_button); ?>

	</form>
</div>