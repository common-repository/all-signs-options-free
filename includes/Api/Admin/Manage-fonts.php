<?php
namespace ASOWP\Api\Admin;

use WP_Error;
use WP_Post;
use WP_Query;
use WP_REST_Controller;



class ASOWP_Api_Manage_fonts extends WP_REST_Controller {

    /**
     * [__construct description]
     */
    public function __construct() {
        $this->namespace = 'asowp/v1';
        $this->rest_base = 'manage-fonts';
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
                    'callback'            => array( $this, 'get_manage_fonts' ),
                    'permission_callback' => array( $this, 'get_config_permissions_check' )
                ),
                array(
                    'methods'             => \WP_REST_Server::CREATABLE,
                    'callback'            => array( $this, 'add_font_to_manages_fonts' ),
                    'permission_callback' => array( $this, 'get_config_permissions_check' ),
                ),
            )
        );
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base."/(?P<font_id>\d+)",
            array(
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_manage_font' ),
                    'permission_callback' => array( $this, 'get_config_permissions_check' ),
                    'args'                => array (
                        'font_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        )
                    ),
                ),
                array(
                    'methods'             => \WP_REST_Server::EDITABLE,
                    'callback'            => array( $this, 'update_font' ),
                    'permission_callback' => array( $this, 'get_config_permissions_check' ),
                    'args'                => array (
                        'font_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        )
                    ),
                ),
                array(
                    'methods'             => \WP_REST_Server::DELETABLE,
                    'callback'            => array( $this, 'delete_manage_fonts_config'),
                    'permission_callback' => array( $this, 'get_config_permissions_check' ),
                    'args'                => array (
                        'font_id' => array (
                            'type' => 'integer',
                            'required' => true,
                        )
                    ),
                )
            )
        );
    }
       /**
     * Create fonts group
     * @param \WP_REST_Request $request Full details about the request.
     *
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function add_font_to_manages_fonts ( $request ) {
        $data=json_decode($request->get_body(),true);
        $all_fonts = get_option("asowp-manages-fonts",[]);
        if($data["many"] == true){
            foreach ($data["fonts"] as $font) {
                array_push($all_fonts,$font);
            }
        }else{
            array_push($all_fonts,$data["font"]);
        }        
        $update = update_option("asowp-manages-fonts",$all_fonts);
        if($update){
            return rest_ensure_response( ["success"=>true,"message"=>__("Font created with success",'all-signs-options-free')] );
        }else{
            return rest_ensure_response(["success"=>false,"message" => "Registration failed"]);
        }
    }
    /**
     * Get all fonts
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_manage_fonts($request){
        $all_fonts = get_option("asowp-manages-fonts",[]);
        if(count($all_fonts) != 0){
            return rest_ensure_response($all_fonts);          
        }else{
            return rest_ensure_response(["message"=>__("No Fonts Found",'all-signs-options-free')]);
        }
    }
    /**
     * Get font info from id
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_manage_font($request){
        $font_id=$request->get_param('font_id');
        $all_fonts = get_option("asowp-manages-fonts",[]);
        if($all_fonts[$font_id]){
            return rest_ensure_response($all_fonts[$font_id]);      
        }else{
            return rest_ensure_response(["success"=>false,"message"=>__('Font not found','all-signs-options-free')]);
        }
        
    }


    /**
     * Update of font from ID
     * @param \WP_REST_Request $request Full details about the request.
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function update_font($request){
        $font=json_decode($request->get_body(),true);
        $font_id = $request->get_param( 'font_id' );
        $all_fonts = get_option("asowp-manages-fonts",[]);
        if($all_fonts[$font_id]){
            if($all_fonts[$font_id] != $font){
                $all_fonts[$font_id] = $font;
                $update = update_option("asowp-manages-fonts",$all_fonts);
                if($update){
                    return rest_ensure_response(array('success' => true, "message" => __("The font has been updated with success",'all-signs-options-free') ) );
                }
                else{
                    return rest_ensure_response(array('success' => false, "message"=>__("Font update failed",'all-signs-options-free') ) );
                } 
            }else{
                return rest_ensure_response(array('success' => "same", "message"=>__("No change observed in font",'all-signs-options-free') ) );
            }
        }else{
            return rest_ensure_response(["success"=>false,"message"=>__('Font not found','all-signs-options-free')]);
        }
        
    }

    /**
     * Remove aso fonts from ID in request
     * @param \WP_REST_Request $request Full details about the request.
     *
     * @return $success message if is ok and fail otherwise. 
    */
    public function delete_manage_fonts_config($request){
        $font_id = $request->get_param( 'font_id' );
        $all_fonts = get_option("asowp-manages-fonts",[]);
        if($all_fonts[$font_id]){
            array_splice($all_fonts,$font_id,1);
            $update = update_option("asowp-manages-fonts",$all_fonts);
            if($update){
                return rest_ensure_response(["success"=>true,"message"=>__("The font was well removed",'all-signs-options-free')]);
            }else{
                return rest_ensure_response(["success"=>false,"message"=>__("Deleting the font failed",'all-signs-options-free')]);   
            }
        }else{
            return rest_ensure_response(["success"=>false,"message"=>__('Font not found','all-signs-options-free')]);
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
     * Create an item in font group
     *
     * @param \WP_REST_Request $request Full details about the request.
     *
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */

    /**
     * Retrieves the query params for the items collection.
     *
     * @return array Collection parameters.
     */
    public function get_collection_params() {
            return [];
    }
}
