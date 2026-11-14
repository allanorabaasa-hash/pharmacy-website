# God's Care Pharmacy — Website

Practical exam project for **BIT 2103 Internet Technologies & Advanced Website Development**,
Mountains of the Moon University (BCS, Year 2, Semester I 2026/27).

- **Client:** God's Care Pharmacy
- **Developer:** Allan
- **Platform:** WordPress (CMS) + WooCommerce, with a custom child theme and a custom plugin
- **Local environment:** Kali Linux, Apache, MariaDB/MySQL, PHP (`/var/www/html/mmu`)
- **Live server:** cPanel hosting on the project's own domain (to be added)

## Why WordPress

| Client requirement | How WordPress meets it |
|---|---|
| Staff are not technical and must update products, prices and posts themselves | Visual block editor, WooCommerce product screens, role-based logins |
| Sell over-the-counter products online | WooCommerce (pay on delivery / manual mobile money; test-mode payments) |
| Track stock, suppliers and expiry dates | Custom plugin `pharmacy-stock` with its own MySQL tables |
| Fast, secure, findable on Google | Mature caching, security and SEO plugins; automatic updates |
| Low hosting cost | Runs on any standard cPanel host |

## Repository layout

```
pharmacy-website/
├── pharmacy-child/          Child theme of Twenty Twenty-Five (customisation layer)
│   ├── style.css            Theme header + custom CSS
│   ├── functions.php        Enqueues assets, opening-hours shortcode, WhatsApp button
│   ├── theme.json           Brand colours, fonts and layout
│   ├── patterns/hero.php    Homepage hero block pattern
│   └── assets/js/open-status.js
├── pharmacy-stock/          Custom plugin: Pharmacy Stock Manager (CRUD module)
│   ├── pharmacy-stock.php   Plugin bootstrap + activation hook
│   ├── includes/class-psm-db.php     All SQL (prepared statements)
│   ├── includes/class-psm-admin.php  wp-admin screens and form handlers
│   ├── assets/admin.css
│   └── uninstall.php        Drops the plugin's tables on uninstall
└── README.md
```

WordPress core, uploads and third-party plugins are **not** kept in Git. Only custom code is versioned.

## Custom module: God's Care Pharmacy Manager (`pharmacy-stock/`)

One plugin, five related MySQL tables (prefix `wp_psm_`):

```
suppliers (id, name, contact_person, phone, email, location)
    1 ──< medicines (id, supplier_id → suppliers.id, name, category, form,
                     unit_price, requires_prescription)
              1 ──< stock_batches (id, medicine_id → medicines.id, batch_no,
                                   quantity, expiry_date, received_on)

services (id, name, description, duration_minutes, price, is_active)
    1 ──< appointments (id, service_id → services.id, customer_name, phone, email,
                        appointment_date, appointment_time, channel, status,
                        customer_note, staff_note)
```

| Who | What they do with the database |
|---|---|
| Owner (Administrator) | Everything, including Settings |
| Pharmacy Staff (custom role) | Stock, appointments and services only (capability `psm_manage`) |
| Customers (website) | **Read** stock through the availability search; **create** appointment requests |
| Examiner | Temporary administrator account, deleted after marking |

**What it does**

- **Stock:** suppliers, medicines and batches with full CRUD; overview of batches expiring within 90 days and expired batches.
- **Medicine availability search** (`[psm_medicine_search]`): customers see *In stock*, *Few left*, *Out of stock* or *Prescription required*. Quantities, suppliers and batch numbers are never shown; expired batches are ignored.
- **Appointments** (`[psm_booking_form]`): customers request a service and a time slot within opening hours. The request is saved, the pharmacy is emailed (default `orallan19@gmail.com`, editable in Settings) and the customer gets a confirmation email. Staff confirm, complete, cancel, edit or delete; confirming or cancelling emails the customer. Bookings received on WhatsApp, phone, email or walk-in are added by staff, so every appointment is in one list.

**JOINs used:** batch list (batches + medicines + suppliers), medicine list (medicines + suppliers + summed in-date batches), public search (medicines + batches), appointment list (appointments + services), service and supplier lists (LEFT JOIN counts).

**Security:** every query with user input uses `$wpdb->prepare()`; capability and nonce checks on every admin action; sanitised input and escaped output; foreign keys protect related data; the booking form has a nonce, a hidden spam trap, a per-visitor rate limit, slot double-booking checks and a consent checkbox. Old finished appointments can be purged from Settings (privacy).

## Local setup (Kali)

```bash
# one-time
sudo mkdir -p /opt/pharmacy-website && sudo chown $USER:$USER /opt/pharmacy-website
git clone https://github.com/allanorabaasa-hash/pharmacy-website.git /opt/pharmacy-website
WP=/var/www/html/mmu/wp-content
sudo ln -s /opt/pharmacy-website/pharmacy-child $WP/themes/pharmacy-child
sudo ln -s /opt/pharmacy-website/pharmacy-stock $WP/plugins/pharmacy-stock

# every update
cd /opt/pharmacy-website && git pull
```

Then in wp-admin: **Appearance → Themes → activate "God's Care Pharmacy"** (needs the
Twenty Twenty-Five parent theme installed) and **Plugins → activate "Pharmacy Stock Manager"**.

## Git workflow

- `main` holds working, tested code.
- New features are built on `feature/*` branches and merged through pull requests.

## Status

- [x] Child theme with brand colours
- [x] Stock manager plugin (CRUD, JOINs, expiry report)
- [x] Public medicine availability search
- [x] Appointment booking with email notifications and a Pharmacy Staff role
- [ ] Real client content and photos
- [x] Prescription refill page (WhatsApp)
- [ ] WooCommerce shop (optional: the booking system already covers the business-driven feature)
- [ ] Deployment to cPanel, SSL, hardening, SEO, speed tests
