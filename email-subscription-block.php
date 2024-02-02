<?php
/**
 * Plugin Name:       Email Subscription Block
 * Description:       Example block scaffolded with Create Block tool.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       email-subscription-block
 *
 * @package           create-block
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function create_block_email_subscription_block_block_init() {
    register_block_type( __DIR__ . '/build', array(
        'render_callback' => 'email_subscription_block_render',
    ));
}

add_action( 'init', 'create_block_email_subscription_block_block_init' );

function myplugin_handle_email_subscription( $request ) {
      // Get the JSON-decoded body of the request
	  $params = $request->get_json_params();
    
	  // Extract the 'email' parameter from the object
	  $email = isset($params['email']) ? sanitize_email($params['email']) : '';
	  // Validate the email address
	  if (empty($email) || !is_email($email)) {
		  return new WP_Error('invalid_email', 'The email address provided is invalid.', array('status' => 400));
	  }
  
	  // Process the email subscription here. For example, save to a database or integrate with an email marketing service.
      // This is a placeholder for your subscription logic.
  
	  // Return a success response
	  return new WP_REST_Response(array('message' => 'Thank you for subscribing! 👍', 'email' => $email), 200);
}

add_action( 'rest_api_init', function () {
    register_rest_route( 'email-subscription-block', '/email-subscribe', array(
        'methods' => WP_REST_Server::CREATABLE,
        'callback' => 'myplugin_handle_email_subscription',
        'permission_callback' => '__return_true', // Ensure this is appropriate for your use case
		'args' => array(
            'email' => array(
                'validate_callback' => function ($param, $request, $key) {
					if (!is_email($param)) {
						// Return a WP_Error with a custom error message
						return new WP_Error(
							'rest_invalid_param',
							'The email address provided is invalid. Please provide a valid email address.',
							array('status' => 400)
						);
					}
					return true; // Return true if validation passes
                },
                'required' => true,
                'sanitize_callback' => 'sanitize_email',
            ),
        ),
    ));
});

function email_subscription_block_render($attributes, $content) {
    ob_start();
    ?>
    <form id="email-subscription-form" data-mailchimp-url="fake-url">
        <input type="email" name="email" placeholder="Enter your email" required>
        <button type="submit">Subscribe</button>
        <div id="snackbar"></div>
    </form>
    <?php
    return ob_get_clean();
}


function my_enqueue_scripts() {
    wp_enqueue_script( 'wp-api-fetch' ); // Ensure API Fetch is enqueued.
    wp_localize_script( 'wp-api-fetch', 'wpApiSettings', array(
        'root'  => esc_url_raw( rest_url() ),
        'nonce' => wp_create_nonce( 'wp_rest' ),
    ));
}
add_action( 'wp_enqueue_scripts', 'my_enqueue_scripts' );

