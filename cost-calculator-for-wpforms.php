<?php
/**
 * Plugin Name: Cost Calculator for WPForms
 * Plugin URI: https://add-ons.org/plugin/wpforms-cost-calculator/
 * Description: Create forms with field values calculated based in other form field values for WPForms
 * Version: 1.3.1
 * Author: add-ons.org
 * Author URI: https://add-ons.org/
*/
if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
define( 'SUPERADDONS_WPFORMS_CL_FIELD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SUPERADDONS_WPFORMS_CL_FIELD_PATH', plugin_dir_path( __FILE__ ) );
class Superaddons_WPForms_Cost_Calculator_Load { 
    function __construct(){ 
        add_action( 'wpforms_loaded', array($this,"load_plugin") );
        add_action( 'wpforms_fields_show_options_setting', '__return_true' );
        add_filter("wpforms_payment_fields",array($this,"custom_total"));
        include SUPERADDONS_WPFORMS_CL_FIELD_PATH."superaddons/check_purchase_code.php";
        new Superaddons_Check_Purchase_Code( 
            array("plugin" => "cost-calculator-for-wpforms/cost-calculator-for-wpforms.php",
                "id"=>"1522",
                "pro"=>"https://add-ons.org/plugin/wpforms-cost-calculator/",
                "plugin_name"=> "Cost Calculator for WPForms",
                "document"=> "https://add-ons.org/demo-wpforms-cost-calculator/",
            )
        );
    }
    function custom_total($fields){
        $fields[] = "calculator";
        return $fields;
    }
    function load_plugin(){
        include SUPERADDONS_WPFORMS_CL_FIELD_PATH."calculated-field.php";
        include SUPERADDONS_WPFORMS_CL_FIELD_PATH."number_format.php";
    }
}
new Superaddons_WPForms_Cost_Calculator_Load;
if(!class_exists('Superaddons_List_Addons')) {  
    include SUPERADDONS_WPFORMS_CL_FIELD_PATH."add-ons.php"; 
}