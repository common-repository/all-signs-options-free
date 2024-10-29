<?php
namespace ASOWP\Api\Admin\Globals_Settings;

use WP_Error;
use WP_Poste;
use WP_Query;
use WP_REST_Controller;


/**
 * class for api routes reaching generals settings
*/

class ASOWP_Api_Globals_Settings extends WP_REST_Controller {
   
  /**
   * [__construct description]
  */
  public function __construct(){
    $this->namespace = 'asowp/v1';
    $this->rest_base = 'globals-settings';
    }
  /**
    * Register the routes
    *
    * @return void
    */
  
  public function register_route(){
    register_rest_route(
      $this->namespace,
      '/' . $this->rest_base."/config-page",
      array(
        array(
          'methods'             => \WP_REST_Server::CREATABLE,
          'callback'            => array( $this, 'save_config_page' ),
          'permission_callback' => array( $this, 'get_config_permissions_check' ),
        ),
        array(
          'methods'             => \WP_REST_Server::READABLE,
          'callback'            => array( $this, 'get_config_pages' ),
          'permission_callback' => array( $this, 'get_config_permissions_check' ),
        ),
      )
    );
  register_rest_route(
      $this->namespace,
      '/' . $this->rest_base."/pages",
      array(
        array(
          'methods'             => \WP_REST_Server::CREATABLE,
          'callback'            => array( $this, 'create_new_page' ),
          'permission_callback' => array( $this, 'get_config_permissions_check' ),
        ),
        array(
          'methods'             => \WP_REST_Server::READABLE,
          'callback'            => array( $this, 'get_available_pages' ),
          'permission_callback' => array( $this, 'get_config_permissions_check' ),
        ),
      )
    );
    /* register_rest_route(
      $this->namespace,
      '/' . $this->rest_base."/template-page",
      array(
        array(
          'methods'             => \WP_REST_Server::CREATABLE,
          'callback'            => array( $this, 'save_config_theme' ),
          'permission_callback' => array( $this, 'get_config_permissions_check' ),
        ),
        array(
          'methods'             => \WP_REST_Server::READABLE,
          'callback'            => array( $this, 'get_config_theme' ),
          'permission_callback' => array( $this, 'get_config_permissions_check' ),
        ),
      )
    ); */
    register_rest_route(
      $this->namespace,
      '/' . $this->rest_base."/output",
      array(
        array(
          'methods'             => \WP_REST_Server::READABLE,
          'callback'            => array( $this, 'get_output_options_globals_settings' ),
          'permission_callback' => array( $this, 'get_config_permissions_check' ),
        ),
        array(
          'methods'             => \WP_REST_Server::EDITABLE,
          'callback'            => array( $this, 'update_output_options_globals_settings' ),
          'permission_callback' => array( $this, 'get_config_permissions_check' ),
        )
      )
    );
    register_rest_route(
      $this->namespace,
      '/' . $this->rest_base."/shapes",
      array(
        array(
          'methods'             => \WP_REST_Server::READABLE,
          'callback'            => array( $this, 'get_shapes_options_globals_settings' ),
          'permission_callback' => array( $this, 'get_config_permissions_check' ),
        )
      )
    );
    register_rest_route(
      $this->namespace,
      '/' . $this->rest_base."/shapes/(?P<shape_id>\d+)",
      array(
        array(
          'methods'             => \WP_REST_Server::EDITABLE,
          'callback'            => array( $this, 'update_shapes_options_globals_settings' ),
          'permission_callback' => array( $this, 'get_config_permissions_check' ),
          'args'                => array(
            'shape_id' => array (
              'type' => 'integer',
              'required' => true,
            )
          )
        )
      )
    );
  
    register_rest_route(
      $this->namespace,
      '/' . $this->rest_base."/fixing-methods",
      array(
        array(
          'methods'             => \WP_REST_Server::READABLE,
          'callback'            => array( $this, 'get_fixing_methods_options_globals_settings' ),
          'permission_callback' => array( $this, 'get_config_permissions_check' ),
        )
      )
    );
    register_rest_route(
      $this->namespace,
      '/' . $this->rest_base."/fixing-methods/(?P<fixingMethod_id>\d+)",
      array(
        array(
          'methods'             => \WP_REST_Server::EDITABLE,
          'callback'            => array( $this, 'update_fixing_methods_options_globals_settings' ),
          'permission_callback' => array( $this, 'get_config_permissions_check' ),
          'args'                => array(
            'fixingMethod_id' => array (
              'type' => 'integer',
              'required' => true,
            )
          )
        )
      )
    );
    register_rest_route(
      $this->namespace,
      '/' . $this->rest_base."/borders",
      array(
        array(
          'methods'             => \WP_REST_Server::READABLE,
          'callback'            => array( $this, 'get_borders_options_globals_settings' ),
          'permission_callback' => array( $this, 'get_config_permissions_check' ),
        )
      )
    );
    register_rest_route(
      $this->namespace,
      '/' . $this->rest_base."/borders/(?P<border_id>\d+)",
      array(
        array(
          'methods'             => \WP_REST_Server::EDITABLE,
          'callback'            => array( $this, 'update_borders_options_globals_settings' ),
          'permission_callback' => array( $this, 'get_config_permissions_check' ),
          'args'                => array(
            'border_id' => array (
              'type' => 'integer',
              'required' => true,
            )
          )
        )
      )
    );
  }
  
