<?php
/**
 * Front-end tweaks every Tailgunner Pro site gets. Each is on by default; turn
 * one off for a single site from its child theme, e.g.
 *
 *   add_filter( 'tg_stack_tweak_disable_jquery_everywhere', '__return_false' );
 *
 * Tweaks: remove_generator, dequeue_block_library, remove_global_styles,
 * jquery_passive_listeners, disable_jquery_everywhere,
 * disable_custom_fields_map, eager_no_lazy_images.
 */

defined( 'ABSPATH' ) || exit;

function tg_stack_tweak_enabled( $tweak ) {
	return (bool) apply_filters( "tg_stack_tweak_{$tweak}", true );
}

// Hide the WordPress version from the page head and feeds.
add_action( 'init', function () {
	if ( tg_stack_tweak_enabled( 'remove_generator' ) ) {
		add_filter( 'the_generator', '__return_empty_string' );
		remove_action( 'wp_head', 'wp_generator' );
	}
} );

// Block library CSS is only needed on blog posts.
add_action( 'wp_enqueue_scripts', function () {
	if ( ! tg_stack_tweak_enabled( 'dequeue_block_library' ) || ( is_single() && 'post' === get_post_type() ) ) {
		return;
	}

	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'wc-blocks-style' );
}, 100 );

// Block theme global styles and SVG filters.
add_action( 'init', function () {
	if ( tg_stack_tweak_enabled( 'remove_global_styles' ) ) {
		remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
		remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );
	}
} );

// Passive touch and wheel listeners for jQuery. Only prints on sites where
// something loads jQuery, since it's attached to the jquery handle.
add_action( 'wp_enqueue_scripts', function () {
	if ( ! tg_stack_tweak_enabled( 'jquery_passive_listeners' ) ) {
		return;
	}

	wp_add_inline_script( 'jquery', <<<'JS'
document.addEventListener('DOMContentLoaded', function () {
	jQuery.event.special.touchstart = {
		setup: function (_, ns, handle) {
			this.addEventListener('touchstart', handle, { passive: !ns.includes('noPreventDefault') });
		}
	};
	jQuery.event.special.touchmove = {
		setup: function (_, ns, handle) {
			this.addEventListener('touchmove', handle, { passive: !ns.includes('noPreventDefault') });
		}
	};
	jQuery.event.special.wheel = {
		setup: function (_, ns, handle) {
			this.addEventListener('wheel', handle, { passive: true });
		}
	};
	jQuery.event.special.mousewheel = {
		setup: function (_, ns, handle) {
			this.addEventListener('mousewheel', handle, { passive: true });
		}
	};
});
JS
	);
} );

// Stop Pro loading jQuery on every page. It still loads where a plugin needs it.
add_filter( 'cs_use_jquery_everywhere', function ( $use ) {
	return tg_stack_tweak_enabled( 'disable_jquery_everywhere' ) ? false : $use;
} );

// Remove Themeco's custom fields additions to menus.
add_filter( 'cs_use_custom_fields_map', function ( $use ) {
	return tg_stack_tweak_enabled( 'disable_custom_fields_map' ) ? false : $use;
} );

// Images with an x-no-lazy class load eagerly at high priority (hero images).
add_filter( 'cs_tag_content', function ( $content, $atts ) {
	if ( ! tg_stack_tweak_enabled( 'eager_no_lazy_images' ) || empty( $atts['class'] ) || ! is_array( $atts['class'] ) ) {
		return $content;
	}

	foreach ( $atts['class'] as $class ) {
		if ( is_string( $class ) && false !== strpos( $class, 'x-no-lazy' ) ) {
			return str_replace( 'loading="lazy"', 'loading="eager" fetchpriority="high"', $content );
		}
	}

	return $content;
}, 10, 2 );
