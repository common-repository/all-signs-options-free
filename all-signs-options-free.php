<?php

use ASOWP\ASOWP_Post_Type;
use ASOWP\ASOWP_Public;

/*
Plugin Name: All Signs Options Free
Plugin URI: https://signsdesigner.us/
Description: The leading app for selling all types of custom signs with WordPress/Woocommerce.  ASO is designed to streamline and improve the process of designing, quoting and ordering custom signs for sign makers and their customers via a beautiful, user-friendly configurator with flexible options for setting up your online store.
Version: 1.0
Author: Vertim Coders
Author URI: https://vertimcoders.com
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: all-signs-options-free
Domain Path: /languages
*/

/**
 * Copyright (c) 2023 Vertim Coders. All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 * 
 * Inspired by: https://github.com/tareq1988/vue-wp-starter
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * **********************************************************************
 */

// don't call the file directly
if (!defined('ABSPATH')) exit;

/**
 * ASOWP_All_Signs_Options_Free class
 *
 * @class ASOWP_All_Signs_Options_Free The class that holds the entire ASOWP_All_Signs_Options_Free plugin
 */
final class ASOWP_All_Signs_Options_Free
{

    /**
     * Plugin version
     *
     * @var string
     */
    public $version = '1.0';

    /**
     * Holds various class instances
     *
     * @var array
     */
    private $container = array();

    /**
     * Constructor for the All_Signs_Options class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     */
    public function __construct()
    {

        $this->define_constants();
        $this->asowp_save_output_settings();
        $this->asowp_save_pages_settings();
        $this->asowp_define_borders();
        $this->asowp_define_shapes();
        $this->asowp_define_fixingMethods();
        add_filter('plugin_row_meta', [$this,'warning_message_for_woocommerce_missing'], 10, 2);
        add_action('admin_init', [$this,'auto_deactivate_when_woocommerce_is_inactive']);
        add_filter('plugin_action_links', [$this,'modify_action_links'], 99, 2);

        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        add_action('admin_notices', [$this, 'check_woocommerce_install_and_version']);
        add_action('plugins_loaded', array($this, 'init_plugin'));
        add_action('admin_notices', [$this, 'check_config_pageselected']);
        //add_action('admin_notices', [$this, 'permalink_notice']);
    }

    public function modify_action_links($actions, $plugin_file) {
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');
        // Vérifier si nous sommes sur notre plugin spécifique
        if ($plugin_file == plugin_basename(__FILE__)) {
            if (is_plugin_active('woocommerce/woocommerce.php')) {
                // Si WooCommerce n'est pas actif, désactiver le lien d'activation
                if (isset($actions['activate'])) {
                    unset($actions['activate']);
                }
                $actions['go_pro'] = sprintf( '<a href="%s" style="%s">%s</a>', 'https://signsdesigner.us/all-signs-options-product/', 'color:#35b747;font-weight:bold', __( 'Get Premium!', 'all-signs-options-free' ) );
                $actions['go_docs'] = sprintf( '<a href="%s" style="%s">%s</a>', 'https://docs.signsdesigner.us/docs/asowp-wp-documentation/', 'color:#35b747;font-weight:bold', __( 'Go Docs!', 'all-signs-options-free' ) );
                // Ajouter un message ou une action personnalisée si besoin
            }
        }
        return $actions;
    }

    public function auto_deactivate_when_woocommerce_is_inactive() {
        // Vérifie si WooCommerce est désactivé
        if (!is_plugin_active('woocommerce/woocommerce.php') && is_plugin_active(plugin_basename(__FILE__))) {
            // Désactive votre plugin
            deactivate_plugins(plugin_basename(__FILE__));
        }
    }
    public function warning_message_for_woocommerce_missing($plugin_meta, $plugin_file) {
        // Vérifiez si le plugin est bien le vôtre
        if (plugin_basename(__FILE__) == $plugin_file) {
            if (!is_plugin_active('woocommerce/woocommerce.php')) {
                // Ajoutez un message personnalisé à côté de la description
                $plugin_meta[] = '<span style="color: red;"><strong>'.esc_html__("This plugin requires WooCommerce to run. Please install or activate WooCommerce.","all-signs-options-free").'</strong></span>';
            }
        }
        return $plugin_meta;
    }

    /**
     * Initializes the All_Signs_Options() class
     *
     * Checks for an existing All_Signs_Options() instance
     * and if it doesn't find one, creates it.
     */
    public static function init()
    {
        static $instance = false;

        if (!$instance) {
            $instance = new ASOWP_All_Signs_Options_Free();
        }

        return $instance;
    }

