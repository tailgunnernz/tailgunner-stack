<?php
/**
 * Updates come from GitHub releases. The repo is public, so no token is
 * needed. Only the zip the release workflow attaches is accepted, never the
 * source zipball, so a failed build serves nothing rather than a broken plugin.
 */

defined( 'ABSPATH' ) || exit;

require_once TG_STACK_DIR . 'vendor/plugin-update-checker/plugin-update-checker.php';

$tg_stack_update_checker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
	'https://github.com/tailgunnernz/tailgunner-stack/',
	TG_STACK_FILE,
	'tailgunner-stack'
);

$tg_stack_update_api = $tg_stack_update_checker->getVcsApi();
$tg_stack_update_api->enableReleaseAssets( '/^tailgunner-stack\.zip$/', $tg_stack_update_api::REQUIRE_RELEASE_ASSETS );

// Every release installs itself on the next WordPress cron run.
add_filter( 'auto_update_plugin', function ( $update, $item ) {
	if ( isset( $item->plugin ) && plugin_basename( TG_STACK_FILE ) === $item->plugin ) {
		return true;
	}

	return $update;
}, 10, 2 );
