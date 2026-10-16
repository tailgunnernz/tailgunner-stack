<?php
/**
 * Default values for the stack's own (tg_*) settings, and saving them.
 *
 * Defaults are written to each site's database the first time the plugin runs,
 * and only where nothing is saved yet. Changing a default here in a later
 * release therefore only affects sites that don't have that setting yet. The
 * Starter (x_*) settings the stack reuses already come with every Pro Starter
 * clone, so they aren't seeded.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Base (desktop) values. Typography matches Pro Starter's Global CSS as of
 * October 2026.
 */
function tg_stack_default_values() {
	return [
		'tg_body_line_height'     => '1.8',
		'tg_headings_line_height' => '1.1',

		'tg_h0_font_size' => '550%',
		'tg_h1_font_size' => '400%',
		'tg_h2_font_size' => '230%',
		'tg_h3_font_size' => '175%',
		'tg_h4_font_size' => '140%',
		'tg_h5_font_size' => '120%',
		'tg_h6_font_size' => '110%',

		'tg_cf7_button_bg'     => 'global-color:hD7eHJdz5b9ddZgsyo',
		'tg_cf7_button_text'   => '#ffffff',
		'tg_cf7_button_radius' => '3px',
		'tg_cf7_input_border'  => '#dddddd',
		'tg_cf7_input_radius'  => '0px',
	];
}

/**
 * Heading sizes below 767px, previously the @media (max-width: 766px) block in
 * Pro Starter's Global CSS.
 */
function tg_stack_mobile_defaults() {
	return [
		'tg_h0_font_size' => '240%',
		'tg_h1_font_size' => '200%',
		'tg_h2_font_size' => '160%',
		'tg_h3_font_size' => '140%',
		'tg_h4_font_size' => '120%',
		'tg_h5_font_size' => '110%',
		'tg_h6_font_size' => '105%',
	];
}

/**
 * Settings that get per-breakpoint values in Theme Options, with the unit
 * appended when a bare number is entered.
 */
function tg_stack_breakpoint_keys() {
	$keys = [];

	foreach ( array_keys( tg_stack_mobile_defaults() ) as $key ) {
		$keys[ $key ] = [ 'unit' => '%' ];
	}

	return $keys;
}

function tg_stack_seed_defaults() {
	global $wpdb;

	foreach ( tg_stack_default_values() as $key => $value ) {
		// Checked against the table directly: Cornerstone registers in-memory
		// defaults for these keys, so get_option() can't tell "unset" apart.
		$exists = $wpdb->get_var( $wpdb->prepare(
			"SELECT 1 FROM {$wpdb->options} WHERE option_name = %s LIMIT 1",
			$key
		) );

		if ( ! $exists ) {
			update_option( $key, $value );
		}
	}

	tg_stack_seed_breakpoint_defaults();

	update_option( 'tg_stack_seeded_version', TG_STACK_VERSION );
}

/**
 * Per-breakpoint values live in the cs_option_data option, in a bucket named
 * after the breakpoint setup (e.g. _bp_data4_4 for base 4 of 4 ranges), as one
 * value per range slot. Each mobile default goes in the slot that covers
 * 766px, so it applies from there down.
 */
function tg_stack_seed_breakpoint_defaults() {
	if ( ! function_exists( 'cornerstone' ) ) {
		return;
	}

	list( $base, $ranges, $size ) = cornerstone( 'Breakpoints' )->breakpointConfig();

	$data = get_option( 'cs_option_data', [] );
	$data = is_array( $data ) ? $data : [];

	if ( empty( $data['_bp_base'] ) ) {
		$data['_bp_base'] = $base . '_' . $size;
	}

	// Saved for a different breakpoint setup: the builder converts that on
	// load, so leave it rather than guess.
	if ( $data['_bp_base'] !== $base . '_' . $size ) {
		return;
	}

	$slot = 0;
	foreach ( $ranges as $index => $min_width ) {
		if ( $min_width < 767 ) {
			$slot = $index;
		}
	}

	if ( $slot >= $base ) {
		return;
	}

	$bucket_key = '_bp_data' . $data['_bp_base'];
	$bucket     = isset( $data[ $bucket_key ] ) && is_array( $data[ $bucket_key ] ) ? $data[ $bucket_key ] : [];
	$changed    = false;

	foreach ( tg_stack_mobile_defaults() as $key => $value ) {
		if ( isset( $bucket[ $key ] ) ) {
			continue;
		}

		$values          = array_fill( 0, count( $ranges ), null );
		$values[ $slot ] = $value;
		$bucket[ $key ]  = $values;
		$changed         = true;
	}

	if ( $changed ) {
		$data[ $bucket_key ] = $bucket;
		update_option( 'cs_option_data', $data );
	}
}

function tg_stack_activate() {
	if ( ! tg_stack_requirements_met() ) {
		return;
	}

	tg_stack_seed_defaults();
	update_option( 'x_stack', TG_STACK_ID );
}

/**
 * Starter reads the same x_* settings, so widths, fonts and colours survive.
 * Only the tg_* extras (heading sizes, line-heights, CF7 styling) drop off.
 */
function tg_stack_deactivate() {
	if ( TG_STACK_ID === get_option( 'x_stack' ) ) {
		update_option( 'x_stack', 'starter' );
	}
}
