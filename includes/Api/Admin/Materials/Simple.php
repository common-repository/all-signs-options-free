<?php
namespace ASOWP\Api\Admin\Materials;

use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;

/**
 * REST_API Handler
 */
class ASOWP_Materials_Simple extends WP_REST_Controller {

    /**
     * [__construct description]
     */
    public function __construct() {
        $this->namespace = 'asowp/v1';
        $this->rest_base = '/configs';
    }

    /**
     * Register the routes
     *
     * @return void
     */
    public function register_routes() {
        register_rest_route(
            $this->namespace,
            $this->rest_base.'/(?P<config_id>\d+)/materials/(?P<material_id>\d+)/shapes',
            array(
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'save_material_shapes' ),
                    'permission_callback' => array( $this, 'get_items_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'material_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        )
                    ),
                ),
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_material_shapes' ),
                    'permission_callback' => array( $this, 'get_items_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'material_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        )
                    ),
                ),  
            )
        );
        register_rest_route(
            $this->namespace,
            $this->rest_base.'/(?P<config_id>\d+)/materials/(?P<material_id>\d+)/sizes',
            array(
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'save_material_sizes' ),
                    'permission_callback' => array( $this, 'get_items_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'material_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        )
                    ),
                ),
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_material_sizes' ),
                    'permission_callback' => array( $this, 'get_items_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'material_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        )
                    )
                )
            )
        );        
        register_rest_route(
            $this->namespace,
            $this->rest_base.'/(?P<config_id>\d+)/materials/(?P<material_id>\d+)/colors',
            array(
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'save_material_colors' ),
                    'permission_callback' => array( $this, 'get_items_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'material_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        )
                    ),
                ),
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_material_colors' ),
                    'permission_callback' => array( $this, 'get_items_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'material_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        )
                    ),
                )
            )
        );      
        register_rest_route(
            $this->namespace,
            $this->rest_base.'/(?P<config_id>\d+)/materials/(?P<material_id>\d+)/text-images',
            array(
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'save_material_textImages' ),
                    'permission_callback' => array( $this, 'get_items_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'material_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        )
                    ),
                ),
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_material_textImages' ),
                    'permission_callback' => array( $this, 'get_items_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'material_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        )
                    ),
                )
            )
        );      
        register_rest_route(
            $this->namespace,
            $this->rest_base.'/(?P<config_id>\d+)/materials/(?P<material_id>\d+)/borders',
            array(
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'save_materials_borders' ),
                    'permission_callback' => array( $this, 'get_items_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'material_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        )
                    ),
                ),
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_material_borders' ),
                    'permission_callback' => array( $this, 'get_items_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'material_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        )
                    ),
                ),
            )
        );       
        register_rest_route(
            $this->namespace,
            $this->rest_base.'/(?P<config_id>\d+)/materials/(?P<material_id>\d+)/fixing-methods',
            array(
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_material_fixingMethods' ),
                    'permission_callback' => array( $this, 'get_items_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'material_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        )
                    ),
                ),
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'save_material_fixingMethods' ),
                    'permission_callback' => array( $this, 'get_items_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'material_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        )
                    ),
                ), 
            )
        );      
        register_rest_route(
            $this->namespace,
            $this->rest_base.'/(?P<config_id>\d+)/materials/(?P<material_id>\d+)/additional-options',
            array(
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_material_additionalOptions' ),
                    'permission_callback' => array( $this, 'get_items_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'material_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        )
                    ),
                ),    
                array(
                    'methods'             => \WP_REST_Server::CREATABLE,
                    'callback'            => array( $this, 'create_material_additionalOptions' ),
                    'permission_callback' => array( $this, 'get_items_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'material_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        )
                    ),
                )
            )
        );
        register_rest_route(
            $this->namespace,
            $this->rest_base.'/(?P<config_id>\d+)/materials/(?P<material_id>\d+)/additional-options/(?P<additional_id>\d+)',
            array(
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_material_additionalOption' ),
                    'permission_callback' => array( $this, 'get_items_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'material_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'additional_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        )
                    ),
                ),    
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'update_material_additionalOption' ),
                    'permission_callback' => array( $this, 'get_items_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'material_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'additional_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        )
                    ),
                ),
                array(
                    'methods'             => \WP_REST_Server::DELETABLE,
                    'callback'            => array( $this, 'delete_material_additionalOption' ),
                    'permission_callback' => array( $this, 'get_items_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'material_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'additional_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        )
                    ),
                ),        
            )
        );
        register_rest_route(
            $this->namespace,
            $this->rest_base.'/(?P<config_id>\d+)/materials/(?P<material_id>\d+)/additional-options/(?P<additional_id>\d+)/new-option',
            array(
                array(
                    'methods'             => \WP_REST_Server::CREATABLE,
                    'callback'            => array( $this, 'create_Option_in_AdditionalOption' ),
                    'permission_callback' => array( $this, 'get_items_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'material_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'additional_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        )
                    ),
                )
            )
        );
        register_rest_route(
            $this->namespace,
            $this->rest_base.'/(?P<config_id>\d+)/materials/(?P<material_id>\d+)/additional-options/(?P<additional_id>\d+)/(?P<option_id>\d+)',
            array(
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_Option_In_AdditionalOption' ),
                    'permission_callback' => array( $this, 'get_items_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'material_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'additional_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'option_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        )
                    ),
                ),
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'update_Option_In_AdditionalOption' ),
                    'permission_callback' => array( $this, 'get_items_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'material_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'additional_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'option_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        )
                    ),
                ),
                array(
                    'methods'             => \WP_REST_Server::DELETABLE,
                    'callback'            => array( $this, 'delete_Option_In_AdditionalOption' ),
                    'permission_callback' => array( $this, 'get_items_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'material_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'additional_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        ),
                        'option_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        )
                    ),
                )
            )
        );
    }

    /**
     * Save a collection of shapes.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function save_material_shapes( $request ) {
        $config_id = $request->get_param('config_id');
        $material_id = $request->get_param('material_id');
        if($config_id != 0){
            $meta_value = get_post_meta($config_id,'asowp-configs-meta',true);
            if(is_array($meta_value) && !empty($meta_value)){
                if(isset($meta_value['data']['materials'][$material_id]) && $meta_value['data']['materials'][$material_id]['type'] === "simple"){
                    $new_shapes = json_decode($request->get_body(),true);
                    if($meta_value['data']['materials'][$material_id]['data']['shapes'] !== $new_shapes){
                        $meta_value['data']['materials'][$material_id]['data']['shapes'] = $new_shapes;
                        $update = update_post_meta($config_id,'asowp-configs-meta',$meta_value);
                        if($update === true){
                            return rest_ensure_response(["success"=>true, "message"=>__("Materiel component shapes successfully updated",'all-signs-options-free')]);
                        }else{
                            return rest_ensure_response(["success"=>false, "message"=>__("Materiel component shapes has not been updated",'all-signs-options-free')]);
                        }
                    }else{
                        return rest_ensure_response(["success"=>"same", "message"=>__("No change was observed",'all-signs-options-free')]);
                    }
                }else{
                    return rest_ensure_response(["success"=>false, "message"=>__("Materiel component shapes has not been updated",'all-signs-options-free')]);
                }

            }else{
                return rest_ensure_response(["sucess"=>false,"message"=>__("No config data found",'all-signs-options-free')]);
            }
        }else{
            return rest_ensure_response(["sucess"=>false,"message"=>__("No config data found",'all-signs-options-free')]);
        }
    }
    /**
     * Get a collection of shapes.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_material_shapes( $request ){
        $config_id = $request->get_param('config_id');
        $material_id = $request->get_param('material_id');
        if($config_id != 0){
            $meta_value = get_post_meta($config_id,'asowp-configs-meta',true);
            if(is_array($meta_value) && !empty($meta_value)){
                if(isset($meta_value['data']['materials'][$material_id])){
                    $all_manages_shapes = get_option("asowp_all_shapes",[]);
                    if(isset($meta_value['data']['materials'][$material_id]['data']['shapes']) && count($meta_value['data']['materials'][$material_id]['data']['shapes']) > 0){
                        return rest_ensure_response(["materialShapes"=>$meta_value['data']['materials'][$material_id]['data']['shapes'],"manageShapes"=>$all_manages_shapes]);
                    }else{
                        return rest_ensure_response(["message"=>__('Shape not found','all-signs-options-free'),"manageShapes"=>$all_manages_shapes]);
                    }    
                }
                else{
                    return rest_ensure_response(["success"=>false, "message"=>__("Materiel component shapes has not been updated",'all-signs-options-free')]);
                }
            }else{
                return rest_ensure_response(["sucess"=>false,"message"=>__("No config data found",'all-signs-options-free')]);
            }
        }else{
            return rest_ensure_response(["sucess"=>false,"message"=>__("No config data found",'all-signs-options-free')]);
        }        
    }

    /**
     * Save a collection of sizes.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function save_material_sizes( $request ) {
        $config_id = $request->get_param('config_id');
        $material_id = $request->get_param('material_id');

        if($config_id != 0){
            $meta_value = get_post_meta($config_id,'asowp-configs-meta',true);
            if(is_array($meta_value) && !empty($meta_value)){
                if(isset($meta_value['data']['materials'][$material_id]) && $meta_value['data']['materials'][$material_id]['type'] === "simple"){
                    $new_sizes = json_decode($request->get_body(),true);
                    if($meta_value['data']['materials'][$material_id]['data']['sizes'] !== $new_sizes){
                        $meta_value['data']['materials'][$material_id]['data']['sizes'] = $new_sizes;
                        $update = update_post_meta($config_id,'asowp-configs-meta',$meta_value);
                        if($update === true){
                            return rest_ensure_response(["success"=>true, "message"=>__("Materiel component sizes successfully updated",'all-signs-options-free')]);
                        }else{
                            return rest_ensure_response(["success"=>false, "message"=>__("Materiel component sizes has not been updated",'all-signs-options-free')]);
                        }
                    }else{
                        return rest_ensure_response(["success"=>"same", "message"=>__("No change was observed",'all-signs-options-free')]);
                    }
                }else{
                    return rest_ensure_response(["success"=>false, "message"=>__("Materiel component sizes has not been updated",'all-signs-options-free')]);
                }

            }else{
                return rest_ensure_response(["sucess"=>false,"message"=>__("No config data found",'all-signs-options-free')]);
            }
        }else{
            return rest_ensure_response(["sucess"=>false,"message"=>__("No config data found",'all-signs-options-free')]);
        }
    }
    /**
     * Get a collection of sizes.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_material_sizes( $request ){
        $config_id = $request->get_param('config_id');
        $material_id = $request->get_param('material_id');
        if($config_id != 0){
            $meta_value = get_post_meta($config_id,'asowp-configs-meta',true);
            if(is_array($meta_value) && !empty($meta_value)){
                if(isset($meta_value['data']['materials'][$material_id])){
                    if(count($meta_value['data']['materials'][$material_id]['data']['sizes']['allSizes'])>0){
                        return rest_ensure_response(["materialSizes"=>$meta_value['data']['materials'][$material_id]['data']['sizes'],"measurementUnit"=>$meta_value["data"]["settings"]["customizerSign"]['customizerOptions']["measurementUnit"]]);
                    }else{
                        return rest_ensure_response(["message"=>__('Sizes not found','all-signs-options-free'),"materialSizes"=>$meta_value['data']['materials'][$material_id]['data']['sizes'],"measurementUnit"=>$meta_value["data"]["settings"]["customizerSign"]['customizerOptions']["measurementUnit"]]);
                    }    
                }
                else{
                    return rest_ensure_response(["success"=>false, "message"=>__("Materiel ID not Found",'all-signs-options-free')]);
                }
            }else{
                return rest_ensure_response(["sucess"=>false,"message"=>__("No config data found",'all-signs-options-free')]);
            }
        }else{
            return rest_ensure_response(["sucess"=>false,"message"=>__("No config data found",'all-signs-options-free')]);
        }        
    }

    /**
     * Save a collection of borders.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function save_materials_borders( $request ) {
        $config_id = $request->get_param('config_id');
        $material_id = $request->get_param('material_id');

        if($config_id != 0){
            $meta_value = get_post_meta($config_id,'asowp-configs-meta',true);
            if(is_array($meta_value) && !empty($meta_value)){
                if(isset($meta_value['data']['materials'][$material_id]) && $meta_value['data']['materials'][$material_id]['type'] === "simple"){
                    $new_borders = json_decode($request->get_body(),true);
                    if($meta_value['data']['materials'][$material_id]['data']['borders'] !== $new_borders){
                        $meta_value['data']['materials'][$material_id]['data']['borders'] = $new_borders;
                        $update = update_post_meta($config_id,'asowp-configs-meta',$meta_value);
                        if($update === true){
                            return rest_ensure_response(["success"=>true, "message"=>__("Materiel component borders successfully updated",'all-signs-options-free')]);
                        }else{
                            return rest_ensure_response(["success"=>false, "message"=>__("Materiel component borders has not been updated",'all-signs-options-free')]);
                        }
                    }else{
                        return rest_ensure_response(["success"=>"same", "message"=>__("No change was observed",'all-signs-options-free')]);
                    }
                }else{
                    return rest_ensure_response(["success"=>false, "message"=>__("Materiel component borders has not been updated",'all-signs-options-free')]);
                }

            }else{
                return rest_ensure_response(["sucess"=>false,"message"=>__("No config data found",'all-signs-options-free')]);
            }
        }else{
            return rest_ensure_response(["sucess"=>false,"message"=>__("No config data found",'all-signs-options-free')]);
        }
    }
    /**
     * Get a collection of borders.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_material_borders( $request ){
        $config_id = $request->get_param('config_id');
        $material_id = $request->get_param('material_id');
        if($config_id != 0){
            $meta_value = get_post_meta($config_id,'asowp-configs-meta',true);
            if(is_array($meta_value) && !empty($meta_value)){
                if(isset($meta_value['data']['materials'][$material_id])){
                    $all_manage_borders = get_option("asowp_all_borders",[]);
                    if(count($meta_value['data']['materials'][$material_id]['data']['borders']["allBorders"])>0){
                        return rest_ensure_response(["materialBorders"=>$meta_value['data']['materials'][$material_id]['data']['borders'],"manageBorders"=>$all_manage_borders,"materialSizes"=>$meta_value['data']['materials'][$material_id]['data']['sizes']["allSizes"]]);
                    }else{
                        return rest_ensure_response(["message"=>__('Border not found','all-signs-options-free'),"materialBorders"=>$meta_value['data']['materials'][$material_id]['data']['borders'],"manageBorders"=>$all_manage_borders,"materialSizes"=>$meta_value['data']['materials'][$material_id]['data']['sizes']["allSizes"]]);
                    }    
                }
                else{
                    return rest_ensure_response(["success"=>false, "message"=>__("Materiel component borders has not been updated",'all-signs-options-free')]);
                }
            }else{
                return rest_ensure_response(["sucess"=>false,"message"=>__("No config data found",'all-signs-options-free')]);
            }
        }else{
            return rest_ensure_response(["sucess"=>false,"message"=>__("No config data found",'all-signs-options-free')]);
        }        
    }
    /**
     * Save a collection of textImages.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function save_material_textImages( $request ) {
        $config_id = $request->get_param('config_id');
        $material_id = $request->get_param('material_id');

        if($config_id != 0){
            $meta_value = get_post_meta($config_id,'asowp-configs-meta',true);
            if(is_array($meta_value) && !empty($meta_value)){
                if(isset($meta_value['data']['materials'][$material_id]) && $meta_value['data']['materials'][$material_id]['type'] === "simple"){
                    $new_textImages = json_decode($request->get_body(),true);
                    if($meta_value['data']['materials'][$material_id]['data']['textImages'] !== $new_textImages){
                        $meta_value['data']['materials'][$material_id]['data']['textImages'] = $new_textImages;
                        $update = update_post_meta($config_id,'asowp-configs-meta',$meta_value);
                        if($update === true){
                            return rest_ensure_response(["success"=>true, "message"=>__("Materiel component textImages successfully updated",'all-signs-options-free')]);
                        }else{
                            return rest_ensure_response(["success"=>false, "message"=>__("Materiel component textImages has not been updated",'all-signs-options-free')]);
                        }
                    }else{
                        return rest_ensure_response(["success"=>"same", "message"=>__("No change was observed",'all-signs-options-free')]);
                    }
                }else{
                    return rest_ensure_response(["success"=>false, "message"=>__("Materiel component borders has not been updated",'all-signs-options-free')]);
                }

            }else{
                return rest_ensure_response(["sucess"=>false,"message"=>__("No config data found",'all-signs-options-free')]);
            }
        }else{
            return rest_ensure_response(["sucess"=>false,"message"=>__("No config data found",'all-signs-options-free')]);
        }
    }
    /**
     * Get a collection of text Images.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_material_textImages( $request ){
        $config_id = $request->get_param('config_id');
        $material_id = $request->get_param('material_id');
        if($config_id != 0){
            $meta_value = get_post_meta($config_id,'asowp-configs-meta',true);
            if(is_array($meta_value) && !empty($meta_value)){
                if(isset($meta_value['data']['materials'][$material_id])){
                    if($meta_value['data']['materials'][$material_id]['data']['textImages']){
                        return rest_ensure_response($meta_value['data']['materials'][$material_id]['data']['textImages']);
                    }else{
                        return rest_ensure_response(["message"=>__('Text not found','all-signs-options-free')]);
                    }    
                }
                else{
                    return rest_ensure_response(["success"=>false, "message"=>__("Materiel component textImages has not been found",'all-signs-options-free')]);
                }
            }else{
                return rest_ensure_response(["sucess"=>false,"message"=>__("No config data found",'all-signs-options-free')]);
            }
        }else{
            return rest_ensure_response(["sucess"=>false,"message"=>__("No config data found",'all-signs-options-free')]);
        }        
    }
    /**
     * Save a collection of colors.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function save_material_colors( $request ) {
        $config_id = $request->get_param('config_id');
        $material_id = $request->get_param('material_id');

        if($config_id != 0){
            $meta_value = get_post_meta($config_id,'asowp-configs-meta',true);
            if(is_array($meta_value) && !empty($meta_value)){
                if(isset($meta_value['data']['materials'][$material_id]) && $meta_value['data']['materials'][$material_id]['type'] === "simple"){
                    $new_colors = json_decode($request->get_body(),true);
                    if($meta_value['data']['materials'][$material_id]['data']['colors'] !== $new_colors){
                        $meta_value['data']['materials'][$material_id]['data']['colors'] = $new_colors;
                        $update = update_post_meta($config_id,'asowp-configs-meta',$meta_value);
                        if($update === true){
                            return rest_ensure_response(["success"=>true, "message"=>__("Materiel component colors successfully updated",'all-signs-options-free')]);
                        }else{
                            return rest_ensure_response(["success"=>false, "message"=>__("Materiel component colors has not been updated",'all-signs-options-free')]);
                        }
                    }else{
                        return rest_ensure_response(["success"=>"same", "message"=>__("No change was observed",'all-signs-options-free')]);
                    }
                }else{
                    return rest_ensure_response(["success"=>false, "message"=>__("Materiel component colors has not been updated",'all-signs-options-free')]);
                }

            }else{
                return rest_ensure_response(["sucess"=>false,"message"=>__("No config data found",'all-signs-options-free')]);
            }
        }else{
            return rest_ensure_response(["sucess"=>false,"message"=>__("No config data found",'all-signs-options-free')]);
        }
    }
    /**
     * Get a collection of colors.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_material_colors( $request ){
        $config_id = $request->get_param('config_id');
        $material_id = $request->get_param('material_id');
        if($config_id != 0){
            $meta_value = get_post_meta($config_id,'asowp-configs-meta',true);
            if(is_array($meta_value) && !empty($meta_value)){
                if(isset($meta_value['data']['materials'][$material_id])){
                    if(count($meta_value['data']['materials'][$material_id]['data']['colors']["allColors"])>0){
                        return rest_ensure_response($meta_value['data']['materials'][$material_id]['data']['colors']);
                    }else{
                        return rest_ensure_response(["message"=>__('Color not found','all-signs-options-free'),"colors"=>$meta_value['data']['materials'][$material_id]['data']['colors']]);
                    }    
                }
                else{
                    return rest_ensure_response(["success"=>false, "message"=>__("Materiel component colors has not been updated",'all-signs-options-free')]);
                }
            }else{
                return rest_ensure_response(["sucess"=>false,"message"=>__("No config data found",'all-signs-options-free')]);
            }
        }else{
            return rest_ensure_response(["sucess"=>false,"message"=>__("No config data found",'all-signs-options-free')]);
        }  

    }
    /**
     * Save a collection of fixingMethods.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function save_material_fixingMethods( $request ) {
        $config_id = $request->get_param('config_id');
        $material_id = $request->get_param('material_id');

        if($config_id != 0){
            $meta_value = get_post_meta($config_id,'asowp-configs-meta',true);
            if(is_array($meta_value) && !empty($meta_value)){
                if(isset($meta_value['data']['materials'][$material_id]) && $meta_value['data']['materials'][$material_id]['type'] === "simple"){
                    $new_fixingMethods = json_decode($request->get_body(),true);
                    if($meta_value['data']['materials'][$material_id]['data']['fixingMethods'] != $new_fixingMethods){
                        $meta_value['data']['materials'][$material_id]['data']['fixingMethods'] = $new_fixingMethods;
                        $update = update_post_meta($config_id,'asowp-configs-meta',$meta_value);
                        if($update === true){
                            return rest_ensure_response(["success"=>true, "message"=>__("Materiel component fixingMethods successfully updated",'all-signs-options-free')]);
                        }else{
                            return rest_ensure_response(["success"=>false, "message"=>__("Materiel component fixingMethods has not been updated",'all-signs-options-free')]);
                        }
                    }else{
                        return rest_ensure_response(["success"=>"same", "message"=>__("No change was observed",'all-signs-options-free')]);
                    }
                }else{
                    return rest_ensure_response(["success"=>false, "message"=>__("Materiel component fixingMethods has not been updated",'all-signs-options-free')]);
                }

            }else{
                return rest_ensure_response(["sucess"=>false,"message"=>__("No config data found",'all-signs-options-free')]);
            }
        }else{
            return rest_ensure_response(["sucess"=>false,"message"=>__("No config data found",'all-signs-options-free')]);
        }
    }
    /**
     * Get a collection of fixingMethods.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_material_fixingMethods( $request ){
        $config_id = $request->get_param('config_id');
        $material_id = $request->get_param('material_id');
        if($config_id != 0){
            $meta_value = get_post_meta($config_id,'asowp-configs-meta',true);
            if(is_array($meta_value) && !empty($meta_value)){
                if(isset($meta_value['data']['materials'][$material_id])){
                    $all_manage_fixingMethods = get_option("asowp_all_fixingMethods",[]);
                    if($meta_value['data']['materials'][$material_id]['data']['fixingMethods']){
                        return rest_ensure_response(["materialFixingMethods"=>$meta_value['data']['materials'][$material_id]['data']['fixingMethods'],"manageFixingMethods"=>$all_manage_fixingMethods,"materialSizes"=>$meta_value['data']['materials'][$material_id]['data']['sizes']["allSizes"]]);
                    }else{
                        return rest_ensure_response(["message"=>__('Fixing Methods not found','all-signs-options-free'),"manageFixingMethods"=>$all_manage_fixingMethods,"materialSizes"=>$meta_value['data']['materials'][$material_id]['data']['sizes']["allSizes"]]);
                    }    
                }
                else{
                    return rest_ensure_response(["success"=>false, "message"=>__("Materiel component fixing Methods has not been updated",'all-signs-options-free')]);
                }
            }else{
                return rest_ensure_response(["sucess"=>false,"message"=>__("No config data found",'all-signs-options-free')]);
            }
        }else{
            return rest_ensure_response(["sucess"=>false,"message"=>__("No config data found",'all-signs-options-free')]);
        }
        
    }
    /**
     * Create material additional option.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function create_material_additionalOptions($request) {
        $config_id = $request->get_param('config_id');
        $material_id = $request->get_param('material_id');
        if ($config_id != 0) {
            $meta = get_post_meta($config_id, 'asowp-configs-meta', true);
            if (is_array($meta) && !empty($meta)) {
                if (isset($meta["data"]['materials'][$material_id]) && isset($meta["data"]['materials'][$material_id]['data']['additionalOptions'])) {
                    $new_additional = json_decode($request->get_body(), true);
                    $additional = [
                        "title" => $new_additional['title'],
                        "description" => $new_additional['description'],
                        "icon" => $new_additional['icon'],
                        "options" => $new_additional['options']
                    ];
                    array_push($meta["data"]['materials'][$material_id]['data']['additionalOptions'], $additional);
                    $update = update_post_meta($config_id, 'asowp-configs-meta', $meta);
                    if ($update === true) {
                        return rest_ensure_response(["success" => true, "message" => __("Material component Additional Option successfully added", 'all-signs-options-free')]);
                    } else {
                        return rest_ensure_response(["success" => false, "message" => __("Material component Additional Option has not been added", 'all-signs-options-free')]);
                    }
                } else {
                    return rest_ensure_response(["success" => false, "message" => __("Additional Option array not found in meta data", 'all-signs-options-free')]);
                }
            } else {
                return rest_ensure_response(["success" => false, "message" => __("Material ID not found in meta data", 'all-signs-options-free')]);
            }
        } else {
            return rest_ensure_response(["success" => false, "message" => __("Invalid configuration ID", 'all-signs-options-free')]);
        }
    } 
    /**
     * Read material additional option.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function  get_material_additionalOptions ($request) {
        $config_id = $request->get_param('config_id');
        $material_id = $request->get_param('material_id');
        if($config_id != 0){
            $meta_value = get_post_meta($config_id,'asowp-configs-meta',true);
            if(is_array($meta_value) && !empty($meta_value)){
                if(isset($meta_value['data']['materials'][$material_id])){
                    if($meta_value['data']['materials'][$material_id]['data']['additionalOptions']){
                        return rest_ensure_response($meta_value['data']['materials'][$material_id]['data']['additionalOptions']);
                    }else{
                        return rest_ensure_response(["message"=>__('Additional Option not found','all-signs-options-free')]);
                    }    
                }
                else{
                    return rest_ensure_response(["success"=>false, "message"=>__("Materiel component Additional Option has not been updated",'all-signs-options-free')]);
                }
            }else{
                return rest_ensure_response(["sucess"=>false,"message"=>__("No config data found",'all-signs-options-free')]);
            }
        }else{
            return rest_ensure_response(["sucess"=>false,"message"=>__("No config data found",'all-signs-options-free')]);
        }    
    }
    /**
     * Read material additional option.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function  get_material_additionalOption ($request) {
        $config_id = $request->get_param('config_id');
        $material_id = $request->get_param('material_id');
        $additional_id = $request->get_param('additional_id');
        if($config_id != 0){
            $meta_value = get_post_meta($config_id,'asowp-configs-meta',true);
            if(isset($meta_value["data"]['materials'][$material_id]) && isset($meta_value["data"]['materials'][$material_id]['data']['additionalOptions'][$additional_id])){
                if(count($meta_value["data"]['materials'][$material_id]['data']['additionalOptions'][$additional_id]['options']) > 0){
                    return rest_ensure_response( ["data"=>$meta_value["data"]['materials'][$material_id]['data']['additionalOptions'][$additional_id]]);
                }else{
                    return rest_ensure_response(["data"=>$meta_value["data"]['materials'][$material_id]['data']['additionalOptions'][$additional_id],"message"=>__("No Options Found",'all-signs-options-free')]);
                }
            }else{
                return rest_ensure_response(["sucess"=>false,"message"=>__("No Additional Option data found",'all-signs-options-free')]);
            }
        }else{
            return rest_ensure_response(["sucess"=>false,"message"=>__("No config data found",'all-signs-options-free')]);
        }    
    }
    /**
     * update material additional option.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function update_material_additionalOption ($request) {
        $config_id = $request->get_param('config_id');
        $material_id = $request->get_param('material_id');
        $additional_id = $request->get_param('additional_id');
        if($config_id != 0){
            $meta = get_post_meta($config_id,'asowp-configs-meta',true);
            if(is_array($meta) && !empty($meta)){
                $additional = json_decode($request->get_body(),true);
                if(isset($meta["data"]['materials'][$material_id]) && isset($meta["data"]['materials'][$material_id]['data']['additionalOptions'][$additional_id])){
                    $old_additional = $meta["data"]['materials'][$material_id]['data']['additionalOptions'][$additional_id];
                    if($old_additional !== $additional){
                        $old_additional["title"] = $additional['title'];
                        $old_additional["description"] = $additional['description'];
                        $old_additional["icon"] = $additional['icon'];
                        $old_additional["popImg"] = $additional['popImg'];
                        $old_additional["options"] = $additional['options'];
                        $meta["data"]['materials'][$material_id]['data']['additionalOptions'][$additional_id] = $old_additional;
                        $update = update_post_meta($config_id,'asowp-configs-meta',$meta);
                        if($update === true){
                            return rest_ensure_response(["success"=>true, "message"=>__("Materiel component Additional Option successfully edited",'all-signs-options-free')]);
                        }else{
                            return rest_ensure_response(["success"=>false, "message"=>__("Materiel component Additional Option  has not been edited",'all-signs-options-free')]);
                        }
                    }else{
                        return rest_ensure_response(["success"=>"same", "message"=>__("No change was observed",'all-signs-options-free')]);
                    }
                }else{
                    return rest_ensure_response(["success"=>false, "message"=>__("Materiel component Additional Option  has not been edited",'all-signs-options-free')]);
                }
            }else{
                return rest_ensure_response(["success"=>false, "message"=>__("Materiel component Additional Option  has not been edited",'all-signs-options-free')]);
            }
        }else{
            return rest_ensure_response(["message"=>__("No Configuration found",'all-signs-options-free')]);
        } 
    }
    /**
     * Delatable material additional option.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function delete_material_additionalOption ($request) {
        $config_id = $request->get_param('config_id');
        $material_id = $request->get_param('material_id');
        $additional_id = $request->get_param('additional_id');
        if($config_id != 0){
            $meta = get_post_meta($config_id,'asowp-configs-meta',true);
            if(is_array($meta) && !empty($meta)){
                if($meta["data"]['materials'][$material_id]['data']['additionalOptions'][$additional_id]){
                    array_splice($meta["data"]['materials'][$material_id]['data']['additionalOptions'],$additional_id,1);
                    $update = update_post_meta($config_id,'asowp-configs-meta',$meta);
                    if($update === true){
                        return rest_ensure_response(['success'=>true,"message"=>__("Component Additional Option successfully deleted",'all-signs-options-free')]);
                    }else{                        
                        return rest_ensure_response(['success'=>false,"message"=>__("Component Additional Option have not been deleted",'all-signs-options-free')]);
                    }
                }else{
                    return rest_ensure_response(['success'=>false,"message"=>__("No materials component Additional Option found",'all-signs-options-free')]);
                }
            }else{                    
                return rest_ensure_response(['success'=>false,"message"=>__("No materials component Additional Option found",'all-signs-options-free')]);
            }
        }else{
            return rest_ensure_response(['success'=>false,"message"=>__("No Materials Additional Option found",'all-signs-options-free')]);
        }
        
    }
    /**
     * Create material option.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function create_Option_in_AdditionalOption ($request) {
        $config_id = $request->get_param('config_id');
        $material_id = $request->get_param('material_id');
        $additional_id = $request->get_param('additional_id');
        if ($config_id != 0) {
            $meta = get_post_meta($config_id, 'asowp-configs-meta', true);
            if (is_array($meta) && !empty($meta)) {
                if (isset($meta["data"]['materials'][$material_id]) && isset($meta["data"]['materials'][$material_id]['data']['additionalOptions']) && isset($meta["data"]['materials'][$material_id]['data']['additionalOptions'][$additional_id]['options']) ) {
                    $new_option = json_decode($request->get_body(), true);
                    if(count($meta["data"]['materials'][$material_id]['data']['additionalOptions'][$additional_id]['options'])>0){
                        $i=0;
                        $hasDefault=false;
                        while ($i<count($meta["data"]['materials'][$material_id]['data']['additionalOptions'][$additional_id]['options'])) {
                            if($meta["data"]['materials'][$material_id]['data']['additionalOptions'][$additional_id]['options'][$i]["isDefault"]){
                                $hasDefault = true;
                            }
                            $i++;
                        }
                        if(!$hasDefault){
                            $meta["data"]['materials'][$material_id]['data']['additionalOptions'][$additional_id]['options'][0]["isDefault"]=true;
                        }
                        array_push($meta["data"]['materials'][$material_id]['data']['additionalOptions'][$additional_id]['options'], $new_option);
                    }else{
                        $new_option["isDefault"]=true;
                        $meta["data"]['materials'][$material_id]['data']['additionalOptions'][$additional_id]['options'][0]=$new_option;
                    }
                    $update = update_post_meta($config_id, 'asowp-configs-meta', $meta);
                    if ($update === true) {
                        return rest_ensure_response(["success" => true, "message" => __("Material component Option successfully added", 'all-signs-options-free')]);
                    } else {
                        return rest_ensure_response(["success" => false, "message" => __("Material component  Option has not been added", 'all-signs-options-free')]);
                    }
                } else {
                    return rest_ensure_response(["success" => false, "message" => __(" Option array not found in meta data", 'all-signs-options-free')]);
                }
            } else {
                return rest_ensure_response(["success" => false, "message" => __("Material ID not found in meta data", 'all-signs-options-free')]);
            }
        } else {
            return rest_ensure_response(["success" => false, "message" => __("Invalid configuration ID", 'all-signs-options-free')]);
        }  
    }
    /**
     * Read material option.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_Option_In_AdditionalOption ($request) {
        $config_id = $request->get_param('config_id');
        $material_id = $request->get_param('material_id');
        $additional_id = $request->get_param('additional_id');
        $option_id = $request->get_param('option_id');
        if($config_id != 0){
            $meta_value = get_post_meta($config_id,'asowp-configs-meta',true);
            if(is_array($meta_value) && !empty($meta_value)){
                if(isset($meta_value['data']['materials'][$material_id])){
                    if($meta_value['data']['materials'][$material_id]['data']['additionalOptions'][$additional_id]['options']){
                        return rest_ensure_response($meta_value['data']['materials'][$material_id]['data']['additionalOptions'][$additional_id]['options'][$option_id]);
                    }else{
                        return rest_ensure_response(["message"=>__(' Option not found','all-signs-options-free')]);
                    }    
                }
                else{
                    return rest_ensure_response(["success"=>false, "message"=>__("Materiel component  Option has not been updated",'all-signs-options-free')]);
                }
            }else{
                return rest_ensure_response(["sucess"=>false,"message"=>__("No config data found",'all-signs-options-free')]);
            }
        }else{
            return rest_ensure_response(["sucess"=>false,"message"=>__("No config data found",'all-signs-options-free')]);
        } 
    }
    /**
     * update material option.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function update_Option_In_AdditionalOption ($request) {
        $config_id = $request->get_param('config_id');
        $material_id = $request->get_param('material_id');
        $additional_id = $request->get_param('additional_id');
        $option_id = $request->get_param('option_id');
        if($config_id != 0){
            $meta = get_post_meta($config_id,'asowp-configs-meta',true);
            if(is_array($meta) && !empty($meta)){
                $option = json_decode($request->get_body(),true);
                if(isset($meta["data"]['materials'][$material_id]) && isset($meta["data"]['materials'][$material_id]['data']['additionalOptions'][$additional_id])){
                    $old_option = $meta["data"]['materials'][$material_id]['data']['additionalOptions'][$additional_id]['options'][$option_id];
                    if($old_option !== $option){
                        $old_option["title"] = $option['title'];
                        $old_option["description"] = $option['description'];
                        $old_option["icon"] = $option['icon'];
                        $old_option["image"] = $option['image'];
                        $old_option["additionalPrice"] = $option['additionalPrice'];
                        $meta["data"]['materials'][$material_id]['data']['additionalOptions'][$additional_id]['options'][$option_id] = $old_option;
                        $update = update_post_meta($config_id,'asowp-configs-meta',$meta);
                        if($update === true){
                            return rest_ensure_response(["success"=>true, "message"=>__("Materiel component  Option successfully edited",'all-signs-options-free')]);
                        }else{
                            return rest_ensure_response(["success"=>false, "message"=>__("Materiel component  Option  has not been edited",'all-signs-options-free')]);
                        }
                    }else{
                        return rest_ensure_response(["success"=>"same", "message"=>__("No change was observed",'all-signs-options-free')]);
                    }
                }else{
                    return rest_ensure_response(["success"=>false, "message"=>__("Materiel component Option  has not been edited",'all-signs-options-free')]);
                }
            }else{
                return rest_ensure_response(["success"=>false, "message"=>__("Materiel component  Option  has not been edited",'all-signs-options-free')]);
            }
        }else{
            return rest_ensure_response(["message"=>__("No Configuration found",'all-signs-options-free')]);
        } 
    }

    /**
     * Delatable material option.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function delete_Option_In_AdditionalOption ($request) {
        $config_id = $request->get_param('config_id');
        $material_id = $request->get_param('material_id');
        $additional_id = $request->get_param('additional_id');
        $option_id = $request->get_param('additional_id');
        if($config_id != 0){
            $meta = get_post_meta($config_id,'asowp-configs-meta',true);
            if(is_array($meta) && !empty($meta)){
                if($meta["data"]['materials'][$material_id]['data']['additionalOptions'][$additional_id]['options'][$option_id]){
                    array_splice($meta["data"]['materials'][$material_id]['data']['additionalOptions'][$additional_id]['options'],$option_id,1);
                    $update = update_post_meta($config_id, 'asowp-configs-meta', $meta);
                    if ($update === true) 
                        return rest_ensure_response(['success'=>true,"message"=>__("Component  Option successfully deleted",'all-signs-options-free')]);
                    else 
                        return rest_ensure_response(['success'=>true,"message"=>__("Component  Option have not been deleted",'all-signs-options-free')]);

                }else{
                    return rest_ensure_response(['success'=>false,"message"=>__("No materials component  Option found",'all-signs-options-free')]);
                }
            }else{                    
                return rest_ensure_response(['success'=>false,"message"=>__("No materials component Option found",'all-signs-options-free')]);
            }
        }else{
            return rest_ensure_response(['success'=>false,"message"=>__("No Materials Option found",'all-signs-options-free')]);
        }  
    }
    /**
     * Checks if a given request has access to read the items.
     *
     * @param  WP_REST_Request $request Full details about the request.
     *
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function get_items_permissions_check( $request ) {
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
