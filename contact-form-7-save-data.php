<?php
/**
* Plugin Name: Save Data Contact Form 7
* Description: This plugin allows create Save Data Contact Form 7 plugin.
* Version: 1.0.1
* Copyright: 2020
* Text Domain: save-data-contact-form-7
* Domain Path: /languages 
*/


if (!defined('ABSPATH')) {
    die('-1');
}
if (!defined('CF7SD_PLUGIN_NAME')) {
    define('CF7SD_PLUGIN_NAME', 'Save Data Contact Form 7');
}
if (!defined('CF7SD_VERSION')) {
    define('CF7SD_VERSION', '1.0.0');
}
if (!defined('CF7SD_PATH')) {
    define('CF7SD_PATH', __FILE__);
}
if (!defined('CF7SD_PLUGIN_DIR')) {
    define('CF7SD_PLUGIN_DIR',plugins_url('', __FILE__));
}
if (!defined('CF7SD_PLUGIN_FILE')) {
    define('CF7SD_PLUGIN_FILE', __FILE__);
}
if (!defined('CF7SD_DOMAIN')) {
    define('CF7SD_DOMAIN', 'save-data-contact-form-7');
}
if (!defined('CF7SD_PREFIX')) {
    define('CF7SD_PREFIX', "cf7sd_");
}
if (!defined('CF7SD_PAGE_SLUG')) {
    define('CF7SD_PAGE_SLUG', "cf7sd_form_entries");
}
if (!defined('CF7SD_BASE_NAME')) {
    define('CF7SD_BASE_NAME', plugin_basename(CF7SD_PLUGIN_FILE));
}



if (!class_exists('CF7SD')) {

    class CF7SD {

        protected static $instance;

        function includes() {
            include_once('admin/cf7sd-backend.php');
            include_once('admin/cf7sd-save-data.php');
        }


        function init() {
            add_action( 'admin_enqueue_scripts', array($this, 'CF7SD_load_admin_script_style'));
            add_action( 'admin_init', array($this, 'CF7SD_load_plugin'), 11 );
            add_filter( 'plugin_row_meta', array( $this, 'CF7SD_plugin_row_meta' ), 10, 2 );
            session_start();
            global $wpdb;
            $table_name = $wpdb->prefix.'cf7sd_forms';
            if( $wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name ) {
                $charset_collate = $wpdb->get_charset_collate();
                $sql = "CREATE TABLE $table_name (
                    form_id bigint(20) NOT NULL AUTO_INCREMENT,
                    form_post_id bigint(20) NOT NULL,
                    form_value longtext NOT NULL,
                    form_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                    PRIMARY KEY  (form_id)
                ) $charset_collate;";

                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                dbDelta( $sql );
            }

            $upload_dir      = wp_upload_dir();
            $cf7sd_dirname = $upload_dir['basedir'].'/cf7sd_uploads';
            if ( ! file_exists( $cf7sd_dirname ) ) {
                wp_mkdir_p( $cf7sd_dirname );
            }
        }


        function CF7SD_load_admin_script_style() {
            wp_enqueue_style( 'CF7SD-back-style', CF7SD_PLUGIN_DIR . '/includes/css/back_style.css', false, '1.0.0' );
            
        }


        function CF7SD_load_plugin() {
            if ( ! ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) ) {
                add_action( 'admin_notices', array($this,'CF7SD_install_error') );
            }
        }


        function CF7SD_install_error() {
            deactivate_plugins( plugin_basename( __FILE__ ) );
            ?>
                <div class="error">
                    <p>
                        <?php _e( ' cf7 calculator plugin is deactivated because it require <a href="plugin-install.php?tab=search&s=contact+form+7">Contact Form 7</a> plugin installed and activated.', CF7COSTCALOC_DOMAIN ); ?>
                    </p>
                </div>
            <?php
        }

        function CF7SD_plugin_row_meta( $links, $file ) {
            if ( CF7SD_BASE_NAME === $file ) {
                $row_meta = array(
                    'rating'    =>  '<a href="https://oceanwebguru.com/save-data-contact-form-7/" target="_blank">Documentation</a> | <a href="https://oceanwebguru.com/contact-us/" target="_blank">Support</a> | <a href="https://wordpress.org/support/plugin/save-data-contact-form-7/reviews/?filter=5" target="_blank"><img src="'.CF7SD_PLUGIN_DIR.'/includes/images/star.png" class="cf7sd_rating_div"></a>',
                );
                return array_merge( $links, $row_meta );
            }
            return (array) $links;
        } 

      
        public static function instance() {
            if (!isset(self::$instance)) {
                self::$instance = new self();
                self::$instance->init();
                self::$instance->includes();
            }
            return self::$instance;
        }
    }
    add_action('plugins_loaded', array('CF7SD', 'instance'));
}



add_action( 'plugins_loaded', 'CF7WPAY_load_textdomain' );
function CF7WPAY_load_textdomain() {
    load_plugin_textdomain( 'save-data-contact-form-7', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}

function CF7WPAY_load_my_own_textdomain( $mofile, $domain ) {
    if ( 'save-data-contact-form-7' === $domain && false !== strpos( $mofile, WP_LANG_DIR . '/plugins/' ) ) {
        $locale = apply_filters( 'plugin_locale', determine_locale(), $domain );
        $mofile = WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ) . '/languages/' . $domain . '-' . $locale . '.mo';
    }
    return $mofile;
}
add_filter( 'load_textdomain_mofile', 'CF7WPAY_load_my_own_textdomain', 10, 2 );