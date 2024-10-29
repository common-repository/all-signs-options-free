<?php
namespace ASOWP;

/**
 * Admin Pages Handler
 */
class ASOWP_Admin {

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'admin_menu' ] );
        add_filter( 'upload_mimes', [$this, 'asowp_add_custom_mime_types'] );
        add_filter( 'wp_check_filetype_and_ext', [$this, 'asowp_check_filetype_and_ext'], 99, 5 );
    }

    /**
     * Register our menu page
     *
     * @return void
     */
    public function admin_menu() {
        if(is_plugin_active('woocommerce/woocommerce.php')){
            global $submenu;

            $capability = 'manage_options';
            $slug       = 'asowp';

            $hook = add_menu_page( __( 'All Signs Options', 'all-signs-options-free' ), __( 'All Signs Options', 'all-signs-options-free' ), $capability, $slug, [ $this, 'plugin_page' ], ASOWP_ASSETS.'/images/im_icon_aso.png' );

            if ( current_user_can( $capability ) ) {
                $submenu[ $slug ][] = array( __( 'Home', 'all-signs-options-free' ), $capability, 'admin.php?page=' . $slug . '#/' );
                $submenu[ $slug ][] = array( __( 'Configurations', 'all-signs-options-free' ), $capability, 'admin.php?page=' . $slug . '#/configurations' );
                $submenu[ $slug ][] = array( __( 'Global Settings', 'all-signs-options-free' ), $capability, 'admin.php?page=' . $slug . '#/global-settings/configuration-page' );
            }

            add_action( 'load-' . $hook, [ $this, 'init_hooks'] );
        }
    }

    /**
     * Initialize our hooks for the admin page
     *
     * @return void
     */
    public function init_hooks() {
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
    }

    /**
     * Load scripts and styles for the app
     *
     * @return void
     */
    public function enqueue_scripts() {

        wp_enqueue_script( 'asowp-runtime',ASOWP_ASSETS . '/js/runtime.js',[],filemtime( ASOWP_PATH . '/assets/js/runtime.js' ),true );
        wp_enqueue_script( 'asowp-vendor',ASOWP_ASSETS . '/js/vendors.js',[],filemtime( ASOWP_PATH . '/assets/js/vendors.js' ),true );

        wp_enqueue_style( 'asowp-admin',ASOWP_ASSETS . '/css/admin.css',false,ASOWP_VERSION );
        wp_enqueue_style( 'asowp-frontend', ASOWP_ASSETS . '/css/frontend.css',false,ASOWP_VERSION);

        wp_enqueue_script( 'asowp-admin',ASOWP_ASSETS . '/js/admin.js',[ 'jquery', 'asowp-vendor', 'asowp-runtime' ],filemtime( ASOWP_PATH . '/assets/js/admin.js' ),true );
        wp_enqueue_script( 'asowp-frontend',ASOWP_ASSETS . '/js/frontend.js',[ 'jquery', 'asowp-vendor', 'asowp-runtime' ],filemtime( ASOWP_PATH . '/assets/js/frontend.js' ),true );

        wp_enqueue_style('asowp-toast',ASOWP_ASSETS . '/utilities/toast.min.css',false,ASOWP_VERSION);
        wp_enqueue_script('asowp-toast',ASOWP_ASSETS . '/utilities/toast.min.js',[],ASOWP_VERSION,true);
        wp_enqueue_media();
    }

    /**
     * Render our admin page
     *
     * @return void
     */
    public function plugin_page() {
        $api_url = get_rest_url(); ?>
        <div class="wrap">
            <div id="asowp-admin-app"></div>
        </div>
        <?php wp_localize_script("asowp-admin","asowp_data",[
            "rest_url"=>$api_url."asowp/v1",
            "ajax_url"=>esc_url(admin_url('admin-ajax.php')),
            "site_url"=>urlencode(get_site_url()),
            "assets_url"=>ASOWP_ASSETS,
            "page"=>"admin",
            "version"=> ASOWP_VERSION,
            'currencySymbol'     => class_exists( 'WooCommerce' ) ? html_entity_decode(get_woocommerce_currency_symbol()) : '',
            'currency_pos'       => class_exists( 'WooCommerce' ) ? get_option('woocommerce_currency_pos') : ''
        ]);
        wp_localize_script("asowp-admin","asowp_configurator_data",array(
            "fixing_methods_url"  => ASOWP_ASSETS.'/images/fixing-methodes',
            "borders_url"  => ASOWP_ASSETS.'/images/borders',
        ));
    }

    public function asowp_add_custom_mime_types( $mimes ) {
		return array_merge(
			$mimes,
			array(
				'svg' => 'image/svg+xml',
				'ttf' => 'application/x-font-ttf',
				'woff2' => 'application/x-font-woff2',
				'woff' => 'application/x-font-woff',
				'otf' => 'application/x-font-otf',
				'icc' => 'application/vnd.iccprofile',
			)
		);
	}

    /**
	 * Check file type and extension.
	 *
	 * @param array  $data The data.
	 * @param mixed  $file The file.
	 * @param string $filename The file name.
	 * @param array $mimes The mimes.
	 * @param string $real_mime The real mimes.
	 * @return array
	 */
	public function asowp_check_filetype_and_ext( $data, $file, $filename, $mimes, $real_mime ) {
		if ( ! empty( $data['ext'] ) && ! empty( $data['type'] ) ) {
			return $data;
		}

			$wp_file_type = wp_check_filetype( $filename, $mimes );

		// Check for the file type you want to enable, e.g. 'svg'.
		if ( 'ttf' === $wp_file_type['ext'] ) {
			$data['ext']  = 'ttf';
			$data['type'] = 'font/ttf';
		}

		if ( 'otf' === $wp_file_type['ext'] ) {
			$data['ext']  = 'otf';
			$data['type'] = 'font/otf';
		}

		if ( 'woff' === $wp_file_type['ext'] ) {
			$data['ext']  = 'woff';
			$data['type'] = 'font/woff';
		}

		if ( 'woff2' === $wp_file_type['ext'] ) {
			$data['ext']  = 'woff2';
			$data['type'] = 'font/woff2';
		}

		if ( 'svg' === $wp_file_type['svg'] ) {
			$data['ext']  = 'svg';
			$data['type'] = 'image/svg+xml';
		}

		return $data;
	}
}
