<?php
namespace ASOWP\Api\Admin\Settings;

use WP_Error;
use WP_Post;
use WP_Query;
use WP_REST_Controller;

/**
 * class for api routes reaching language images settings
 */
class ASOWP_Api_Language_Images_Settings extends WP_REST_Controller {
    
    /**
     * [__construct description]
     */
    public function __construct() {
        $this->namespace = 'asowp/v1';
        $this->rest_base = 'configs/(?P<config_id>\d+)/settings/language-images';
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
                    'callback'            => array( $this, 'get_language_images_settings' ),
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
            '/' . $this->rest_base."/main",
            array(
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'update_main_options_language_images_settings' ),
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
            '/' . $this->rest_base."/upload-design",
            array(
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'update_upload_design_options_language_images_settings' ),
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
            '/' . $this->rest_base."/visualizer",
            array(
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'update_visualizer_options_language_images_settings' ),
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
                    'callback'            => array( $this, 'update_image_language_images_settings' ),
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
     * Get all language images settings
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    public function get_language_images_settings($request){
        $id = $request->get_param('config_id');
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'asowp-configs-meta', true);
                if(empty($meta_value)){
                    return rest_ensure_response(["message" => "No Settings found"]);
                }else{
                    if(isset($meta_value["data"]["settings"]["languageImages"])){
                        return rest_ensure_response($meta_value["data"]["settings"]["languageImages"]);
                    }
                    return rest_ensure_response(["message" => __("No language Images Settings found",'all-signs-options-free')]);
                }
            }else{
                return rest_ensure_response(["message" => __("Custom ID invalid",'all-signs-options-free')]);
            }
        }else{
            return rest_ensure_response(["message" =>__("Custom ID invalid",'all-signs-options-free')]);
        }
    }
     /**
     * Update Main options of language images settings 
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    public function update_main_options_language_images_settings($request){
        $id = $request->get_param('config_id');
        $main_options = json_decode($request->get_body(),true);
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'asowp-configs-meta', true);
                
                if($meta_value["data"]["settings"]["languageImages"]['main'] != $main_options){
                    $meta_value["data"]["settings"]["languageImages"]['main'] = $main_options;
                    
                    $response = update_post_meta($id,'asowp-configs-meta',$meta_value);

                    if($response){
                        return rest_ensure_response(["success" =>true,"message"=>__("Main options in language Images settings updated successfully",'all-signs-options-free')]);
                    }else{
                        return rest_ensure_response(["success" =>false,"message" => __("Update Main options in language Images settings failed",'all-signs-options-free')]);
                    }
                }else{
                    return rest_ensure_response(["success" =>"same","message" => __("No change observed in Main options in language Images settings failed",'all-signs-options-free')]);
                }
            }else{
                return rest_ensure_response(["success" =>false,"message" => __("Custom ID invalid",'all-signs-options-free')]);
            }
        }else{
            return rest_ensure_response(["success" =>false,"message" => __("Custom ID invalid",'all-signs-options-free')]);
        }
    }
     /**
     * Update upload design options of language images settings 
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    public function  update_upload_design_options_language_images_settings($request){
        $id = $request->get_param('config_id');
        $customizer_options = json_decode($request->get_body(),true);
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'asowp-configs-meta', true);
                
                if($meta_value["data"]["settings"]["languageImages"]['uploadDesign'] !=   $customizer_options){
                    $meta_value["data"]["settings"]["languageImages"]['uploadDesign'] =   $customizer_options;
                    
                    $response = update_post_meta($id,'asowp-configs-meta',$meta_value);

                    if($response){
                        return rest_ensure_response(["success" =>true,"message"=> __("Customizer Design options in language Images settings updated successfully",'all-signs-options-free')]);
                    }else{
                        return rest_ensure_response(["success" =>false,"message" => __("Update Customizer Design options in language Images settings failed",'all-signs-options-free')]);
                    }
                }else{
                    return rest_ensure_response(["success"=>"same","message" => __("No change observed in Customizer Design options in language Images settings",'all-signs-options-free')]);
                }
            }else{
                return rest_ensure_response(["success" =>false,"message" => __("Custom ID invalid",'all-signs-options-free')]);
            }
        }else{
            return rest_ensure_response(["success" =>false,"message" => __("Custom ID invalid",'all-signs-options-free')]);
        }
    }
     /**
     * Update visualizer design options of language images settings 
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    public function update_visualizer_options_language_images_settings ($request){
        $id = $request->get_param('config_id');
        $visualizer_options = json_decode($request->get_body(),true);
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'asowp-configs-meta', true);
                
                if($meta_value["data"]["settings"]["languageImages"]['visualizer'] !=   $visualizer_options){
                    $meta_value["data"]["settings"]["languageImages"]['visualizer'] =   $visualizer_options;

                    $response = update_post_meta($id,'asowp-configs-meta',$meta_value);

                    if($response){
                        return rest_ensure_response(["success" =>true, "message"=> __("Visualizer options in language Images settings updated successfully",'all-signs-options-free')]);
                    }else{
                        return rest_ensure_response(["success"=>false,"message" => __("update Visualizer options in language Images settings failed",'all-signs-options-free')]);
                    }
                }else{
                    return rest_ensure_response(["success"=>"same","message" => __("No change observed in Visualizer options in language Images",'all-signs-options-free')]);
                }
            }else{
                return rest_ensure_response(["success" =>false,"message" => __("Custom ID invalid",'all-signs-options-free')]);
            }
        }else{
            return rest_ensure_response(["success" =>false,"message" => __("Custom ID invalid",'all-signs-options-free')]);
        }
    }
     /**
     * Update images options of language images settings 
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
    */
    public function update_image_language_images_settings ($request){
        $id = $request->get_param('config_id');
        $image_options = json_decode($request->get_body(),true);
        if($id!=0){
            $post = get_post($id);
            if($post){
                $meta_value = get_post_meta($id, 'asowp-configs-meta', true);
                
                if($meta_value["data"]["settings"]["languageImages"]['images'] !=   $image_options){
                    $meta_value["data"]["settings"]["languageImages"]['images'] =   $image_options;
                    $response = update_post_meta($id,'asowp-configs-meta',$meta_value);

                    if($response){
                        return rest_ensure_response(["success" =>true,"message"=> __("Images options in language Images settings updated successfully",'all-signs-options-free')]);
                    }else{
                        return rest_ensure_response(["success" =>false,"message" => __("Update Images options in language Images settings failed",'all-signs-options-free')]);
                    }
                }else{
                    return rest_ensure_response(["success"=>"same","message" => __("No change observed in Images options in language Images settings",'all-signs-options-free')]);
                }
            }else{
                return rest_ensure_response(["success" =>false,"message" => __("Custom ID invalid",'all-signs-options-free')]);
            }
        }else{
            return rest_ensure_response(["success" =>false,"message" => __("Custom ID invalid",'all-signs-options-free')]);
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