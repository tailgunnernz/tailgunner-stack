<?php
/**
 * Root font size: stepped. One size per breakpoint, output with Pro's own
 * breakpoint loop so it follows the site's breakpoint settings.
 */

defined( 'ABSPATH' ) || exit;
?>
html {
  font-size: {{dc:theme:x_root_font_size_stepped_xs}}{{dc:theme:x_root_font_size_stepped_unit}};
}

<?php
cs_breakpoint_output_loop_output( [
	'font-size: {{dc:theme:x_root_font_size_stepped_sm}}{{dc:theme:x_root_font_size_stepped_unit}};',
	'font-size: {{dc:theme:x_root_font_size_stepped_md}}{{dc:theme:x_root_font_size_stepped_unit}};',
	'font-size: {{dc:theme:x_root_font_size_stepped_lg}}{{dc:theme:x_root_font_size_stepped_unit}};',
	'font-size: {{dc:theme:x_root_font_size_stepped_xl}}{{dc:theme:x_root_font_size_stepped_unit}};',
], 'html {', '}' );
