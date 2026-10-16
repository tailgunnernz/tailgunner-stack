<?php
/**
 * The Tailgunner stack: extends Blank (so none of Starter's CSS loads), reuses
 * Starter's x_* settings where they exist, and adds tg_* settings for the rest.
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'cs_theme_option_breakpoint_keys', function ( $keys ) {
	return array_merge( $keys, tg_stack_breakpoint_keys() );
} );

// Blank adds Social (which holds Open Graph) and Portfolio. Tailgunner sites
// don't use them (Rank Math handles Open Graph), so they're hidden while this
// stack is active. Their saved values are left as they are.
foreach ( [ 'cs_theme_options_social_group', 'cs_theme_options_portfolio_group' ] as $tg_stack_hidden_group ) {
	add_filter( $tg_stack_hidden_group, function ( $group ) {
		return TG_STACK_ID === get_option( 'x_stack' ) ? null : $group;
	}, 20 );
}

// Pro registers Blank and Starter on this hook at priority 0, so Blank exists
// by the time we extend it.
add_action( 'cs_theme_options_before_init', function () {
	cs_stack_register( [
		'id'       => TG_STACK_ID,
		'label'    => __( 'Tailgunner', 'tailgunner-stack' ),
		'extends'  => 'blank',
		'values'   => tg_stack_default_values(),
		'css'      => [
			TG_STACK_DIR . 'css/layout.css',
			function () {
				return tg_stack_render_css( 'scaling' === cs_stack_get_value( 'x_root_font_size_mode' ) ? 'root-font-scaling.css' : 'root-font-stepped.php' );
			},
			function () {
				return tg_stack_render_css( 'typography.php' );
			},
			function () {
				return tg_stack_cf7_css();
			},
		],
		'controls' => function () {
			return tg_stack_controls();
		},
	] );
} );

/**
 * Stack CSS files may hold {{dc:theme:*}} tags, which Cornerstone resolves on
 * output. .php files can also run PHP (breakpoint loops, Pro's selectors).
 */
function tg_stack_render_css( $file ) {
	$path = TG_STACK_DIR . 'css/' . $file;

	if ( '.php' !== substr( $file, -4 ) ) {
		return (string) file_get_contents( $path );
	}

	ob_start();
	include $path;
	return ob_get_clean();
}

