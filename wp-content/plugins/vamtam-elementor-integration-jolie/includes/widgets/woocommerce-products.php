<?php
namespace VamtamElementor\Widgets\Products;

use ElementorPro\Modules\Woocommerce\Widgets\Products as Elementor_Products;

// Extending the WC Products widget.

// Is WC Widget.
if ( ! vamtam_has_woocommerce() ) {
	return;
}

// Is Pro Widget.
if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
	return;
}

// Theme preferences.
if ( ! \Vamtam_Elementor_Utils::is_widget_mod_active( 'woocommerce-products' ) ) {
	return;
}

if ( vamtam_theme_supports( 'woocommerce-products--horizontal-layout' ) ) {
	function update_columns_control( $controls_manager, $widget ) {
		// Columns.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'columns', [
			'selectors' => [
				'{{WRAPPER}}' => '--vamtam-cols: {{VALUE}}',
			],
		] );
		// Columns.
		\Vamtam_Elementor_Utils::replace_control_options( $controls_manager, $widget, 'columns', [
			// This is required cause of https://github.com/elementor/elementor/issues/12947
			'prefix_class' => 'elementor-grid%s-',
		] );
	}
	function add_hr_layout_controls( $controls_manager, $widget ) {
		$widget->add_control(
			'vamtam_use_hr_layout',
			[
				'label' => __( 'Use Horizontal Layout', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'prefix_class' => 'vamtam-has-',
				'return_value' => 'hr-layout',
			]
		);

		// Additional Columns Hint.
		$widget->add_responsive_control(
			'vamtam_additional_cols_hint',
			[
				'label' => esc_html__( 'Additional Columns Hint', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
					'size' => 20,
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--vamtam-col-hint: {{SIZE}}{{UNIT}}',
				],
				'required' => true,
				'condition' => [
					'vamtam_use_hr_layout!' => '',
				],
			]
		);

		$widget->add_control(
			'vamtam_has_nav',
			[
				'label' => __( 'Show Navigation', 'vamtam-elementor-integration' ),
				'description' => __( 'Disabled on mobile devices.', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'prefix_class' => 'vamtam-has-',
				'return_value' => 'nav',
				'condition' => [
					'vamtam_use_hr_layout!' => '',
				],
				'default' => 'nav',
				'render_type' => 'template',
			]
		);
	}

	function section_content_before_section_end( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		add_hr_layout_controls( $controls_manager, $widget );
		update_columns_control( $controls_manager, $widget );
	}
	add_action( 'elementor/element/woocommerce-products/section_content/before_section_end', __NAMESPACE__ . '\section_content_before_section_end', 10, 2 );

	function update_column_gap_control( $controls_manager, $widget ) {
		// Column Gap.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'column_gap', [
			'selectors' => [
				'{{WRAPPER}}' => '--vamtam-col-gap: {{SIZE}}{{UNIT}}',
			],
		] );

	}

	function section_products_style_before_section_end( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		update_column_gap_control( $controls_manager, $widget );
	}
	add_action( 'elementor/element/woocommerce-products/section_products_style/before_section_end', __NAMESPACE__ . '\section_products_style_before_section_end', 10, 2 );

	// Vamtam_Widget_Products.
	function widgets_registered() {
		if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
			return;
		}

		if ( ! class_exists( '\ElementorPro\Modules\Woocommerce\Widgets\Products' ) ) {
			return; // Elementor's autoloader acts weird sometimes.
		}

		class Vamtam_Widget_Products extends Elementor_Products {
			public $extra_depended_scripts = [
				'vamtam-woocommerce-products',
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
					'vamtam-woocommerce-products',
					VAMTAM_ELEMENTOR_INT_URL . 'assets/js/widgets/woocommerce-products/vamtam-woocommerce-products' . $suffix . '.js',
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
		}

		// Replace current products widget with our extended version.
		$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
		$widgets_manager->unregister( 'woocommerce-products' );
		$widgets_manager->register( new Vamtam_Widget_Products );
	}
	add_action( \Vamtam_Elementor_Utils::get_widgets_registration_hook(), __NAMESPACE__ . '\widgets_registered', 100 );
}
