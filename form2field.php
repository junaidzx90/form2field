<?php
 /**
 *
 * @link              https://github.com/
 * @since             1.0.0
 * @package           Form2field
 *
 * @wordpress-plugin
 * Plugin Name:       Form2field
 * Plugin URI:        https://github.com/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Mql4Expert.com
 * Author URI:        Mql4Expert.com
 * Text Domain:       form2field
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
define( 'PLUGIN_NAME', 'form2field' );
define( 'Fieldfrm_PATH', plugin_dir_path( __FILE__ ) );

register_activation_hook( __FILE__, 'activate_form2field_cplgn' );
register_deactivation_hook( __FILE__, 'deactivate_form2field_cplgn' );

// Activision function
function activate_form2field_cplgn(){
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    $form2field_v1 = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}form2field_v1` (
        `ID` INT NOT NULL AUTO_INCREMENT,
        `user_id` INT NOT NULL,
        `username` VARCHAR(255) NOT NULL,
        `field` VARCHAR(255) NOT NULL,
        `account_number` INT NOT NULL,
        PRIMARY KEY (`ID`)) ENGINE = InnoDB";
        dbDelta($form2field_v1);
}

// Dectivision function
function deactivate_form2field_cplgn(){
    // Nothing For Now
}

// Admin Enqueue Scripts
add_action('admin_enqueue_scripts',function(){
    wp_register_script( PLUGIN_NAME, plugin_dir_url( __FILE__ ).'js/form2field-admin.js', array(), 
    microtime(), true );
    wp_localize_script( PLUGIN_NAME, 'form2field_actions', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' )
    ) );
});

// WP Enqueue Scripts
add_action('wp_enqueue_scripts',function(){
    wp_register_style( PLUGIN_NAME, plugin_dir_url( __FILE__ ).'css/form2field-public.css', array(), microtime(), 'all' );
    wp_enqueue_style(PLUGIN_NAME);

    wp_register_script( PLUGIN_NAME, plugin_dir_url( __FILE__ ).'js/form2field-public.js', array(), 
    microtime(), true );
    wp_enqueue_script(PLUGIN_NAME);
    wp_localize_script( PLUGIN_NAME, 'form2field_actions', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'nonce' => wp_create_nonce( 'nonces' )
    ) );
});

// Register Menu
add_action('admin_menu', function(){
    add_menu_page( 'Form2field', 'Form2field', 'manage_options', 'form2field', 'form2field_menupage_display', 'dashicons-format-aside', 45 );
});

// Menu callback funnction
function form2field_menupage_display(){
    wp_enqueue_script(PLUGIN_NAME);
    ?>
    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row">Short Code</th>
                <td><input type="text" readonly value='[form2field_v1]'></td>
            </tr>
            <tr>
                <th scope="row">Form Background</th>
                <td><input type="checkbox" name="form-bg" id="form-bg" value="<?php echo (get_option( 'form_form2field__bg') == 'checked'? 1: 0); ?>" <?php echo get_option( 'form_form2field__bg'); ?> ></td>
            </tr>
        </tbody>
    </table>
    <?php
}

// Output with Shortcode
add_shortcode('form2field_v1', 'form2field_output');
require_once 'inc/form2field-output.php';

/**
 * { AJAX CALLING FOR FORM BG UPDATE }
 */
add_action("wp_ajax_form2field_bg_update", "form2field_bg_update");
add_action("wp_ajax_nopriv_form2field_bg_update", "form2field_bg_update");
function form2field_bg_update(){
    if(isset($_POST['data'])){
        if($_POST['data'] === 'true'){
            add_option( 'form_form2field__bg', 'checked' );
            update_option( 'form_form2field__bg', 'checked' );
            die;
        }
        if($_POST['data'] === 'false'){
            update_option( 'form_form2field__bg', 'none' );
            die;
        }
    }
    die;
}

/**
 * { AJAX CALLING FOR INSERTING AND UPDATING }
 */
add_action("wp_ajax_form2field_data_check", "form2field_data_check");
add_action("wp_ajax_nopriv_form2field_data_check", "form2field_data_check");
function form2field_data_check(){
    if(wp_verify_nonce( $_POST['nonce'], 'nonces' )){
        global $wpdb,$current_user;
        $number = intval($_POST['number']);
        $field = sanitize_text_field($_POST['field']);

        if(!empty($number) && !empty($field)){
            $table = $wpdb->prefix.'form2field_v1';
            $data = $wpdb->get_row("SELECT * FROM $table WHERE user_id = $current_user->ID");

            if($data){
                $wpdb->update($table, array('account_number' => $number,'field' => $field ),array("user_id" => $current_user->ID),array('%d','%s'),array('%d'));
            }else{
                $wpdb->insert($table, array('user_id' => $current_user->ID,'account_number' => $number, 'username' => $current_user->display_name, 'field' => $field, ),array('%d','%d','%s','%s')); 
            }

            if ( !is_wp_error( $wpdb ) ) {
                echo wp_json_encode(array('success' => 'Success'));
                wp_die();
            }else{
                echo wp_json_encode(array('error' => 'Error.'));
                wp_die();
            }
        }
        
        wp_die();
    }
    wp_die();
}