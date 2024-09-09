<?php
namespace VamtamElementor\Widgets\Heading;

// Extending the Heading widget.

// Theme preferences.
if ( ! \Vamtam_Elementor_Utils::is_widget_mod_active( 'heading' ) ) {
	return;
}

if ( vamtam_theme_supports( 'heading--is-bubble' ) ) {

	function add_buble_controls( $controls_manager, $widget ) {
		$widget->add_control(
            'vamtam_heading_is_bubble',
            [
                'label' => __( 'Is Chat Bubble', 'vamtam-elementor-integration' ),
                'type' => $controls_manager::SWITCHER,
                'return_value' => 'bubble',
                'prefix_class' => 'vamtam-heading-is-',
            ]
        );

		$widget->add_control(
			'vamtam_bubble_tip_pos',
			[
				'label' => esc_html__( 'Tip Position', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'vamtam-elementor-integration' ),
						'icon' => 'eicon-arrow-left',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'vamtam-elementor-integration' ),
						'icon' => 'eicon-arrow-right',
					],
				],
				'default' => '',
				'prefix_class' => 'vamtam-bubble-tip-',
				'condition' => [
					'vamtam_heading_is_bubble!' => '',
				],
			]
		);

		$widget->add_control(
			'vamtam_bubble_tip_size',
			[
				'label' => __( 'Tip Size (em)', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SLIDER,
                'size_units' => [ 'em' ],
				'range' => [
                    'em' => [
                        'min' => 0,
                        'max' => 2,
                        'step' => 0.01,
                    ],
                ],
				'default' => [
					'unit' => 'em',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--vamtam-bubble-tip-size: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'vamtam_heading_is_bubble!' => '',
				],
			]
		);

		$widget->add_control(
			'vamtam_bubble_bg_color',
			[
				'label' => __( 'Background Color', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--vamtam-bubble-bg-color: {{VALUE}}',
				],
				'condition' => [
					'vamtam_heading_is_bubble!' => '',
				],
			]
		);

		$widget->add_control(
			'vamtam_bubble_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}}' => '--vamtam-bubble-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'vamtam_heading_is_bubble!' => '',
				],
			]
		);
	}

	// Style - Title Section.
	function section_title_style_before_section_end( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		add_buble_controls( $controls_manager, $widget );
	}
	add_action( 'elementor/element/heading/section_title_style/before_section_end', __NAMESPACE__ . '\section_title_style_before_section_end', 10, 2 );
}
