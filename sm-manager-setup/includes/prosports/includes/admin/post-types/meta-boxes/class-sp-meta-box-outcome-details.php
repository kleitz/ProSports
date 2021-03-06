<?php
/**
 * Outcome Details
 *
 * @author 		ProSports
 * @category 	Admin
 * @package 	ProSports/Admin/Meta_Boxes
 * @version     1.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'SP_Meta_Box_Config' ) )
	include( 'class-sp-meta-box-config.php' );

/**
 * SP_Meta_Box_Outcome_Details
 */
class SP_Meta_Box_Outcome_Details extends SP_Meta_Box_Config {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		wp_nonce_field( 'prosports_save_data', 'prosports_meta_nonce' );
		$abbreviation = get_post_meta( $post->ID, 'sp_abbreviation', true );
		$condition = get_post_meta( $post->ID, 'sp_condition', true );
		$main_result = get_option( 'prosports_primary_result', null );
		$result = get_page_by_path( $main_result, ARRAY_A, 'sp_result' );
		$label = sp_array_value( $result, 'post_title', __( 'Primary', 'prosports' ) );
		?>
		<p><strong><?php _e( 'Variable', 'prosports' ); ?></strong></p>
		<p>
			<input name="sp_default_key" type="hidden" id="sp_default_key" value="<?php echo $post->post_name; ?>">
			<input name="sp_key" type="text" id="sp_key" value="<?php echo $post->post_name; ?>">
		</p>
		<p><strong><?php _e( 'Abbreviation', 'prosports' ); ?></strong></p>
		<p>
			<input name="sp_abbreviation" type="text" id="sp_abbreviation" value="<?php echo $abbreviation; ?>" placeholder="<?php echo substr( $post->post_title, 0, 1 ); ?>">
		</p>
		<p><strong><?php _e( 'Condition', 'prosports' ); ?></strong></p>
		<p>
			<select name="sp_condition">
				<?php
				$options = array(
					'0' => __( '&mdash;', 'prosports' ),
					'>' => sprintf( __( 'Most %s', 'prosports' ), $label ),
					'<' => sprintf( __( 'Least %s', 'prosports' ), $label ),
					'=' => sprintf( __( 'Equal %s', 'prosports' ), $label ),
				);
				for( $i = 1; $i <= $count->publish; $i++ ):
					$options[ $i ] = $i;
				endfor;
				foreach ( $options as $key => $value ):
					printf( '<option value="%s" %s>%s</option>', $key, selected( true, $key == $condition, false ), $value );
				endforeach;
				?>
			</select>
		</p>
		<?php
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {
		update_post_meta( $post_id, 'sp_abbreviation', sp_array_value( $_POST, 'sp_abbreviation', array() ) );
		update_post_meta( $post_id, 'sp_condition', sp_array_value( $_POST, 'sp_condition', array() ) );
	}
}