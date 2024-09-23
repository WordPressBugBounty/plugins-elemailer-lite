<?php

/**
 * gloabl function declaration file
 * 
 */

//ajax action to get taxonomies by post type
add_action('wp_ajax_nopriv_elemailer_get_taxonomies', 'elemailer_get_taxonomies');
add_action('wp_ajax_elemailer_get_taxonomies', 'elemailer_get_taxonomies');

function elemailer_get_taxonomies()
{
	// first check if data is being sent and that it is the data we want   
	if (isset($_POST['postTypeNonce'])) {
		if (!wp_verify_nonce($_POST['postTypeNonce'], 'wp_rest')) {
			wp_die('You are not allowed!');
		}
		$post_type = sanitize_text_field(isset($_POST['post_type']) ? $_POST['post_type'] : '');
		$taxonomoies = get_object_taxonomies($post_type, 'names');
		$taxonomy_name = array();
		foreach ($taxonomoies as $taxonomy) {
			$taxonomy_name[] = array('name'    => $taxonomy);
		}
		echo json_encode($taxonomy_name);
		wp_die();
	}
}

//ajax action to get terms by category
add_action('wp_ajax_nopriv_elemailer_get_terms', 'elemailer_get_terms');
add_action('wp_ajax_elemailer_get_terms', 'elemailer_get_terms');

function elemailer_get_terms()
{
	// first check if data is being sent and that it is the data we want
	if (isset($_POST['postTypeNonce'])) {
		if (!wp_verify_nonce($_POST['postTypeNonce'], 'wp_rest')) {
			wp_die('You are not allowed!');
		}
		$taxonomy_type = sanitize_text_field(isset($_POST['taxonomy_type']) ? $_POST['taxonomy_type'] : '');
		$term_slug = array();
		$terms =  get_terms(array('taxonomy' => $taxonomy_type));
		foreach ($terms as $term) {
			$id = $term->term_id;
			$name = $term->name;
			$term_slug[] = array(
				'id'    => $id,
				'name'  => $name
			);
		}
		//to process the current post terms           
		$term_slug[] = array('id' => 'current', 'name' => 'Current Post');
		echo json_encode($term_slug);
		wp_die();
	}
}

//ajax action to get terms by category
add_action('wp_ajax_nopriv_elemailer_get_posts', 'elemailer_get_posts');
add_action('wp_ajax_elemailer_get_posts', 'elemailer_get_posts');

function elemailer_get_posts()
{
	// first check if data is being sent and that it is the data we want
	if (isset($_POST['postTypeNonce'])) {
		if (!wp_verify_nonce($_POST['postTypeNonce'], 'wp_rest')) {
			wp_die('You are not allowed!');
		}
		//$taxonomy_type = sanitize_text_field(isset($_POST['taxonomy_type']) ? $_POST['taxonomy_type'] : '');
		$post_type = sanitize_text_field(isset($_POST['post_type']) ? $_POST['post_type'] : '');

		$posts = array();
		//$obj_terms =  get_terms(array('taxonomy' => $taxonomy_type));
		//$terms =  wp_list_pluck($obj_terms, 'slug');

		$args = array(
			'post_type' => $post_type,
			'posts_per_page' => -1,
			'no_found_rows'		=> true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		);

		// Append our tax-query if we have terms. Make sure it is a valid string or array
		// if ( $taxonomy_type ) {
		//     $args['tax_query'] = array(
		// 		array(
		// 			'taxonomy' => $taxonomy_type,
		// 			'field' => 'slug',
		// 			'terms' => $terms,
		// 		),
		// 	);
		// }


		$loop = new WP_Query($args);

		$posts = wp_list_pluck($loop->posts, 'post_title', 'ID');

		wp_reset_query();

		echo json_encode($posts);
		wp_die();
	}
}
