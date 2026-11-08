<?php
/**
 * Plugin Name:       Pharmacy Stock Manager
 * Plugin URI:        https://github.com/allanorabaasa-hash/pharmacy-website
 * Description:       Custom stock module for God's Care Pharmacy: suppliers, medicines and stock batches with expiry tracking. Full CRUD using prepared statements.
 * Version:           1.0.0
 * Author:            Allan
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * License:           GPL-2.0-or-later
 * Text Domain:       pharmacy-stock
 *
 * @package pharmacy-stock
 */

defined( 'ABSPATH' ) || exit;

define( 'PSM_VERSION', '1.0.0' );
define( 'PSM_PATH', plugin_dir_path( __FILE__ ) );
define( 'PSM_URL', plugin_dir_url( __FILE__ ) );

require_once PSM_PATH . 'includes/class-psm-db.php';
require_once PSM_PATH . 'includes/class-psm-admin.php';

// Create the database tables when the plugin is activated.
register_activation_hook( __FILE__, array( 'PSM_DB', 'install' ) );

// Load the admin screens.
add_action(
	'plugins_loaded',
	function () {
		if ( is_admin() ) {
			new PSM_Admin();
		}
	}
);
