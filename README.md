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

## Custom module: Pharmacy Stock Manager

Three related tables (prefix `wp_psm_`):

```
suppliers (id, name, contact_person, phone, email, location, created_at)
    1 ──< medicines (id, supplier_id → suppliers.id, name, category, form,
                     unit_price, requires_prescription, created_at)
              1 ──< stock_batches (id, medicine_id → medicines.id, batch_no,
                                   quantity, expiry_date, received_on, created_at)
```

- Foreign keys: a supplier cannot be deleted while it still has medicines (`ON DELETE RESTRICT`);
  deleting a medicine removes its batches (`ON DELETE CASCADE`).
- **JOINs:** the batch list joins all three tables (medicine + supplier per batch); the medicine
  list joins suppliers and sums stock; the supplier list counts medicines with a `LEFT JOIN`.
- **Expiry report:** batches expiring within 90 days, with days remaining.
- **Full CRUD** for every table, written with `$wpdb->prepare()` (WordPress's prepared statements),
  protected by nonces and capability checks, with input sanitised and output escaped.

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
- [ ] Real client content and photos
- [ ] WooCommerce shop + prescription refill requests
- [ ] Deployment to cPanel, SSL, hardening, SEO, speed tests
