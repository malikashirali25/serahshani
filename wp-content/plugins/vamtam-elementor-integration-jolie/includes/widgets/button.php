<?php
namespace VamtamElementor\Widgets\Button;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Theme preferences.
if ( ! \Vamtam_Elementor_Utils::is_widget_mod_active( 'button' ) ) {
	return;
}

if ( vamtam_theme_supports( 'button--icon-style-section' ) ) {
	function add_icon_controls_section( $controls_manager, $widget ) {
		// Icon Section.
		$widget->start_controls_section(
			"vamtam_icon_style_section",
			[
				'label' => __( 'Icon', 'vamtam-elementor-integration' ),
				'tab' => $controls_manager::TAB_STYLE,
				'condition' => [
					'selected_icon[value]!' => '',
				],
			]
		);
		// Icon Size.
		$widget->add_responsive_control(
			'vamtam_btn_icon_size',
			[
				'label' => __( 'Size', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--vamtam-btn-icon-font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);
		$widget->start_controls_tabs( 'tabs_btn_icon_style' );

		// Normal Tab.
		$widget->start_controls_tab(
			'tab_btn_icon_normal',
			[
				'label' => esc_html__( 'Normal', 'vamtam-elementor-integration' ),
			]
		);
		// Icon Color.
		$widget->add_control(
			'vamtam_btn_icon_color',
			[
				'label' => __( 'Color', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--vamtam-btn-icon-color: {{VALUE}}',
				],
			]
		);
		$widget->end_controls_tab();

		// Hover Tab.
		$widget->start_controls_tab(
			'tab_btn_icon_hover',
			[
				'label' => esc_html__( 'Hover', 'vamtam-elementor-integration' ),
			]
		);
		// Icon Hover Color.
		$widget->add_control(
			'vamtam_btn_icon_hover_color',
			[
				'label' => __( 'Color', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}' => '--vamtam-btn-icon-hover-color: {{VALUE}}',
				],
			]
		);

		$widget->end_controls_tab();
		$widget->end_controls_tabs();

		$widget->end_controls_section();
	}
	// Style - Button Section (After).
	function section_button_after_section_end( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		add_icon_controls_section( $controls_manager, $widget );
	}
	add_action( 'elementor/element/button/section_style/after_section_end', __NAMESPACE__ . '\section_button_after_section_end', 10, 2 );
}
