<?php
/*
Plugin Name: ProSports Staff Directories
Plugin URI: http://prosportspro.com/
Description: Adds staff directories to ProSports.
Author: ProSports
Author URI: http://prosportspro.com
Version: 1.4
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main ProSports Staff Directories Class
 *
 * @class ProSports_Staff_Directories
 * @version	1.4
 */
class ProSports_Staff_Directories {

	/**
	 * Constructor
	 */
	public function __construct() {
		// Define constants
		$this->define_constants();

		// Include required files
		$this->includes();

		// Hooks
		register_activation_hook( __FILE__, array( $this, 'install' ) );
		add_action( 'init', array( $this, 'init' ) );
		add_filter( 'widget_text', array( $this, 'widget_text' ), 9 );
		add_action( 'prosports_include_post_type_handlers', array( $this, 'include_post_type_handlers' ) );
		add_filter( 'prosports_permalink_slugs', array( $this, 'add_permalink_slug' ) );
		add_filter( 'prosports_post_types', array( $this, 'add_post_type' ) );
		add_filter( 'prosports_post_type_hierarchy', array( $this, 'add_to_hierarchy' ) );
		add_filter( 'prosports_screen_ids', array( $this, 'add_screen_ids' ) );
		add_action( 'prosports_single_directory_content', array( $this, 'output_directory' ), 10 );
		add_action( 'prosports_after_single_directory', 'prosports_output_br_tag', 100 );
		add_filter( 'prosports_league_object_types', array( $this, 'add_taxonomy_object' ) );
		add_filter( 'prosports_season_object_types', array( $this, 'add_taxonomy_object' ) );
		add_filter( 'prosports_formats', array( $this, 'add_formats' ) );
		add_filter( 'prosports_text', array( $this, 'add_text_options' ) );
		add_filter( 'prosports_staff_settings', array( $this, 'add_options' ) );
		add_action( 'prosports_single_staff_content', array( $this, 'output_staff_contacts' ), 20 );
		add_action( 'prosports_single_team_content', array( $this, 'output_team_directories' ), 25 );
		add_action( 'prosports_widgets', array( $this, 'widgets' ) );
		add_action( 'prosports_register_post_type_staff', array( $this, 'add_staff_attributes_support' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		if ( defined( 'SP_PRO_PLUGIN_FILE' ) )
			register_activation_hook( SP_PRO_PLUGIN_FILE, array( $this, 'install' ) );
	}

	/**
	 * Define constants.
	*/
	private function define_constants() {
		if ( !defined( 'SP_STAFF_DIRECTORIES_VERSION' ) )
			define( 'SP_STAFF_DIRECTORIES_VERSION', '1.4' );

		if ( !defined( 'SP_STAFF_DIRECTORIES_URL' ) )
			define( 'SP_STAFF_DIRECTORIES_URL', plugin_dir_url( __FILE__ ) );

		if ( !defined( 'SP_STAFF_DIRECTORIES_DIR' ) )
			define( 'SP_STAFF_DIRECTORIES_DIR', plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Include required files.
	*/
	private function includes() {
		include_once( 'includes/class-sp-staff-directory.php' );

		if ( ! is_admin() || defined( 'DOING_AJAX' ) ) {
			$this->frontend_includes();
		}
	}

	/**
	 * Include required frontend files.
	 */
	public function frontend_includes() {
		include_once( 'includes/class-sp-directory-template-loader.php' );
		include_once( 'includes/class-sp-shortcode-staff-list.php' );
		include_once( 'includes/class-sp-shortcode-staff-gallery.php' );
	}

	/**
	 * Init plugin when WordPress Initialises.
	 */
	public function init() {
		// Register post type
		$this->register_post_type();
	}

	public function register_post_type() {
		register_post_type( 'sp_directory',
			apply_filters( 'prosports_register_post_type_directory',
				array(
					'labels' => array(
						'name' 					=> __( 'Directories', 'prosports' ),
						'singular_name' 		=> __( 'Staff Directory', 'prosports' ),
						'add_new_item' 			=> __( 'Add New Staff Directory', 'prosports' ),
						'edit_item' 			=> __( 'Edit Staff Directory', 'prosports' ),
						'new_item' 				=> __( 'New', 'prosports' ),
						'view_item' 			=> __( 'View', 'prosports' ),
						'search_items' 			=> __( 'Search', 'prosports' ),
						'not_found' 			=> __( 'No results found.', 'prosports' ),
						'not_found_in_trash' 	=> __( 'No results found.', 'prosports' ),
					),
					'public' 				=> true,
					'show_ui' 				=> true,
					'capability_type' 		=> 'sp_directory',
					'map_meta_cap' 			=> true,
					'publicly_queryable' 	=> true,
					'exclude_from_search' 	=> false,
					'hierarchical' 			=> false,
					'rewrite' 				=> array( 'slug' => get_option( 'prosports_directory_slug', 'directory' ) ),
					'supports' 				=> array( 'title', 'author', 'thumbnail' ),
					'has_archive' 			=> false,
					'show_in_nav_menus' 	=> true,
					'show_in_menu' 			=> 'edit.php?post_type=sp_staff',
					'show_in_admin_bar' 	=> true,
				)
			)
		);
	}

	/**
	 * Add post type
	 */
	public static function add_post_type( $post_types = array() ) {
		$post_types[] = 'sp_directory';
		return $post_types;
	}

	/**
	 * Add to hierarchy
	 */
	public static function add_to_hierarchy( $hierarchy = array() ) {
		$hierarchy['sp_staff'][] = 'sp_directory';
		return $hierarchy;
	}

	/**
	 * Add screen ids
	 */
	public static function add_screen_ids( $screen_ids = array() ) {
		$screen_ids[] = 'edit-sp_directory';
		$screen_ids[] = 'sp_directory';
		return $screen_ids;
	}

	/**
	 * Output the staff directory.
	 *
	 * @access public
	 * @subpackage	Directory
	 * @return void
	 */
	public static function output_directory() {
        $id = get_the_ID();
        $format = get_post_meta( $id, 'sp_format', true );
        if ( array_key_exists( $format, SP()->formats->directory ) )
			sp_get_template( 'staff-' . $format . '.php', array( 'id' => $id ), 'staff-' . $format, SP_STAFF_DIRECTORIES_DIR . 'templates/' );
        else
			sp_get_template( 'staff-list.php', array( 'id' => $id ), 'staff-list', SP_STAFF_DIRECTORIES_DIR . 'templates/' );
	}

	/**
	 * Do shortcode in widgets
	 */
	function widget_text( $content ) {
		if ( ! preg_match( '/\[[\r\n\t ]*(staff(_|-)(list|gallery))?[\r\n\t ].*?\]/', $content ) )
			return $content;

		$content = do_shortcode( $content );

		return $content;
	}

	/**
	 * Add object to taxonomy.
	 *
	 * @return array
	 */
	public function add_taxonomy_object( $object_types ) {
		$object_types[] = 'sp_directory';
		return $object_types;
	}

	/**
	 * Conditonally load classes and functions only needed when viewing the post type.
	 */
	public function include_post_type_handlers() {
		include_once( 'includes/class-sp-staff-directory-meta-boxes.php' );
		include_once( 'includes/class-sp-admin-cpt-directory.php' );
	}

	/**
	 * Add slug to permalink options.
	 *
	 * @return array
	 */
	public function add_permalink_slug( $slugs ) {
		$slugs[] = array( 'directory', __( 'Staff Directories', 'prosports' ) );
		return $slugs;
	}


	/** 
	 * Add formats.
	 */
	public function add_formats( $formats ) {
		$formats['directory'] = array(
			'list' => __( 'List', 'prosports' ),
			'gallery' => __( 'Gallery', 'prosports' ),
		);
		return $formats;
	}

	/**
	 * Add options to settings page.
	 *
	 * @return array
	 */
	public function add_options( $settings ) {
		array_splice( $settings, -1, 0, array(
			array(
				'title'     => __( 'Contact Info', 'prosports' ),
				'desc' 		=> __( 'Link phone', 'prosports' ),
				'id' 		=> 'prosports_link_phone',
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
				'checkboxgroup'		=> 'start',
			),

			array(
				'desc' 		=> __( 'Link email', 'prosports' ),
				'id' 		=> 'prosports_link_email',
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
				'checkboxgroup'		=> 'end',
			),
		) );

		return array_merge( $settings, array(
			array( 'title' => __( 'Staff Directories', 'prosports' ), 'type' => 'title', 'id' => 'directory_options' ),

			array(
				'title'     => __( 'Pagination', 'prosports' ),
				'desc' 		=> __( 'Paginate', 'prosports' ),
				'id' 		=> 'prosports_directory_paginated',
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
			),
			
			array(
				'title' 	=> __( 'Limit', 'prosports' ),
				'id' 		=> 'prosports_directory_rows',
				'class' 	=> 'small-text',
				'default'	=> '10',
				'desc' 		=> __( 'staff', 'prosports' ),
				'type' 		=> 'number',
				'custom_attributes' => array(
					'min' 	=> 1,
					'step' 	=> 1
				),
			),

			array( 'type' => 'sectionend', 'id' => 'directory_options' ),
		) );
	}

	/**
	 * Add text options 
	 */
	public function add_text_options( $options = array() ) {
		return array_merge( $options, array(
			__( 'Job', 'prosports' ),
			__( 'Phone', 'prosports' ),
			__( 'Email', 'prosports' ),
			__( 'View all staff', 'prosports' ),
		) );
	}

	/**
	 * Install
	 */
	public function install() {
		$this->add_capabilities();
		$this->register_post_type();

		// Queue upgrades
		$current_version = get_option( 'prosports_staff_directories_version', null );

		// Update version
		update_option( 'prosports_staff_directories_version', SP_STAFF_DIRECTORIES_VERSION );

		// Flush rules after install
		flush_rewrite_rules();
	}

	/**
	 * Add capabilities
	 */
	public function add_capabilities() {
		global $wp_roles;

		if ( class_exists( 'WP_Roles' ) ):
			if ( ! isset( $wp_roles ) ):
				$wp_roles = new WP_Roles();
			endif;
		endif;

		if ( is_object( $wp_roles ) ):
			$capability_type = 'sp_directory';
			$capabilities = array(
				"edit_{$capability_type}",
				"read_{$capability_type}",
				"edit_{$capability_type}s",
				"edit_published_{$capability_type}s",
				"assign_{$capability_type}_terms",
			);

			foreach ( $capabilities as $cap ):
				$wp_roles->add_cap( 'sp_team_manager', $cap );
			endforeach;

			$capabilities = array_merge( $capabilities, array(
				"delete_{$capability_type}",
				"edit_others_{$capability_type}s",
				"publish_{$capability_type}s",
				"read_private_{$capability_type}s",
				"delete_{$capability_type}s",
				"delete_private_{$capability_type}s",
				"delete_published_{$capability_type}s",
				"delete_others_{$capability_type}s",
				"edit_private_{$capability_type}s",
				"manage_{$capability_type}_terms",
				"edit_{$capability_type}_terms",
				"delete_{$capability_type}_terms",
			));

			foreach ( $capabilities as $cap ):
				$wp_roles->add_cap( 'sp_league_manager', $cap );
				$wp_roles->add_cap( 'administrator', $cap );
			endforeach;
		endif;
	}

	/**
	 * Add page attribute support to staff post type
	 */
	public function add_staff_attributes_support( $arr ) {
		$arr['supports'][] = 'page-attributes';
		return $arr;
	}

	/**
	 * Enqueue scripts
	 */
	public function admin_enqueue_scripts() {
		$screen = get_current_screen();

		if ( in_array( $screen->id, array( 'sp_directory', 'edit-sp_directory' ) ) ) {
			wp_enqueue_script( 'prosports-staff-directories-admin', SP_STAFF_DIRECTORIES_URL . 'js/admin.js', array( 'jquery' ), SP_STAFF_DIRECTORIES_VERSION );
		}
	}

	/**
	 * Output the staff contact info.
	 *
	 * @access public
	 * @subpackage	Staff
	 * @return void
	 */
	public function output_staff_contacts() {
		sp_get_template( 'staff-contacts.php', array(), 'staff-contacts', SP_STAFF_DIRECTORIES_DIR . 'templates/' );
	}

	/**
	 * Output the team staff directories.
	 *
	 * @access public
	 * @subpackage	Team
	 * @return void
	 */
	public function output_team_directories() {
		sp_get_template( 'team-directories.php', array(), 'team-directories', SP_STAFF_DIRECTORIES_DIR . 'templates/' );
	}


	/**
	 * Register widgets
	 */
	public static function widgets() {
		include_once( 'includes/class-sp-widget-staff-list.php' );
		include_once( 'includes/class-sp-widget-staff-gallery.php' );
	}
}

new ProSports_Staff_Directories();
