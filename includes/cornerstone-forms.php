<?php
/**
 * Cornerstone Forms helpers:
 * - Turnstile keys come from the Simple Cloudflare Turnstile plugin when it's
 *   active, so keys are entered once and also cover login and comments.
 * - The all-fields email table ({{dc:form:data_table}}) shows input labels and
 *   leaves out system fields such as the honeypot.
 */

defined( 'ABSPATH' ) || exit;

// {{dc:tg:admin_email}}: the site's admin email. The contact form component's
// recipient defaults to it, so each site's form follows its own settings.
add_filter( 'cs_dynamic_content_tg', function ( $result, $field ) {
	return 'admin_email' === $field ? get_option( 'admin_email' ) : $result;
}, 10, 2 );

/**
 * Keys from Simple Cloudflare Turnstile (its settings, or the
 * CF_TURNSTILE_SITE_KEY / CF_TURNSTILE_SECRET_KEY constants). Null when the
 * plugin is inactive or either key is missing.
 */
function tg_stack_turnstile_plugin_keys() {
	if ( ! function_exists( 'cfturnstile_check' ) ) {
		return null;
	}

	$site   = defined( 'CF_TURNSTILE_SITE_KEY' ) && CF_TURNSTILE_SITE_KEY ? CF_TURNSTILE_SITE_KEY : get_option( 'cfturnstile_key' );
	$secret = defined( 'CF_TURNSTILE_SECRET_KEY' ) && CF_TURNSTILE_SECRET_KEY ? CF_TURNSTILE_SECRET_KEY : get_option( 'cfturnstile_secret' );
	$site   = is_string( $site ) ? trim( $site ) : '';
	$secret = is_string( $secret ) ? trim( $secret ) : '';

	return ( '' !== $site && '' !== $secret ) ? [ 'site' => $site, 'secret' => $secret ] : null;
}

// Cornerstone's Turnstile element uses its own keys first, then these global
// settings. With the Turnstile plugin keyed, its keys stand in for them;
// otherwise whatever is saved under Cornerstone > Settings > Forms is used.
add_filter( 'pre_option_cs_forms_turnstile_site_key', function ( $pre ) {
	$keys = tg_stack_turnstile_plugin_keys();
	return $keys ? $keys['site'] : $pre;
} );

add_filter( 'pre_option_cs_forms_turnstile_secret_key', function ( $pre ) {
	$keys = tg_stack_turnstile_plugin_keys();
	return $keys ? $keys['secret'] : $pre;
} );

/**
 * Whether a Turnstile element has keys to work with: its own, or the global
 * ones above.
 */
function tg_stack_turnstile_has_keys( $element ) {
	$site   = ( $element['site_key'] ?? '' ) ?: get_option( 'cs_forms_turnstile_site_key' );
	$secret = ( $element['site_secret'] ?? '' ) ?: get_option( 'cs_forms_turnstile_secret_key' );

	return ! empty( $site ) && ! empty( $secret );
}

// A Turnstile element always checks for a token, so with no keys anywhere every
// submission would fail. Without keys the element renders nothing and skips
// its check, and the honeypot carries on. It stays registered (rather than
// switching Cornerstone's Turnstile setting off) so the builder never sees the
// component's Turnstile as a missing element.
add_filter( 'cs_register_element_cornerstone-form-cloudflare-turnstile', function ( $element ) {
	$render   = $element['render'] ?? null;
	$validate = $element['options']['form_validate'] ?? null;

	if ( $render ) {
		$element['render'] = function ( $data ) use ( $render ) {
			$preview = function_exists( 'is_cornerstone_preview' ) && is_cornerstone_preview();
			return ( $preview || tg_stack_turnstile_has_keys( $data ) ) ? call_user_func( $render, $data ) : '';
		};
	}

	if ( $validate ) {
		$element['options']['form_validate'] = function ( $data, $element ) use ( $validate ) {
			return tg_stack_turnstile_has_keys( $element ) ? call_user_func( $validate, $data, $element ) : null;
		};
	}

	return $element;
} );

/**
 * Input labels by field name, collected as Cornerstone renders the form. The
 * submission is validated by rendering the whole form first, so every label is
 * known before the email action builds its table.
 */
function tg_stack_form_label( $name, $element = null ) {
	static $labels = [];

	if ( null === $element ) {
		return $labels[ $name ] ?? null;
	}

	if ( empty( $element['label_enabled'] ) || ! is_string( $element['label_html'] ) || '' === trim( $element['label_html'] ) ) {
		return null;
	}

	$label = function_exists( 'cs_dynamic_content' ) ? cs_dynamic_content( $element['label_html'] ) : $element['label_html'];
	$label = trim( wp_strip_all_tags( (string) $label ) );

	if ( '' !== $label ) {
		$labels[ $name ] = $label;
	}

	return null;
}

add_filter( 'cs_element_render_function', function ( $render, $data ) {
	if ( isset( $data['name'], $data['label_html'] ) && is_string( $data['name'] ) && '' !== $data['name'] ) {
		tg_stack_form_label( $data['name'], $data );
	}

	return $render;
}, 10, 2 );

/**
 * Fields left out of the email table: anything starting with cs_, cs- or _
 * (the honeypot, Cornerstone's cs-form-id, nonces) and captcha tokens. Real
 * fields should never be named that way.
 */
function tg_stack_form_field_hidden( $name ) {
	$hidden = 0 === strpos( $name, 'cs_' )
		|| 0 === strpos( $name, 'cs-' )
		|| 0 === strpos( $name, '_' )
		|| in_array( $name, [ 'cf-turnstile-response', 'g-recaptcha-response', 'h-captcha-response' ], true );

	return (bool) apply_filters( 'tg_stack_form_field_hidden', $hidden, $name );
}

/**
 * full_name => "Full name", for inputs without a visible label.
 */
function tg_stack_form_field_title( $name ) {
	return ucfirst( strtolower( trim( preg_replace( '/[_\-\s]+/', ' ', $name ) ) ) );
}

add_filter( 'cs_forms_generate_data_table_row', function ( $row, $key, $value ) {
	$key = (string) $key;

	if ( tg_stack_form_field_hidden( $key ) ) {
		return '';
	}

	$label = tg_stack_form_label( $key ) ?? tg_stack_form_field_title( $key );

	return '<tr><td><b>' . esc_html( $label ) . '</b></td><td>' . cs_forms_human_readable_value( $value ) . '</td></tr>';
}, 10, 3 );

// Drop the "Key / Value" header row.
add_filter( 'cs_forms_generate_data_table', function ( $table ) {
	return preg_replace( '#<thead>.*?</thead>#s', '', $table );
} );
