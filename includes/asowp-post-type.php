<?php
namespace ASOWP;
class ASOWP_Post_Type
{
    public function init_hooks(){
        add_action('init',array($this,'register_asowp_post_type'));
        add_action('init',array($this,'register_asowp_config_meta'));

        add_filter( 'the_content', array($this,'get_editor_shortcode_handler'));
		add_filter( 'init', array($this,'asowp_add_design_page_rewrite_rules'), 99 );
		add_filter( 'query_vars', array($this, 'asowp_add_query_vars' ));
    }

	/**
	 * create post type 
	 */
    public function register_asowp_post_type() {

		$labels = array(
			'name'               => esc_html__( 'ASO Configurations', 'all-signs-options-free' ),
			'singular_name'      => esc_html__( 'ASO Configurations', 'all-signs-options-free' ),
			'add_new'            => esc_html__( 'New ASO configuration', 'all-signs-options-free' ),
			'add_new_item'       => esc_html__( 'New ASO configuration', 'all-signs-options-free' ),
			'edit_item'          => esc_html__( 'Edit ASO configuration', 'all-signs-options-free' ),
			'new_item'           => esc_html__( 'New ASO configuration', 'all-signs-options-free' ),
			'view_item'          => esc_html__( 'View ASO configuration', 'all-signs-options-free' ),
			'not_found'          => esc_html__( 'No ASO configuration found', 'all-signs-options-free' ),
			'not_found_in_trash' => esc_html__( 'No ASO configuration in the trash', 'all-signs-options-free' ),
			'menu_name'          => esc_html__( 'All Signs Options', 'all-signs-options-free' ),
			'all_items'          => esc_html__( 'ASO Configurations', 'all-signs-options-free' ),
		);

		$args = array(
			'labels'              => $labels,
			'hierarchical'        => false,
			'description'         => 'ASO Configurations',
			'supports'            => array( 'title' ),
			'public'              => false,
			'show_in_rest' 		  => true,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'show_in_nav_menus'   => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'has_archive'         => false,
			'query_var'           => false,
			'can_export'          => true,
		);

		register_post_type( 'asowp-configs', $args );
	}

    /**
	 * Create meta data of asowp-configs-meta
	*/
	public function register_asowp_config_meta(){
		register_meta(
			'asowp-configs',
			'asowp-configs-meta',
			array(
				'show_in_rest' => array(
					'schema' => array(
						'type'  => 'array',
						'items' => array(
							'type'  => 'array',
							'items' => array(
								'type'        => 'mixed'
							)
						)
					)
				),
				'type' => 'array',
				'single' => true,
			)
		);
	}

    /**
	 * Add short code on config page
	*/

	public function get_editor_shortcode_handler( $content ) {
		global $wp_query;
		$page_settings = get_option("asowp_config_page");
		if ( (get_the_ID() == $page_settings["configuratorPage"]) && is_page($page_settings["configuratorPage"]) ){
			if(!isset( $wp_query->query_vars['asowp-product-id'] )){
				ob_start();
				?>
				<div class="asowp-config-page-error">
					<div class="asowp-config-page-error-title">
						<?php echo esc_html__("All Signs Options Warning",'all-signs-options-free') ?>
					</div>
					<div>				
						<p><?php echo esc_html__( "You are trying to access the personalization page without the personalized button of a product to be personalized. 
						This page should only be accessible using one of the customization buttons. 
						If you don't like this procedure, don't define this page as a personalization page and use the short code to display the configurator.", 'all-signs-options-free' );?></p>
					</div>
				</div>
				<?php
				$content .= ob_get_clean();
			}else{
				$content .= do_shortcode("[asowp-configurator productid='".$wp_query->query_vars['asowp-product-id']."']");									
			}
		}
		return $content;
	}
	public function asowp_add_query_vars( $a_vars ) {
		$a_vars[] = 'asowp-product-id';
		$a_vars[] = 'asowp-tplid';
		$a_vars[] = 'edit';
		$a_vars[] = 'design-index';
		$a_vars[] = 'vcid';
		return $a_vars;
	}
	public function asowp_add_design_page_rewrite_rules() {
		global $wp_rewrite;
		$page_settings = get_option("asowp_config_page");
		if (!empty($page_settings) && $page_settings != false) {
			$asowp_page_id = $page_settings["configuratorPage"];
			$asowp_page = get_post($asowp_page_id);
			if (is_object($asowp_page)) {
				$raw_slug = get_permalink($asowp_page->ID);
				$home_url = home_url('/');
				$slug = trim(str_replace($home_url, '', $raw_slug), '/');
				
				// Règle pour URL avec asowp-tplid
				add_rewrite_rule(
					$slug . '/asowp-design/([^/]+)/([^/]+)/?$',
					'index.php?pagename=' . $slug . '&asowp-product-id=$matches[1]&asowp-tplid=$matches[2]',
					'top'
				);
	
				// Règle pour URL sans asowp-tplid
				add_rewrite_rule(
					$slug . '/asowp-design/([^/]+)/?$',
					'index.php?pagename=' . $slug . '&asowp-product-id=$matches[1]',
					'top'
				);
			}
		}
		$wp_rewrite->flush_rules(false);
	}
	
	
}
