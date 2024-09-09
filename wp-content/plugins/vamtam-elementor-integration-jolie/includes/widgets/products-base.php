<?php
namespace VamtamElementor\Widgets\ProductsBase;

/*
	Common extensions for:
		- woocommerce-product-related
		- woocommerce-product-upsell
		- wc-archive-products
		- woocommerce-products
	products widgets.
*/

// Is WC Widget.
if ( ! vamtam_has_woocommerce() ) {
	return;
}

// Is Pro Widget.
if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
	return;
}

function update_controls_style_tab_products_section( $controls_manager, $widget ) {
	// Image Spacing.
	\Vamtam_Elementor_Utils::replace_control_options( $controls_manager, $widget, 'image_spacing', [
		'selectors' => [
			'{{WRAPPER}}' => '--vamtam-img-spacing: {{SIZE}}{{UNIT}}',
		]
	] );

	// Increase specificity of View cart selectors so they override the Button ones, if needed.
	$new_options = [
		'selectors' => [
			'{{WRAPPER}}.elementor-wc-products .products .product .added_to_cart' => '{{_RESET_}}',
		]
	];
	// View Cart Color.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'view_cart_color', $new_options );
	// View Cart Typography.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'view_cart_typography', $new_options, \Elementor\Group_Control_Typography::get_type() );

	// Alignment
	\Vamtam_Elementor_Utils::replace_control_options( $controls_manager, $widget, 'align', [
		'prefix_class' => 'elementor-product-loop-item%s--align-',
	] );
}

function add_controls_style_tab_products_section( $controls_manager, $widget ) {
	add_content_controls( $controls_manager, $widget );
	add_title_min_height_controls( $controls_manager, $widget );
}

function update_controls_style_tab_sale_section( $controls_manager, $widget ) {
	// Show Sale.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'show_onsale_flash', [
		'prefix_class' => 'vamtam-has-onsale-',
	] );
	// Background Color.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'onsale_text_background_color', [
		'selectors' => [
			'{{WRAPPER}} .onsale' => '--vamtam-onsale-bg-color: {{VALUE}}',
		],
	] );
	// Position.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'onsale_horizontal_position', [
		'prefix_class' => 'vamtam-onsale-',
	] );
}

function add_content_controls( $controls_manager, $widget ) {
	$widget->add_control(
		'heading_content',
		[
			'label' => __( 'Content', 'vamtam-elementor-integration' ),
			'type' => $controls_manager::HEADING,
			'separator' => 'before',
		]
	);
	$widget->start_controls_tabs( 'content_style_tabs' );
	$widget->start_controls_tab( 'content_style_normal',
		[
			'label' => __( 'Normal', 'vamtam-elementor-integration' ),
		]
	);
	$widget->add_control(
		'content_bg_color',
		[
			'label' => __( 'Background Color', 'vamtam-elementor-integration' ),
			'type' => $controls_manager::COLOR,
			'default' => '',
			'selectors' => [
				'{{WRAPPER}} .elementor-widget-container ul.products li.product .vamtam-product-content' => 'background-color: {{VALUE}};',
			],
			'scheme' => [
				'type' => \Elementor\Core\Schemes\Color::get_type(),
				'value' => \Elementor\Core\Schemes\Color::COLOR_1,
			],
		]
	);
	$widget->end_controls_tab();
	$widget->start_controls_tab( 'content_style_hover',
		[
			'label' => __( 'Hover', 'vamtam-elementor-integration' ),
		]
	);
	$widget->add_control(
		'content_bg_color_hover',
		[
			'label' => __( 'Background Color', 'vamtam-elementor-integration' ),
			'type' => $controls_manager::COLOR,
			'default' => '',
			'selectors' => [
				'{{WRAPPER}} .elementor-widget-container ul.products li.product .vamtam-product-content:hover' => 'background-color: {{VALUE}};',
			],
			'scheme' => [
				'type' => \Elementor\Core\Schemes\Color::get_type(),
				'value' => \Elementor\Core\Schemes\Color::COLOR_1,
			],
		]
	);
	$widget->end_controls_tab();
	$widget->end_controls_tabs();
}

function add_title_min_height_controls( $controls_manager, $widget ) {
	$widget->start_injection( [
		'of' => 'title_spacing',
	] );
	// Use Title Min-Height.
	$widget->add_control(
		'has_title_min_height',
		[
			'label' => __( 'Use Title Min Height', 'vamtam-elementor-integration' ),
			'description' => __( 'Use this option to equalize any differences caused by inconsistent title names.', 'vamtam-elementor-integration' ),
			'type' => $controls_manager::SWITCHER,
			'prefix_class' => 'vamtam-has-',
			'return_value' => 'title-min-height',
		]
	);
	// Title Min-Height.
	$widget->add_responsive_control(
		'title_min_height',
		[
			'label' => __( 'Min Height', 'vamtam-elementor-integration' ),
			'type' => $controls_manager::SLIDER,
			'size_units' => [ 'px' ],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .elementor-widget-container ul.products li.product .vamtam-product-content .woocommerce-loop-product__title' => 'min-height: {{SIZE}}{{UNIT}}',
			],
			'condition' => [
				'has_title_min_height!' => '',
			],
		]
	);
	$widget->end_injection();
}

