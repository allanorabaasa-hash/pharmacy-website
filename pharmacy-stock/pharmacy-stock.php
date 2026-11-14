<?php
/**
 * Plugin Name:       God's Care Pharmacy Manager
 * Plugin URI:        https://github.com/allanorabaasa-hash/pharmacy-website
 * Description:       Custom module for God's Care Pharmacy: stock (suppliers, medicines, batches with expiry tracking), a public medicine availability search, and appointment booking with email notifications. Full CRUD using prepared statements.
 * Version:           1.1.0
 * Author:            Allan
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * License:           GPL-2.0-or-later
 * Text Domain:       pharmacy-stock
 *
 * @package pharmacy-stock
 */

defined( 'ABSPATH' ) || exit;

define( 'PSM_VERSION', '1.1.0' );
define( 'PSM_PATH', plugin_dir_path( __FILE__ ) );
define( 'PSM_URL', plugin_dir_url( __FILE__ ) );

require_once PSM_PATH . 'includes/class-psm-db.php';
require_once PSM_PATH . 'includes/class-psm-admin.php';
require_once PSM_PATH . 'includes/class-psm-admin-appointments.php';
require_once PSM_PATH . 'includes/class-psm-public.php';

/**
 * Module settings, with God's Care Pharmacy defaults.
 * Editable under Pharmacy → Settings.
 *
 * @param string $key notify_email | whatsapp | slot_capacity | bookings_open
 */
function psm_setting( $key ) {
	$defaults = array(
		'notify_email'  => 'orallan19@gmail.com',
		'whatsapp'      => '256745546101',
		'slot_capacity' => 1,
		'bookings_open' => 1,
	);
	$saved = get_option( 'psm_settings', array() );
	$all   = wp_parse_args( is_array( $saved ) ? $saved : array(), $defaults );
	return isset( $all[ $key ] ) ? $all[ $key ] : null;
}

/**
 * Opening hours used to offer appointment times (24h, pharmacy time zone).
 * The theme can supply its own hours through the "psm_opening_hours" filter.
 */
function psm_opening_hours() {
	return apply_filters(
		'psm_opening_hours',
		array(
			'mon' => array( '08:00', '21:00' ),
			'tue' => array( '08:00', '21:00' ),
			'wed' => array( '08:00', '21:00' ),
			'thu' => array( '08:00', '21:00' ),
			'fri' => array( '08:00', '21:00' ),
			'sat' => array( '08:00', '21:00' ),
			'sun' => array( '10:00', '18:00' ),
		)
	);
}

/**
 * Turn a Ugandan phone number into WhatsApp's international format.
 * 0745 546 101 -> 256745546101
 */
function psm_whatsapp_number( $phone ) {
	$digits = preg_replace( '/\D/', '', (string) $phone );
	if ( 10 === strlen( $digits ) && '0' === $digits[0] ) {
		$digits = '256' . substr( $digits, 1 );
	}
	return $digits;
}

/** Booking reference shown to customers, e.g. GC-0007. */
function psm_reference( $id ) {
	return 'GC-' . str_pad( (string) (int) $id, 4, '0', STR_PAD_LEFT );
}

// Create the tables (and the staff role) when the plugin is activated.
register_activation_hook( __FILE__, array( 'PSM_DB', 'install' ) );

// Upgrade sites that activated an older version: adds the new tables and role.
add_action(
	'plugins_loaded',
	function () {
		if ( get_option( 'psm_db_version' ) !== PSM_VERSION ) {
			PSM_DB::install();
		}
	}
);

// Public shortcodes: [psm_medicine_search] and [psm_booking_form].
PSM_Public::init();

// Admin screens.
add_action(
	'plugins_loaded',
	function () {
		if ( is_admin() ) {
			new PSM_Admin();
			new PSM_Admin_Appointments();
		}
	}
);
