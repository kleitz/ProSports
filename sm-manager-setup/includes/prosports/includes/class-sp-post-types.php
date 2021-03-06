<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Post types
 *
 * Registers post types and taxonomies
 *
 * @class 		SP_Post_types
 * @version		1.5
 * @package		ProSports/Classes
 * @category	Class
 * @author 		ProSports
 */
class SP_Post_types {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 5 );
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );
		add_action( 'wp_trash_post', array( $this, 'delete_config_post' ) );
		add_filter( 'the_posts', array( $this, 'display_scheduled_events' ) );
	}

	/**
	 * Register ProSports taxonomies.
	 */
	public static function register_taxonomies() {
		do_action( 'prosports_register_taxonomy' );

		$labels = array(
			'name' => __( 'Competitions', 'prosports' ),
			'singular_name' => __( 'Competition', 'prosports' ),
			'all_items' => __( 'All', 'prosports' ),
			'edit_item' => __( 'Edit Competition', 'prosports' ),
			'view_item' => __( 'View', 'prosports' ),
			'update_item' => __( 'Update', 'prosports' ),
			'add_new_item' => __( 'Add New', 'prosports' ),
			'new_item_name' => __( 'Name', 'prosports' ),
			'parent_item' => __( 'Parent', 'prosports' ),
			'parent_item_colon' => __( 'Parent:', 'prosports' ),
			'search_items' =>  __( 'Search', 'prosports' ),
			'not_found' => __( 'No results found.', 'prosports' ),
		);
		$args = array(
			'label' => __( 'Competitions', 'prosports' ),
			'labels' => $labels,
			'public' => true,
			'show_in_nav_menus' => false,
			'show_tagcloud' => false,
			'hierarchical' => true,
			'rewrite' => array( 'slug' => get_option( 'prosports_league_slug', 'league' ) ),
		);
		$object_types = apply_filters( 'prosports_league_object_types', array( 'sp_event', 'sp_calendar', 'sp_team', 'sp_table', 'sp_player', 'sp_list', 'sp_staff' ) );
		register_taxonomy( 'sp_league', $object_types, $args );
		foreach ( $object_types as $object_type ):
			register_taxonomy_for_object_type( 'sp_league', $object_type );
		endforeach;

		$labels = array(
			'name' => __( 'Seasons', 'prosports' ),
			'singular_name' => __( 'Season', 'prosports' ),
			'all_items' => __( 'All', 'prosports' ),
			'edit_item' => __( 'Edit Season', 'prosports' ),
			'view_item' => __( 'View', 'prosports' ),
			'update_item' => __( 'Update', 'prosports' ),
			'add_new_item' => __( 'Add New', 'prosports' ),
			'new_item_name' => __( 'Name', 'prosports' ),
			'parent_item' => __( 'Parent', 'prosports' ),
			'parent_item_colon' => __( 'Parent:', 'prosports' ),
			'search_items' =>  __( 'Search', 'prosports' ),
			'not_found' => __( 'No results found.', 'prosports' ),
		);
		$args = array(
			'label' => __( 'Seasons', 'prosports' ),
			'labels' => $labels,
			'public' => true,
			'show_in_nav_menus' => false,
			'show_tagcloud' => false,
			'hierarchical' => true,
			'rewrite' => array( 'slug' => get_option( 'prosports_season_slug', 'season' ) ),
		);
		$object_types = apply_filters( 'prosports_season_object_types', array( 'sp_event', 'sp_calendar', 'sp_team', 'sp_table', 'sp_player', 'sp_list', 'sp_staff' ) );
		register_taxonomy( 'sp_season', $object_types, $args );
		foreach ( $object_types as $object_type ):
			register_taxonomy_for_object_type( 'sp_season', $object_type );
		endforeach;

		$labels = array(
			'name' => __( 'Venues', 'prosports' ),
			'singular_name' => __( 'Venue', 'prosports' ),
			'all_items' => __( 'All', 'prosports' ),
			'edit_item' => __( 'Edit Venue', 'prosports' ),
			'view_item' => __( 'View', 'prosports' ),
			'update_item' => __( 'Update', 'prosports' ),
			'add_new_item' => __( 'Add New', 'prosports' ),
			'new_item_name' => __( 'Name', 'prosports' ),
			'parent_item' => __( 'Parent', 'prosports' ),
			'parent_item_colon' => __( 'Parent:', 'prosports' ),
			'search_items' =>  __( 'Search', 'prosports' ),
			'not_found' => __( 'No results found.', 'prosports' ),
		);
		$args = array(
			'label' => __( 'Venues', 'prosports' ),
			'labels' => $labels,
			'public' => true,
			'show_in_nav_menus' => false,
			'show_tagcloud' => false,
			'hierarchical' => true,
			'rewrite' => array( 'slug' => get_option( 'prosports_venue_slug', 'venue' ) ),
		);
		$object_types = apply_filters( 'prosports_event_object_types', array( 'sp_event', 'sp_calendar', 'sp_team' ) );
		register_taxonomy( 'sp_venue', $object_types, $args );
		foreach ( $object_types as $object_type ):
			register_taxonomy_for_object_type( 'sp_venue', $object_type );
		endforeach;

		$labels = array(
			'name' => __( 'Positions', 'prosports' ),
			'singular_name' => __( 'Position', 'prosports' ),
			'all_items' => __( 'All', 'prosports' ),
			'edit_item' => __( 'Edit Position', 'prosports' ),
			'view_item' => __( 'View', 'prosports' ),
			'update_item' => __( 'Update', 'prosports' ),
			'add_new_item' => __( 'Add New', 'prosports' ),
			'new_item_name' => __( 'Name', 'prosports' ),
			'parent_item' => __( 'Parent', 'prosports' ),
			'parent_item_colon' => __( 'Parent:', 'prosports' ),
			'search_items' =>  __( 'Search', 'prosports' ),
			'not_found' => __( 'No results found.', 'prosports' ),
		);
		$args = array(
			'label' => __( 'Positions', 'prosports' ),
			'labels' => $labels,
			'public' => true,
			'show_in_nav_menus' => false,
			'show_tagcloud' => false,
			'hierarchical' => true,
			'rewrite' => array( 'slug' => get_option( 'prosports_position_slug', 'position' ) ),
		);
		$object_types = apply_filters( 'prosports_position_object_types', array( 'sp_player' ) );
		register_taxonomy( 'sp_position', $object_types, $args );
		foreach ( $object_types as $object_type ):
			register_taxonomy_for_object_type( 'sp_position', $object_type );
		endforeach;

		$labels = array(
			'name' => __( 'Jobs', 'prosports' ),
			'singular_name' => __( 'Job', 'prosports' ),
			'all_items' => __( 'All', 'prosports' ),
			'edit_item' => __( 'Edit Job', 'prosports' ),
			'view_item' => __( 'View', 'prosports' ),
			'update_item' => __( 'Update', 'prosports' ),
			'add_new_item' => __( 'Add New', 'prosports' ),
			'new_item_name' => __( 'Name', 'prosports' ),
			'parent_item' => __( 'Parent', 'prosports' ),
			'parent_item_colon' => __( 'Parent:', 'prosports' ),
			'search_items' =>  __( 'Search', 'prosports' ),
			'not_found' => __( 'No results found.', 'prosports' ),
		);
		$args = array(
			'label' => __( 'Jobs', 'prosports' ),
			'labels' => $labels,
			'public' => true,
			'show_in_nav_menus' => false,
			'show_tagcloud' => false,
			'hierarchical' => true,
			'rewrite' => array( 'slug' => get_option( 'prosports_role_slug', 'role' ) ),
		);
		$object_types = apply_filters( 'prosports_role_object_types', array( 'sp_staff' ) );
		register_taxonomy( 'sp_role', $object_types, $args );
		foreach ( $object_types as $object_type ):
			register_taxonomy_for_object_type( 'sp_role', $object_type );
		endforeach;
	}

	/**
	 * Register core post types
	 */
	public static function register_post_types() {
		do_action( 'prosports_register_post_type' );

		register_post_type( 'sp_result',
			apply_filters( 'prosports_register_post_type_result',
				array(
					'labels' => array(
						'name' 					=> __( 'Team Results', 'prosports' ),
						'singular_name' 		=> __( 'Result', 'prosports' ),
						'add_new_item' 			=> __( 'Add New Result', 'prosports' ),
						'edit_item' 			=> __( 'Edit Result', 'prosports' ),
						'new_item' 				=> __( 'New', 'prosports' ),
						'view_item' 			=> __( 'View', 'prosports' ),
						'search_items' 			=> __( 'Search', 'prosports' ),
						'not_found' 			=> __( 'No results found.', 'prosports' ),
						'not_found_in_trash' 	=> __( 'No results found.', 'prosports' ),
					),
					'public' 				=> false,
					'show_ui' 				=> true,
					'capability_type' 		=> 'sp_config',
					'map_meta_cap' 			=> true,
					'publicly_queryable' 	=> false,
					'exclude_from_search' 	=> true,
					'hierarchical' 			=> false,
					'supports' 				=> array( 'title', 'page-attributes', 'excerpt' ),
					'has_archive' 			=> false,
					'show_in_nav_menus' 	=> false,
					'can_export' 			=> false,
					'show_in_menu' => false,
				)
			)
		);

		register_post_type( 'sp_outcome',
			apply_filters( 'prosports_register_post_type_outcome',
				array(
					'labels' => array(
						'name' 					=> __( 'Event Outcomes', 'prosports' ),
						'singular_name' 		=> __( 'Outcome', 'prosports' ),
						'add_new_item' 			=> __( 'Add New Outcome', 'prosports' ),
						'edit_item' 			=> __( 'Edit Outcome', 'prosports' ),
						'new_item' 				=> __( 'New', 'prosports' ),
						'view_item' 			=> __( 'View', 'prosports' ),
						'search_items' 			=> __( 'Search', 'prosports' ),
						'not_found' 			=> __( 'No results found.', 'prosports' ),
						'not_found_in_trash' 	=> __( 'No results found.', 'prosports' ),
					),
					'public' 				=> false,
					'show_ui' 				=> true,
					'capability_type' 		=> 'sp_config',
					'map_meta_cap' 			=> true,
					'publicly_queryable' 	=> false,
					'exclude_from_search' 	=> true,
					'hierarchical' 			=> false,
					'supports' 				=> array( 'title', 'page-attributes', 'excerpt' ),
					'has_archive' 			=> false,
					'show_in_nav_menus' 	=> false,
					'can_export' 			=> false,
					'show_in_menu' => false,
				)
			)
		);

		register_post_type( 'sp_column',
			apply_filters( 'prosports_register_post_type_column',
				array(
					'labels' => array(
						'name' 					=> __( 'Table Columns', 'prosports' ),
						'singular_name' 		=> __( 'Column', 'prosports' ),
						'add_new_item' 			=> __( 'Add New Column', 'prosports' ),
						'edit_item' 			=> __( 'Edit Column', 'prosports' ),
						'new_item' 				=> __( 'New', 'prosports' ),
						'view_item' 			=> __( 'View', 'prosports' ),
						'search_items' 			=> __( 'Search', 'prosports' ),
						'not_found' 			=> __( 'No results found.', 'prosports' ),
						'not_found_in_trash' 	=> __( 'No results found.', 'prosports' ),
					),
					'public' 				=> false,
					'show_ui' 				=> true,
					'capability_type' 		=> 'sp_config',
					'map_meta_cap' 			=> true,
					'publicly_queryable' 	=> false,
					'exclude_from_search' 	=> true,
					'hierarchical' 			=> false,
					'supports' 				=> array( 'title', 'page-attributes', 'excerpt' ),
					'has_archive' 			=> false,
					'show_in_nav_menus' 	=> false,
					'can_export' 			=> false,
					'show_in_menu' => false,
				)
			)
		);

		register_post_type( 'sp_metric',
			apply_filters( 'prosports_register_post_type_metric',
				array(
					'labels' => array(
						'name' 					=> __( 'Player Metrics', 'prosports' ),
						'singular_name' 		=> __( 'Metric', 'prosports' ),
						'add_new_item' 			=> __( 'Add New Metric', 'prosports' ),
						'edit_item' 			=> __( 'Edit Metric', 'prosports' ),
						'new_item' 				=> __( 'New', 'prosports' ),
						'view_item' 			=> __( 'View', 'prosports' ),
						'search_items' 			=> __( 'Search', 'prosports' ),
						'not_found' 			=> __( 'No results found.', 'prosports' ),
						'not_found_in_trash' 	=> __( 'No results found.', 'prosports' ),
					),
					'public' 				=> false,
					'show_ui' 				=> true,
					'capability_type' 		=> 'sp_config',
					'map_meta_cap' 			=> true,
					'publicly_queryable' 	=> false,
					'exclude_from_search' 	=> true,
					'hierarchical' 			=> false,
					'supports' 				=> array( 'title', 'page-attributes', 'excerpt' ),
					'has_archive' 			=> false,
					'show_in_nav_menus' 	=> false,
					'can_export' 			=> false,
					'show_in_menu' => false,
				)
			)
		);

		register_post_type( 'sp_performance',
			apply_filters( 'prosports_register_post_type_performance',
				array(
					'labels' => array(
						'name' 					=> __( 'Player Performance', 'prosports' ),
						'singular_name' 		=> __( 'Player Performance', 'prosports' ),
						'add_new_item' 			=> __( 'Add New Performance', 'prosports' ),
						'edit_item' 			=> __( 'Edit Performance', 'prosports' ),
						'new_item' 				=> __( 'New', 'prosports' ),
						'view_item' 			=> __( 'View', 'prosports' ),
						'search_items' 			=> __( 'Search', 'prosports' ),
						'not_found' 			=> __( 'No results found.', 'prosports' ),
						'not_found_in_trash' 	=> __( 'No results found.', 'prosports' ),
					),
					'public' 				=> false,
					'show_ui' 				=> true,
					'capability_type' 		=> 'sp_config',
					'map_meta_cap' 			=> true,
					'publicly_queryable' 	=> false,
					'exclude_from_search' 	=> true,
					'hierarchical' 			=> false,
					'supports' 				=> array( 'title', 'thumbnail', 'page-attributes', 'excerpt' ),
					'has_archive' 			=> false,
					'show_in_nav_menus' 	=> false,
					'can_export' 			=> false,
					'show_in_menu' => false,
				)
			)
		);

		register_post_type( 'sp_statistic',
			apply_filters( 'prosports_register_post_type_statistic',
				array(
					'labels' => array(
						'name' 					=> __( 'Player Statistics', 'prosports' ),
						'singular_name' 		=> __( 'Statistic', 'prosports' ),
						'add_new_item' 			=> __( 'Add New Statistic', 'prosports' ),
						'edit_item' 			=> __( 'Edit Statistic', 'prosports' ),
						'new_item' 				=> __( 'New', 'prosports' ),
						'view_item' 			=> __( 'View', 'prosports' ),
						'search_items' 			=> __( 'Search', 'prosports' ),
						'not_found' 			=> __( 'No results found.', 'prosports' ),
						'not_found_in_trash' 	=> __( 'No results found.', 'prosports' ),
					),
					'public' 				=> false,
					'show_ui' 				=> true,
					'capability_type' 		=> 'sp_config',
					'map_meta_cap' 			=> true,
					'publicly_queryable' 	=> false,
					'exclude_from_search' 	=> true,
					'hierarchical' 			=> false,
					'supports' 				=> array( 'title', 'page-attributes', 'excerpt' ),
					'has_archive' 			=> false,
					'show_in_nav_menus' 	=> false,
					'can_export' 			=> false,
					'show_in_menu' => false,
				)
			)
		);

		$args = array(
			'labels' => array(
				'name' 					=> __( 'Events', 'prosports' ),
				'singular_name' 		=> __( 'Event', 'prosports' ),
				'add_new_item' 			=> __( 'Add New Event', 'prosports' ),
				'edit_item' 			=> __( 'Edit Event', 'prosports' ),
				'new_item' 				=> __( 'New', 'prosports' ),
				'view_item' 			=> __( 'View Event', 'prosports' ),
				'search_items' 			=> __( 'Search', 'prosports' ),
				'not_found' 			=> __( 'No results found.', 'prosports' ),
				'not_found_in_trash' 	=> __( 'No results found.', 'prosports' ),
			),
			'public' 				=> true,
			'show_ui' 				=> true,
			'capability_type' 		=> 'sp_event',
			'map_meta_cap' 			=> true,
			'publicly_queryable' 	=> true,
			'exclude_from_search' 	=> false,
			'hierarchical' 			=> false,
			'rewrite' 				=> array( 'slug' => get_option( 'prosports_event_slug', 'event' ) ),
			'supports' 				=> array( 'title', 'author', 'thumbnail', 'excerpt' ),
			'has_archive' 			=> false,
			'show_in_nav_menus' 	=> true,
			'menu_icon' 			=> 'dashicons-calendar',
		);

		if ( get_option( 'prosports_event_comment_status', 'no' ) == 'yes' ):
			$args[ 'supports' ][] = 'comments';
		endif;

		register_post_type( 'sp_event', apply_filters( 'prosports_register_post_type_event', $args  ) );

		register_post_type( 'sp_calendar',
			apply_filters( 'prosports_register_post_type_calendar',
				array(
					'labels' => array(
						'name' 					=> __( 'Calendars', 'prosports' ),
						'singular_name' 		=> __( 'Calendar', 'prosports' ),
						'add_new_item' 			=> __( 'Add New Calendar', 'prosports' ),
						'edit_item' 			=> __( 'Edit Calendar', 'prosports' ),
						'new_item' 				=> __( 'New', 'prosports' ),
						'view_item' 			=> __( 'View Calendar', 'prosports' ),
						'search_items' 			=> __( 'Search', 'prosports' ),
						'not_found' 			=> __( 'No results found.', 'prosports' ),
						'not_found_in_trash' 	=> __( 'No results found.', 'prosports' ),
					),
					'public' 				=> true,
					'show_ui' 				=> true,
					'capability_type' 		=> 'sp_calendar',
					'map_meta_cap' 			=> true,
					'publicly_queryable' 	=> true,
					'exclude_from_search' 	=> false,
					'hierarchical' 			=> false,
					'rewrite' 				=> array( 'slug' => get_option( 'prosports_calendar_slug', 'calendar' ) ),
					'supports' 				=> array( 'title', 'author', 'thumbnail' ),
					'has_archive' 			=> false,
					'show_in_nav_menus' 	=> true,
					'show_in_menu' => 'edit.php?post_type=sp_event',
					'show_in_admin_bar' 	=> true,
				)
			)
		);

		register_post_type( 'sp_team',
			apply_filters( 'prosports_register_post_type_team',
				array(
					'labels' => array(
						'name' 					=> __( 'Teams', 'prosports' ),
						'singular_name' 		=> __( 'Team', 'prosports' ),
						'add_new_item' 			=> __( 'Add New Team', 'prosports' ),
						'edit_item' 			=> __( 'Edit Team', 'prosports' ),
						'new_item' 				=> __( 'New', 'prosports' ),
						'view_item' 			=> __( 'View Team', 'prosports' ),
						'search_items' 			=> __( 'Search', 'prosports' ),
						'not_found' 			=> __( 'No results found.', 'prosports' ),
						'not_found_in_trash' 	=> __( 'No results found.', 'prosports' ),
					),
					'public' 				=> true,
					'show_ui' 				=> true,
					'capability_type' 		=> 'sp_team',
					'map_meta_cap' 			=> true,
					'publicly_queryable' 	=> true,
					'exclude_from_search' 	=> false,
					'hierarchical' 			=> true,
					'rewrite' 				=> array( 'slug' => get_option( 'prosports_team_slug', 'team' ) ),
					'supports' 				=> array( 'title', 'author', 'thumbnail', 'page-attributes', 'excerpt' ),
					'has_archive' 			=> false,
					'show_in_nav_menus' 	=> true,
					'menu_icon' 			=> 'dashicons-shield-alt',
				)
			)
		);

		register_post_type( 'sp_table',
			apply_filters( 'prosports_register_post_type_table',
				array(
					'labels' => array(
						'name' 					=> __( 'League Tables', 'prosports' ),
						'singular_name' 		=> __( 'League Table', 'prosports' ),
						'add_new_item' 			=> __( 'Add New League Table', 'prosports' ),
						'edit_item' 			=> __( 'Edit League Table', 'prosports' ),
						'new_item' 				=> __( 'New', 'prosports' ),
						'view_item' 			=> __( 'View League Table', 'prosports' ),
						'search_items' 			=> __( 'Search', 'prosports' ),
						'not_found' 			=> __( 'No results found.', 'prosports' ),
						'not_found_in_trash' 	=> __( 'No results found.', 'prosports' ),
					),
					'public' 				=> true,
					'show_ui' 				=> true,
					'capability_type' 		=> 'sp_table',
					'map_meta_cap' 			=> true,
					'publicly_queryable' 	=> true,
					'exclude_from_search' 	=> false,
					'hierarchical' 			=> false,
					'rewrite' 				=> array( 'slug' => get_option( 'prosports_table_slug', 'table' ) ),
					'supports' 				=> array( 'title', 'page-attributes', 'thumbnail' ),
					'has_archive' 			=> false,
					'show_in_nav_menus' 	=> true,
					'show_in_menu' 			=> 'edit.php?post_type=sp_team',
					'show_in_admin_bar' 	=> true,
				)
			)
		);

		register_post_type( 'sp_player',
			apply_filters( 'prosports_register_post_type_player',
				array(
					'labels' => array(
						'name' 					=> __( 'Players', 'prosports' ),
						'singular_name' 		=> __( 'Player', 'prosports' ),
						'add_new_item' 			=> __( 'Add New Player', 'prosports' ),
						'edit_item' 			=> __( 'Edit Player', 'prosports' ),
						'new_item' 				=> __( 'New', 'prosports' ),
						'view_item' 			=> __( 'View Player', 'prosports' ),
						'search_items' 			=> __( 'Search', 'prosports' ),
						'not_found' 			=> __( 'No results found.', 'prosports' ),
						'not_found_in_trash' 	=> __( 'No results found.', 'prosports' ),
					),
					'public' 				=> true,
					'show_ui' 				=> true,
					'capability_type' 		=> 'sp_player',
					'map_meta_cap' 			=> true,
					'publicly_queryable' 	=> true,
					'exclude_from_search' 	=> false,
					'hierarchical' 			=> false,
					'rewrite' 				=> array( 'slug' => get_option( 'prosports_player_slug', 'player' ) ),
					'supports' 				=> array( 'title', 'author', 'thumbnail', 'excerpt', 'page-attributes' ),
					'has_archive' 			=> false,
					'show_in_nav_menus' 	=> true,
					'menu_icon' 			=> 'dashicons-groups',
				)
			)
		);

		register_post_type( 'sp_list',
			apply_filters( 'prosports_register_post_type_list',
				array(
					'labels' => array(
						'name' 					=> __( 'Player Lists', 'prosports' ),
						'singular_name' 		=> __( 'Player List', 'prosports' ),
						'add_new_item' 			=> __( 'Add New Player List', 'prosports' ),
						'edit_item' 			=> __( 'Edit Player List', 'prosports' ),
						'new_item' 				=> __( 'New', 'prosports' ),
						'view_item' 			=> __( 'View Player List', 'prosports' ),
						'search_items' 			=> __( 'Search', 'prosports' ),
						'not_found' 			=> __( 'No results found.', 'prosports' ),
						'not_found_in_trash' 	=> __( 'No results found.', 'prosports' ),
					),
					'public' 				=> true,
					'show_ui' 				=> true,
					'capability_type' 		=> 'sp_list',
					'map_meta_cap' 			=> true,
					'publicly_queryable' 	=> true,
					'exclude_from_search' 	=> false,
					'hierarchical' 			=> false,
					'rewrite' 				=> array( 'slug' => get_option( 'prosports_list_slug', 'list' ) ),
					'supports' 				=> array( 'title', 'page-attributes', 'author', 'thumbnail' ),
					'has_archive' 			=> false,
					'show_in_nav_menus' 	=> true,
					'show_in_menu' 			=> 'edit.php?post_type=sp_player',
					'show_in_admin_bar' 	=> true,
				)
			)
		);

		register_post_type( 'sp_staff',
			apply_filters( 'prosports_register_post_type_staff',
				array(
					'labels' => array(
						'name' 					=> _n( 'Staff', 'Staff', 2, 'prosports' ),
						'singular_name' 		=> _n( 'Staff', 'Staff', 1, 'prosports' ),
						'add_new_item' 			=> __( 'Add New Staff', 'prosports' ),
						'edit_item' 			=> __( 'Edit Staff', 'prosports' ),
						'new_item' 				=> __( 'New', 'prosports' ),
						'view_item' 			=> __( 'View Staff', 'prosports' ),
						'search_items' 			=> __( 'Search', 'prosports' ),
						'not_found' 			=> __( 'No results found.', 'prosports' ),
						'not_found_in_trash' 	=> __( 'No results found.', 'prosports' ),
					),
					'public' 				=> true,
					'show_ui' 				=> true,
					'capability_type' 		=> 'sp_staff',
					'map_meta_cap' 			=> true,
					'publicly_queryable' 	=> true,
					'exclude_from_search' 	=> false,
					'hierarchical' 			=> false,
					'rewrite' 				=> array( 'slug' => get_option( 'prosports_staff_slug', 'staff' ) ),
					'supports' 				=> array( 'title', 'author', 'thumbnail', 'excerpt' ),
					'has_archive' 			=> false,
					'show_in_nav_menus' 	=> true,
					'menu_icon' 			=> 'dashicons-businessman',
				)
			)
		);
	}

	public function delete_config_post( $post_id ) {
		$post_type = get_post_type( $post_id );
		if ( is_sp_config_type( $post_type ) ) {
			wp_delete_post( $post_id, true );
		}
	}

	public function display_scheduled_events( $posts ) {
		global $wp_query, $wpdb;
		if ( is_single() && $wp_query->post_count == 0 && isset( $wp_query->query_vars['sp_event'] )) {
			$posts = $wpdb->get_results( $wp_query->request );
		}
		return $posts;
	}
}

new SP_Post_types();
