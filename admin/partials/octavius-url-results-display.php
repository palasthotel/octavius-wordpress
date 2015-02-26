<?php

/**
 * template file for Octavius URL Check page
 */

?>

<div class="wrap octavius-url-results">

	<h2>URLs <?php echo $type; ?> in postmeta "<?php echo $meta_key; ?>" <a href="<?php echo $base_url; ?>" class="add-new-h2">Zurück</a></h2>

	<ul>
		<?php
		foreach ($elements as $element) {
			echo "<li><a href='".$element->url."' target='_new'>".$element->url."</a></li>";
		};
		?>
	</ul>

	<div class="tablenav">
		<div class="tablenav-pages octavius-pages-nav">
			<span class="displaying-num"><?php echo $overall; ?> Elemente</span>
			<span class="pagination-links">
				<a class="first-page"  href="<?php echo $paged_url."1"; ?>">«</a>
				<a class="prev-page" title="Zur vorherigen Seite gehen" href="<?php echo $paged_url.$prev_page; ?>">‹</a>
				<span class="paging-input"><?php echo $paged; ?> von <span class="total-pages"><?php echo $pages; ?></span></span>
				<a class="next-page" title="Zur nächsten Seite gehen" href="<?php echo $paged_url.$next_page; ?>">›</a>
				<a class="last-page" title="Zur letzten Seite gehen" href="<?php echo $paged_url.$pages ?>">»</a>
			</span>
		</div>
	</div>

</div>