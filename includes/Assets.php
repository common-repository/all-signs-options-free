<?php
namespace ASOWP;

/**
 * Scripts and Styles Class
 */
class Assets {

    function __construct() {

        if ( is_admin() ) {
            add_action( 'admin_enqueue_scripts', [ $this, 'register' ], 5 );
        } else {
            add_action( 'wp_enqueue_scripts', [ $this, 'register' ], 5 );
        }
    }

    /**
     * Register our app scripts and styles
     *
     * @return void
     */
    public function register() {
        $this->register_scripts( $this->get_scripts() );
        $this->register_styles( $this->get_styles() );
    }

    /**
     * Register scripts
     *
     * @param  array $scripts
     *
     * @return void
     */
    private function register_scripts( $scripts ) {
        // Return if JS folder not exists
        if (!is_dir(ASOWP_PATH . '/assets/js')) {
            return;
        }

        foreach ( $scripts as $handle => $script ) {
            $deps      = isset( $script['deps'] ) ? $script['deps'] : false;
            $in_footer = isset( $script['in_footer'] ) ? $script['in_footer'] : false;
            $version   = isset( $script['version'] ) ? $script['version'] : ASOWP_VERSION;

            wp_register_script( $handle, $script['src'], $deps, $version, $in_footer );
        }
    }

    /**
     * Register styles
     *
     * @param  array $styles
     *
     * @return void
     */
    public function register_styles( $styles ) {
        foreach ( $styles as $handle => $style ) {
            $deps = isset( $style['deps'] ) ? $style['deps'] : false;

            wp_register_style( $handle, $style['src'], $deps, ASOWP_VERSION );
        }
    }

    /**
     * Get all registered scripts
     *
     * @return array
     */
    public function get_scripts() {
        // Return if JS folder not exists
        if (!is_dir(ASOWP_PATH . '/assets/js')) {
            return;
        }

        $prefix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.min' : '';

        $scripts = [
            'asowp-runtime' => [
                'src'       => ASOWP_ASSETS . '/js/runtime.js',
                'version'   => filemtime( ASOWP_PATH . '/assets/js/runtime.js' ),
                'in_footer' => true
            ],
            'asowp-vendor' => [
                'src'       => ASOWP_ASSETS . '/js/vendors.js',
                'version'   => filemtime( ASOWP_PATH . '/assets/js/vendors.js' ),
                'in_footer' => true
            ],
            'asowp-frontend' => [
                'src'       => ASOWP_ASSETS . '/js/frontend.js',
                'deps'      => [ 'jquery', 'asowp-vendor', 'asowp-runtime' ],
                'version'   => filemtime( ASOWP_PATH . '/assets/js/frontend.js' ),
                'in_footer' => true
            ],
            'asowp-admin' => [
                'src'       => ASOWP_ASSETS . '/js/admin.js',
                'deps'      => [ 'jquery','asowp-vendor', 'asowp-runtime' ],
                'version'   => filemtime( ASOWP_PATH . '/assets/js/admin.js' ),
                'in_footer' => true
            ],
            'asowp-omodal' => [
                'src'       => ASOWP_ASSETS . '/utilities/modal.min.js',
                'in_footer' => true
            ],
            'asowp-toast' => [
                'src'       => ASOWP_ASSETS . '/utilities/toast.min.js',
                'in_footer' => true
            ],
            'asowp-tinymce-script' => [
                'src'       => includes_url('/js/tinymce/') . 'tinymce.min.js',
                'in_footer' => true
            ],
            'asowp-product-min' => [
                'src' =>  ASOWP_ASSETS .'/utilities/asowp-product-min.js',
                'in_footer' => true
            ]
        ];

        return $scripts;
    }

    /**
     * Get registered styles
     *
     * @return array
     */
    public function get_styles() {

        $styles = [
            'asowp-style' => [
                'src' =>  ASOWP_ASSETS . '/css/style.css'
            ],
            'asowp-frontend' => [
                'src' =>  ASOWP_ASSETS . '/css/frontend.css'
            ],
            'asowp-admin' => [
                'src' =>  ASOWP_ASSETS . '/css/admin.css'
            ],
            'asowp-omodal' => [
                'src' =>  ASOWP_ASSETS . '/utilities/modal.min.css'
            ],
            'asowp-toast' => [
                'src' =>  ASOWP_ASSETS . '/utilities/toast.min.css'
            ],
        ];

        return $styles;
    }

}