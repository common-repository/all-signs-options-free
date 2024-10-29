<?php
namespace ASOWP\Api\Admin\Settings;

use WP_Error;
use WP_Post;
use WP_Query;
use WP_REST_Controller;

/**
 * class for api routes reaching custom design settings
 */
class ASOWP_Api_Customizer_Sign_Settings extends WP_REST_Controller {
    
    /**
     * [__construct description]
     */
    public function __construct() {
        $this->namespace = 'asowp/v1';
        $this->rest_base = 'configs/(?P<config_id>\d+)/settings/customizer-signs';
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
                    'callback'            => array( $this, 'get_customizer_sign_settings' ),
                    'permission_callback' => array( $this, 'get_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        )
                    ),
                )
            )
        );
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base."/customizer",
            array(
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'update_customizer_options_customizer_sign_settings' ),
                    'permission_callback' => array( $this, 'get_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        )
                    ),
                )
            )
        );
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base."/sign-part",
            array(
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'update_sign_part_customizer_sign_settings' ),
                    'permission_callback' => array( $this, 'get_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        )
                    ),
                )
            )
        );
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base."/text",
            array(
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'update_text_option_customizer_sign_settings' ),
                    'permission_callback' => array( $this, 'get_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        )
                    ),
                )
            )
        );
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base."/images",
            array(
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'updates_image_customizer_sign_settings' ),
                    'permission_callback' => array( $this, 'get_permissions_check' ),
                    'args'                => array(
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
     * Get all customizer sign settings
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    public function get_customizer_sign_settings($request){
        $id = $request->get_param('config_id');
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'asowp-configs-meta', true);
                if(empty($meta_value)){
                    return rest_ensure_response(["success"=>false,"message" => __("No Settings found",'all-signs-options-free')]);
                }else{
                    if(isset($meta_value["data"]["settings"]["customizerSign"])){

                        return rest_ensure_response($meta_value["data"]["settings"]["customizerSign"]);
                    }
                    return rest_ensure_response(["success"=>false,"message" => __("No Customizer Sign Settings found",'all-signs-options-free')]);
                }
            }else{
                return rest_ensure_response(["success"=>false,"message" => __("Customizer ID invalid",'all-signs-options-free')]);
            }
        }else{
            return rest_ensure_response(["success"=>false,"message" => __("Customizer ID invalid",'all-signs-options-free')]);
        }
    }
    /**
     * Update Customizer options of customizer sign settings 
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    public function update_customizer_options_customizer_sign_settings($request){
        $id = $request->get_param('config_id');
        $customizer_options = json_decode($request->get_body(),true);
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'asowp-configs-meta', true);
                $meta_value["data"]["settings"]["customizerSign"]['customizerOptions'] = $customizer_options;

                $response = update_post_meta($id,'asowp-configs-meta',$meta_value);

                if($response){
                    return rest_ensure_response(["success"=>true,"message" => __("customizer in customizer sign settings upadted successfully",'all-signs-options-free')]);
                }else{
                    return rest_ensure_response(["success"=>false,"message" => __("Update customizer in customizer sign settings failed",'all-signs-options-free')]);
                }
            }else{
                return rest_ensure_response(["success"=>false,"message" => __("Custom ID invalid",'all-signs-options-free')]);
            }
        }else{
            return rest_ensure_response(["success"=>false,"message" => __("Custom ID invalid",'all-signs-options-free')]);
        }
    }
    /**
     * Update Sign part options of customizer sign settings 
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    public function update_sign_part_customizer_sign_settings($request){
        $id = $request->get_param('config_id');
        $sign_options = json_decode($request->get_body(),true);
            if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'asowp-configs-meta', true);
                
                if($meta_value["data"]["settings"]["customizerSign"]['signPart'] != $sign_options){
                    $meta_value["data"]["settings"]["customizerSign"]['signPart'] = $sign_options;
                    
                    $response = update_post_meta($id,'asowp-configs-meta',$meta_value);
                    if($response){
                        return rest_ensure_response(["success"=>true,"message" => __("Sign part in customizer sign settings upadted successfully",'all-signs-options-free')]);
                    }else{
                        return rest_ensure_response(["success"=>false,"message" => __("Update Sign part in customizer sign settings failed",'all-signs-options-free')]);
                    }
                }else{
                    return rest_ensure_response(["success"=>"same","message" => __("No change observed in Sign part in customizer sign settings ",'all-signs-options-free')]);
                }
            }else{
                return rest_ensure_response(["success"=>false,"message" => __("Custom ID invalid",'all-signs-options-free')]);
            }
        }else{  
            return rest_ensure_response(["success"=>false,"message" => __("Custom ID invalid",'all-signs-options-free')]);
        }
    }
    /**
     * Update Text options of customizer sign settings 
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    public function update_text_option_customizer_sign_settings ($request){
       $id = $request->get_param('config_id');
       $text_options = json_decode($request->get_body(),true);
       if($id!=0){
          $post = get_post($id);
          if($post){
                $meta_value = get_post_meta($id,'asowp-configs-meta',true);
                
                if($meta_value["data"]["settings"]["customizerSign"]['text'] != $text_options){
                    $meta_value["data"]["settings"]["customizerSign"]['text'] =$text_options; 
                    
                    $response = update_post_meta($id,'asowp-configs-meta',$meta_value);
                    if($response){
                        return rest_ensure_response(["success"=>true,"message"=>__("Text in customizer sign settings upadted successfully",'all-signs-options-free')]);
                    }else{
                        return rest_ensure_response(["success"=>false,"message"=>__("Update Text in customizer sign settings failed",'all-signs-options-free')]);
                    }
                }else{
                    return rest_ensure_response(["success"=>"same","message"=>__("No change observed in Text in customizer sign settings",'all-signs-options-free')]);

                }
            }else{
                return rest_ensure_response(["success"=>false,"message" => __("Custom ID invalid",'all-signs-options-free')]);
            }
        }else{  
            return rest_ensure_response(["success"=>false,"message" => __("Custom ID invalid",'all-signs-options-free')]);
        }
    }
    /**
     * Update   Image options of customizer sign settings 
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    public function updates_image_customizer_sign_settings($request){
        $id = $request->get_param('config_id');
        $image_options = json_decode($request->get_body(),true);
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'asowp-configs-meta', true);
                if($meta_value["data"]["settings"]["customizerSign"]['images'] != $image_options){
                    $meta_value["data"]["settings"]["customizerSign"]['images'] = $image_options;
                    $response = update_post_meta($id,'asowp-configs-meta',$meta_value);

                    if($response){
                        return rest_ensure_response(["success"=>true,"message" => __("image in Customizer sign settings upadted successfully",'all-signs-options-free')]);
                    }else{
                        return rest_ensure_response(["success"=>false,"message" =>__( "Update image in Customizer sign settings failed",'all-signs-options-free')]);
                    }
                }else{
                    return rest_ensure_response(["success"=>"same","message" =>__( "No change observed in image in Customizer sign settings",'all-signs-options-free')]);

                }
            }else{
                return rest_ensure_response(["success"=>false,"message" => __("Custom ID invalid",'all-signs-options-free')]);
            }
        }else{
            return rest_ensure_response(["success"=>false,"message" => __("Custom ID invalid",'all-signs-options-free')]);
        }  
    }
    /**
     * Checks if a given request has access to read the materials.
     *
     * @param  WP_REST_Request $request Full details about the request.
     *
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function get_permissions_check( $request ) {
        return true;
    }
}   