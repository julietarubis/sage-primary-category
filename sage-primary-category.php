<?php
/*
Plugin Name: Sage Primary Category
Plugin URI: https://github.com/julietaGit/plugins-sage-primary-category/
Description: Create a WordPress plugin that allows publishers to designate a primary category for posts.
Author: Julieta Rubis
Version: 1.0.0
Author URI: https://github.com/julietaGit
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Sage_Primary_Category {
	
	public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'sagepc_add_metabox' ) );
        add_action( 'save_post', array( $this, 'sagepc_save_metabox' ) );
    }
	
	public function sagepc_add_metabox() {
		
		$items = $this->sagepc_build_lists();

		
		$post_types = $items->post_type_list;

		
		if ( ! empty( $post_types ) ) {
		   
            foreach ( $post_types as $post_type ) {
                add_meta_box (
                    'select_primary_category', 
                    'Select Primary Category', 
                    array( $this, 'sagepc_metabox_callback' ), 
                    $post_type, 
                    'side', 
                    'high' 
                );
            }
        }
    }
	
	public function sagepc_metabox_callback( $post ) {

        
        wp_nonce_field( 'sagepc_category_nonce', 'sagepc_category_nonce_field' );

     
        $items = $this->sagepc_build_lists();

    	$primary_category = '';
    
    	
        $primary_selected_category = get_post_meta( $post->ID, 'select_primary_category', true );
    
    	
    	if ( $primary_selected_category != '' ) {
    	   
    		$primary_category = $primary_selected_category;
    	}
    
    	
        $post_categories = $items->categories_list;

       
        $html = '';
        $html .= '<select class="widefat" name="select_primary_category" id="select_primary_category">';
        $html .= '<option value="0" >-- select category --</option>';
    	
        if ( ! empty( $post_categories ) ) {
            foreach( $post_categories as $category ) {
                $html .= '<option value="' . $category->name . '" ' . selected( $primary_category, $category->name, false ) . '>' . ucwords($category->name) . '</option>';
            }
        }
        $html .= '</select>';
        
        $html .= '<small>Select a primary category for your post.</small>';

        
    	echo $html;
    }
	
	public function sagepc_save_metabox( $post_id ) {

        
        if( ! isset( $_POST['sagepc_category_nonce_field'] ) || ! wp_verify_nonce( $_POST['sagepc_category_nonce_field'],'sagepc_category_nonce' ) ) {
           return;
        }

        
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        
        if ( isset( $_POST[ 'select_primary_category' ] ) ) {
            
    		$primary_category = sanitize_text_field( $_POST[ 'select_primary_category' ] );
    		
    		update_post_meta( $post_id, 'select_primary_category', $primary_category );
    	}
    }
	
	public function sagepc_build_lists() {
        
		$item = new stdClass();

		
		$args = array(
			'public' => true, 
			'_builtin' => false 
		);
		
		$item->post_type_list = get_post_types( $args, 'names' );
		
		$item->post_type_list['post'] = 'post';

		
		$item->categories_list = get_the_category();

		
		return $item;
	}
	

	
}

if(class_exists('Sage_Primary_Category')){
	$sagepc = new Sage_Primary_Category();

}