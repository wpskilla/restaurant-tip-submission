```md
# Restaurant Tip Submission

This plugin adds a lightweight, AJAX-based form to WordPress that allows visitors to submit restaurant tips.
Each submission is saved as a custom post type (`restaurant_tip`) in **draft status**, so site editors can review and decide what gets published.

---

## Installation

1. Copy the plugin folder into:
```

wp-content/plugins/

```

2. Activate it from the WordPress admin panel:
**Plugins → Installed Plugins**

3. Add the form anywhere you want:

**Using shortcode:**
```

[restaurant_tip_form]

````

**Or directly in a template:**
```php
<?php rts_render_tip_form(); ?>
````

You can also check:

```
templates/template-part-restaurant-tip-form.php
```

for a ready-to-use example.

---

## How it works

The plugin is split into a few small modules:

```
restaurant-tip-submission/
├── restaurant-tip-submission.php   Main plugin bootstrap file
├── includes/
│   ├── class-rts-cpt.php           Registers custom post type + meta fields
│   ├── class-rts-form.php          Handles form rendering (shortcode + helpers)
│   ├── class-rts-ajax.php          Processes AJAX requests (validation + saving)
│   └── class-rts-assets.php        Loads CSS/JS and passes AJAX data to JS
├── assets/
│   ├── css/rts-form.css            Front-end styling (responsive + accessible)
│   └── js/rts-form.js              AJAX submission using fetch()
└── templates/
    └── template-part-restaurant-tip-form.php
```

---

## Why admin-ajax.php?

We use WordPress’s built-in `admin-ajax.php` system because it keeps things simple and reliable for this use case.

**Why this approach works well here:**

* No extra routing setup needed
* Built-in nonce support
* Perfect for single-form submissions
* Fully compatible with WordPress core

REST API would also work, but it adds extra structure that isn’t necessary for a single front-end form.

---

## Security overview

The plugin follows standard WordPress security practices:

* **Nonce protection** to prevent CSRF attacks
* **Input sanitization** using WordPress helper functions
* **Server-side validation** for all submitted fields
* **Escaped output** in templates to prevent XSS
* **Draft-only storage** so submissions never appear publicly

---

## Front-end behavior

* Uses `fetch()` instead of jQuery
* Fully responsive layout
* Shows loading state during submission
* Inline error messages for each field
* Accessible form structure with proper labels and ARIA support
* Works for logged-in and guest users

---

## Where submissions go

All tips are stored as:

* Post Type: `restaurant_tip`
* Status: `draft`

You can view them in:
**WP Admin → Restaurant Tips**

Each entry includes:

* Restaurant name
* Submitter name
* Email (stored as post meta)
* Tip message

---

## Extending the plugin

A few common enhancements you might add:

**Send email notifications**

```php
wp_mail(...)
```

inside the AJAX handler after saving the post.

**Switch to REST API**
Replace AJAX with:

```
register_rest_route('rts/v1', '/tips', ...)
```

**Gutenberg block support**
Wrap the form in a `render_callback` block for easier editor integration.

---

## License

GPL-2.0-or-later