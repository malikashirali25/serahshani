<?php
namespace VamtamElementor\Widgets\Section;

// Extending the Section widget.

// Theme preferences.
if ( ! \Vamtam_Elementor_Utils::is_widget_mod_active( 'section' ) ) {
	return;
}

if ( vamtam_theme_supports( 'section--vamtam-sticky-header-controls' ) ) {
	function add_vamtam_sticky_header_controls( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => 'sticky_effects_offset',
		] );
		$widget->add_control(
			'use_vamtam_sticky_header',
			[
				'label' => __( 'Use Theme Sticky Header (Desktop)', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'prefix_class' => '',
				'return_value' => 'vamtam-sticky-header',
				'condition' => [
					'sticky' => 'top',
				]
			]
		);
		$widget->add_control(
			'vamtam_sticky_header_transparent',
			[
				'label' => __( 'Header Is Transparent', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'prefix_class' => '',
				'return_value' => 'vamtam-sticky-header--transparent-header',
				'condition' => [
					'sticky' => 'top',
					'use_vamtam_sticky_header!' => '',
				],
			]
		);
		$widget->end_injection();
	}

	// Advanced - Motion effects.
	function section_effects_before_section_end( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		add_vamtam_sticky_header_controls( $controls_manager, $widget );
	}
	add_action( 'elementor/element/section/section_effects/before_section_end', __NAMESPACE__ . '\section_effects_before_section_end', 10, 2 );
}
