<?php
/**
 * Octavius Cron for PALASTHOTEL Octavius service
 * 
 */
define( 'WP_MEMORY_LIMIT','1G' );

/**
 * require wordpress core
 */
define('WP_USE_THEMES', false);
$paths = explode( 'wp-content',__FILE__ );
require_once( $paths[0] . 'wp-load.php' );

require_once(dirname(__FILE__)."/classes/class-ph-octavius-curl.php");
require_once(dirname(__FILE__)."/classes/class-ph-octavius-store.php");

do_action("ph_octavius_cron_before");

$store = new PH_Octavius_Store();
print json_encode($store->get_data_from_remote(false));

do_action("ph_octavius_cron_after");


exit;

?>