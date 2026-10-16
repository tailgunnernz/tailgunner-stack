<?php
/**
 * Contact Form 7 styling. The settings group and the CSS only appear while CF7
 * is active, so sites on Cornerstone Forms carry none of it.
 */

defined( 'ABSPATH' ) || exit;

function tg_stack_cf7_active() {
	return defined( 'WPCF7_VERSION' );
}

function tg_stack_cf7_css() {
	return tg_stack_cf7_active() ? tg_stack_render_css( 'cf7.css' ) : '';
}

function tg_stack_cf7_controls() {
	$options_radius = [
		'available_units' => [ 'px', 'em', 'rem' ],
		'ranges'          => [
			'px'  => [ 'min' => 0, 'max' => 30, 'step' => 1 ],
			'em'  => [ 'min' => 0, 'max' => 2, 'step' => 0.05 ],
			'rem' => [ 'min' => 0, 'max' => 2, 'step' => 0.05 ],
		],
	];

	return [
		'type'     => 'group-sub-module',
		'label'    => __( 'CF7 Forms', 'tailgunner-stack' ),
		'options'  => [ 'tag' => 'tg-cf7', 'name' => 'tg-theme-options:cf7' ],
		'controls' => [
			[
				'type'        => 'group',
				'label'       => __( 'Button', 'tailgunner-stack' ),
				'controls'    => [
					[
						'key'   => 'tg_cf7_button_bg',
						'type'  => 'color',
						'label' => __( 'Background', 'tailgunner-stack' ),
					],
					[
						'key'   => 'tg_cf7_button_text',
						'type'  => 'color',
						'label' => __( 'Text', 'tailgunner-stack' ),
					],
					[
						'key'     => 'tg_cf7_button_radius',
						'type'    => 'unit-slider',
						'label'   => __( 'Radius', 'tailgunner-stack' ),
						'options' => $options_radius,
					],
				],
			],
			[
				'type'        => 'group',
				'label'       => __( 'Inputs', 'tailgunner-stack' ),
				'description' => __( 'Error and success colours are fixed to match the Cornerstone form component.', 'tailgunner-stack' ),
				'controls'    => [
					[
						'key'   => 'tg_cf7_input_border',
						'type'  => 'color',
						'label' => __( 'Border', 'tailgunner-stack' ),
					],
					[
						'key'     => 'tg_cf7_input_radius',
						'type'    => 'unit-slider',
						'label'   => __( 'Radius', 'tailgunner-stack' ),
						'options' => $options_radius,
					],
				],
			],
		],
	];
}
