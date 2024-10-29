<?php
namespace ASOWP\Api\Admin\Settings;

use WP_Error;
use WP_Post;
use WP_Query;
use WP_REST_Controller;

/**
 * class for api routes reaching generals settings
 */
class ASOWP_Api_General_Settings extends WP_REST_Controller {
    
    /**
     * [__construct description]
     */
    public function __construct() {
        $this->namespace = 'asowp/v1';
        $this->rest_base = 'configs/(?P<config_id>\d+)/settings/generals';
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
                    'callback'            => array( $this, 'get_generals_settings' ),
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
            '/' . $this->rest_base."/product",
            array(
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'update_product_options_generals_settings' ),
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
            '/' . $this->rest_base."/output",
            array(
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'update_output_options_generals_settings' ),
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
            '/' . $this->rest_base."/mobile",
            array(
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'update_mobile_options_generals_settings' ),
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
     * Get all generals settings
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    public function get_generals_settings($request){
        $id = $request->get_param('config_id');
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'asowp-configs-meta', true);
                if(empty($meta_value)){
                    return rest_ensure_response(["message" => __("No Settings found",'all-signs-options-free')]);
                }else{
                    if(isset($meta_value["data"]["settings"]["generals"])){
                        return rest_ensure_response($meta_value["data"]["settings"]["generals"]);
                    }
                    return rest_ensure_response(["message" => __("No generals Settings found",'all-signs-options-free')]);
                }
            }else{
                return rest_ensure_response(["message" =>__("Custom ID invalid",'all-signs-options-free')]);
            }
        }else{
            return rest_ensure_response(["message" => __("Custom ID invalid",'all-signs-options-free')]);
        }
    }
     /**
     * Update Product options of generals settings 
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    public function update_product_options_generals_settings ($request){
        $id = $request->get_param('config_id');
        $product_options = json_decode($request->get_body(),true);
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'asowp-configs-meta', true);
                
                if($meta_value["data"]["settings"]["generals"]['product'] != $product_options){
                    $meta_value["data"]["settings"]["generals"]['product'] = $product_options;
                    $response = update_post_meta($id,'asowp-configs-meta',$meta_value);

                    if($response){
                        return rest_ensure_response(["success" =>true,"message"=> __("Product options in generals settings updated successfully",'all-signs-options-free')]);
                    }else{
                        return rest_ensure_response(["success" =>false,"message" => __("Update product options in generals settings failed",'all-signs-options-free')]);
                    }
                }else{
                    return rest_ensure_response(["success" =>"same","message" => __("No change observed in product options in generals settings failed",'all-signs-options-free')]);
                }
            }else{
                return rest_ensure_response(["success" =>false,"message" => __("Custom ID invalid",'all-signs-options-free')]);
            }
        }else{
            return rest_ensure_response(["success" =>false,"message" => __("Custom ID invalid",'all-signs-options-free')]);
        }
    }

    /**
     * Update Mobile options of generals settings 
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    public function update_mobile_options_generals_settings($request){
        $id = $request->get_param('config_id');
        $mobile_options = json_decode($request->get_body(),true);
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'asowp-configs-meta', true);
                
                if($meta_value["data"]["settings"]["generals"]['mobile'] != $mobile_options){
                    $meta_value["data"]["settings"]["generals"]['mobile'] = $mobile_options;
                    $response = update_post_meta($id,'asowp-configs-meta',$meta_value);

                    if($response){
                        return rest_ensure_response(["success" =>true,"message" => __("Mobile options in generals settings updated successfully",'all-signs-options-free')]);
                    }else{
                        return rest_ensure_response(["success" =>false,"message" => __("Update mobile options in generals settings failed",'all-signs-options-free')]);
                    }
                }else{
                    return rest_ensure_response(["success" =>"same","message" => __("No change observed in mobile options in generals settings failed",'all-signs-options-free')]);

                }
            }else{
                return rest_ensure_response(["success" =>false,"message" => __("Custom ID invalid",'all-signs-options-free')]);
            }
        }else{
            return rest_ensure_response(["success" =>false,"message" => __("Custom ID invalid",'all-signs-options-free')]);
        }
    }
    /**
     * Update Output options of generals settings 
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    public function update_output_options_generals_settings($request){
        $id = $request->get_param('config_id');
        $output_options=json_decode($request->get_body(),true);
        if($id!=0){
            $post=get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'asowp-configs-meta', true); 
                
                if($meta_value["data"]["settings"]["generals"]['output'] != $output_options){
                    $meta_value["data"]["settings"]["generals"]['output']=$output_options;
                    $response= update_post_meta($id,'asowp-configs-meta',$meta_value);
                    if($response){
                        return rest_ensure_response(["success" => true,"message" => __("Output options in generals settings updated successfully",'all-signs-options-free')]);
                    }else{
                        return rest_ensure_response(["success" => false,"message" => __("Update output options in generals settings failed",'all-signs-options-free')]);
                    }
                }else{
                    return rest_ensure_response(["success" => "same","message" => __("No change observed in output options in generals settings failed",'all-signs-options-free')]);
                }
            }else{
                return rest_ensure_response(["success" => false,"message" => __("Custom ID invalid",'all-signs-options-free')]);
            }
        }else{
            return rest_ensure_response(["success" => false,"message" => __("Custom ID invalid",'all-signs-options-free')]);
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

   
