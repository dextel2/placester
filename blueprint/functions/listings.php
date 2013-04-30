<?php 

PLS_Listing_Helper::init();
class PLS_Listing_Helper {
	
	function init() {
		add_action('wp_ajax_pls_listings_for_options', array(__CLASS__,'listings_for_options'));
		add_action('wp_ajax_pls_get_search_count', array(__CLASS__,'get_search_count'));
		add_action('wp_ajax_nopriv_pls_get_search_count', array(__CLASS__,'get_search_count'));
	}

	function listings_for_options () {
		$api_response = PLS_Plugin_API::get_property_list($_POST);
		$formatted_listings = '';
		if ($api_response['listings']) {
			foreach ($api_response['listings'] as $listing) {
			  if ( !empty($listing['location']['unit']) ) {
				  $formatted_listings .= '<option value="' . $listing['id'] . '" >' . $listing['location']['address'] . ', #' . $listing['location']['unit'] . ', ' . $listing['location']['locality'] . ', ' . $listing['location']['region'] . '</option>';
				} else {
				  $formatted_listings .= '<option value="' . $listing['id'] . '" >' . $listing['location']['address'] . ', ' . $listing['location']['locality'] . ', ' . $listing['location']['region'] . '</option>';
				}
			}
		} else {
		$formatted_listings .= "No Results. Broaden your search.";
		}
		echo json_encode($formatted_listings);
		die();
	}

	function get_featured ( $featured_option_id, $args = array() ) {
		$option_ids = pls_get_option($featured_option_id); 
		if (!empty( $option_ids ) ) {
			$property_ids = array_keys($option_ids);

			if( ! empty( $property_ids ) ) {
				$args['property_ids'] = $property_ids;
			}
			$api_response = PLS_Plugin_API::get_listings_details_list( $args );
      // remove listings without images
	      foreach ($api_response['listings'] as $key => $listing) {
	          if ( empty($listing['images']) ) {
	            unset($api_response['listings'][$key]);
	          }
	      } 
		  return $api_response;	
		} else {
			return array('listings' => array());
		}
	}
	
	// pass property IDs array
	function get_featured_from_post ( $post_id, $post_meta_key ) {
		$property_data = get_post_meta( $post_id, $post_meta_key );
		
		// Data comes in different forms
		$property_ids = empty( $property_data ) ? array() : @json_decode( $property_data[0], true );
		
		if( empty( $property_ids ) && is_array( $property_data ) && isset( $property_data[0]['featured-listings-type'] ) ) {
			$listings_array = $property_data[0]['featured-listings-type'];
			if( is_array( $listings_array ) ) {
				$property_ids = array_keys( $listings_array );
			}
			// $property_ids = implode(',', $property_ids );
		} else if( is_array( $property_ids ) ) {
			$property_ids = array_keys( $property_ids );
		} 
		
		if (! empty( $property_ids ) ) {
			$api_response = PLS_Plugin_API::get_listings_details_list(array('property_ids' => $property_ids));
			return $api_response;
		} else {
			return array('listings' => array());
		}
	}

	function get_compliance ($args) {
		$message = PLS_Plugin_API::mls_message($args);
		if ($message && !empty($message) && isset($args['context'])) {
			$_POST['compliance_message'] = $message;
			PLS_Route::router(array($args['context'] . '-compliance.php'), true, false);
		}
		return false;
	}

  function get_search_count() {
    $response = PLS_Plugin_API::get_listings_list($_POST);
    echo json_encode(array('count' => $response['total']));
    die();
  }
}