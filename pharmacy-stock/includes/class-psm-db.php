<?php
/**
 * Database layer for Pharmacy Stock Manager.
 *
 * Every query that contains user input goes through $wpdb->prepare(),
 * WordPress's prepared-statement API (placeholders %s = string,
 * %d = integer, %f = float). Table names are built from the site's
 * own prefix, never from user input.
 *
 * @package pharmacy-stock
 */

defined( 'ABSPATH' ) || exit;

class PSM_DB {

	/** Days ahead that count as "expiring soon". */
	const EXPIRY_WINDOW = 90;

	/**
	 * Full table name, e.g. wp_psm_suppliers.
	 *
	 * @param string $name suppliers | medicines | stock_batches.
	 */
	public static function table( $name ) {
		global $wpdb;
		return $wpdb->prefix . 'psm_' . $name;
	}

	/* =================================================================
	 * Installation
	 * ================================================================= */

	/**
	 * Create the three tables (runs on plugin activation).
	 * InnoDB is required for the foreign keys.
	 */
	public static function install() {
		global $wpdb;

		$charset = $wpdb->get_charset_collate();
		$s       = self::table( 'suppliers' );
		$m       = self::table( 'medicines' );
		$b       = self::table( 'stock_batches' );

		$wpdb->query(
			"CREATE TABLE IF NOT EXISTS $s (
				id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				name           VARCHAR(150)    NOT NULL,
				contact_person VARCHAR(100)    DEFAULT NULL,
				phone          VARCHAR(30)     DEFAULT NULL,
				email          VARCHAR(100)    DEFAULT NULL,
				location       VARCHAR(150)    DEFAULT NULL,
				created_at     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id),
				UNIQUE KEY uq_supplier_name (name)
			) ENGINE=InnoDB $charset"
		);

		$wpdb->query(
			"CREATE TABLE IF NOT EXISTS $m (
				id                    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				supplier_id           BIGINT UNSIGNED NOT NULL,
				name                  VARCHAR(150)    NOT NULL,
				category              VARCHAR(60)     NOT NULL,
				form                  VARCHAR(30)     NOT NULL,
				unit_price            DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
				requires_prescription TINYINT(1)      NOT NULL DEFAULT 0,
				created_at            DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id),
				KEY idx_medicine_supplier (supplier_id),
				CONSTRAINT fk_psm_medicine_supplier
					FOREIGN KEY (supplier_id) REFERENCES $s (id)
					ON UPDATE CASCADE ON DELETE RESTRICT
			) ENGINE=InnoDB $charset"
		);

		$wpdb->query(
			"CREATE TABLE IF NOT EXISTS $b (
				id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				medicine_id BIGINT UNSIGNED NOT NULL,
				batch_no    VARCHAR(50)     NOT NULL,
				quantity    INT UNSIGNED    NOT NULL DEFAULT 0,
				expiry_date DATE            NOT NULL,
				received_on DATE            DEFAULT NULL,
				created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id),
				UNIQUE KEY uq_medicine_batch (medicine_id, batch_no),
				KEY idx_batch_expiry (expiry_date),
				CONSTRAINT fk_psm_batch_medicine
					FOREIGN KEY (medicine_id) REFERENCES $m (id)
					ON UPDATE CASCADE ON DELETE CASCADE
			) ENGINE=InnoDB $charset"
		);

		$sv = self::table( 'services' );
		$ap = self::table( 'appointments' );

		$wpdb->query(
			"CREATE TABLE IF NOT EXISTS $sv (
				id               BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
				name             VARCHAR(120)     NOT NULL,
				description      VARCHAR(255)     DEFAULT NULL,
				duration_minutes SMALLINT UNSIGNED NOT NULL DEFAULT 15,
				price            DECIMAL(10,2)    DEFAULT NULL,
				is_active        TINYINT(1)       NOT NULL DEFAULT 1,
				created_at       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id),
				UNIQUE KEY uq_service_name (name)
			) ENGINE=InnoDB $charset"
		);

		$wpdb->query(
			"CREATE TABLE IF NOT EXISTS $ap (
				id               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				service_id       BIGINT UNSIGNED NOT NULL,
				customer_name    VARCHAR(120)    NOT NULL,
				phone            VARCHAR(30)     NOT NULL,
				email            VARCHAR(120)    DEFAULT NULL,
				appointment_date DATE            NOT NULL,
				appointment_time TIME            NOT NULL,
				channel          VARCHAR(20)     NOT NULL DEFAULT 'website',
				status           VARCHAR(20)     NOT NULL DEFAULT 'pending',
				customer_note    VARCHAR(500)    DEFAULT NULL,
				staff_note       VARCHAR(500)    DEFAULT NULL,
				created_at       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id),
				KEY idx_appointment_when (appointment_date, appointment_time),
				KEY idx_appointment_status (status),
				KEY idx_appointment_service (service_id),
				CONSTRAINT fk_psm_appointment_service
					FOREIGN KEY (service_id) REFERENCES $sv (id)
					ON UPDATE CASCADE ON DELETE RESTRICT
			) ENGINE=InnoDB $charset"
		);

		// Starter services the pharmacy offers (editable under Pharmacy → Services).
		if ( 0 === (int) $wpdb->get_var( "SELECT COUNT(*) FROM $sv" ) ) {
			$starter = array(
				array( 'Pharmacist consultation', 'Advice on symptoms, medicines, side effects or a long-term condition.', 15 ),
				array( 'Blood pressure check', 'A quick reading with advice on what it means.', 10 ),
				array( 'Blood sugar test', 'Finger-prick glucose test with advice on the result.', 15 ),
				array( 'Malaria rapid test', 'Result in about 15 minutes, before you start treatment.', 20 ),
				array( 'Prescription refill collection', 'Collect a refill we have prepared for you.', 10 ),
				array( 'Mother and child advice', 'Pregnancy, breastfeeding, baby nutrition and child illness.', 20 ),
			);
			foreach ( $starter as $row ) {
				$wpdb->query(
					$wpdb->prepare(
						"INSERT INTO $sv (name, description, duration_minutes) VALUES (%s, %s, %d)",
						$row[0],
						$row[1],
						$row[2]
					)
				);
			}
		}

		self::install_roles();
		update_option( 'psm_db_version', PSM_VERSION );
	}

	/**
	 * Capability "psm_manage" opens the Pharmacy menu. Administrators get it,
	 * and the "Pharmacy Staff" role gets only that (least privilege).
	 */
	public static function install_roles() {
		$admin = get_role( 'administrator' );
		if ( $admin ) {
			$admin->add_cap( 'psm_manage' );
		}
		if ( ! get_role( 'pharmacy_staff' ) ) {
			add_role(
				'pharmacy_staff',
				'Pharmacy Staff',
				array(
					'read'       => true,
					'psm_manage' => true,
				)
			);
		}
	}

	/* =================================================================
	 * Suppliers
	 * ================================================================= */

	/** READ (list) — LEFT JOIN counts each supplier's medicines. */
	public static function get_suppliers() {
		global $wpdb;
		$s = self::table( 'suppliers' );
		$m = self::table( 'medicines' );

		return $wpdb->get_results(
			"SELECT s.*, COUNT(m.id) AS medicine_count
			   FROM $s AS s
			   LEFT JOIN $m AS m ON m.supplier_id = s.id
			  GROUP BY s.id
			  ORDER BY s.name ASC"
		);
	}

	/** READ (one). */
	public static function get_supplier( $id ) {
		global $wpdb;
		$s = self::table( 'suppliers' );
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $s WHERE id = %d", $id ) );
	}

	/** CREATE. Returns rows affected, or false on error. */
	public static function insert_supplier( array $d ) {
		global $wpdb;
		$s = self::table( 'suppliers' );
		return $wpdb->query(
			$wpdb->prepare(
				"INSERT INTO $s (name, contact_person, phone, email, location)
				 VALUES (%s, %s, %s, %s, %s)",
				$d['name'],
				$d['contact_person'],
				$d['phone'],
				$d['email'],
				$d['location']
			)
		);
	}

	/** UPDATE. */
	public static function update_supplier( $id, array $d ) {
		global $wpdb;
		$s = self::table( 'suppliers' );
		return $wpdb->query(
			$wpdb->prepare(
				"UPDATE $s
				    SET name = %s, contact_person = %s, phone = %s, email = %s, location = %s
				  WHERE id = %d",
				$d['name'],
				$d['contact_person'],
				$d['phone'],
				$d['email'],
				$d['location'],
				$id
			)
		);
	}

	/** DELETE. Fails (returns false) if the supplier still has medicines — ON DELETE RESTRICT. */
	public static function delete_supplier( $id ) {
		global $wpdb;
		$s = self::table( 'suppliers' );
		return $wpdb->query( $wpdb->prepare( "DELETE FROM $s WHERE id = %d", $id ) );
	}

	/* =================================================================
	 * Medicines
	 * ================================================================= */

	/**
	 * READ (list) — INNER JOIN to show the supplier's name, LEFT JOIN to
	 * total the stock held in batches that have not yet expired.
	 */
	public static function get_medicines() {
		global $wpdb;
		$s = self::table( 'suppliers' );
		$m = self::table( 'medicines' );
		$b = self::table( 'stock_batches' );

		return $wpdb->get_results(
			"SELECT m.*, s.name AS supplier_name,
			        COALESCE(SUM(CASE WHEN b.expiry_date >= CURDATE() THEN b.quantity END), 0) AS stock_in_date
			   FROM $m AS m
			  INNER JOIN $s AS s ON s.id = m.supplier_id
			   LEFT JOIN $b AS b ON b.medicine_id = m.id
			  GROUP BY m.id, s.name
			  ORDER BY m.name ASC"
		);
	}

	/** READ (one). */
	public static function get_medicine( $id ) {
		global $wpdb;
		$m = self::table( 'medicines' );
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $m WHERE id = %d", $id ) );
	}

	/** CREATE. */
	public static function insert_medicine( array $d ) {
		global $wpdb;
		$m = self::table( 'medicines' );
		return $wpdb->query(
			$wpdb->prepare(
				"INSERT INTO $m (supplier_id, name, category, form, unit_price, requires_prescription)
				 VALUES (%d, %s, %s, %s, %f, %d)",
				$d['supplier_id'],
				$d['name'],
				$d['category'],
				$d['form'],
				$d['unit_price'],
				$d['requires_prescription']
			)
		);
	}

	/** UPDATE. */
	public static function update_medicine( $id, array $d ) {
		global $wpdb;
		$m = self::table( 'medicines' );
		return $wpdb->query(
			$wpdb->prepare(
				"UPDATE $m
				    SET supplier_id = %d, name = %s, category = %s, form = %s,
				        unit_price = %f, requires_prescription = %d
				  WHERE id = %d",
				$d['supplier_id'],
				$d['name'],
				$d['category'],
				$d['form'],
				$d['unit_price'],
				$d['requires_prescription'],
				$id
			)
		);
	}

	/** DELETE. Its batches are removed too — ON DELETE CASCADE. */
	public static function delete_medicine( $id ) {
		global $wpdb;
		$m = self::table( 'medicines' );
		return $wpdb->query( $wpdb->prepare( "DELETE FROM $m WHERE id = %d", $id ) );
	}

	/* =================================================================
	 * Stock batches
	 * ================================================================= */

	/**
	 * READ (list) — joins all three tables so every batch row shows its
	 * medicine and supplier, plus days left until expiry.
	 *
	 * @param int|null $within_days Only batches expiring within this many days (includes expired).
	 */
	public static function get_batches( $within_days = null ) {
		global $wpdb;
		$s = self::table( 'suppliers' );
		$m = self::table( 'medicines' );
		$b = self::table( 'stock_batches' );

		$sql = "SELECT b.*, m.name AS medicine_name, m.form, m.requires_prescription,
		               s.name AS supplier_name,
		               DATEDIFF(b.expiry_date, CURDATE()) AS days_left
		          FROM $b AS b
		         INNER JOIN $m AS m ON m.id = b.medicine_id
		         INNER JOIN $s AS s ON s.id = m.supplier_id";

		if ( null !== $within_days ) {
			$sql = $wpdb->prepare(
				$sql . ' WHERE b.expiry_date <= DATE_ADD(CURDATE(), INTERVAL %d DAY)',
				$within_days
			);
		}

		return $wpdb->get_results( $sql . ' ORDER BY b.expiry_date ASC' );
	}

	/** READ (one). */
	public static function get_batch( $id ) {
		global $wpdb;
		$b = self::table( 'stock_batches' );
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $b WHERE id = %d", $id ) );
	}

	/** CREATE. */
	public static function insert_batch( array $d ) {
		global $wpdb;
		$b = self::table( 'stock_batches' );
		return $wpdb->query(
			$wpdb->prepare(
				"INSERT INTO $b (medicine_id, batch_no, quantity, expiry_date, received_on)
				 VALUES (%d, %s, %d, %s, NULLIF(%s, ''))",
				$d['medicine_id'],
				$d['batch_no'],
				$d['quantity'],
				$d['expiry_date'],
				$d['received_on']
			)
		);
	}

	/** UPDATE. */
	public static function update_batch( $id, array $d ) {
		global $wpdb;
		$b = self::table( 'stock_batches' );
		return $wpdb->query(
			$wpdb->prepare(
				"UPDATE $b
				    SET medicine_id = %d, batch_no = %s, quantity = %d,
				        expiry_date = %s, received_on = NULLIF(%s, '')
				  WHERE id = %d",
				$d['medicine_id'],
				$d['batch_no'],
				$d['quantity'],
				$d['expiry_date'],
				$d['received_on'],
				$id
			)
		);
	}

	/** DELETE. */
	public static function delete_batch( $id ) {
		global $wpdb;
		$b = self::table( 'stock_batches' );
		return $wpdb->query( $wpdb->prepare( "DELETE FROM $b WHERE id = %d", $id ) );
	}

	/* =================================================================
	 * Public medicine availability search (read-only)
	 * ================================================================= */

	/**
	 * Find medicines by name or category for the public website.
	 * Returns only what a customer needs: no quantities leave this method
	 * beyond the in-date total used to work out the availability label,
	 * and expired batches are never counted.
	 *
	 * @param string $term  What the customer typed.
	 * @param int    $limit Max results.
	 */
	public static function search_medicines( $term, $limit = 20 ) {
		global $wpdb;
		$m = self::table( 'medicines' );
		$b = self::table( 'stock_batches' );

		$like   = '%' . $wpdb->esc_like( $term ) . '%';
		$starts = $wpdb->esc_like( $term ) . '%';

		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT m.id, m.name, m.form, m.category, m.requires_prescription,
				        COALESCE(SUM(CASE WHEN b.expiry_date >= CURDATE() THEN b.quantity END), 0) AS in_date
				   FROM $m AS m
				   LEFT JOIN $b AS b ON b.medicine_id = m.id
				  WHERE m.name LIKE %s OR m.category LIKE %s
				  GROUP BY m.id
				  ORDER BY (m.name LIKE %s) DESC, m.name ASC
				  LIMIT %d",
				$like,
				$like,
				$starts,
				$limit
			)
		);
	}

	/* =================================================================
	 * Services (what customers can book)
	 * ================================================================= */

	/** READ (list) — LEFT JOIN counts appointments per service. */
	public static function get_services( $active_only = false ) {
		global $wpdb;
		$sv    = self::table( 'services' );
		$ap    = self::table( 'appointments' );
		$where = $active_only ? 'WHERE s.is_active = 1' : '';

		return $wpdb->get_results(
			"SELECT s.*, COUNT(a.id) AS appointment_count
			   FROM $sv AS s
			   LEFT JOIN $ap AS a ON a.service_id = s.id
			   $where
			  GROUP BY s.id
			  ORDER BY s.name ASC"
		);
	}

	public static function get_service( $id ) {
		global $wpdb;
		$sv = self::table( 'services' );
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $sv WHERE id = %d", $id ) );
	}

	public static function insert_service( array $d ) {
		global $wpdb;
		$sv = self::table( 'services' );
		return $wpdb->query(
			$wpdb->prepare(
				"INSERT INTO $sv (name, description, duration_minutes, price, is_active)
				 VALUES (%s, %s, %d, NULLIF(%s, ''), %d)",
				$d['name'],
				$d['description'],
				$d['duration_minutes'],
				$d['price'],
				$d['is_active']
			)
		);
	}

	public static function update_service( $id, array $d ) {
		global $wpdb;
		$sv = self::table( 'services' );
		return $wpdb->query(
			$wpdb->prepare(
				"UPDATE $sv
				    SET name = %s, description = %s, duration_minutes = %d,
				        price = NULLIF(%s, ''), is_active = %d
				  WHERE id = %d",
				$d['name'],
				$d['description'],
				$d['duration_minutes'],
				$d['price'],
				$d['is_active'],
				$id
			)
		);
	}

	/** DELETE. Refused by the database while appointments use it (ON DELETE RESTRICT). */
	public static function delete_service( $id ) {
		global $wpdb;
		$sv = self::table( 'services' );
		return $wpdb->query( $wpdb->prepare( "DELETE FROM $sv WHERE id = %d", $id ) );
	}

	/* =================================================================
	 * Appointments
	 * ================================================================= */

	/**
	 * READ (list) — INNER JOIN to show the service name and duration.
	 *
	 * @param string $view upcoming | today | pending | past | all
	 */
	public static function get_appointments( $view = 'upcoming' ) {
		global $wpdb;
		$ap = self::table( 'appointments' );
		$sv = self::table( 'services' );

		$today = current_time( 'Y-m-d' );
		switch ( $view ) {
			case 'today':
				$where = $wpdb->prepare( 'WHERE a.appointment_date = %s', $today );
				$order = 'a.appointment_time ASC';
				break;
			case 'pending':
				$where = "WHERE a.status = 'pending'";
				$order = 'a.appointment_date ASC, a.appointment_time ASC';
				break;
			case 'past':
				$where = $wpdb->prepare( 'WHERE a.appointment_date < %s', $today );
				$order = 'a.appointment_date DESC, a.appointment_time DESC';
				break;
			case 'all':
				$where = '';
				$order = 'a.appointment_date DESC, a.appointment_time DESC';
				break;
			default: // upcoming: today onwards, still open.
				$where = $wpdb->prepare( "WHERE a.appointment_date >= %s AND a.status IN ('pending','confirmed')", $today );
				$order = 'a.appointment_date ASC, a.appointment_time ASC';
		}

		return $wpdb->get_results(
			"SELECT a.*, s.name AS service_name, s.duration_minutes
			   FROM $ap AS a
			  INNER JOIN $sv AS s ON s.id = a.service_id
			   $where
			  ORDER BY $order
			  LIMIT 300"
		);
	}

	/** READ (one), with the service name. */
	public static function get_appointment( $id ) {
		global $wpdb;
		$ap = self::table( 'appointments' );
		$sv = self::table( 'services' );
		return $wpdb->get_row(
			$wpdb->prepare(
				"SELECT a.*, s.name AS service_name
				   FROM $ap AS a
				  INNER JOIN $sv AS s ON s.id = a.service_id
				  WHERE a.id = %d",
				$id
			)
		);
	}

	/** CREATE. Returns the new ID, or false. */
	public static function insert_appointment( array $d ) {
		global $wpdb;
		$ap = self::table( 'appointments' );
		$ok = $wpdb->query(
			$wpdb->prepare(
				"INSERT INTO $ap (service_id, customer_name, phone, email, appointment_date, appointment_time,
				                  channel, status, customer_note, staff_note)
				 VALUES (%d, %s, %s, NULLIF(%s, ''), %s, %s, %s, %s, NULLIF(%s, ''), NULLIF(%s, ''))",
				$d['service_id'],
				$d['customer_name'],
				$d['phone'],
				$d['email'],
				$d['appointment_date'],
				$d['appointment_time'],
				$d['channel'],
				$d['status'],
				$d['customer_note'],
				$d['staff_note']
			)
		);
		return $ok ? (int) $wpdb->insert_id : false;
	}

	/** UPDATE. */
	public static function update_appointment( $id, array $d ) {
		global $wpdb;
		$ap = self::table( 'appointments' );
		return $wpdb->query(
			$wpdb->prepare(
				"UPDATE $ap
				    SET service_id = %d, customer_name = %s, phone = %s, email = NULLIF(%s, ''),
				        appointment_date = %s, appointment_time = %s, channel = %s, status = %s,
				        customer_note = NULLIF(%s, ''), staff_note = NULLIF(%s, '')
				  WHERE id = %d",
				$d['service_id'],
				$d['customer_name'],
				$d['phone'],
				$d['email'],
				$d['appointment_date'],
				$d['appointment_time'],
				$d['channel'],
				$d['status'],
				$d['customer_note'],
				$d['staff_note'],
				$id
			)
		);
	}

	/** UPDATE (status only). */
	public static function set_appointment_status( $id, $status ) {
		global $wpdb;
		$ap = self::table( 'appointments' );
		return $wpdb->query( $wpdb->prepare( "UPDATE $ap SET status = %s WHERE id = %d", $status, $id ) );
	}

	/** DELETE. */
	public static function delete_appointment( $id ) {
		global $wpdb;
		$ap = self::table( 'appointments' );
		return $wpdb->query( $wpdb->prepare( "DELETE FROM $ap WHERE id = %d", $id ) );
	}

	/** How many open bookings already hold a time slot (double-booking check). */
	public static function count_slot( $date, $time, $exclude_id = 0 ) {
		global $wpdb;
		$ap = self::table( 'appointments' );
		return (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM $ap
				  WHERE appointment_date = %s AND appointment_time = %s
				    AND status IN ('pending','confirmed') AND id <> %d",
				$date,
				$time,
				$exclude_id
			)
		);
	}

	/** Delete finished (completed/cancelled) appointments older than N days. Returns rows removed. */
	public static function purge_appointments( $days ) {
		global $wpdb;
		$ap = self::table( 'appointments' );
		return (int) $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM $ap
				  WHERE status IN ('completed','cancelled')
				    AND appointment_date < DATE_SUB(%s, INTERVAL %d DAY)",
				current_time( 'Y-m-d' ),
				$days
			)
		);
	}

	public static function get_appointment_stats() {
		global $wpdb;
		$ap    = self::table( 'appointments' );
		$today = current_time( 'Y-m-d' );
		return $wpdb->get_row(
			$wpdb->prepare(
				"SELECT
					(SELECT COUNT(*) FROM $ap WHERE appointment_date = %s AND status IN ('pending','confirmed')) AS today,
					(SELECT COUNT(*) FROM $ap WHERE status = 'pending') AS pending,
					(SELECT COUNT(*) FROM $ap WHERE appointment_date >= %s AND status IN ('pending','confirmed')) AS upcoming",
				$today,
				$today
			)
		);
	}

	/* =================================================================
	 * Dashboard figures
	 * ================================================================= */

	public static function get_stats() {
		global $wpdb;
		$m = self::table( 'medicines' );
		$b = self::table( 'stock_batches' );

		return $wpdb->get_row(
			$wpdb->prepare(
				"SELECT
					(SELECT COUNT(*) FROM $m) AS medicines,
					(SELECT COALESCE(SUM(quantity), 0) FROM $b WHERE expiry_date >= CURDATE()) AS units,
					(SELECT COUNT(*) FROM $b
					  WHERE expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL %d DAY)) AS expiring,
					(SELECT COUNT(*) FROM $b WHERE expiry_date < CURDATE()) AS expired",
				self::EXPIRY_WINDOW
			)
		);
	}
}