  /**
     * Add config page
     * @param \WP_REST_Request $request Full details about the request.
     *
     * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
  public function save_config_page( $request ) {
    $data=json_decode($request->get_body(),true);
    if(isset($data['configuratorPage'])){
        
        $config_page = get_option("asowp_config_page",[]);
        
        if($config_page != $data){
          update_option("asowp_config_page",$data);
          return rest_ensure_response(["success" =>true, "message"=> __("Data updated successfully",'all-signs-options-free')]);
        }else{
          return rest_ensure_response(["success"=>"same","message" => __("No change observed",'all-signs-options-free')]);        
        }
    }
    return rest_ensure_response(["message" => __("Config page not found",'all-signs-options-free')]);
  }

  /**
   * Get config page
   * @param \WP_REST_Request $request Full details about the request.
   *
   * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
   */
  public function get_config_pages() {

    $option = get_option("asowp_config_page");
    
    if($option == false || empty($option) ){
        return rest_ensure_response(["message" => __("Config page not found",'all-signs-options-free')]);
    }else{
        return rest_ensure_response($option);
    }
  }

  /**
   * Show all published pages
   *  @param  \WP_REST_Request $request Full details about the request.
   * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
   */
  function get_available_pages() {
    $args = array(
        'post_type' => 'page', // Type de contenu "page"
        'post_status' => 'publish', // Statut "publié"
        'posts_per_page' => -1, // Tous les articles (-1 pour afficher tous les articles)
    );

    $existing_pages = get_posts($args);
    $pages = [];
    $pages[] = ["id"=>0,"title"=>esc_html_x("None",'all-signs-options-free')];
    
    foreach ($existing_pages as $page) {
        $pages[] = ["id"=>$page->ID,"title"=>$page->post_title];
    }
  
    return rest_ensure_response($pages);
  }