    /**
     * Magic getter to bypass referencing plugin.
     *
     * @param $prop
     *
     * @return mixed
     */
    public function __get($prop)
    {
        if (array_key_exists($prop, $this->container)) {
            return $this->container[$prop];
        }

        return $this->{$prop};
    }

    /**
     * Magic isset to bypass referencing plugin.
     *
     * @param $prop
     *
     * @return mixed
     */
    public function __isset($prop)
    {
        return isset($this->{$prop}) || isset($this->container[$prop]);
    }

    /**
     * Define the constants
     *
     * @return void
     */
    public function define_constants()
    {
        define('ASOWP_VERSION', $this->version);
        define('ASOWP_ID', 3107);
        define('ASOWP_FILE', __FILE__);
        define('ASOWP_PATH', dirname(ASOWP_FILE));
        define('ASOWP_INCLUDES', ASOWP_PATH . '/includes');
        define('ASOWP_URL', plugins_url('', ASOWP_FILE));
        define('ASOWP_ASSETS', ASOWP_URL . '/assets');
        define("ASOWP_CHECK_TRANSIENT_EXPIRATION", 12 * HOUR_IN_SECONDS); // 12 hours
        define("ASOWP_CHECK_TRANSIENT_NAME", "wp_update_check_asowp_pro");

        $upload_dir = wp_upload_dir();
        $generation_path = $upload_dir['basedir'] . "/ASOWP/";
        $generation_url = $upload_dir['baseurl'] . "/ASOWP/";

        define('ASOWP_IMAGE_PATH', $generation_path . "images");
        define('ASOWP_IMAGE_URL', $generation_url . "images");

        define('ASOWP_ORDER_PATH', $generation_path . "ORDERS");
        define('ASOWP_ORDER_URL', $generation_url . "ORDERS");
    }

