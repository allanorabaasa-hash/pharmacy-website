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

		update_option( 'psm_db_version', PSM_VERSION );
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
