<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Number text field.
 *
 * @since 1.0.0
 */
class Superaddon_WPForms_Field_Cost_Calculator extends WPForms_Field {
	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		// Define field type information.
		$this->name  = esc_html__( 'Calculator', 'cost-calculator-for-wpforms' );
		$this->type  = 'calculator';
		$this->icon  = 'fa-hashtag';
		$this->order = 130;
		add_action( 'wpforms_frontend_js', array( $this, 'frontend_js' ) );
		add_action( 'wpforms_frontend_css', array( $this, 'frontend_css' ) );
		add_action( 'wpforms_builder_enqueues', array( $this, 'addmin_js' ) );
		add_action( 'wpforms_builder_enqueues', array( $this, 'addmin_css' ) );
		add_filter('wpforms_get_conditional_logic_form_fields_supported', array($this,"add_logic"));
	}

	function add_edit_entry($fields,$entry,$form_data){
		foreach( $form_data["fields"] as $key=> $data ){
			if($data["type"] == "calculator" || $data["type"] == "number_format"  ){
				$fields[ $key ] = array("name"=>$data["label"],"value"=>"","type"=>$data["type"]);
			}
		}
		return $fields;
	}
	function edit_entry($data,$type){
		$type = $type["type"];
		if( $type == "calculator" || $type == "number_format") {
			return true;
		}
		return $data;
	}
	function add_logic($allowed_form_fields){
		array_push( $allowed_form_fields,"calculator","number_format" );
		return $allowed_form_fields;
	}
	function custom_total($fields){
        $fields["calculator"];
        return $fields;
    }
	function addmin_js(){
		wp_enqueue_script(
				'tribute',
				SUPERADDONS_WPFORMS_CL_FIELD_PLUGIN_URL . 'libs/tribute/tribute.min.js',
				array("jquery"),
			);
		wp_enqueue_script(
				'wpforms-calculator',
				SUPERADDONS_WPFORMS_CL_FIELD_PLUGIN_URL . 'libs/wpforms-calculator-admin.js',
				array("jquery","tribute"),
				'1.3.8',
				true
			);
		$check = get_option( '_redmuber_item_35600080');
		$datas = array();
		$datas_done = array();
		$text_pro = "";
		$disable_pro = "";
		if($text_pro == "ok"){
			$text_pro = "-Pro version";
			$disable_pro = " disabled";
		}
		$datas[] = array("key"=>"if( condition, true, false)", "value"=>"if( condition, true, false)");
		$datas[] = array("key"=>"if( condition, true, if(condition, true, false))", "value"=>"if( condition, true, if( condition, true, false))");
		$datas[] = array("key"=>"days( date_end, date_start)", "value"=>"days( end, start)");
		$datas[] = array("key"=>"months( date_end, date_start)", "value"=>"months( end, start)");
		$datas[] = array("key"=>"years( date_end, date_start)", "value"=>"years( end, start)");
		$datas[] = array("key"=>"round( number )", "value"=>"round( number )");
		$datas[] = array("key"=>"round2( number, decimal)", "value"=>"round2( number, 2)");
		$datas[] = array("key"=>"floor( number )", "value"=>"floor( number )");
		$datas[] = array("key"=>"floor2( number, decimal)", "value"=>"floor2( number, 2)");
		$datas[] = array("key"=>"ceil( number )", "value"=>"ceil( number )");
		$datas[] = array("key"=>"mod( number % number)", "value"=>"mod( number, number)");
		$datas[] = array("key"=>"age( Birth date )", "value"=>"age()");
		$datas[] = array("key"=>"age2( Birth date, Age at the Date of)", "value"=>"age2( birth_date, date)");
		$datas[] = array("key"=>"now (Current date)", "value"=>"now");		
		$datas[] = array("key"=>"==", "value"=>"==");
		$datas[] = array("key"=>"pi = 3.14", "value"=>"pi");
		$datas[] = array("key"=>"e = 2.71", "value"=>"e");
		$datas[] = array("key"=>"abs( -3 ) = 3", "value"=>"abs( number )");
		$datas[] = array("key"=>"sqrt( 16 ) = 4", "value"=>"sqrt( number )");
		$datas[] = array("key"=>"sin( 0 ) = 0", "value"=>"sin( number )");
		$datas[] = array("key"=>"cos( 0 ) = 1", "value"=>"cos( number )");
		$datas[] = array("key"=>"pow( 2,3 ) = 8", "value"=>"pow( number , number )");
		$datas[] = array("key"=>"random( number start , number end ) ", "value"=>"random( number, number )");
		$datas[] = array("key"=>"mod( 2,3) = 1", "value"=>"mod( number, number )");
		$datas[] = array("key"=>"avg( 10,20,60,...) = 30", "value"=>"avg( number, number )");
		$datas[] = array("key"=>"min( number 1, number 2, ...)", "value"=>"min( number1, number2)");
		$datas[] = array("key"=>"max( number 1, number 2, ...)", "value"=>"max( number1, number2)");
		foreach( $datas as $data ){
        	$datas_done[] = array("key"=>$data["key"].$text_pro,"value"=>$data["value"]);
        }
        $datas_done[] = array("key"=>"a + b", "value"=>"+");
		$datas_done[] = array("key"=>"a - b", "value"=>"-");
		$datas_done[] = array("key"=>"a / b", "value"=>"/");
		$datas_done[] = array("key"=>"a * b", "value"=>"*");
		wp_localize_script( "wpforms-calculator", "wpforms_calculator", array("data"=>$datas_done) );
	}
	function addmin_css($forms){
		wp_enqueue_style(
				'tribute',
				SUPERADDONS_WPFORMS_CL_FIELD_PLUGIN_URL . 'libs/tribute/tribute.css',
			);
	}
	function frontend_js($forms){
		if (
			wpforms()->frontend->assets_global() ||
			true === wpforms_has_field_type( 'calculator', $forms, true )
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
				time(),
				true
			);
			wp_localize_script( "wpforms-calculator", "wpforms_calculator", array("pro"=>get_option( '_redmuber_item_1522')) );
		}
	}
	function frontend_css($forms){
		if (
			wpforms()->frontend->assets_global() ||
			true === wpforms_has_field_type( 'calculator', $forms, true )
		) {
			wp_enqueue_style(
				'wpforms-calculator',
				SUPERADDONS_WPFORMS_CL_FIELD_PLUGIN_URL . 'libs/calculator.css',
				array(),
				
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
			case "formula":
				$value   = ! empty( $field['formula'] ) ? esc_html( $field['formula'] ) : '';
			$tooltip = esc_html__( 'Enter text for the form field description.', 'cost-calculator-for-wpforms' );
			$toggle  = '<a href="#" class="toggle-smart-tag-display toggle-unfoldable-cont" data-type="fields"><i class="fa fa-tags"></i><span>' . esc_html__( 'Show Smart Tags', 'cost-calculator-for-wpforms' ) . '</span></a>';
			$output  = $this->field_element( 'label',    $field, array( 'slug' => 'formula', 'value' => esc_html__( 'Formula', 'cost-calculator-for-wpforms' ), 'tooltip' => $tooltip,'after_tooltip' => $toggle ), false );
			$output .= $this->field_element( 'textarea', $field, array( 'slug' => 'formula', 'value' => $value ), false );
			$output  = $this->field_element( 'row',      $field, array( 'slug' => 'formula', 'content' => $output ), false );
				break;
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
			case "style":
				$value   = ! empty( $field['style'] ) ? esc_html( $field['style'] ) : '';
				$output  = $this->field_element( 'label',    $field, array( 'slug' => 'style', 'value' => esc_html__( 'Style', 'cost-calculator-for-wpforms' )), false );
				$output .= $this->field_element( 'select', $field, array( 'slug' => 'style', 'value' => $value,'options' => array(
								'input'  => esc_html__( 'Input', 'cost-calculator-for-wpforms' ),
								'label' => esc_html__( 'Label', 'cost-calculator-for-wpforms' ),
							) ), false );
				$output  = $this->field_element( 'row',      $field, array( 'slug' => 'style', 'content' => $output ), false );
				break;
			case "total_payment":
				$value   = isset( $field['total_payment'] ) ? $field['total_payment'] : '0';
				$tooltip = esc_html__( 'Payment field', 'cost-calculator-for-wpforms' );
				// Build output.
				$output = $this->field_element(
					'toggle',
					$field,
					[
						'slug'    => 'total_payment',
						'value'   => $value,
						'attrs' => $disabled,
						'desc'    => esc_html__( 'Payment field'.$text_pro, 'cost-calculator-for-wpforms' ),
						'tooltip' => $tooltip,
					],
					false
				);
				$output = $this->field_element(
					'row',
					$field,
					[
						'slug'    => 'total_payment',
						'content' => $output,
					],
					false
				);
				break;
			case "input_lable":
				$value   = isset( $field['input_lable'] ) ? $field['input_lable'] : '0';
				$tooltip = esc_html__( 'Use lable text', 'cost-calculator-for-wpforms' );
				// Build output.
				$output = $this->field_element(
					'toggle',
					$field,
					[
						'slug'    => 'input_lable',
						'value'   => $value,
						'desc'    => esc_html__( 'Remove input and Use lable text', 'cost-calculator-for-wpforms' ),
						'tooltip' => $tooltip,
					],
					false
				);
				$output = $this->field_element(
					'row',
					$field,
					[
						'slug'    => 'input_lable',
						'content' => $output,
					],
					false
				);
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
		$this->formula_options("formula", $field );
		$this->formula_options("number_format", $field );
		
		$this->formula_options("input_lable", $field );
		$this->formula_options("total_payment", $field );
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
		// Custom CSS classes.
		$this->field_option( 'css', $field );
		$this->formula_options("style", $field );
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
		$placeholder = ! empty( $field['input_lable'] ) ? $field['input_lable'] : '';
		// Label.
		$this->field_preview_option( 'label', $field );
		?>
		<input value="" type="text" placeholder="<?php echo esc_attr( $placeholder ) ?>" class="primary-input" readonly>
		<?php
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
		$formula = ! empty( $field['formula'] ) ? $field['formula'] : '';
		$class = $primary['class'];
		if( ! empty( $field['total_payment'] ) && $field['total_payment']  == "1"  ) { 
			$class = array_merge(array("wpforms-payment-price"),$class);
		}
		if( isset($_GET["view"]) && isset($_GET["entry_id"]) ) {
			$datas = array();
		}else{
			$datas= array("data-formula"=>$formula,"readonly"=>"readonly");
		}
		if( ! empty( $field['input_lable'] ) && $field['input_lable']  == "1"  ) {
			$class = array_merge(array("wpforms-lable-text"),$class);
		}
		if( ! empty( $field['number_format'] ) && $field['number_format']  == "1"  ) {
			// Primary field.
			$datas=  array_merge($datas, array(
							"data-a-sign" => ! empty( $field['number_format_symbols'] ) ? $field['number_format_symbols'] : '',
							"data-a-dec"  =>! empty( $field['number_format_decimal_sep'] ) ? $field['number_format_decimal_sep'] : '',
							"data-a-sep"  =>! empty( $field['number_format_thousand_sep'] ) ? $field['number_format_thousand_sep'] : '',
							"data-m-dec"  => ! empty( $field['number_format_num_decimals'] ) ? $field['number_format_num_decimals'] : '',
							"data-p-sign"  => ! empty( $field['number_format_symbols_position'] ) ? $field['number_format_symbols_position'] : '',
							));
			$class = array_merge(array("wpforms-number-format"),$class);
		}
		$attr = array_merge($datas,$primary['attr']);
		$type = "text";
		if(  ! empty( $field['style'] ) && $field['style']  == "label"  ) {
			?>
			<div class="wpforms-number-show" <?php echo esc_attr($this->wpforms_html_attributes( "",array(),array(),$attr )) ?>></div>
			<?php
			$type = "hidden";
		}
		$data_attr = $this->wpforms_html_attributes( $primary['id'], $class, $primary['data'], $attr );
		?>
		<input type="<?php echo esc_attr($type) ?>" <?php echo wp_kses_post($data_attr);  ?> <?php echo esc_attr( $primary['required'] ); ?>>
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
	public function format( $field_id, $field_submit, $form_data ) {
		$field = $form_data['fields'][ $field_id ];
		$name  = ! empty( $field['label'] ) ? sanitize_text_field( $field['label'] ) : '';
		if( $field["total_payment"] == 1 ) {
			$amount = wpforms_sanitize_amount( $field_submit );
			wpforms()->process->fields[ $field_id ] = array(
				'name'       => $name,
				'value'      => wpforms_format_amount( $amount, true ),
				'amount'     => wpforms_format_amount( $amount ),
				'amount_raw' => $amount,
				'value_raw' => $amount,
				'currency'   => wpforms_get_currency(),
				'id'         => absint( $field_id ),
				'type'       => $this->type,
			);
		}else {
			wpforms()->process->fields[ $field_id ] = array(
				'name'  => sanitize_text_field( $name ),
				'value' => $this->sanitize_value( $field_submit ),
				'id'    => absint( $field_id ),
				'type'  => $this->type,
			);
		}
	}
	private function sanitize_value( $value ) {
		return $value;
	}
}
new Superaddon_WPForms_Field_Cost_Calculator();