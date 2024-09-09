<?php
namespace VamtamElementor\Widgets\ProductsCategories;

// Extending the Products Categories widget.

// Is WC Widget.
if ( ! vamtam_has_woocommerce() ) {
	return;
}

// Is Pro Widget.
if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
	return;
}

// Theme Settings.
if ( ! \Vamtam_Elementor_Utils::is_widget_mod_active( 'wc-categories' ) ) {
	return;
}

// Product Categories, before render_content.
function product_categories_before_render_content( $widget ) {
    $widget_name = $widget->get_name();
    if ( $widget->get_name() === 'global' ) {
        $widget_name = $widget->get_original_element_instance()->get_name();
    }

	if ( $widget_name === 'wc-categories' ) {
		do_action( 'vamtam_before_products_cat_widget_before_render_content', $widget_name );
	}
}
add_action( 'elementor/widget/before_render_content', __NAMESPACE__ . '\product_categories_before_render_content', 10, 1 );