<?php
/**
 * Runs only when the plugin is deleted from Plugins → Delete.
 * Drops the module's tables (child tables first, because of the foreign keys),
 * removes the Pharmacy Staff role and the module's settings.
 *
 * @package pharmacy-stock
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

global $wpdb;

foreach ( array( 'appointments', 'services', 'stock_batches', 'medicines', 'suppliers' ) as $name ) {
	$table = $wpdb->prefix . 'psm_' . $name;
	$wpdb->query( "DROP TABLE IF EXISTS $table" ); // Table name is built from the prefix, not user input.
}

delete_option( 'psm_db_version' );
delete_option( 'psm_settings' );

remove_role( 'pharmacy_staff' );
$admin = get_role( 'administrator' );
if ( $admin ) {
	$admin->remove_cap( 'psm_manage' );
}
