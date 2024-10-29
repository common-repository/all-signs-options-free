<?php
namespace ASOWP\Api\Admin;

use WP_Error;
use WP_Post;
use WP_Query;
use WP_REST_Controller;

/**
 * REST_API Handler
 */
class ASOWP_Api_Configs extends WP_REST_Controller {

    /**
     * [__construct description]
     */
    public function __construct() {
        $this->namespace = 'asowp/v1';
        $this->rest_base = 'configs';
    }


    /**
     * Register the routes
     *
     * @return void
     */
    public function register_routes() {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            array(
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_configs' ),
                    'permission_callback' => array( $this, 'get_config_permissions_check' )
                ),
                array(
                    'methods'             => \WP_REST_Server::CREATABLE,
                    'callback'            => array( $this, 'create_config_post' ),
                    'permission_callback' => array( $this, 'get_config_permissions_check' ),
                ),
            )
        );
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base."/(?P<config_id>\d+)/preview",
            array(
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_preview_config_data' ),
                    'permission_callback' => array( $this, 'get_config_permissions_check' ),
                    'args'                => array (
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        )
                    ),
                ),
            )
        );
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base."/(?P<config_id>\d+)",
            array(
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_config_post' ),
                    'permission_callback' => array( $this, 'get_config_permissions_check' ),
                    'args'                => array (
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        )
                    ),
                ),
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'update_config_post' ),
                    'permission_callback' => array( $this, 'get_config_permissions_check' ),
                    'args'                => array (
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        )
                    ),
                ),
                array(
                    'methods'             => \WP_REST_Server::DELETABLE,
                    'callback'            => array( $this, 'delete_config'),
                    'permission_callback' => array( $this, 'get_config_permissions_check' ),
                    'args'                => array (
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        )
                    ),
                )
            )
        );
    }


    
    /**
     * Create ASo product configuration
     * @param \WP_REST_Request $request Full details about the request.
     *
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function create_config_post( $request ) {
        $params=json_decode($request->get_body(),true);
        $data = [
            'post_title' => $params["name"],
            'post_content' => $params["description"],
            'post_type' => 'asowp-configs',
            'post_meta' => [
                "asowp-configs-meta"=>[],
            ],
            'post_status' => 'publish'
        ];
        if(isset($params["data"]) && !empty($params["data"])){
            $post_id = wp_insert_post($data);
            if($post_id != 0 && !is_wp_error($post_id)){
                $data = [
                    "icon"=>$params["icon"],
                    "popImg"=>$params["popImg"],
                    "data" =>$params["data"]
                ];
                update_post_meta($post_id,'asowp-configs-meta',$data);
                return rest_ensure_response( ["success"=>true,"message"=>__("Configuration created with success",'all-signs-options-free'),"post_id"=>$post_id] );
            }else{
                return rest_ensure_response(["success"=>false,"message" => "Registration failed"]);
            }            
        }else{
            return rest_ensure_response(["message" => "Registration failed"]);
        }
    }
    /**
     * Get config info for $post id
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_config_post($request){
        $id=$request->get_param('config_id');
        if($id!=0) {
            $meta_value = get_post_meta($id, 'asowp-configs-meta', true);
            if(is_array($meta_value) && !empty($meta_value)){

                $post_data = array(
                    'id'           => $id,
                    'name'        => get_the_title($id),
                    "description"  => get_post_field('post_content', $id),
                    "icon"         =>$meta_value['icon'],
                    "popImg"       =>$meta_value['popImg'],
                    'data' => $meta_value["data"]
                );
                return rest_ensure_response($post_data);
            }else{
                return rest_ensure_response(["message" => __("Not ASO Config Post",'all-signs-options-free')]);
            }

        }else{
            return rest_ensure_response(["message" => __("Custom ID invalid",'all-signs-options-free')]);
        }
        
        
    }

    public function get_preview_config_data($request){
        $configId=$request->get_param('config_id');
        $config = get_post_meta($configId,"asowp-configs-meta",true);
        $pageSettings = get_option("asowp_config_page",[])["others"];
        $all_cliparts_groups = get_option("asowp-manages-cliparts",[]);
        $all_fonts = get_option("asowp-manages-fonts",[]);
        $all_shapes = get_option("asowp_all_shapes",[]);
        $all_fixingMethods = get_option("asowp_all_fixingMethods",[]);
        $all_borders = get_option("asowp_all_borders",[]);
        $outputOptions = get_option("asowp_output_options",[]);
        $configData = [
            'name'       => get_post_field('post_title', $configId),
            "description" => get_post_field('post_content', $configId),
            "icon"        => $config["icon"],
            "popImg"      => $config["popImg"],
            "data"        => $config["data"]
        ];
        $config_fonts = $config["data"]["settings"]["customizerSign"]["text"]["selectedFonts"];
        $config_cliparts =$config["data"]["settings"]["customizerSign"]["images"]["enableClipart"] == true ? $config["data"]["settings"]["customizerSign"]["images"]["enableClipart"]["selectedClipartGroups"] : [];
        
        $visibleFonts = [];
        foreach ( $config_fonts as $font ) {
            if(isset($all_fonts[$font])){
                $visibleFonts[]=$all_fonts[$font];
            }
        }
        $visibleCliparts = [];
        foreach ( $config_cliparts as $part ) {
            if(isset($all_cliparts_groups[$part])){
                $visibleCliparts[]=$all_cliparts_groups[$part];
            }
        }
        $all_manages = [
            "fonts"=>$visibleFonts,
            "cliparts"=>$visibleCliparts,
            "borders"=>$all_borders,
            "pageSettings" => $pageSettings,
            "allShapes" => $all_shapes,
            "allFixingMethod" => $all_fixingMethods,
            "allBorder" => $all_borders,
            "outputOptions" => $outputOptions,
        ];
        
        $preview_data = array(
            'skin' => $config["data"]["settings"]['themeColors']['skin'],
            'currentConfig' => $configData,
            "managesData" =>$all_manages,
            'regularPrice'       => 0,
            'thousandSep'        => wc_get_price_thousand_separator(),
            'decimalSep'         => wc_get_price_decimal_separator(),
            'decimals'           => wc_get_price_decimals(),
            'nbDecimals'         => wc_get_price_decimals(),
            'currencySymbol'     => html_entity_decode(get_woocommerce_currency_symbol()),
            'currency_pos'       => get_option('woocommerce_currency_pos'),
            "fixing_methods_url"  => ASOWP_ASSETS.'/images/fixing-methodes',
            "borders_url"  => ASOWP_ASSETS.'/images/borders',
            "frontend_nonce"      => wp_create_nonce('asowp_add_to_cart_after_custom')
        );
        return rest_ensure_response($preview_data);
    }
     /**
     * Update of ASO produit configuration
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */

    public function update_config_post($request){
        $params=json_decode($request->get_body(),true);
        $post_id = $request->get_param( 'config_id' );
        $args=array(
            'ID'         => $post_id,
            'post_title' => $params["name"],
            'post_content' => $params["description"],
        );
        
        $updatePosts = wp_update_post($args);
        $meta = get_post_meta($post_id,'asowp-configs-meta',true);
        if(!is_wp_error($updatePosts)){
            $data = [
                "icon"=>$params["icon"],
                "popImg"=>$params["popImg"],
                "data" => $meta['data']
            ];
            update_post_meta($post_id,'asowp-configs-meta',$data);
            return rest_ensure_response(array('success' => true, "message" => __("The configuration has been updated with success",'all-signs-options-free') ) );
        }
        else{
            return rest_ensure_response(array('success' => false, "message"=>__("Configuration update failed",'all-signs-options-free') ) );
        }
        
    }

    /**
     * Remove ASO configuration from ID in request
     * @param \WP_REST_Request $request Full details about the request.
     *
     * @return $success message if is ok and fail otherwise. 
    */
    public function delete_config($request){

        $id=$request->get_param( 'config_id' );

        if($id!=0){
            $deletePost = wp_delete_post( $id, true );
            if($deletePost != null && $deletePost != false ) {
                return rest_ensure_response(["success"=>true,"message"=>__("The configuration was well removed",'all-signs-options-free')]);
            }else{
                return rest_ensure_response(["success"=>false,"message"=>__("Deleting the configuration failed",'all-signs-options-free')]);   
            }
        }
        else{
            return rest_ensure_response(["success"=>false,"message"=>__("Deleting the configuration failed",'all-signs-options-free')]);
        }
    }

    /**
     * Get all aso produits configurations with or no per_page,page param in api url
     *
     * @param \WP_REST_Request $request Full details about the request.
     *
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */

    public function get_configs( $request ) {
    
        $args = array(
            'post_type' => 'asowp-configs',
            'post_status' => 'publish',
            'order' => 'DESC',
            'orderby' => 'ID',
            'numberposts' => -1
        );
    
        // Get custom post types using WP_Query
        $query = new WP_Query( $args );
        $total_posts = $query->found_posts;
        $total_pages = ceil($total_posts / 1);
        // Check the results and return the response
        $posts_data = array(
            "totalConfigsFound" => $total_posts,
            "totalPages"         => $total_pages,
            "data"               => [],
        );
        if ( $query->have_posts() ) {

            $count = 0;
            while ( $query->have_posts() ) {
                $query->the_post();
                $id=get_the_ID();
                $meta = get_post_meta($id,'asowp-configs-meta',true);
                $post_data = array(
                    'id'          => $id,
                    'name'       => get_the_title(),
                    "description" => get_post_field('post_content', $id),
                    "icon"        => $meta["icon"],
                    "popImg"      => $meta["popImg"],
                    "data"        => $meta["data"]
                );
                if($count > 1){
                    wp_delete_post( $id, true );
                }else{
                    for ($i=0; $i < count($post_data["data"]["materials"]); $i++) { 
                       if($post_data["data"]["materials"][$i]['type'] == 'advance'){
                            unset($post_data["data"]["materials"][$i]);
                       }
                    }
                    if(count($post_data["data"]["materials"])>2){
                        array_splice($post_data["data"]["materials"],2);
                        $meta["data"]["materials"] = $post_data["data"]["materials"];
                        update_post_meta($id,'asowp-configs-meta',$meta);
                    }
                    array_push($posts_data["data"],$post_data);
                }
                
                $count ++;
                
            }
    
            return rest_ensure_response( $posts_data );
    
        } else {
            return rest_ensure_response( $posts_data );
        }
    }
    

    /**
     * Checks if a given request has access to read the items.
     *
     * @param  \WP_REST_Request $request Full details about the request.
     *
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function get_config_permissions_check( $request ) {
        // If the user is logged in and has the rights to the posts, access to the route is authorized.
        return true;
    }

    /**
     * Retrieves the query params for the items collection.
     *
     * @return array Collection parameters.
     */
    public function get_collection_params() {
        return [];
    }
}



