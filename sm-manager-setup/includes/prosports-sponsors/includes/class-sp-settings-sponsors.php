<?php
/**
 * ProSports Sponsor Settings
 *
 * @author 		ProSports
 * @category 	Admin
 * @package 	ProSports Sponsors
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'SP_Settings_Sponsors' ) ) :

/**
 * SP_Settings_Sponsors
 */
class SP_Settings_Sponsors extends SP_Settings_Page {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id    = 'sponsors';
		$this->label = __( 'Sponsors', 'prosports' );

		add_filter( 'prosports_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'prosports_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'prosports_admin_field_header_sponsor_size', array( $this, 'header_size_setting' ) );
		add_action( 'prosports_admin_field_header_sponsor_position', array( $this, 'header_position_setting' ) );
		add_action( 'prosports_admin_field_footer_sponsor_size', array( $this, 'footer_size_setting' ) );
		add_action( 'prosports_settings_save_' . $this->id, array( $this, 'save' ) );
	}

	/**
	 * Get settings array
	 *
	 * @return array
	 */
	public function get_settings() {

		return apply_filters( 'prosports_sponsor_settings', array(

			array( 'title' => __( 'Sponsors', 'prosports' ), 'type' => 'title', 'id' => 'sponsor_options' ),

			array(
				'title' 	=> __( 'Impressions', 'prosports' ),
				'desc' 		=> __( 'Exclude logged-in users', 'prosports' ),
				'id' 		=> 'prosports_exclude_authenticated_sponsor_impressions',
				'default'	=> 'no',
				'type' 		=> 'checkbox',
			),

			array(
				'title' 	=> __( 'Clicks', 'prosports' ),
				'desc' 		=> __( 'Exclude logged-in users', 'prosports' ),
				'id' 		=> 'prosports_exclude_authenticated_sponsor_clicks',
				'default'	=> 'no',
				'type' 		=> 'checkbox',
			),

			array(
				'title'     => __( 'Visit Site', 'prosports' ),
				'desc' 		=> __( 'Open link in a new window/tab', 'prosports' ),
				'id' 		=> 'prosports_sponsor_site_target_blank',
				'default'	=> 'no',
				'type' 		=> 'checkbox',
			),

			array( 'type' => 'sectionend', 'id' => 'sponsor_options' ),

			array( 'title' => __( 'Header', 'prosports' ), 'type' => 'title', 'id' => 'header_sponsor_options' ),
			
			array(
				'title' 	=> __( 'Display', 'prosports' ),
				'id' 		=> 'prosports_header_sponsors_limit',
				'class' 	=> 'small-text',
				'default'	=> '3',
				'desc' 		=> __( 'sponsors', 'prosports' ),
				'type' 		=> 'number',
				'custom_attributes' => array(
					'min' 	=> 0,
					'step' 	=> 1
				),
			),

			array(
				'title' 	=> __( 'Sort by', 'prosports' ),
				'id' 		=> 'prosports_header_sponsors_orderby',
				'default'	=> 'menu_order',
				'type' 		=> 'select',
				'options'	=> array(
					'menu_order' 	=> __( 'Menu Order', 'prosports' ),
					'date' 			=> __( 'Date', 'prosports' ),
					'title' 		=> __( 'Name', 'prosports' ),
					'rand' 			=> __( 'Random', 'prosports' ),
				),
			),

			array(
				'title' 	=> __( 'Sort Order', 'prosports' ),
				'id' 		=> 'prosports_header_sponsors_order',
				'default'	=> 'ASC',
				'type' 		=> 'select',
				'options'	=> array(
					'ASC' 			=> __( 'Ascending', 'prosports' ),
					'DESC' 			=> __( 'Descending', 'prosports' ),
				),
			),

			array( 'type' => 'header_sponsor_size' ),

			array( 'type' => 'header_sponsor_position' ),

			array( 'type' => 'sectionend', 'id' => 'header_sponsor_options' ),

			array( 'title' => __( 'Footer', 'prosports' ), 'type' => 'title', 'id' => 'footer_sponsor_options' ),
			
			array(
				'title' => __( 'Title', 'prosports' ),
				'id' => 'prosports_footer_sponsors_title',
				'default' => '',
				'type' => 'text',
			),

			array(
				'title' 	=> __( 'Display', 'prosports' ),
				'id' 		=> 'prosports_footer_sponsors_limit',
				'class' 	=> 'small-text',
				'default'	=> '20',
				'desc' 		=> __( 'sponsors', 'prosports' ),
				'type' 		=> 'number',
				'custom_attributes' => array(
					'min' 	=> 0,
					'step' 	=> 1
				),
			),

			array(
				'title' 	=> __( 'Sort by', 'prosports' ),
				'id' 		=> 'prosports_footer_sponsors_orderby',
				'default'	=> 'menu_order',
				'type' 		=> 'select',
				'options'	=> array(
					'menu_order' 	=> __( 'Menu Order', 'prosports' ),
					'date' 			=> __( 'Date', 'prosports' ),
					'title' 		=> __( 'Name', 'prosports' ),
					'rand' 			=> __( 'Random', 'prosports' ),
				),
			),

			array(
				'title' 	=> __( 'Sort Order', 'prosports' ),
				'id' 		=> 'prosports_footer_sponsors_order',
				'default'	=> 'ASC',
				'type' 		=> 'select',
				'options'	=> array(
					'ASC' 			=> __( 'Ascending', 'prosports' ),
					'DESC' 			=> __( 'Descending', 'prosports' ),
				),
			),

			array( 'type' => 'footer_sponsor_size' ),

			array(
				'title' => __( 'Text Color', 'prosports' ),
				'id' 		=> 'prosports_footer_sponsors_css_text',
				'type' 		=> 'color',
				'css' 		=> 'width:6em;',
				'default'	=> '#363f48',
				'autoload'  => false
			),

			array(
				'title' => __( 'Background Color', 'prosports' ),
				'id' 		=> 'prosports_footer_sponsors_css_background',
				'type' 		=> 'color',
				'css' 		=> 'width:6em;',
				'default'	=> '#f4f4f4',
				'autoload'  => false
			),

			array( 'type' => 'sectionend', 'id' => 'footer_sponsor_options' ),

		)); // End sponsor settings
	}

	/**
	 * Save settings
	 */
	public function save() {
		$settings = $this->get_settings();
		SP_Admin_Settings::save_fields( $settings );
    	update_option( 'prosports_header_sponsor_width', (int) sp_array_value( $_POST, 'prosports_header_sponsor_width', 128 ) );
    	update_option( 'prosports_header_sponsor_height', (int) sp_array_value( $_POST, 'prosports_header_sponsor_height', 64 ) );
    	update_option( 'prosports_header_sponsors_top', (int) sp_array_value( $_POST, 'prosports_header_sponsors_top', 10) );
    	update_option( 'prosports_header_sponsors_right', (int) sp_array_value( $_POST, 'prosports_header_sponsors_right', 10 ) );
    	update_option( 'prosports_footer_sponsor_width', (int) sp_array_value( $_POST, 'prosports_footer_sponsor_width', 256 ) );
    	update_option( 'prosports_footer_sponsor_height', (int) sp_array_value( $_POST, 'prosports_footer_sponsor_height', 128 ) );
	}

	/**
	 * Header sponsor size settings
	 *
	 * @access public
	 * @return void
	 */
	public function header_size_setting() {
		$width = (int) get_option( 'prosports_header_sponsor_width', 128 );
		$height = (int) get_option( 'prosports_header_sponsor_height', 64 );
    	?>
		<tr valign="top">
			<th scope="row" class="titledesc"><?php _e( 'Logo', 'prosports' ); ?></th>
            <td class="forminp">
				<fieldset>
					<legend class="screen-reader-text"><span><?php _e( 'Logo', 'prosports' ); ?></span></legend>
					<label for="prosports_header_sponsor_width"><?php _e( 'Max Width', 'prosports' ); ?></label>
					<input name="prosports_header_sponsor_width" type="number" step="1" min="0" id="prosports_header_sponsor_width" value="<?php echo $width; ?>" class="small-text">
					<label for="prosports_header_sponsor_height"><?php _e( 'Max Height', 'prosports' ); ?></label>
					<input name="prosports_header_sponsor_height" type="number" step="1" min="0" id="prosports_header_sponsor_height" value="<?php echo $height; ?>" class="small-text">
				</fieldset>
       		</td>
       	</tr>
       	<?php
	}

	/**
	 * Header sponsor position settings
	 *
	 * @access public
	 * @return void
	 */
	public function header_position_setting() {
		$top = (int) get_option( 'prosports_header_sponsors_top', 10 );
		$right = (int) get_option( 'prosports_header_sponsors_right', 10 );
    	?>
		<tr valign="top">
			<th scope="row" class="titledesc"><?php _e( 'Position', 'prosports' ); ?></th>
            <td class="forminp">
				<fieldset>
					<legend class="screen-reader-text"><span><?php _e( 'Position', 'prosports' ); ?></span></legend>
					<label for="prosports_header_sponsors_top"><?php _e( 'Top', 'prosports' ); ?></label>
					<input name="prosports_header_sponsors_top" type="number" step="1" id="prosports_header_sponsors_top" value="<?php echo $top; ?>" class="small-text">
					<label for="prosports_header_sponsors_right"><?php _e( 'Right', 'prosports' ); ?></label>
					<input name="prosports_header_sponsors_right" type="number" step="1" id="prosports_header_sponsors_right" value="<?php echo $right; ?>" class="small-text">
				</fieldset>
       		</td>
       	</tr>
       	<?php
	}

	/**
	 * Footer sponsor size settings
	 *
	 * @access public
	 * @return void
	 */
	public function footer_size_setting() {
		$width = (int) get_option( 'prosports_footer_sponsor_width', 256 );
		$height = (int) get_option( 'prosports_footer_sponsor_height', 128 );
    	?>
		<tr valign="top">
			<th scope="row" class="titledesc"><?php _e( 'Logo', 'prosports' ); ?></th>
            <td class="forminp">
				<fieldset>
					<legend class="screen-reader-text"><span><?php _e( 'Logo', 'prosports' ); ?></span></legend>
					<label for="prosports_footer_sponsor_width"><?php _e( 'Max Width', 'prosports' ); ?></label>
					<input name="prosports_footer_sponsor_width" type="number" step="1" min="0" id="prosports_footer_sponsor_width" value="<?php echo $width; ?>" class="small-text">
					<label for="prosports_footer_sponsor_height"><?php _e( 'Max Height', 'prosports' ); ?></label>
					<input name="prosports_footer_sponsor_height" type="number" step="1" min="0" id="prosports_footer_sponsor_height" value="<?php echo $height; ?>" class="small-text">
				</fieldset>
       		</td>
       	</tr>
       	<?php
	}
}

endif;

return new SP_Settings_Sponsors();
