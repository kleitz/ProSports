<?php
/**
 * Performance Details
 *
 * @author 		ProSports
 * @category 	Admin
 * @package 	ProSports/Admin/Meta_Boxes
 * @version     0.8
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'SP_Meta_Box_Config' ) )
	include( 'class-sp-meta-box-config.php' );

/**
 * SP_Meta_Box_Performance_Details
 */
class SP_Meta_Box_Performance_Details extends SP_Meta_Box_Config {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		wp_nonce_field( 'prosports_save_data', 'prosports_meta_nonce' );
		?>
		<p><strong><?php _e( 'Variable', 'prosports' ); ?></strong></p>
		<p>
			<input name="sp_default_key" type="hidden" id="sp_default_key" value="<?php echo $post->post_name; ?>">
			<input name="sp_key" type="text" id="sp_key" value="<?php echo $post->post_name; ?>">
		</p>
		<?php
	}
}