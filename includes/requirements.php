<?php
/**
 * The stack only runs on Pro 6.9+, which added per-breakpoint theme options.
 */

defined( 'ABSPATH' ) || exit;

function tg_stack_requirements_met() {
	return 'pro' === get_template()
		&& defined( 'X_VERSION' )
		&& version_compare( X_VERSION, TG_STACK_MIN_PRO, '>=' );
}

function tg_stack_requirements_notice() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	printf(
		'<div class="notice notice-error"><p>%s</p></div>',
		esc_html( sprintf(
			/* translators: %s: minimum Pro version */
			__( 'Tailgunner Stack needs the Pro theme %s or newer, so it is doing nothing on this site.', 'tailgunner-stack' ),
			TG_STACK_MIN_PRO
		) )
	);
}
