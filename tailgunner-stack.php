<?php
/**
 * Plugin Name:       Tailgunner Stack
 * Plugin URI:        https://github.com/tailgunnernz/tailgunner-stack
 * Description:       Tailgunner's default stack for the Pro theme: lean layout and typography settings, CF7 and Cornerstone Forms helpers, and the usual front-end tweaks.
 * Version:           1.0.1
 * Requires at least: 6.5
 * Requires PHP:      8.1
 * Author:            Tailgunner
 * Author URI:        https://tailgunner.co.nz
 * License:           GPL-2.0-or-later
 * Text Domain:       tailgunner-stack
 */

defined( 'ABSPATH' ) || exit;

define( 'TG_STACK_VERSION', '1.0.1' );
define( 'TG_STACK_FILE', __FILE__ );
define( 'TG_STACK_DIR', plugin_dir_path( __FILE__ ) );
define( 'TG_STACK_ID', 'tailgunner' );
define( 'TG_STACK_MIN_PRO', '6.9' );

require_once TG_STACK_DIR . 'includes/requirements.php';
require_once TG_STACK_DIR . 'includes/defaults.php';
require_once TG_STACK_DIR . 'includes/updater.php';

register_activation_hook( __FILE__, 'tg_stack_activate' );
register_deactivation_hook( __FILE__, 'tg_stack_deactivate' );

/**
 * Pro defines X_VERSION while the theme loads, so the version check and every
 * hook that depends on Pro wait for after_setup_theme. Priority -100 keeps us
 * ahead of Pro registering its stacks (0) and Cornerstone Forms booting (10).
 * The updater stays outside this gate so a fix can still reach a site that
 * fails the check.
 */
add_action( 'after_setup_theme', function () {
	if ( ! tg_stack_requirements_met() ) {
		add_action( 'admin_notices', 'tg_stack_requirements_notice' );
		return;
	}

	require_once TG_STACK_DIR . 'includes/stack.php';
	require_once TG_STACK_DIR . 'includes/cf7.php';
	require_once TG_STACK_DIR . 'includes/cornerstone-forms.php';
	require_once TG_STACK_DIR . 'includes/tweaks.php';

	// Sites cloned from Pro Starter, and sites updated to a release that adds
	// settings, get any missing defaults saved without needing a reactivation.
	add_action( 'init', function () {
		if ( get_option( 'tg_stack_seeded_version' ) !== TG_STACK_VERSION ) {
			tg_stack_seed_defaults();
		}
	} );
}, -100 );
