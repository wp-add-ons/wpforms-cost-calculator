<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Number text field.
 *
 * @since 1.0.0
 */
class Superaddon_WPForms_Field_Number_Format extends WPForms_Field {
	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		// Define field type information.
		$this->name  = esc_html__( 'Number Format', 'cost-calculator-for-wpforms' );
		$this->type  = 'number_format';
		$this->icon  = 'fa-hashtag';
		$this->order = 130;
		add_action( 'wpforms_frontend_js', array( $this, 'frontend_js' ) );
		add_action( 'wpforms_frontend_css', array( $this, 'frontend_css' ) );
		add_action( 'wpforms_builder_enqueues', array( $this, 'addmin_js' ) );
	}
	function addmin_js(){
		wp_enqueue_script(
				'wpforms-calculator',
				SUPERADDONS_WPFORMS_CL_FIELD_PLUGIN_URL . 'libs/wpforms-calculator-admin.js',
				array("jquery"),
				'1.3.8',
				true
			);
	}
	function frontend_js($forms){
		if (
			wpforms()->frontend->assets_global() ||
			true === wpforms_has_field_type( 'number_format', $forms, true )
		) {
			wp_enqueue_script(
				'evaluator',
				SUPERADDONS_WPFORMS_CL_FIELD_PLUGIN_URL . 'libs/formula_evaluator-min.js',
				array("jquery"),
				'1.3.8',
				true
			);
			wp_enqueue_script(
				'autoNumeric',
				SUPERADDONS_WPFORMS_CL_FIELD_PLUGIN_URL . 'libs/autoNumeric-1.9.45.js',
				array("jquery"),
				'1.9.45',
				true
			);
			wp_enqueue_script(
				'wpforms-calculator',
				SUPERADDONS_WPFORMS_CL_FIELD_PLUGIN_URL . 'libs/calculator.js',
				array("jquery","evaluator","autoNumeric"),
				'1.0',
				true
			);
		}
	}
	function frontend_css($forms){
		if (
			wpforms()->frontend->assets_global() ||
			true === wpforms_has_field_type( 'number_format', $forms, true )
		) {
			wp_enqueue_style(
				'wpforms-calculator',
				SUPERADDONS_WPFORMS_CL_FIELD_PLUGIN_URL . 'libs/calculator.css',
				array(),
				'1.0',
				true
			);
		}
	}
	function formula_options($type,$field){
		$output ="";
		$check = get_option( '_redmuber_item_1522');
		$disabled = array();
		$text_pro = "";
		if($check != "ok"){
			$disabled = array('disabled' => 'disabled');
			$text_pro = " (Pro Version)";
		}
		switch($type) {
			case "number_format":
				$data = $this->field_element(
					'toggle',
					$field,
					[
						'slug'    => 'number_format',
						'value'   => isset( $field['number_format'] ) ? '1' : '0',
						'desc'    => esc_html__( 'Number Format', 'cost-calculator-for-wpforms' ),
						'tooltip' => esc_html__( 'Check this option to enable using number formats.', 'cost-calculator-for-wpforms' ),
					],
					false
				);
				$output  = $this->field_element( 'row', $field, array( 'slug' => 'number_format', 'content' => $data ), false );
				//data
				$data = $this->field_element(
					'text',
					$field,
					[
						'slug'    => 'number_format_symbols',
						'attrs' => $disabled,
						'value'   => isset( $field['number_format_symbols'] ) ? $field['number_format_symbols'] : '',
						'desc'    => esc_html__( 'Symbols', 'cost-calculator-for-wpforms' ),
					],
					false
				);
				$lable  = $this->field_element( 'label', $field, array( 'slug' => 'label', 'value' => esc_html__( 'Symbols'.$text_pro, 'cost-calculator-for-wpforms' )), false );
				$class ="";
				if( empty( $field['number_format']) ||  $field['number_format'] != "1"  ){
					$class ="wpforms-hidden";
				}
				$output  .= $this->field_element( 'row', $field, array( 'class'   => $class,'slug' => 'number_format_symbols', 'content' => $lable. $data ), false );	
				//data
				$data = $this->field_element(
					'select',
					$field,
					[
						'slug'    => 'number_format_symbols_position',
						'value'   => isset( $field['number_format_symbols_position'] ) ? $field['number_format_symbols_position'] : 's',
						'desc'    => esc_html__( 'Symbols position ', 'cost-calculator-for-wpforms' ),
						'options' => array(
							'p'  => esc_html__( 'Left', 'cost-calculator-for-wpforms' ),
							's' => esc_html__( 'Right', 'cost-calculator-for-wpforms' ),
						),
					],
					false
				);
				$lable  = $this->field_element( 'label', $field, array( 'slug' => 'label', 'value' => esc_html__( 'Symbols position', 'cost-calculator-for-wpforms' )), false );
				$class ="";
				if( empty( $field['number_format']) ||  $field['number_format'] != "1"  ){
					$class ="wpforms-hidden";
				}
				$output  .= $this->field_element( 'row', $field, array( 'class'   => $class,'slug' => 'number_format_symbols_position', 'content' => $lable. $data ), false );	
				//data
				$data = $this->field_element(
					'text',
					$field,
					[
						'slug'    => 'number_format_thousand_sep',
						'attrs' => $disabled,
						'value'   => isset( $field['number_format_thousand_sep'] ) ? $field['number_format_thousand_sep'] : ',',
						'desc'    => esc_html__( 'Thousand separator', 'cost-calculator-for-wpforms' ),
					],
					false
				);
				$lable  = $this->field_element( 'label', $field, array( 'slug' => 'label', 'value' => esc_html__( 'Thousand separator'.$text_pro, 'cost-calculator-for-wpforms' )), false );
				$class ="";
				if( empty( $field['number_format']) ||  $field['number_format'] != "1"  ){
					$class ="wpforms-hidden";
				}
				$output  .= $this->field_element( 'row', $field, array( 'class'   => $class,'slug' => 'number_format_thousand_sep', 'content' => $lable. $data ), false );	
				//data
				$data = $this->field_element(
					'text',
					$field,
					[
						'slug'    => 'number_format_decimal_sep',
						'attrs' => $disabled,
						'value'   => isset( $field['number_format_decimal_sep'] ) ? $field['number_format_decimal_sep'] : '.',
						'desc'    => esc_html__( 'Decimal separator', 'cost-calculator-for-wpforms' ),
					],
					false
				);
				$lable  = $this->field_element( 'label', $field, array( 'slug' => 'label', 'value' => esc_html__( 'Decimal separator'.$text_pro, 'cost-calculator-for-wpforms' )), false );
				$class ="";
				if( empty( $field['number_format']) ||  $field['number_format'] != "1"  ){
					$class ="wpforms-hidden";
				}
				$output  .= $this->field_element( 'row', $field, array( 'class'   => $class,'slug' => 'number_format_decimal_sep', 'content' => $lable. $data ), false );	
				//data
				$data = $this->field_element(
					'text',
					$field,
					[
						'slug'    => 'number_format_num_decimals',
						'attrs' => $disabled,
						'value'   => isset( $field['number_format_num_decimals'] ) ? $field['number_format_num_decimals'] : '2',
						'desc'    => esc_html__( 'Number of decimals', 'cost-calculator-for-wpforms' ),
					],
					false
				);
				$lable  = $this->field_element( 'label', $field, array( 'slug' => 'label', 'value' => esc_html__( 'Number of decimals'.$text_pro, 'cost-calculator-for-wpforms' )), false );
				$class ="";
				if( empty( $field['number_format']) ||  $field['number_format'] != "1"  ){
					$class ="wpforms-hidden";
				}
				$output  .= $this->field_element( 'row', $field, array( 'class'   => $class,'slug' => 'number_format_num_decimals', 'content' => $lable. $data ), false );
				break;
		}
        printf("%s",$output);     
    }
	/**
	 * Field options panel inside the builder.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field Field data.
	 */
	public function field_options( $field ) {
		/*
		 * Basic field options.
		 */
		// Options open markup.
		$args = array(
			'markup' => 'open',
		);
		$this->field_option( 'basic-options', $field, $args );
		// Label.
		$this->field_option( 'label', $field );
		$this->formula_options("number_format", $field );
		// Description.
		$this->field_option( 'description', $field );
		// Required toggle.
		$this->field_option( 'required', $field );
		// Options close markup.
		$args = array(
			'markup' => 'close',
		);
		$this->field_option( 'basic-options', $field, $args );
		/*
		 * Advanced field options.
		 */
		// Options open markup.
		$args = [
			'markup' => 'open',
		];
		$this->field_option( 'advanced-options', $field, $args );
		// Size.
		$this->field_option( 'size', $field );
		// Placeholder.
		$this->field_option( 'placeholder', $field );
		// Default value.
		$this->field_option( 'default_value', $field );
		// Custom CSS classes.
		$this->field_option( 'css', $field );
		// Hide label.
		$this->field_option( 'label_hide', $field );
		// Options close markup.
		$args = [
			'markup' => 'close',
		];
		$this->field_option( 'advanced-options', $field, $args );
	}
	/**
	 * Field preview inside the builder.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field Field data.
	 */
	public function field_preview( $field ) {
		// Define data.
		$placeholder = ! empty( $field['placeholder'] ) ? esc_attr( $field['placeholder'] ) : '';
		// Label.
		$this->field_preview_option( 'label', $field );
		// Primary input.
		?>
		<input type="text" placeholder="<?php echo esc_attr( $placeholder ) ?>" class="primary-input" readonly>
		<?php
		// Description.
		$this->field_preview_option( 'description', $field );
	}
	/**
	 * Field display on the form front-end.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field      Field data.
	 * @param array $deprecated Deprecated, not used.
	 * @param array $form_data  Form data.
	 */
	public function field_display( $field, $deprecated, $form_data ) {
		// Define data.
		$primary = $field['properties']['inputs']['primary'];
		$datas= array();
		$class = $primary['class'];
		if( ! empty( $field['number_format'] ) && $field['number_format']  == "1"  ) {
			// Primary field.
			$datas=  array_merge($datas, array(
							"data-a-sign" => ! empty( $field['number_format_symbols'] ) ? $field['number_format_symbols'] : '',
							"data-a-dec"  =>! empty( $field['number_format_decimal_sep'] ) ? $field['number_format_decimal_sep'] : '',
							"data-a-sep"  =>! empty( $field['number_format_thousand_sep'] ) ? $field['number_format_thousand_sep'] : '',
							"data-m-dec"  => ! empty( $field['number_format_num_decimals'] ) ? $field['number_format_num_decimals'] : '',
							"data-p-sign"  => ! empty( $field['number_format_symbols_position'] ) ? $field['number_format_symbols_position'] : '',
							));
			$class = array_merge(array("wpforms-number-format"),$primary['class']);
		}
		$attr = array_merge($datas,$primary['attr']);
		$data_attr = $this->wpforms_html_attributes( $primary['id'], $class, $primary['data'], $attr );
		?>
		<input type="text" <?php echo esc_attr($data_attr);  ?> <?php echo esc_attr( $primary['required'] ); ?>>
		<?php
	}
	function wpforms_html_attributes( $id = '', $class = array(), $datas = array(), $atts = array(), $echo = false ) {
		$id    = trim( $id );
		$parts = array();
		if ( ! empty( $id ) ) {
			$id = sanitize_html_class( $id );
			if ( ! empty( $id ) ) {
				$parts[] = 'id="' . $id . '"';
			}
		}
		if ( ! empty( $class ) ) {
			$class = wpforms_sanitize_classes( $class, true );
			if ( ! empty( $class ) ) {
				$parts[] = 'class="' . $class . '"';
			}
		}
		if ( ! empty( $datas ) ) {
			foreach ( $datas as $data => $val ) {
				$parts[] = 'data-' . sanitize_html_class( $data ) . '="' . esc_attr( $val ) . '"';
			}
		}
		if ( ! empty( $atts ) ) {
			foreach ( $atts as $att => $val ) {
					if ( $att[0] === '[' ) {
						// Handle special case for bound attributes in AMP.
						$escaped_att = '[' . sanitize_html_class( trim( $att, '[]' ) ) . ']';
					} else {
						$escaped_att = sanitize_html_class( $att );
					}
					$parts[] = $escaped_att . '="' . esc_attr( $val ) . '"';
			}
		}
		$output = implode( ' ', $parts );
		if ( $echo ) {
			echo wp_kses_post($output); 
		} else {
			return trim( $output );
		}
	}
}
new Superaddon_WPForms_Field_Number_Format();