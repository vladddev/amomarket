<?php
/**
 * AmoMarket functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package AmoMarket
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

if ( ! function_exists( 'amomarket_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function amomarket_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on AmoMarket, use a find and replace
		 * to change 'amomarket' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'amomarket', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus(
			array(
				'menu-1' => esc_html__( 'Primary', 'amomarket' ),
			)
		);

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
			)
		);
	}
endif;
add_action( 'after_setup_theme', 'amomarket_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */

/**
 * Enqueue scripts and styles.
 */
function amomarket_scripts() {
	wp_enqueue_style( 'amomarket-style', get_stylesheet_uri(), array(), _S_VERSION );
	wp_style_add_data( 'amomarket-style', 'rtl', 'replace' );

	wp_enqueue_script( 'amomarket-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'amomarket_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

add_action( 'save_post', 'add_widget_hook', 10, 3 );
function add_widget_hook( $post_ID, $post, $update ){
	if ($post->post_type == 'widget') {
		$slug = get_field('slug', $post_ID);
		if (file_exists(__DIR__ . '/widgets/' . $slug)) return;
		
		mkdir(__DIR__ . '/widgets/' . $slug);
		$files_dir = __DIR__ . '/widgets/' . $slug . '/files';
		mkdir($files_dir);
		file_put_contents($files_dir . '/index.php', '');
		file_put_contents($files_dir . '/view.php', '');
		file_put_contents($files_dir . '/hook_name.php', '');
		mkdir($files_dir . '/assets/');
		mkdir($files_dir . '/assets/css/');
		mkdir($files_dir . '/assets/js/');
		file_put_contents($files_dir . '/assets/js/once.js', '');
		file_put_contents($files_dir . '/assets/js/always.js', '');
		mkdir(__DIR__ . '/widgets/' . $slug . '/data/');
	} else if ($post->post_type == 'widget-order') {
		$widget_id = get_field('widget', $post_ID);
		$slug = get_field('slug', $widget_id);
		$domain = get_field('domain', $post_ID)->post_title;
		$price = get_field('price', $post_ID);
		$price_type = get_field('price_type', $post_ID);
		$users_count = get_field('users_count', $post_ID);

		$total = $price;
		if ($price_type == 'month_user') {
			$total = $price * $users_count;
		} else if ($price_type == 'free') {
			$total = 0;
		}

		$data = [
			'name' => 'Лицензия АМО ' . $domain,
			'price' => $total,
			'status_id' => 37835206,
			'pipeline_id' => 2085811,
			'custom_fields_values' => [
				[
					'field_id' => 0,
					'values' => [
						[
							'value' => ''
						]
					]
				]
			]
		];

		$token = get_tokens('devmarket.amocrm.ru', 'amomarket');

		$link = 'https://devmarket.amocrm.ru/api/v4/leads'; 
        $curl = curl_init(); 
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
        curl_setopt($curl, CURLOPT_URL, $link);
		curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type:application/json', 'Authorization: ' . $token['token_type'] . ' ' . $token['accessToken']]);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

        $out = curl_exec($curl);
        
        curl_close($curl);
	}
}


function get_tokens($domain, $widget) {
    $file = __DIR__ . '/widgets/' . $widget . '/data/' . $domain . '/tokens/tokens.json';
    $amo_settings_file = __DIR__ . '/widgets/' . $widget . '/data/' . $domain . '/tokens/amo.json';

    $amo_settings = json_decode(file_get_contents($amo_settings_file), true);
    $token = json_decode(file_get_contents($file), true);

    if (time() > $token['expires']) {
            
        $data = [
            'client_id' => $amo_settings['clientId'],
            'client_secret' => $amo_settings['clientSecret'],
            'grant_type' => 'refresh_token',
            'refresh_token' => $token['refresh_token'],
            'redirect_uri' => 'https://marketplace.market.ru/' . $widget . '/',
        ];

        $link = 'https://' . $domain . '/oauth2/access_token'; 
        $curl = curl_init(); 
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-oAuth-client/1.0');
        curl_setopt($curl, CURLOPT_URL, $link);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

        $out = curl_exec($curl);
        
        curl_close($curl);

        $response = json_decode($out, true);
		
        $new_token = [
            'access_token' => $response['access_token'],
            'refresh_token' => $response['refresh_token'],
            'expires' => time() + (int)$response['expires_in'],
            'token_type' => $response['token_type'],
            'base_domain' => $domain,
        ];
        set_token($domain, $widget, $new_token);
        $token = $new_token;
    }
    return $token;
}

function set_token($domain, $widget, $token_array) {
    $file = __DIR__ . '/widgets/' . $widget . '/data/' . $domain . '/tokens/tokens.json';
    
    file_put_contents($file, json_encode($token_array));
}