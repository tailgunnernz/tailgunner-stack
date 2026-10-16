# Tailgunner Stack

Tailgunner's default stack for the Pro theme (6.9+). It replaces Pro Starter's Global CSS and child theme code with one plugin that updates itself from GitHub releases.

## What it does

- **Tailgunner stack** (Theme Options > Stack). It extends Blank, so none of Starter's CSS loads, and reuses Starter's `x_*` settings where they exist:
  - **Layout:** site width, max width, background colour.
  - **Typography:** root font size, body and heading font/weight/colour, line-heights, h1–h6 letter spacing, h0–h6 sizes (per breakpoint), link colours.
  - **CF7 Forms:** button and input styling. Shown and output only while Contact Form 7 is active.
  - **Fixed CSS:** `overflow-x: clip`, underlined content links, `small` at 0.64em.
- **Defaults** (`includes/defaults.php`) are saved to the site the first time the plugin runs, only where nothing is saved yet. Changing a default in a later release only affects sites that don't have that setting.
- **Activation** switches the site to the Tailgunner stack. **Deactivation** switches it back to Starter, which reads the same `x_*` settings.
- **Cornerstone Forms:**
  - Turnstile keys come from the Simple Cloudflare Turnstile plugin when it's active and has keys. Otherwise Cornerstone > Settings > Forms is used. With no keys anywhere, Turnstile elements render nothing and skip their check, so forms still submit (the honeypot still applies).
  - The all-fields email table (`{{dc:form:data_table}}`) shows input labels and leaves out fields starting with `cs_` or `_` (honeypot, nonces).
  - `{{dc:tg:admin_email}}` outputs the site's admin email (the contact form component's default recipient).
- **Front-end tweaks** (`includes/tweaks.php`): remove the WP version tag, dequeue block library CSS outside posts, remove global styles, passive jQuery listeners, stop Pro loading jQuery everywhere, turn off Themeco's custom fields map, eager-load `x-no-lazy` images. Turn one off for a site from its child theme:

  ```php
  add_filter( 'tg_stack_tweak_disable_jquery_everywhere', '__return_false' );
  ```

## Releasing

Every release auto-installs on every site, so test on Pro Starter first.

1. Bump `Version:` and `TG_STACK_VERSION` in `tailgunner-stack.php`.
2. Push to `main`.
3. Publish a GitHub release tagged with the same version (`v1.2.0`). The workflow builds `tailgunner-stack.zip` and attaches it.

Mark a release as a pre-release to hold it back.