    private function asowp_save_output_settings()
    {
        $output_settings = [
            "zipName" => true,
            "calculateOutput" => true
        ];
        $have_output_settings = get_option("asowp_output_options");
        if ($have_output_settings == false) {
            update_option("asowp_output_options", $output_settings);
        }
    }
    private function asowp_save_pages_settings()
    {
        $pages_settings = [
            "configuratorPage" => 0,
            "templatePage" => 0,
            "buttons"=>[
                "productDesignButton"=>'Customize The Product',
                "productTemplateButton"=>'Design From Example',
                "templateAddToCartButton"=>'Add To Cart',
                "templateDesignButton"=> 'Customize',
                "recapsButtonOnCart"=>'Sign Recaps'        
            ],
            "others" => [
                "titleBalise" => 'h1'
            ],
        ];
        $have_pages_settings = get_option("asowp_config_page");
        if ($have_pages_settings == false) {
            update_option("asowp_config_page", $pages_settings);
        }else{
            $differenceCles = array_diff_key($pages_settings, $have_pages_settings);
            if (count($differenceCles) > 0) {
                foreach ($differenceCles as $key => $value) {
                    $have_pages_settings[$key] = $value;
                }
                update_option("asowp_config_page", $have_pages_settings);
            }
        }
    }
    private function asowp_define_borders()
    {
        $borders = [
            [
                'name' => 'None',
                "icon" => ASOWP_ASSETS . '/images/borders/ic_border_none.svg',
                'value' => 'none'
            ]
        ];
        $have_borders = get_option("asowp_all_borders");
        if ($have_borders == false) {
            update_option("asowp_all_borders", $borders);
        } else {

            for ($i=0; $i < 1; $i++) { 
                $search_strings = ['all-signs-options-pro/', 'all-signs-options-starter/'];

                $found = false;
                foreach ($search_strings as $string) {
                    if (strpos($have_borders[$i]["icon"], $string) !== false) {
                        $found = true;
                        break;
                    }
                }

                if ($found) {
                    $have_borders[$i]["icon"] = str_replace($search_strings, 'all-signs-options-free/', $have_borders[$i]["icon"]);
                    update_option("asowp_all_borders", $have_borders);
                }
            }
            $differenceCles = array_diff_key($borders, $have_borders);
            if (count($differenceCles) > 0) {
                foreach ($differenceCles as $key => $value) {
                    $have_borders[$key] = $value;
                }
                update_option("asowp_all_borders", $have_borders);
            }
        }
    }
    private function asowp_define_shapes()
    {
        $shapes = [
            [
                'name' => 'Oval',
                "icon" => ASOWP_ASSETS . '/images/shapes/ic_shape_oval.svg',
                'value' => 'oval'
            ],
            [
                'name' => 'Square',
                "icon" => ASOWP_ASSETS . '/images/shapes/ic_shape_square.svg',
                'value' => 'square'
            ],
        ];
        $have_shapes = get_option("asowp_all_shapes");
        if ($have_shapes == false) {
            update_option("asowp_all_shapes", $shapes);
        } else {
            for ($i=0; $i < 2; $i++) { 
                $search_strings = ['all-signs-options-pro/', 'all-signs-options-starter/'];

                $found = false;
                foreach ($search_strings as $string) {
                    if (strpos($have_shapes[$i]["icon"], $string) !== false) {
                        $found = true;
                        break;
                    }
                }

                if ($found) {
                    $have_shapes[$i]["icon"] = str_replace($search_strings, 'all-signs-options-free/', $have_shapes[$i]["icon"]);
                    update_option("asowp_all_shapes", $have_shapes);
                }
            }
            $differenceCles = array_diff_key($shapes, $have_shapes);
            if (count($differenceCles) > 0) {
                foreach ($differenceCles as $key => $value) {
                    $have_shapes[$key] = $value;
                }
                update_option("asowp_all_shapes", $have_shapes);
            }
        }
    }
    private function asowp_define_fixingMethods()
    {
        $fixingMethods = [
            [
                'name' => 'None',
                "description" => "",
                "icon" => ASOWP_ASSETS . '/images/fixing-methodes/ic_fixmethod_none.svg',
                "popImg" => "",
                'type' => 'none'
            ],
            [
                'name' => 'Adhesive Tape',
                "description" => "",
                "icon" => ASOWP_ASSETS . '/images/fixing-methodes/ic_fixmethod_adhesive_tape.svg',
                "popImg" => "",
                'type' => 'adhesive-tape'
            ],
            [
                'name' => 'Screw',
                "description" => "",
                "icon" => ASOWP_ASSETS . '/images/fixing-methodes/ic_fixmethod_screw.svg',
                "popImg" => "",
                'type' => 'screw'
            ],
        ];
        $have_fixingMethods = get_option("asowp_all_fixingMethods");
        if ($have_fixingMethods == false) {
            update_option("asowp_all_fixingMethods", $fixingMethods);
        } else {
            for ($i=0; $i < 3; $i++) { 
                $search_strings = ['all-signs-options-starter/', 'all-signs-options-pro/'];

                $found = false;
                foreach ($search_strings as $string) {
                    if (strpos($have_fixingMethods[$i]["icon"], $string) !== false) {
                        $found = true;
                        break;
                    }
                }

                if ($found) {
                    $have_fixingMethods[$i]["icon"] = str_replace($search_strings, 'all-signs-options-free/', $have_fixingMethods[$i]["icon"]);
                    update_option("asowp_all_fixingMethods", $have_fixingMethods);
                }
            }
            $differenceCles = array_diff_key($fixingMethods, $have_fixingMethods);
            if (count($differenceCles) > 0) {
                foreach ($differenceCles as $key => $value) {
                    $have_fixingMethods[$key] = $value;
                }
                update_option("asowp_all_fixingMethods", $have_fixingMethods);
            }
        }
    }