function tg_stack_controls() {
	$condition_stepped = [ 'x_root_font_size_mode' => 'stepped' ];
	$condition_scaling = [ 'x_root_font_size_mode' => 'scaling' ];

	$choices_px_em = [
		[ 'value' => 'px', 'label' => 'px' ],
		[ 'value' => 'em', 'label' => 'em' ],
	];

	$options_line_height = [
		'unit_mode' => 'unitless',
		'min'       => 0.8,
		'max'       => 2.5,
		'step'      => 0.05,
	];

	$options_letter_spacing = [
		'available_units' => [ 'em' ],
		'fallback_value'  => '-0.015em',
		'ranges'          => [
			'em' => [ 'min' => -0.05, 'max' => 0.25, 'step' => 0.005 ],
		],
	];

	$options_heading_size = [
		'available_units' => [ '%', 'em', 'rem', 'px' ],
		'valid_keywords'  => [ 'calc' ],
		'ranges'          => [
			'%'   => [ 'min' => 80, 'max' => 600, 'step' => 5 ],
			'em'  => [ 'min' => 0.8, 'max' => 6, 'step' => 0.05 ],
			'rem' => [ 'min' => 0.8, 'max' => 6, 'step' => 0.05 ],
			'px'  => [ 'min' => 12, 'max' => 96, 'step' => 1 ],
		],
	];

	$letter_spacing = [];
	$heading_sizes  = [];

	foreach ( range( 1, 6 ) as $level ) {
		$letter_spacing[] = [
			'key'     => "x_h{$level}_letter_spacing",
			'type'    => 'unit-slider',
			'label'   => sprintf( __( 'h%d Letter Spacing', 'tailgunner-stack' ), $level ),
			'options' => $options_letter_spacing,
		];
	}

	foreach ( range( 0, 6 ) as $level ) {
		$heading_sizes[] = [
			'key'     => "tg_h{$level}_font_size",
			'type'    => 'unit-slider',
			'label'   => sprintf( __( 'h%d Size', 'tailgunner-stack' ), $level ),
			'options' => $options_heading_size,
		];
	}

	$controls = [
		[
			'type'     => 'group-sub-module',
			'label'    => __( 'Layout', 'tailgunner-stack' ),
			'options'  => [ 'tag' => 'tg-layout', 'name' => 'tg-theme-options:layout' ],
			'controls' => [
				[
					'type'     => 'group',
					'label'    => __( 'Site', 'tailgunner-stack' ),
					'controls' => [
						[
							'key'     => 'x_layout_site_width',
							'type'    => 'unit-slider',
							'label'   => __( 'Site Width', 'tailgunner-stack' ),
							'options' => [
								'available_units' => [ '%' ],
								'valid_keywords'  => [ 'calc' ],
								'fallback_value'  => '88%',
								'ranges'          => [
									'%' => [ 'min' => 70, 'max' => 100, 'step' => 1 ],
								],
							],
						],
						[
							'key'     => 'x_layout_site_max_width',
							'type'    => 'unit-slider',
							'label'   => __( 'Site Max Width', 'tailgunner-stack' ),
							'options' => [
								'available_units' => [ 'px' ],
								'fallback_value'  => '1200px',
								'ranges'          => [
									'px' => [ 'min' => 800, 'max' => 1800, 'step' => 10 ],
								],
							],
						],
						[
							'key'   => 'x_design_bg_color',
							'type'  => 'color',
							'label' => __( 'Background Color', 'tailgunner-stack' ),
						],
					],
				],
			],
		],
		[
			'type'     => 'group-sub-module',
			'label'    => __( 'Typography', 'tailgunner-stack' ),
			'options'  => [ 'tag' => 'tg-typography', 'name' => 'tg-theme-options:typography' ],
			'controls' => [
				[
					'type'        => 'group',
					'label'       => __( 'Root Font Size', 'tailgunner-stack' ),
					'description' => __( '"Stepped" sets a size at each breakpoint. "Scaling" scales between a minimum and maximum size across the limits you set.', 'tailgunner-stack' ),
					'controls'    => [
						[
							'key'     => 'x_root_font_size_mode',
							'type'    => 'choose',
							'label'   => __( 'Mode', 'tailgunner-stack' ),
							'options' => [
								'choices' => [
									[ 'value' => 'stepped', 'label' => __( 'Stepped', 'tailgunner-stack' ) ],
									[ 'value' => 'scaling', 'label' => __( 'Scaling', 'tailgunner-stack' ) ],
								],
							],
						],
						[
							'key'        => 'x_root_font_size_stepped_unit',
							'type'       => 'choose',
							'label'      => __( 'Unit', 'tailgunner-stack' ),
							'options'    => [ 'choices' => $choices_px_em ],
							'conditions' => [ $condition_stepped ],
						],
						[
							'key'        => 'x_root_font_size_stepped_xs',
							'type'       => 'text',
							'label'      => __( 'XS Breakpoint', 'tailgunner-stack' ),
							'conditions' => [ $condition_stepped ],
						],
						[
							'key'        => 'x_root_font_size_stepped_sm',
							'type'       => 'text',
							'label'      => __( 'SM Breakpoint', 'tailgunner-stack' ),
							'conditions' => [ $condition_stepped ],
						],
						[
							'key'        => 'x_root_font_size_stepped_md',
							'type'       => 'text',
							'label'      => __( 'MD Breakpoint', 'tailgunner-stack' ),
							'conditions' => [ $condition_stepped ],
						],
						[
							'key'        => 'x_root_font_size_stepped_lg',
							'type'       => 'text',
							'label'      => __( 'LG Breakpoint', 'tailgunner-stack' ),
							'conditions' => [ $condition_stepped ],
						],
						[
							'key'        => 'x_root_font_size_stepped_xl',
							'type'       => 'text',
							'label'      => __( 'XL Breakpoint', 'tailgunner-stack' ),
							'conditions' => [ $condition_stepped ],
						],
						[
							'key'        => 'x_root_font_size_scaling_unit',
							'type'       => 'choose',
							'label'      => __( 'Unit', 'tailgunner-stack' ),
							'options'    => [ 'choices' => $choices_px_em ],
							'conditions' => [ $condition_scaling ],
						],
						[
							'key'        => 'x_root_font_size_scaling_min',
							'type'       => 'text',
							'label'      => __( 'Minimum Font Size', 'tailgunner-stack' ),
							'conditions' => [ $condition_scaling ],
						],
						[
							'key'        => 'x_root_font_size_scaling_max',
							'type'       => 'text',
							'label'      => __( 'Maximum Font Size', 'tailgunner-stack' ),
							'conditions' => [ $condition_scaling ],
						],
						[
							'key'        => 'x_root_font_size_scaling_lower_limit',
							'type'       => 'text',
							'label'      => __( 'Lower Limit', 'tailgunner-stack' ),
							'conditions' => [ $condition_scaling ],
						],
						[
							'key'        => 'x_root_font_size_scaling_upper_limit',
							'type'       => 'text',
							'label'      => __( 'Upper Limit', 'tailgunner-stack' ),
							'conditions' => [ $condition_scaling ],
						],
					],
				],
				[
					'type'     => 'group',
					'label'    => __( 'Body', 'tailgunner-stack' ),
					'controls' => [
						[
							'key'   => 'x_body_font_family_selection',
							'type'  => 'font-family',
							'label' => __( 'Font Family', 'tailgunner-stack' ),
						],
						[
							'keys'  => [
								'value'       => 'x_body_font_weight_selection',
								'font_family' => 'x_body_font_family_selection',
							],
							'type'  => 'font-weight',
							'label' => __( 'Font Weight', 'tailgunner-stack' ),
						],
						[
							'key'   => 'x_body_font_color',
							'type'  => 'color',
							'label' => __( 'Color', 'tailgunner-stack' ),
						],
						[
							'key'     => 'tg_body_line_height',
							'type'    => 'unit-slider',
							'label'   => __( 'Line Height', 'tailgunner-stack' ),
							'options' => $options_line_height,
						],
					],
				],
				[
					'type'     => 'group',
					'label'    => __( 'Headings', 'tailgunner-stack' ),
					'controls' => array_merge(
						[
							[
								'key'   => 'x_headings_font_family_selection',
								'type'  => 'font-family',
								'label' => __( 'Font Family', 'tailgunner-stack' ),
							],
							[
								'keys'  => [
									'value'       => 'x_headings_font_weight_selection',
									'font_family' => 'x_headings_font_family_selection',
								],
								'type'  => 'font-weight',
								'label' => __( 'Font Weight', 'tailgunner-stack' ),
							],
							[
								'key'   => 'x_headings_font_color',
								'type'  => 'color',
								'label' => __( 'Color', 'tailgunner-stack' ),
							],
							[
								'key'     => 'tg_headings_line_height',
								'type'    => 'unit-slider',
								'label'   => __( 'Line Height', 'tailgunner-stack' ),
								'options' => $options_line_height,
							],
						],
						$letter_spacing
					),
				],
				[
					'type'        => 'group',
					'label'       => __( 'Heading Sizes', 'tailgunner-stack' ),
					'description' => __( 'h0 is the .text-size-h0 display size. Switch breakpoints to set smaller sizes for mobile.', 'tailgunner-stack' ),
					'controls'    => $heading_sizes,
				],
				[
					'type'     => 'group',
					'label'    => __( 'Links', 'tailgunner-stack' ),
					'controls' => [
						[
							'keys'    => [
								'value' => 'x_site_link_color',
								'alt'   => 'x_site_link_color_hover',
							],
							'type'    => 'color',
							'label'   => __( 'Color', 'tailgunner-stack' ),
							'options' => cs_recall( 'options_swatch_base_interaction_labels' ),
						],
					],
				],
			],
		],
	];

	if ( tg_stack_cf7_active() ) {
		$controls[] = tg_stack_cf7_controls();
	}

	return $controls;
}
