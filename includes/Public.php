<?php
namespace ASOWP;

/**
 * public Pages Handler
 */
class ASOWP_Public {

    public function __construct() {
        $this->render_public();
    }

    /**
     * Render public app
     *
     * @param  array $atts
     * @param  string $content
     *
     * @return string
     */
    public function render_public() {
        wp_enqueue_style( 'asowp-style',ASOWP_ASSETS . '/css/style.css',false,ASOWP_VERSION );
        wp_enqueue_style( 'asowp-omodal',ASOWP_ASSETS . '/utilities/modal.min.css',false,ASOWP_VERSION );
        wp_enqueue_script('asowp-omodal',ASOWP_ASSETS . '/utilities/modal.min.js',[],ASOWP_VERSION,true);
        wp_enqueue_script('asowp-tinymce-script',includes_url('/js/tinymce/') . 'tinymce.min.js',[],ASOWP_VERSION,true);        
        wp_enqueue_script('asowp-product-min',ASOWP_ASSETS .'/utilities/asowp-product-min.js',["jquery"],ASOWP_VERSION,true);
    }
}
