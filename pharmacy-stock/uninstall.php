<?php
/**
 * Runs only when the plugin is deleted from Plugins → Delete.
 * Drops the module's tables (child tables first, because of the foreign keys).
 *
 * @package pharmacy-stock
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

global $wpdb;

foreach ( array( 'stock_batches', 'medicines', 'suppliers' ) as $name ) {
	$table = $wpdb->prefix . 'psm_' . $name;
	$wpdb->query( "DROP TABLE IF EXISTS $table" ); // Table name is built from the prefix, not user input.
}

delete_option( 'psm_db_version' );
