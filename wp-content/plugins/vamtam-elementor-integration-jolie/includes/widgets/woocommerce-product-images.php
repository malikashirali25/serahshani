<?php
namespace VamtamElementor\Widgets\ProductImages;

use \ElementorPro\Modules\Woocommerce\Widgets\Product_Images as Elementor_Product_Images;
// Extending the WC Product Images widget.

// Is WC Widget.
if ( ! vamtam_has_woocommerce() ) {
	return;
}

// Is Pro Widget.
if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
	return;
}

// Theme preferences.
if ( ! \Vamtam_Elementor_Utils::is_widget_mod_active( 'woocommerce-product-images' ) ) {
	return;
}

if ( vamtam_theme_supports( 'woocommerce-product-images--disable-image-link' ) ) {
	function add_disable_image_link_control( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => 'sale_flash',
		] );

		$widget->add_control(
			'disable_image_link',
		[
				'label' => __( 'Disable Image Link', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'prefix_class' => 'vamtam-has-',
				'return_value' => 'disable-image-link',
				'description' => __( 'Disables opening the image on a new tab or Elementor lightbox. Doesn\'t disable WC\'s lightbox (if enabled).', 'vamtam-elementor-integration' ),
			]
		);

		$widget->end_injection();
	}

	function section_product_gallery_style_before_section_end( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		add_disable_image_link_control( $controls_manager, $widget );
	}
	add_action( 'elementor/element/woocommerce-product-images/section_product_gallery_style/before_section_end', __NAMESPACE__ . '\section_product_gallery_style_before_section_end', 10, 2 );

	// Vamtam_Widget_Product_Images.
	function widgets_registered() {
		if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
			return;
		}

		if ( ! class_exists( '\ElementorPro\Modules\Woocommerce\Widgets\Product_Images' ) ) {
			return; // Elementor's autoloader acts weird sometimes.
		}

		class Vamtam_Widget_Product_Images extends Elementor_Product_Images {
			public $extra_depended_scripts = [
				'vamtam-woocommerce-product-images',
			];

			// Extend constructor.
			public function __construct($data = [], $args = null) {
				parent::__construct($data, $args);

				$this->register_assets();

				$this->add_extra_script_depends();
			}

			// Register the assets the widget depends on.
			public function register_assets() {
				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

				wp_register_script(
					'vamtam-woocommerce-product-images',
					VAMTAM_ELEMENTOR_INT_URL . 'assets/js/widgets/woocommerce-product-images/vamtam-woocommerce-product-images' . $suffix . '.js',
					[
						'elementor-frontend'
					],
					\VamtamElementorIntregration::PLUGIN_VERSION,
					true
				);
			}

			// Assets the widget depends upon.
			public function add_extra_script_depends() {
				// Scripts
				foreach ( $this->extra_depended_scripts as $script ) {
					$this->add_script_depends( $script );
				}
			}

			// Override.
			public function render() {
				$settings = $this->get_settings_for_display();
				global $product;

				$product = wc_get_product();

				if ( empty( $product ) ) {
					return;
				}

				if ( 'yes' === $settings['sale_flash'] ) {
					wc_get_template( 'loop/sale-flash.php' );
				}

				wc_get_template( 'single-product/product-image.php' );

				// On render widget from Editor - trigger the init manually.
				if ( wp_doing_ajax() ) {
					?>
					<script>
						jQuery( '.woocommerce-product-gallery' ).each( function() {
							jQuery( this ).wc_product_gallery();
						} );
					</script>
					<?php
				}
			}
		}

		// Replace current products widget with our extended version.
		$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
		$widgets_manager->unregister( 'woocommerce-product-images' );
		$widgets_manager->register( new Vamtam_Widget_Product_Images );
	}
	add_action( \Vamtam_Elementor_Utils::get_widgets_registration_hook(), __NAMESPACE__ . '\widgets_registered', 100 );
}

if ( vamtam_theme_supports( 'woocommerce-product-images--sale-flash-section' ) ) {
	function add_vamtam_sale_flash_section( $controls_manager, $widget ) {
		\Vamtam_Elementor_Utils::remove_control( $controls_manager, $widget, 'sale_flash' );

		$widget->start_controls_section(
			'vamtam_sale_flash_style',
			[
				'label' => __( 'Sale Flash', 'vamtam-elementor-integration' ),
				'tab' => $controls_manager::TAB_STYLE,
			]
		);

		$widget->add_control(
			'sale_flash',
			[
				'label' => __( 'Sale Flash', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'label_on' => __( 'Show', 'vamtam-elementor-integration' ),
				'label_off' => __( 'Hide', 'vamtam-elementor-integration' ),
				'render_type' => 'template',
				'return_value' => 'yes',
				'default' => 'yes',
				'prefix_class' => 'vamtam-has-onsale-',
			]
		);

		$widget->add_control(
			'onsale_text_color',
			[
				'label' => __( 'Text Color', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} span.onsale' => 'color: {{VALUE}}',
				],
				'condition' => [
					'sale_flash' => 'yes',
				],
			]
		);

		$widget->add_control(
			'onsale_text_background_color',
			[
				'label' => __( 'Background Color', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .onsale' => '--vamtam-onsale-bg-color: {{VALUE}}',
				],
				'condition' => [
					'sale_flash' => 'yes',
				],
			]
		);

		$widget->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'onsale_typography',
				'selector' => '{{WRAPPER}} span.onsale',
				'condition' => [
					'sale_flash' => 'yes',
				],
			]
		);

		$widget->add_control(
			'onsale_border_radius',
			[
				'label' => __( 'Border Radius', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} span.onsale' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'sale_flash' => 'yes',
				],
			]
		);

		$widget->add_control(
			'onsale_width',
			[
				'label' => __( 'Width', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} span.onsale' => 'min-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'sale_flash' => 'yes',
				],
			]
		);

		$widget->add_control(
			'onsale_height',
			[
				'label' => __( 'Height', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} span.onsale' => 'min-height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'sale_flash' => 'yes',
				],
			]
		);

		$widget->add_control(
			'onsale_horizontal_position',
			[
				'label' => __( 'Position', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'vamtam-elementor-integration' ),
						'icon' => 'eicon-h-align-left',
					],
					'right' => [
						'title' => __( 'Right', 'vamtam-elementor-integration' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} span.onsale' => '{{VALUE}}',
				],
				'selectors_dictionary' => [
					'left' => 'right: auto; left: 0',
					'right' => 'left: auto; right: 0',
				],
				'condition' => [
					'sale_flash' => 'yes',
				],
				'prefix_class' => 'vamtam-onsale-',
			]
		);

		$widget->add_control(
			'onsale_distance',
			[
				'label' => __( 'Distance', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range' => [
					'px' => [
						'min' => -20,
						'max' => 20,
					],
					'em' => [
						'min' => -2,
						'max' => 2,
					],
				],
				'selectors' => [
					'{{WRAPPER}} span.onsale' => 'margin: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'sale_flash' => 'yes',
				],
			]
		);

		$widget->end_controls_section();
	}
	function section_product_gallery_style_after_section_end( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		add_vamtam_sale_flash_section( $controls_manager, $widget );
	}
	add_action( 'elementor/element/woocommerce-product-images/section_product_gallery_style/after_section_end', __NAMESPACE__ . '\section_product_gallery_style_after_section_end', 10, 2 );
}
