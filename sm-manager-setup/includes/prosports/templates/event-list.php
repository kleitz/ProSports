<?php
/**
 * Event List
 *
 * @author 		ProSports
 * @package 	ProSports/Templates
 * @version     1.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$defaults = array(
	'id' => null,
	'status' => 'default',
	'date' => 'default',
	'date_from' => 'default',
	'date_to' => 'default',
	'number' => -1,
	'show_team_logo' => get_option( 'prosports_event_list_show_logos', 'no' ) == 'yes' ? true : false,
	'link_teams' => get_option( 'prosports_link_teams', 'no' ) == 'yes' ? true : false,
	'link_venues' => get_option( 'prosports_link_venues', 'yes' ) == 'yes' ? true : false,
	'sortable' => get_option( 'prosports_enable_sortable_tables', 'yes' ) == 'yes' ? true : false,
	'scrollable' => get_option( 'prosports_enable_scrollable_tables', 'yes' ) == 'yes' ? true : false,
	'responsive' => get_option( 'prosports_enable_responsive_tables', 'yes' ) == 'yes' ? true : false,
	'paginated' => get_option( 'prosports_event_list_paginated', 'yes' ) == 'yes' ? true : false,
	'rows' => get_option( 'prosports_event_list_rows', 10 ),
	'order' => 'default',
	'columns' => null,
	'show_all_events_link' => false,
);

extract( $defaults, EXTR_SKIP );

$calendar = new SP_Calendar( $id );
if ( $status != 'default' )
	$calendar->status = $status;
if ( $date != 'default' )
	$calendar->date = $date;
if ( $date_from != 'default' )
	$calendar->from = $date_from;
if ( $date_to != 'default' )
	$calendar->to = $date_to;
if ( $order != 'default' )
	$calendar->order = $order;
$data = $calendar->data();
$usecolumns = $calendar->columns;
$title_format = get_option( 'prosports_event_list_title_format', 'title' );
$time_format = get_option( 'prosports_event_list_time_format', 'combined' );

if ( isset( $columns ) ):
	if ( is_array( $columns ) )
		$usecolumns = $columns;
	else
		$usecolumns = explode( ',', $columns );
endif;

if ( $id ) {
	echo '<h4 class="sp-table-caption">' . get_the_title( $id ) . '</h4>';
}
?>
<div class="sp-template sp-template-event-list">
	<div class="sp-table-wrapper<?php if ( $scrollable ) { ?> sp-scrollable-table-wrapper<?php } ?>">
		<table class="sp-event-list sp-data-table<?php if ( $responsive ) { ?> sp-responsive-table<?php } if ( $paginated ) { ?> sp-paginated-table<?php } if ( $sortable ) { ?> sp-sortable-table<?php } ?>" data-sp-rows="<?php echo $rows; ?>">
			<thead>
				<tr>
					<?php
					echo '<th class="data-date">' . __( 'Date', 'prosports' ) . '</th>';

					switch ( $title_format ) {
						case 'homeaway':
							if ( sp_column_active( $usecolumns, 'event' ) ) {
								echo '<th class="data-home">' . __( 'Home', 'prosports' ) . '</th>';

								if ( 'combined' == $time_format && sp_column_active( $usecolumns, 'time' ) ) {
									echo '<th class="data-time">&nbsp;</th>';
								} elseif ( in_array( $time_format, array( 'separate', 'results' ) ) && sp_column_active( $usecolumns, 'results' ) ) {
									echo '<th class="data-results">&nbsp;</th>';
								}

								echo '<th class="data-away">' . __( 'Away', 'prosports' ) . '</th>';

								if ( in_array( $time_format, array( 'separate', 'time' ) ) && sp_column_active( $usecolumns, 'time' ) ) {
									echo '<th class="data-time">' . __( 'Time', 'prosports' ) . '</th>';
								}
							}
							break;
						default:
							if ( sp_column_active( $usecolumns, 'event' ) ) {
								if ( $title_format == 'teams' )
									echo '<th class="data-teams">' . __( 'Teams', 'prosports' ) . '</th>';
								else
									echo '<th class="data-event">' . __( 'Event', 'prosports' ) . '</th>';
							}

							switch ( $time_format ) {
								case 'separate':
									if ( sp_column_active( $usecolumns, 'time' ) )
										echo '<th class="data-time">' . __( 'Time', 'prosports' ) . '</th>';
									if ( sp_column_active( $usecolumns, 'results' ) )
										echo '<th class="data-results">' . __( 'Results', 'prosports' ) . '</th>';
									break;
								case 'time':
									if ( sp_column_active( $usecolumns, 'time' ) )
										echo '<th class="data-time">' . __( 'Time', 'prosports' ) . '</th>';
									break;
								case 'results':
									if ( sp_column_active( $usecolumns, 'results' ) )
										echo '<th class="data-results">' . __( 'Results', 'prosports' ) . '</th>';
									break;
								default:
									if ( sp_column_active( $usecolumns, 'time' ) )
										echo '<th class="data-time">' . __( 'Time/Results', 'prosports' ) . '</th>';
							}
					}

					if ( sp_column_active( $usecolumns, 'league' ) )
						echo '<th class="data-league">' . __( 'Competition', 'prosports' ) . '</th>';

					if ( sp_column_active( $usecolumns, 'season' ) )
						echo '<th class="data-season">' . __( 'Season', 'prosports' ) . '</th>';

					if ( sp_column_active( $usecolumns, 'venue' ) )
						echo '<th class="data-venue">' . __( 'Venue', 'prosports' ) . '</th>';

					if ( sp_column_active( $usecolumns, 'article' ) )
						echo '<th class="data-article">' . __( 'Article', 'prosports' ) . '</th>';
					?>
				</tr>
			</thead>
			<tbody>
				<?php
				$i = 0;

				if ( is_numeric( $number ) && $number > 0 )
					$limit = $number;

				foreach ( $data as $event ):
					if ( isset( $limit ) && $i >= $limit ) continue;

					$teams = get_post_meta( $event->ID, 'sp_team' );
					$video = get_post_meta( $event->ID, 'sp_video', true );

					$main_results = sp_get_main_results( $event );

					$teams_output = '';
					$teams_array = array();
					$team_logos = array();

					if ( $teams ):
						foreach ( $teams as $team ):
							$name = get_the_title( $team );
							if ( $name ):

								if ( $show_team_logo ):
									$name = sp_get_logo( $team, 'mini' ) . ' ' . $name;
									$team_logos[] = sp_get_logo( $team, 'mini' );
								endif;

								if ( $link_teams ):
									$team_output = '<a href="' . get_post_permalink( $team ) . '">' . $name . '</a>';
								else:
									$team_output = $name;
								endif;

								$team_result = sp_array_value( $main_results, $team, null );

								if ( $team_result != null ):
									if ( $usecolumns != null && ! in_array( 'time', $usecolumns ) ):
										$team_output .= ' (' . $team_result . ')';
									endif;
								endif;

								$teams_array[] = $team_output;

								$teams_output .= $team_output . '<br>';
							endif;
						endforeach;
					else:
						$teams_output .= '&mdash;';
					endif;

					echo '<tr class="sp-row sp-post' . ( $i % 2 == 0 ? ' alternate' : '' ) . '">';

						echo '<td class="data-date"><a href="' . get_permalink( $event->ID ) . '"><date>' . get_post_time( 'Y-m-d H:i:s', false, $event ) . '</date>' . get_post_time( get_option( 'date_format' ), false, $event, true ) . '</a></td>';

						switch ( $title_format ) {
							case 'homeaway':
								if ( sp_column_active( $usecolumns, 'event' ) ) {
									$team = array_shift( $teams_array );
									echo '<td class="data-home">' . $team . '</td>';

									if ( 'combined' == $time_format && sp_column_active( $usecolumns, 'time' ) ) {
										echo '<td class="data-time"><a href="' . get_permalink( $event->ID ) . '">';
										if ( ! empty( $main_results ) ):
											echo implode( ' - ', $main_results );
										else:
											echo '<date>&nbsp;' . get_post_time( 'H:i:s', false, $event ) . '</date>' . sp_get_time( $event );
										endif;
										echo '</a></td>';
									} elseif ( in_array( $time_format, array( 'separate', 'results' ) ) && sp_column_active( $usecolumns, 'results' ) ) {
										echo '<td class="data-results"><a href="' . get_permalink( $event->ID ) . '">';
										if ( ! empty( $main_results ) ):
											echo implode( ' - ', $main_results );
										else:
											echo '-';
										endif;
										echo '</a></td>';
									}

									$team = array_shift( $teams_array );
									echo '<td class="data-away">' . $team . '</td>';

									if ( in_array( $time_format, array( 'separate', 'time' ) ) && sp_column_active( $usecolumns, 'time' ) ) {
										echo '<td class="data-time"><a href="' . get_permalink( $event->ID ) . '">';
										echo '<date>&nbsp;' . get_post_time( 'H:i:s', false, $event ) . '</date>' . sp_get_time( $event );
										echo '</a></td>';
									}
								}
								break;
							default:
								if ( sp_column_active( $usecolumns, 'event' ) ) {
									if ( $title_format == 'teams' )
										echo '<td class="data-event data-teams">' . $teams_output . '</td>';
									else
										echo '<td class="data-event"><a href="' . get_permalink( $event->ID ) . '">' . implode( ' ', $team_logos ) . ' ' . $event->post_title . '</a></td>';
								}

								switch ( $time_format ) {
									case 'separate':
										if ( sp_column_active( $usecolumns, 'time' ) ) {
											echo '<td class="data-time"><a href="' . get_permalink( $event->ID ) . '">';
											echo '<date>&nbsp;' . get_post_time( 'H:i:s', false, $event ) . '</date>' . sp_get_time( $event );
											echo '</a></td>';
										}
										if ( sp_column_active( $usecolumns, 'results' ) ) {
											echo '<td class="data-results"><a href="' . get_permalink( $event->ID ) . '">';
											if ( ! empty( $main_results ) ):
												echo implode( ' - ', $main_results );
											else:
												echo '-';
											endif;
											echo '</a></td>';
										}
										break;
									case 'time':
										if ( sp_column_active( $usecolumns, 'time' ) ) {
											echo '<td class="data-time"><a href="' . get_permalink( $event->ID ) . '">';
											echo '<date>&nbsp;' . get_post_time( 'H:i:s', false, $event ) . '</date>' . sp_get_time( $event );
											echo '</a></td>';
										}
										break;
									case 'results':
										if ( sp_column_active( $usecolumns, 'results' ) ) {
											echo '<td class="data-results"><a href="' . get_permalink( $event->ID ) . '">';
											if ( ! empty( $main_results ) ):
												echo implode( ' - ', $main_results );
											else:
												echo '-';
											endif;
											echo '</a></td>';
										}
										break;
									default:
										if ( sp_column_active( $usecolumns, 'time' ) ) {
											echo '<td class="data-time"><a href="' . get_permalink( $event->ID ) . '">';
											if ( ! empty( $main_results ) ):
												echo implode( ' - ', $main_results );
											else:
												echo '<date>&nbsp;' . get_post_time( 'H:i:s', false, $event ) . '</date>' . sp_get_time( $event );
											endif;
											echo '</a></td>';
										}
								}
						}

						if ( sp_column_active( $usecolumns, 'league' ) ):
							echo '<td class="data-league">';
							$leagues = get_the_terms( $event->ID, 'sp_league' );
							if ( $leagues ): foreach ( $leagues as $league ):
								echo $league->name;
							endforeach; endif;
							echo '</td>';
						endif;

						if ( sp_column_active( $usecolumns, 'season' ) ):
							echo '<td class="data-season">';
							$seasons = get_the_terms( $event->ID, 'sp_season' );
							if ( $seasons ): foreach ( $seasons as $season ):
								echo $season->name;
							endforeach; endif;
							echo '</td>';
						endif;

						if ( sp_column_active( $usecolumns, 'venue' ) ):
							echo '<td class="data-venue">';
							if ( $link_venues ):
								the_terms( $event->ID, 'sp_venue' );
							else:
								$venues = get_the_terms( $event->ID, 'sp_venue' );
								if ( $venues ): foreach ( $venues as $venue ):
									echo $venue->name;
								endforeach; endif;
							endif;
							echo '</td>';
						endif;

						if ( sp_column_active( $usecolumns, 'article' ) ):
							echo '<td class="data-article">
								<a href="' . get_permalink( $event->ID ) . '">';

								if ( $video ):
									echo '<div class="dashicons dashicons-video-alt"></div>';
								elseif ( has_post_thumbnail( $event->ID ) ):
									echo '<div class="dashicons dashicons-camera"></div>';
								endif;
								if ( $event->post_content !== null ):
									if ( $event->post_status == 'publish' ):
										_e( 'Recap', 'prosports' );
									else:
										_e( 'Preview', 'prosports' );
									endif;
								endif;

								echo '</a>
							</td>';
						endif;

					echo '</tr>';

					$i++;
				endforeach;
				?>
			</tbody>
		</table>
	</div>
	<?php
	if ( $id && $show_all_events_link )
		echo '<a class="sp-calendar-link sp-view-all-link" href="' . get_permalink( $id ) . '">' . __( 'View all events', 'prosports' ) . '</a>';
	?>
</div>