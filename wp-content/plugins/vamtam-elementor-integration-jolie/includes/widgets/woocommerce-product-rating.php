<?php
namespace VamtamElementor\Widgets\ProductRating;

// Extending the WC Product Rating widget.

// Is WC Widget.
if ( ! vamtam_has_woocommerce() ) {
	return;
}

// Is Pro Widget.
if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
	return;
}

// Theme preferences.
if ( ! \Vamtam_Elementor_Utils::is_widget_mod_active( 'woocommerce-product-rating' ) ) {
	return;
}

// TODO: Remove when fixed: https://github.com/elementor/elementor/issues/16625
if ( vamtam_theme_supports( 'woocommerce-product-rating--alignment-fix' ) ) {
	function update_alignment_control( $controls_manager, $widget ) {
		// Alignment.
		\Vamtam_Elementor_Utils::replace_control_options( $controls_manager, $widget, 'alignment', [
			'prefix_class' => 'elementor-product-rating--align%s-',
		] );
	}
	function section_product_rating_style_before_section_end( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		update_alignment_control( $controls_manager, $widget );
	}
	add_action( 'elementor/element/woocommerce-product-rating/section_product_rating_style/before_section_end', __NAMESPACE__ . '\section_product_rating_style_before_section_end', 10, 2 );
}