    /**
     * Load the plugin after all plugins are loaded
     *
     * @return void
     */
    public function init_plugin()
    {
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Placeholder for activation function
     *
     * Nothing being called here yet.
     */
    public function activate()
    {   
        if(!is_plugin_active('woocommerce/woocommerce.php')){

            $installed = get_option('ASOWP_installed');

            if (!$installed) {
                update_option('ASOWP_installed', time());
            }

            update_option('ASOWP_version', ASOWP_VERSION);
        }
    }

    /**
     * Placeholder for deactivation function
     *
     * Nothing being called here yet.
     */
    public function deactivate()
    {
    }

    /**
     * Include the required files
     *
     * @return void
     */
    public function includes()
    {

        if ($this->is_request('admin')) {
            require_once ASOWP_INCLUDES . '/Admin.php';
        }

        if ($this->is_request('frontend')) {
            require_once ASOWP_INCLUDES . '/Frontend.php';
        }

        if ($this->is_request('ajax')) {
            // require_once ASOWP_INCLUDES . '/class-ajax.php';
        }

        require_once ASOWP_INCLUDES . '/Api.php';
        require_once ASOWP_INCLUDES . '/asowp-post-type.php';
        require_once ASOWP_INCLUDES . '/asowp-design.php';
        require_once ASOWP_INCLUDES . '/asowp-product-config.php';
        require_once ASOWP_INCLUDES . '/Functions.php';
        require_once ASOWP_INCLUDES . '/Public.php';
    }

    /**
     * Initialize the hooks
     *
     * @return void
     */
    public function init_hooks()
    {

        add_action('init', array($this, 'init_classes'));

        (new ASOWP_Post_Type())->init_hooks();
        (new ASOWP_Product_Config())->init_hooks();
        (new ASOWP_Design())->init_hooks();

        // Localize our plugin
        add_action('init', array($this, 'localization_setup'));
    }

    /**
     * Instantiate the required classes
     *
     * @return void
     */
    public function init_classes()
    {

        if ($this->is_request('admin')) {
            $this->container['admin'] = new ASOWP\ASOWP_Admin();
        }

        if ($this->is_request('frontend')) {
            $this->container['frontend'] = new ASOWP\ASOWP_Frontend();
        }

        if ($this->is_request('ajax')) {
            // $this->container['ajax'] =  new ASOWP_PATHAjax();
        }

        $this->container['api'] = new ASOWP\Api();
        $this->container['public'] = new ASOWP\ASOWP_Public();
    }

    /**
     * Initialize plugin for localization
     *
     * @uses load_plugin_textdomain()
     */
    public function localization_setup()
    {
        load_plugin_textdomain('all-signs-options-free', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    /**
     * What type of request is this?
     *
     * @param  string $type admin, ajax, cron or frontend.
     *
     * @return bool
     */
    private function is_request($type)
    {
        switch ($type) {
            case 'admin':
                return is_admin();

            case 'ajax':
                return defined('DOING_AJAX');

            case 'rest':
                return defined('REST_REQUEST');

            case 'cron':
                return defined('DOING_CRON');

            case 'frontend':
                return (!is_admin() || defined('DOING_AJAX')) && !defined('DOING_CRON');
        }
    }

    public function check_config_pageselected()
    {
        if (class_exists('WooCommerce')) {
            $PageSettings = get_option('asowp_config_page', []);
            if (count($PageSettings) == 0) {
?>
                <div class="notice notice-warning asowp-notice-nux is-dismissible">
                    <span class="asowp-icon">
                        <img src="<?php echo esc_url(ASOWP_ASSETS . '/images/im_asowp-icon2.png') ?>" alt="" width="250" />
                    </span>
                    <div>
                        <h2><?php esc_html_e("Customization Page not found", 'all-signs-options-free') ?></h2>
                        <p><?php esc_html_e('To display the configurator on a page without a short code, please select the page on which it should be displayed. Click','all-signs-options-free') ?> <a href="<?php echo esc_url('admin.php?page=aso#/global-settings/configuration-page')?>"><?php echo esc_html__("here","all-signs-options-free")?></a></p>
                    </div>
                </div>
                <?php
            } else {

                if ((!get_post_status($PageSettings["configuratorPage"]) && $PageSettings["configuratorPage"] != 0) || $PageSettings["configuratorPage"] == 0) {
                ?>
                    <div class="notice notice-warning asowp-notice-nux is-dismissible">
                        <span class="asowp-icon">
                            <img src="<?php echo esc_url(ASOWP_ASSETS . '/images/im_asowp-icon2.png') ?>" alt="" width="250" />
                        </span>
                        <div>
                            <h2><?php esc_html_e("Customization Page not found", 'all-signs-options-free') ?></h2>
                            <p><?php esc_html_e('To display the configurator on a page without a short code, please select the page on which it should be displayed. Click','all-signs-options-free') ?> <a href="<?php echo esc_url('admin.php?page=aso#/global-settings/configuration-page')?>"><?php echo esc_html__("here","all-signs-options-free")?></a></p>
                        </div>
                    </div>
                <?php
                }
            }
        }
    }
    /**
     * Check if Woocommerce is installed
     */
    public function check_woocommerce_install_and_version($version = '3.4.0')
    {
        if (class_exists('WooCommerce')) {
            global $woocommerce;
            if (version_compare($woocommerce->version, $version, '<')) {
                ?>
                <div class="notice notice-info asowp-notice-nux is-dismissible">
                    <span class="asowp-icon">
                        <img src="<?php echo esc_url(ASOWP_ASSETS . '/images/im_asowp-icon2.png') ?>" alt="" width="250" />
                    </span>
                    <div>
                        <h2><?php esc_html_e("Welcome to All Signs Options. Let's get you started !!!", 'all-signs-options-free') ?></h2>
                        <p><?php esc_html_e('To avoid performance problems we recommend at least version 3.4 of Woocommerce.', 'all-signs-options-free'); ?></p>
                        <p><?php $this->install_plugin_button('woocommerce', 'woocommerce.php', 'WooCommerce', array(), __('WooCommerce activated', 'all-signs-options-free'), __('Activate WooCommerce', 'all-signs-options-free'), __('Install WooCommerce', 'all-signs-options-free')); ?></p>
                    </div>
                </div>
            <?php
            }
        }
    }

    /**
     * Output a button that will install or activate a plugin if it doesn't exist, or display a disabled button if the
     * plugin is already activated.
     *
     * @param string $plugin_slug The plugin slug.
     * @param string $plugin_file The plugin file.
     * @param string $plugin_name The plugin name.
     * @param array $classes CSS classes.
     * @param string $activated Button activated text.
     * @param string $activate Button activate text.
     * @param string $install Button install text.
     */
    public static function install_plugin_button($plugin_slug, $plugin_file, $plugin_name, $classes = array(), $activated = '', $activate = '', $install = '')
    {
        if (current_user_can('install_plugins') && current_user_can('activate_plugins')) {
            if (is_plugin_active($plugin_slug . '/' . $plugin_file)) {
                // The plugin is already active.
                $button = array(
                    'message' => esc_attr__('Activated', 'all-signs-options-free'),
                    'url'     => '#',
                    'classes' => array('storefront-button', 'disabled'),
                );

                if ('' !== $activated) {
                    $button['message'] = esc_attr($activated);
                }
            } elseif (self::is_plugin_installed($plugin_slug)) {
                $url = self::is_plugin_installed($plugin_slug);

                // The plugin exists but isn't activated yet.
                $button = array(
                    'message' => esc_attr__('Activate', 'all-signs-options-free'),
                    'url'     => $url,
                    'classes' => array('activate-now'),
                );

                if ('' !== $activate) {
                    $button['message'] = esc_attr($activate);
                }
            }

            if (!empty($classes)) {
                $button['classes'] = array_merge($button['classes'], $classes);
            }
            if (isset($button) && is_array($button)) {

                $button['classes'] = implode(' ', $button['classes']);

            ?>
                <span class="plugin-card-<?php echo esc_attr($plugin_slug); ?>">
                    <a href="<?php echo esc_url($button['url']); ?>" class="<?php echo esc_attr($button['classes']); ?>" data-originaltext="<?php echo esc_attr($button['message']); ?>" data-name="<?php echo esc_attr($plugin_name); ?>" data-slug="<?php echo esc_attr($plugin_slug); ?>" aria-label="<?php echo esc_attr($button['message']); ?>"><?php echo esc_html($button['message']); ?></a>
                </span> <?php echo /* translators: conjunction of two alternative options user can choose (in missing plugin admin notice). Example: "Activate WooCommerce or learn more" */ esc_html__('or', 'all-signs-options-free'); ?>
                <a href="https://docs.signsdesigner.us" target="_blank"><?php esc_html_e('learn more', 'all-signs-options-free'); ?></a>
            <?php
            }
        }
    }
    private static function is_plugin_installed($plugin_slug)
    {
        $plugin_folders = plugins_url();
        if ($plugin_folders . '/' . $plugin_slug) {
            $plugins = get_plugins('/' . $plugin_slug);
            if (!empty($plugins)) {
                $keys        = array_keys($plugins);
                $plugin_file = $plugin_slug . '/' . $keys[0];
                $url         = wp_nonce_url(
                    add_query_arg(
                        array(
                            'action' => 'activate',
                            'plugin' => $plugin_file,
                        ),
                        admin_url('plugins.php')
                    ),
                    'activate-plugin_' . $plugin_file
                );
                return $url;
            }
        }
        return false;
    }

    /**
     * 
     */
    /*public function permalink_notice()
    {
        if(is_plugin_active('woocommerce/woocommerce.php')){

            $current_permalink_structure = get_option('permalink_structure');

            if ($current_permalink_structure !== '/%postname%/') { ?>

                <div class="notice notice-warning asowp-notice-nux is-dismissible">
                    <span class="asowp-icon">
                        <img src='<?php echo esc_url(ASOWP_ASSETS . '/images/im_asowp-icon2.png') ?>' alt="" width="250" />
                    </span>
                    <div>
                        <h2><?php esc_html_e('We recommend setting your permalinks to "/%postname%/" to improve natural SEO.w! 🤘', 'all-signs-options-free') ?></h2>
                        <p><?php esc_html_e('To do this, go to', 'all-signs-options-free') ?> <a href="<?php echo esc_url(admin_url('options-permalink.php')) ?>"><?php echo esc_html_e("Settings > Permanent links", 'all-signs-options-free') ?></a></p>
                    </div>
                </div>
                <?php  
            }
        }
    }*/
} // All_Signs_Options

$ASO = ASOWP_All_Signs_Options_Free::init();