function add_btn_widget_aligned_btn_controls( $controls_manager, $widget ) {
	// We have to remove and re-add existing controls.
	\Vamtam_Elementor_Utils::remove_tabs( $controls_manager, $widget, 'tabs_button_style' );
	\Vamtam_Elementor_Utils::remove_control( $controls_manager, $widget, 'button_border', \Elementor\Group_Control_Border::get_type() );
	\Vamtam_Elementor_Utils::remove_control( $controls_manager, $widget, 'button_border_radius' );
	\Vamtam_Elementor_Utils::remove_control( $controls_manager, $widget, 'button_text_padding' );
	\Vamtam_Elementor_Utils::remove_control( $controls_manager, $widget, 'button_spacing' );

	$selectors = '{{WRAPPER}}.elementor-wc-products ul.products li.product .button, {{WRAPPER}}.elementor-wc-products .added_to_cart';

	$widget->start_injection( [
		'of' => 'heading_button_style',
	] );

	$widget->add_group_control(
		\Elementor\Group_Control_Typography::get_type(),
		[
			'name' => 'button_typography',
			'selector' => $selectors,
		]
	);

	$widget->add_group_control(
		\Elementor\Group_Control_Text_Shadow::get_type(),
		[
			'name' => 'button_text_shadow',
			'selector' => $selectors,
		]
	);

	$widget->start_controls_tabs( 'tabs_button_style' );
	$widget->start_controls_tab(
		'tab_button_normal',
		[
			'label' => __( 'Normal', 'vamtam-elementor-integration' ),
		]
	);

	$widget->add_control(
		'button_text_color',
		[
			'label' => __( 'Text Color', 'vamtam-elementor-integration' ),
			'type' => $controls_manager::COLOR,
			'default' => '',
			'selectors' => [
				$selectors => 'fill: {{VALUE}}; color: {{VALUE}};',
			],
		]
	);

	$widget->add_group_control(
		\Elementor\Group_Control_Background::get_type(),
		[
			'name' => 'button_background',
			'label' => __( 'Background', 'vamtam-elementor-integration' ),
			'types' => [ 'classic', 'gradient' ],
			'exclude' => [ 'image' ],
			'selector' => $selectors,
			'fields_options' => [
				'background' => [
					'default' => 'classic',
				],
			],
		]
	);

	$widget->end_controls_tab();
	$widget->start_controls_tab(
		'tab_button_hover',
		[
			'label' => __( 'Hover', 'vamtam-elementor-integration' ),
		]
	);

	$widget->add_control(
		'button_hover_color',
		[
			'label' => __( 'Text Color', 'vamtam-elementor-integration' ),
			'type' => $controls_manager::COLOR,
			'selectors' => [
				'{{WRAPPER}}.elementor-wc-products ul.products li.product .button:hover,
				{{WRAPPER}}.elementor-wc-products ul.products li.product .button:focus,
				{{WRAPPER}}.elementor-wc-products .added_to_cart:hover,
				{{WRAPPER}}.elementor-wc-products .added_to_cart:focus' => 'color: {{VALUE}};',
				'{{WRAPPER}}.elementor-wc-products ul.products li.product .button:hover svg,
				{{WRAPPER}}.elementor-wc-products ul.products li.product .button:focus svg,
				{{WRAPPER}}.elementor-wc-products .added_to_cart:hover svg,
				{{WRAPPER}}.elementor-wc-products .added_to_cart:focus svg' => 'fill: {{VALUE}};',
			],
		]
	);

	$widget->add_group_control(
		\Elementor\Group_Control_Background::get_type(),
		[
			'name' => 'button_hover_background',
			'label' => __( 'Background', 'vamtam-elementor-integration' ),
			'types' => [ 'classic', 'gradient' ],
			'exclude' => [ 'image' ],
			'selector' => '{{WRAPPER}}.elementor-wc-products ul.products li.product .button:hover,
							{{WRAPPER}}.elementor-wc-products ul.products li.product .button:focus,
							{{WRAPPER}}.elementor-wc-products .added_to_cart:hover,
							{{WRAPPER}}.elementor-wc-products .added_to_cart:focus',
			'fields_options' => [
				'background' => [
					'default' => 'classic',
				],
			],
		]
	);

	$widget->add_control(
		'button_hover_border_color',
		[
			'label' => __( 'Border Color', 'vamtam-elementor-integration' ),
			'type' => $controls_manager::COLOR,
			'condition' => [
				'button_border_border!' => '',
			],
			'selectors' => [
				'{{WRAPPER}}.elementor-wc-products ul.products li.product .button:hover,
				{{WRAPPER}}.elementor-wc-products ul.products li.product .button:focus,
				{{WRAPPER}}.elementor-wc-products .added_to_cart:hover,
				{{WRAPPER}}.elementor-wc-products .added_to_cart:focus' => 'border-color: {{VALUE}};',
			],
		]
	);

	// TODO: If we need hover_anims, we need widget class extension.
	// $widget->add_control(
	// 	'hover_animation',
	// 	[
	// 		'label' => __( 'Hover Animation', 'vamtam-elementor-integration' ),
	// 		'type' => $controls_manager::HOVER_ANIMATION,
	// 	]
	// );

	$widget->end_controls_tab();
	$widget->end_controls_tabs();

	$widget->end_injection();

	// We have to re-inject here cause injection_point gets messed up when tabs
	// are used during an injection.

	$widget->start_injection( [
		'of' => 'heading_view_cart_style',
		'at' => 'before',
	] );

	$widget->add_group_control(
		\Elementor\Group_Control_Border::get_type(),
		[
			'name' => 'button_border',
			'selector' => $selectors,
			'separator' => 'before',
		]
	);

	$widget->add_control(
		'button_border_radius',
		[
			'label' => __( 'Border Radius', 'vamtam-elementor-integration' ),
			'type' => $controls_manager::DIMENSIONS,
			'size_units' => [ 'px', '%', 'em' ],
			'selectors' => [
				$selectors => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]
	);

	$widget->add_group_control(
		\Elementor\Group_Control_Box_Shadow::get_type(),
		[
			'name' => 'button_box_shadow',
			'selector' => $selectors,
		]
	);

	$widget->add_responsive_control(
		'button_text_padding',
		[
			'label' => __( 'Padding', 'vamtam-elementor-integration' ),
			'type' => $controls_manager::DIMENSIONS,
			'size_units' => [ 'px', 'em', '%' ],
			'selectors' => [
				$selectors => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
			'separator' => 'before',
		]
	);

	$widget->add_responsive_control(
		'button_spacing',
		[
			'label' => __( 'Spacing', 'vamtam-elementor-integration' ),
			'type' => $controls_manager::SLIDER,
			'size_units' => [ 'px', 'em' ],
			'selectors' => [
				'{{WRAPPER}}.elementor-wc-products ul.products li.product .button' => 'margin-top: {{SIZE}}{{UNIT}}',
				'{{WRAPPER}}.elementor-wc-products ul.products li.product .added_to_cart' => 'margin-top: {{SIZE}}{{UNIT}}',
			],
		]
	);

	$widget->end_injection();

}

// Products Button section (add_to_cart, view_cart).
function section_products_style_before_section_end( $widget, $args ) {
	$controls_manager = \Elementor\Plugin::instance()->controls_manager;
	add_btn_widget_aligned_btn_controls( $controls_manager, $widget );
	update_controls_style_tab_products_section( $controls_manager, $widget );
	add_controls_style_tab_products_section( $controls_manager, $widget );
}
// Theme Settings.
if ( \Vamtam_Elementor_Utils::is_widget_mod_active( 'woocommerce-product-related' ) ) {
	add_action( 'elementor/element/woocommerce-product-related/section_products_style/before_section_end', __NAMESPACE__ . '\section_products_style_before_section_end', 10, 2 );
}
// Theme Settings.
if ( \Vamtam_Elementor_Utils::is_widget_mod_active( 'woocommerce-product-upsell' ) ) {
	add_action( 'elementor/element/woocommerce-product-upsell/section_products_style/before_section_end', __NAMESPACE__ . '\section_products_style_before_section_end', 10, 2 );
}
// Theme Settings.
if ( \Vamtam_Elementor_Utils::is_widget_mod_active( 'wc-archive-products' ) ) {
	add_action( 'elementor/element/wc-archive-products/section_products_style/before_section_end', __NAMESPACE__ . '\section_products_style_before_section_end', 10, 2 );
}
// Theme Settings.
if ( \Vamtam_Elementor_Utils::is_widget_mod_active( 'woocommerce-products' ) ) {
	add_action( 'elementor/element/woocommerce-products/section_products_style/before_section_end', __NAMESPACE__ . '\section_products_style_before_section_end', 10, 2 );
}

// Products Sale section.
function sale_flash_style_before_section_end( $widget, $args ) {
	$controls_manager = \Elementor\Plugin::instance()->controls_manager;
	update_controls_style_tab_sale_section( $controls_manager, $widget );
}
// Theme Settings.
if ( \Vamtam_Elementor_Utils::is_widget_mod_active( 'woocommerce-product-related' ) ) {
	add_action( 'elementor/element/woocommerce-product-related/sale_flash_style/before_section_end', __NAMESPACE__ . '\sale_flash_style_before_section_end', 10, 2 );
}
// Theme Settings.
if ( \Vamtam_Elementor_Utils::is_widget_mod_active( 'woocommerce-product-upsell' ) ) {
	add_action( 'elementor/element/woocommerce-product-upsell/sale_flash_style/before_section_end', __NAMESPACE__ . '\sale_flash_style_before_section_end', 10, 2 );
}
// Theme Settings.
if ( \Vamtam_Elementor_Utils::is_widget_mod_active( 'wc-archive-products' ) ) {
	add_action( 'elementor/element/wc-archive-products/sale_flash_style/before_section_end', __NAMESPACE__ . '\sale_flash_style_before_section_end', 10, 2 );
}
// Theme Settings.
if ( \Vamtam_Elementor_Utils::is_widget_mod_active( 'woocommerce-products' ) ) {
	add_action( 'elementor/element/woocommerce-products/sale_flash_style/before_section_end', __NAMESPACE__ . '\sale_flash_style_before_section_end', 10, 2 );
}

function update_controls_style_tab_pagination_section( $controls_manager, $widget ) {
	$new_options = [
		'selectors' => [
			'{{WRAPPER}} .navigation.vamtam-pagination-wrapper' => '{{_RESET_}}',
		]
	];
	// Pagination Spacing.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'pagination_spacing', $new_options );
	// Pagination Typography.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'pagination_typography', $new_options, \Elementor\Group_Control_Typography::get_type() );
	// Pagination Padding.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'pagination_padding', [
		'selectors' => [
			'{{WRAPPER}} .navigation.vamtam-pagination-wrapper .page-numbers' => 'line-height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
		]
	] );
	$new_options = [
		'selectors' => [
			'{{WRAPPER}} .navigation.vamtam-pagination-wrapper .page-numbers' => '{{_RESET_}}',
		]
	];
	// Pagination Border Color.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'pagination_border_color', $new_options );
	// Pagination Link Color.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'pagination_link_color', $new_options );
	// Pagination Link Bg Color.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'pagination_link_bg_color', $new_options );
	$new_options = [
		'selectors' => [
			'{{WRAPPER}} .navigation.vamtam-pagination-wrapper .page-numbers:hover' => '{{_RESET_}}',
		]
	];
	// Pagination Link Hover Color.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'pagination_link_color_hover', $new_options );
	// Pagination Link Bg Hover Color.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'pagination_link_bg_color_hover', $new_options );
	$new_options = [
		'selectors' => [
			'{{WRAPPER}} .navigation.vamtam-pagination-wrapper .page-numbers.current' => '{{_RESET_}}',
		]
	];
	// Pagination Link Active Color.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'pagination_link_color_active', $new_options );
	// Pagination Link Bg Active Color.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'pagination_link_bg_color_active', $new_options );
}