  /**
   * Add new page 
   * 
   * @param  \WP_REST_Request $request Full details about the request.
   * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
   */
  function create_new_page($request) {
    $params=json_decode($request->get_body());
    if(isset($params->title) && !empty($params->title)){
      
      $new_page = array(
        'post_title' => $params->title,
        'post_status' => 'publish', 
        'post_type' => 'page'
      );
  
      $page_id = wp_insert_post($new_page);
      
      if(!is_wp_error($page_id)){
        return rest_ensure_response(["id"=>$page_id,"message"=>__("Page created successfully",'all-signs-options-free')]);
      }else{
        return rest_ensure_response(["message"=>__("Page was not created",'all-signs-options-free')]);
      }

    }else{
      return rest_ensure_response(["message"=>__("Page was not created",'all-signs-options-free')]);
    } 
  }
  /**
   * Output functions 
   * 
   * @param  \WP_REST_Request $request Full details about the request.
   * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
   */
  public function get_output_options_globals_settings($request){
    $outputOptions = get_option("asowp_output_options",[]);
    if(count($outputOptions)>0)
		  return rest_ensure_response($outputOptions);  
    else
      return rest_ensure_response(["message"=>__("No output options found",'all-signs-options-free')]);
  }
   public function update_output_options_globals_settings($request){
    $data=json_decode($request->get_body(),true);
    $outputOptions = get_option("asowp_output_options",[]);
    if($data != $outputOptions){
      $update = update_option("asowp_output_options",$data);
      if($update){
        return rest_ensure_response(array('success' => true, "message" => __("The ouput settings has been updated with success",'all-signs-options-free') ) );
      }
      else{
        return rest_ensure_response(array('success' => false, "message"=>__("The ouput settings update failed",'all-signs-options-free') ) );
      } 
    }else{
      return rest_ensure_response(array('success' => "same", "message"=>__("No change observed in the ouput settings",'all-signs-options-free') ) );

    }
  }
   /**
   * get all shapes function 
   * 
   * @param  \WP_REST_Request $request Full details about the request.
   * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
   */
	public function get_shapes_options_globals_settings($request){
		$all_shapes = get_option("asowp_all_shapes",[]);
		return rest_ensure_response($all_shapes);  
	}
    /**
   * Update all shape function 
   * 
   * @param  \WP_REST_Request $request Full details about the request.
   * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
   */
	public function update_shapes_options_globals_settings($request){
		$shape=json_decode($request->get_body(),true);
		$shape_id=$request->get_param('shape_id');
		$all_shapes= get_option("asowp_all_shapes",[]); ;
		if($all_shapes[$shape_id]){
      if($all_shapes[$shape_id] != $shape){
        $all_shapes[$shape_id] = $shape;
        $update = update_option("asowp_all_shapes",$all_shapes);
        if($update){
          return rest_ensure_response(array('success' => true, "message" => __("The Shape has been updated with success",'all-signs-options-free') ) );
        }
        else{
          return rest_ensure_response(array('success' => false, "message"=>__("Shape update failed",'all-signs-options-free') ) );
        } 
      }else{
        return rest_ensure_response(array('success' => "same", "message"=>__("No change observed in shape",'all-signs-options-free') ) );

      }
		}else{
			return rest_ensure_response(["success"=>false,"message"=>__('Shape not found','all-signs-options-free')]);
		}
	}
   /**
   * get all fixing methods function 
   * 
   * @param  \WP_REST_Request $request Full details about the request.
   * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
   */
	public function get_fixing_methods_options_globals_settings($request){
		$all_fixingMethods = get_option("asowp_all_fixingMethods",[]);
		return rest_ensure_response($all_fixingMethods);   
	}
    /**
   * Update all fixing method function 
   * 
   * @param  \WP_REST_Request $request Full details about the request.
   * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
   */
	public function update_fixing_methods_options_globals_settings($request){
		$fixingMethod=json_decode($request->get_body(),true);
		$fixingMethod_id=$request->get_param('fixingMethod_id');
		$all_fixingMethods= get_option("asowp_all_fixingMethods",[]);
		if($all_fixingMethods[$fixingMethod_id]){
      if($all_fixingMethods[$fixingMethod_id] != $fixingMethod){
        $all_fixingMethods[$fixingMethod_id] = $fixingMethod;
        $update = update_option("asowp_all_fixingMethods",$all_fixingMethods);
        if($update){
          return rest_ensure_response(array('success' => true, "message" => __("The Fixing Method has been updated with success",'all-signs-options-free') ) );
        }
        else{
          return rest_ensure_response(array('success' => false, "message"=>__("FixingMethod update failed",'all-signs-options-free') ) );
        }
      }else{
        return rest_ensure_response(array('success' => "same", "message"=>__("No change observed in fixing Method",'all-signs-options-free') ) );
      } 
		}else{
			return rest_ensure_response(["success"=>false,"message"=>__('FixingMethod not found','all-signs-options-free')]);
		}
	}
	/**
	 * get all borders function 
	 * 
	 * @param  \WP_REST_Request $request Full details about the request.
	 * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_borders_options_globals_settings($request){
		$all_borders = get_option("asowp_all_borders",[]);
		return rest_ensure_response($all_borders);     
	}
    /**
   * Update all border function 
   * 
   * @param  \WP_REST_Request $request Full details about the request.
   * @return \WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
   */
	public function update_borders_options_globals_settings($request){
		$border=json_decode($request->get_body(),true);
		$border_id=$request->get_param('border_id');
		$all_borders= get_option("asowp_all_borders",[]); ;
		if($all_borders[$border_id]){
      if($all_borders[$border_id] != $border){
        $all_borders[$border_id] = $border;
        $update = update_option("asowp_all_borders",$all_borders);
        if($update){
          return rest_ensure_response(array('success' => true, "message" => __("The Border has been updated with success",'all-signs-options-free') ) );
        }
        else{
          return rest_ensure_response(array('success' => false, "message"=>__("Border update failed",'all-signs-options-free') ) );
        }
      }else{
        return rest_ensure_response(array('success' => "same", "message"=>__("No change observed in border",'all-signs-options-free') ) );
      }   
		}else{
			return rest_ensure_response(["success"=>false,"message"=>__('Border not found','all-signs-options-free')]);
		}
	}
  /**
     * Checks if a given request has access to read the items.
     *
     * @param  WP_REST_Request $request Full details about the request.
     *
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function get_config_permissions_check( $request ) {
      return true;
  }
}

