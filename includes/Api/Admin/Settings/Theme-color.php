<?php
namespace ASOWP\Api\Admin\Settings;

use WP_Error;
use WP_Post;
use WP_Query;
use WP_REST_Controller;

/**
 * class for api routes reaching custom design settings
 */
class ASOWP_Api_Theme_color_Settings extends WP_REST_Controller {
    
    /**
     * [__construct description]
     */
    public function __construct() {
        $this->namespace = 'asowp/v1';
        $this->rest_base = 'configs/(?P<config_id>\d+)/settings/theme-colors';
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
                    'callback'            => array( $this, 'get_theme_color_settings' ),
                    'permission_callback' => array( $this, 'get_permissions_check' ),
                    'args'                => array(
                        'config_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        )
                    ),
                ),
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'update_theme_color_settings' ),
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
    public function get_theme_color_settings ($request){
        $id = $request->get_param('config_id');
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'asowp-configs-meta', true);
                if(empty($meta_value)){
                    return rest_ensure_response(["message" => "No Settings found"]);
                }else{
                    if(isset($meta_value["data"]["settings"]["themeColors"])){

                        return rest_ensure_response($meta_value["data"]["settings"]["themeColors"]);
                    }
                    return rest_ensure_response(["message" => __("No Theme color Settings found",'all-signs-options-free')]);
                }
            }else{
                return rest_ensure_response(["message" => __(" Theme color ID invalid",'all-signs-options-free')]);
            }
        }else{
            return rest_ensure_response(["message" => __("Theme color ID i nvalid",'all-signs-options-free')]);
        }
    }
    public function update_theme_color_settings ($request){
        $id = $request->get_param('config_id');
        $theme_colors = json_decode($request->get_body(),true);
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'asowp-configs-meta', true);
                if($theme_colors != $meta_value["data"]["settings"]["themeColors"]){
                    $meta_value["data"]["settings"]["themeColors"] = $theme_colors;
                    $response = update_post_meta($id,'asowp-configs-meta',$meta_value);
                    if($response){
                        return rest_ensure_response(["success" =>true, "message"=>__("Theme Colors update successfully settings added successfully",'all-signs-options-free')]);
                    }else{
                        return rest_ensure_response(["success" =>false, "message"=> __("Add Theme Colors update successfully settings failed",'all-signs-options-free')]);
                    }
                }else{
                    return rest_ensure_response(["success"=>"same","message" => __("No change observed in theme colors",'all-signs-options-free')]);
                }
            }else{
                return rest_ensure_response(["success" =>false,"message" => __(" Theme color ID invalid",'all-signs-options-free')]);
            }
        }else{
            return rest_ensure_response(["success" =>false,"message" => __("Theme color ID i nvalid",'all-signs-options-free')]);
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