// Products Archive Pagination section.
function section_pagination_style_before_section_end( $widget, $args ) {
	$controls_manager = \Elementor\Plugin::instance()->controls_manager;
	update_controls_style_tab_pagination_section( $controls_manager, $widget );
}
// Theme Settings.
if ( \Vamtam_Elementor_Utils::is_widget_mod_active( 'wc-archive-products' ) ) {
	add_action( 'elementor/element/wc-archive-products/section_pagination_style/before_section_end', __NAMESPACE__ . '\section_pagination_style_before_section_end', 10, 2 );
}

// Products_Base, before render_content.
function products_base_before_render_content( $widget ) {
    $widget_name = $widget->get_name();
    if ( $widget->get_name() === 'global' ) {
        $widget_name = $widget->get_original_element_instance()->get_name();
    }

	$products_widgets = [
		'woocommerce-product-related',
		'woocommerce-product-upsell',
		'woocommerce-products',
		'wc-archive-products',
	];

	if ( in_array( $widget_name, $products_widgets ) ) {
		// Theme Settings.
		if ( \Vamtam_Elementor_Utils::is_widget_mod_active( $widget_name ) ) {
			do_action( 'vamtam_before_products_widget_before_render_content', $widget_name );
		}
	}
}
add_action( 'elementor/widget/before_render_content', __NAMESPACE__ . '\products_base_before_render_content', 10, 1 );
