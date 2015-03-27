<?php
/**
 * Player Statistics
 *
 * @author 		ProSports
 * @package 	ProSports/Templates
 * @version     1.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! isset( $id ) )
	$id = get_the_ID();

$player = new SP_Player( $id );

$scrollable = get_option( 'prosports_enable_scrollable_tables', 'yes' ) == 'yes' ? true : false;
$responsive = get_option( 'prosports_enable_responsive_tables', 'yes' ) == 'yes' ? true : false;
$show_total = get_option( 'prosports_player_show_total', 'no' ) == 'yes' ? true : false;
$leagues = get_the_terms( $id, 'sp_league' );

// Loop through statistics for each league
if ( is_array( $leagues ) ):
	foreach ( $leagues as $league ):
		$data = $player->data( $league->term_id );

		// The first row should be column labels
		$labels = $data[0];

		// Remove the first row to leave us with the actual data
		unset( $data[0] );

		// Skip if there are no rows in the table
		if ( empty( $data ) )
			continue;

		$output = '<h4 class="sp-table-caption">' . $league->name . '</h4>' .
			'<div class="sp-table-wrapper' . ( $scrollable ? ' sp-scrollable-table-wrapper' : '' ) . '">' .
			'<table class="sp-player-statistics sp-data-table' . ( $responsive ? ' sp-responsive-table' : '' ) . '">' . '<thead>' . '<tr>';

		foreach( $labels as $key => $label ):
			$output .= '<th class="data-' . $key . '">' . $label . '</th>';
		endforeach;

		$output .= '</tr>' . '</thead>' . '<tbody>';

		$i = 0;

		foreach( $data as $season_id => $row ):

			$output .= '<tr class="' . ( $i % 2 == 0 ? 'odd' : 'even' ) . '">';

			foreach( $labels as $key => $value ):
				$output .= '<td class="data-' . $key . '">' . sp_array_value( $row, $key, '&mdash;' ) . '</td>';
			endforeach;

			$output .= '</tr>';

			$i++;

		endforeach;

		$output .= '</tbody>' . '</table>' . '</div>';
		?>
		<div class="sp-template sp-template-player-statistics">
			<?php echo $output; ?>
		</div>
		<?php
	endforeach;
endif;

if ( $show_total ):
	$data = $player->data( 0 );

	// The first row should be column labels
	$labels = $data[0];

	// Remove the first row to leave us with the actual data
	unset( $data[0] );

	// Skip if there are no rows in the table
	if ( empty( $data ) )
		return false;

	$output = '<h4 class="sp-table-caption">' . __( 'Career Total', 'prosports' ) . '</h4>' .
		'<div class="sp-table-wrapper' . ( $scrollable ? ' sp-scrollable-table-wrapper' : '' ) . '">' .
		'<table class="sp-player-statistics sp-data-table' . ( $responsive ? ' sp-responsive-table' : '' ) . '">' . '<thead>' . '<tr>';

	foreach( $labels as $key => $label ):
		if ( 'team' == $key )
			continue;
		$output .= '<th class="data-' . $key . '">' . $label . '</th>';
	endforeach;

	$output .= '</tr>' . '</thead>' . '<tbody>';

	$i = 0;

	foreach( $data as $season_id => $row ):

		$output .= '<tr class="' . ( $i % 2 == 0 ? 'odd' : 'even' ) . '">';

		foreach( $labels as $key => $value ):
			if ( 'team' == $key )
				continue;
			$output .= '<td class="data-' . $key . '">' . sp_array_value( $row, $key, '&mdash;' ) . '</td>';
		endforeach;

		$output .= '</tr>';

		$i++;

	endforeach;

	$output .= '</tbody>' . '</table>' . '</div>';
	?>
	<div class="sp-template sp-template-player-statistics sp-template-player-total">
		<?php echo $output; ?>
	</div>
	<?php
endif;