<?php
// DEGUG 2
global $futusign_debug;
if ($futusign_debug === '2') {
	echo 2;
	die();
}
/**
 * futusign endpoint
 *
 * @link       https://github.com/larkintuckerllc
 * @since      2.1.2
 *
 * @package    futusign
 * @subpackage futusign/public/partials
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}
// DEGUG 3
if ($futusign_debug === '3') {
	echo 3;
	die();
}
/**
 * futusign endpoint
 * @param    number     $screen_id      The screen id
 *
 * @since    2.1.2
 */
function futusign_endpoint($screen_id) {
	global $futusign_debug;
	// DEGUG 6
	if ($futusign_debug === '6') {
		echo 6;
		die();
	}
	function term_to_id($o) {
		return $o->term_id;
	}
	$screen = null;
	$subscribed_playlist_ids;
	$subscribed_override_ids = array();
	$images = array();
	$images_override = array();
	$media_decks = array();
	$media_decks_override = array();
	$webs = array();
	$webs_override = array();
	$youtube_videos = array();
	$youtube_videos_override = array();
	$slide_decks = array();
	$slide_decks_override = array();
	$layers = array();
	$overlay = null;
	$ov_widgets = array();
	$monitor = null;
	// SCREEN
	$args = array(
		'post_type' => 'futusign_screen',
		'posts_per_page' => -1,
		'page_id' => $screen_id,
	);
	// DEGUG 7
	if ($futusign_debug === '7') {
		echo 7;
		die();
	}
	$loop = new WP_Query( $args );
	while ( $loop->have_posts() ) {
		// DEGUG 8
		if ($futusign_debug === '8') {
			echo 8;
			die();
		}
		$loop->the_post();
		$id = get_the_ID();
		// SUBSCRIBED PLAYLISTS
		$playlist_terms = get_the_terms( $id, 'futusign_playlist');
		$subscribed_playlist_ids = $playlist_terms ? array_map('term_to_id', $playlist_terms) : [];
		// SUBSCRIBED OVERRIDES
		if (class_exists( 'Futusign_Override' )) {
			$override_terms = get_the_terms( $id, 'futusign_override');
			$subscribed_override_ids = $override_terms ? array_map('term_to_id', $override_terms) : [];
		}
		// POLLING
		$polling = get_field('polling');
		// OVERLAY
		$overlayPost = get_field('overlay');
		$screen = array(
			'id' => $id,
			'title' => get_the_title(),
			'polling' => $polling ? intval( $polling ) : 60, // OPTIONAL IN OLDER VERSIONS
			'subscribedPlaylistIds' => $subscribed_playlist_ids,
			'subscribedOverrideIds' => $subscribed_override_ids,
			'overlay' => $overlayPost ? $overlayPost->ID : null
		);
	}
	// DEGUG 9
	if ($futusign_debug === '9') {
		echo 9;
		die();
	}
	if ($screen === null) {
		status_header(404);
		return;
	}
	// DEBUG 10
	if ($futusign_debug === '10') {
		echo 10;
		die();
	}
	wp_reset_query();
	// IMAGES
	$args = array(
		'post_type' => 'futusign_image',
		'posts_per_page' => -1,
		'tax_query' => array(
			array(
				'taxonomy' => 'futusign_playlist',
				'field'    => 'id',
				'terms'    => $subscribed_playlist_ids,
			),
		),
	);
	$loop = new WP_Query( $args );
	while ( $loop->have_posts() ) {
		$loop->the_post();
		$priority = get_field('priority');
		$images[] = array(
			'id' => get_the_ID(),
			'title' => get_the_title(),
			'file' => get_field('file'),
			'imageDuration' => intval(get_field('image_duration')),
			'priority' => $priority ? intval($priority) : 1
		);
	}
	wp_reset_query();
	// IMAGES OVERRIDE
	if (class_exists( 'Futusign_Override' )) {
		$args = array(
			'post_type' => 'futusign_image',
			'posts_per_page' => -1,
			'tax_query' => array(
				array(
					'taxonomy' => 'futusign_override',
					'field'    => 'id',
					'terms'    => $subscribed_override_ids,
				),
			),
		);
		$loop = new WP_Query( $args );
		while ( $loop->have_posts() ) {
			$loop->the_post();
			$priority = get_field('priority');
			$images_override[] = array(
				'id' => get_the_ID(),
				'title' => get_the_title(),
				'file' => get_field('file'),
				'imageDuration' => intval(get_field('image_duration')),
				'priority' => $priority ? intval($priority) : 1
			);
		}
	  wp_reset_query();
	}
	// MEDIA DECKS
	if (class_exists( 'Futusign_MediaDeck' )) {
		$args = array(
			'post_type' => 'futusign_media_deck',
			'posts_per_page' => -1,
			'tax_query' => array(
				array(
					'taxonomy' => 'futusign_playlist',
					'field'    => 'id',
					'terms'    => $subscribed_playlist_ids,
				),
			),
		);
		$loop = new WP_Query( $args );
		while ( $loop->have_posts() ) {
			$loop->the_post();
			$priority = get_field('priority');
			$media = array();
			$mediaRaw = get_field('media');
			for ($i = 0; $i < sizeof($mediaRaw); $i++) {
				$mediaRawItem = $mediaRaw[$i];
				switch ($mediaRawItem['acf_fc_layout']) {
					case 'image':
						$media[] = array(
							'type' => 'IMAGES',
							'file' => $mediaRawItem['file'],
							'imageDuration' => intval($mediaRawItem['image_duration'])
						);
						break;
					case 'web':
						$media[] = array(
							'type' => 'WEBS',
							'url' => $mediaRawItem['url'],
							'webDuration' => intval($mediaRawItem['web_duration'])
						);
						break;
					case 'youtube':
						$media[] = array(
							'type' => 'YOUTUBE_VIDEOS',
							'url' => $mediaRawItem['url'],
							'suggestedQuality' => $mediaRawItem['suggested_quality']
						);
						break;
					default:
				}
			}
			$media_decks[] = array(
				'id' => get_the_ID(),
				'title' => get_the_title(),
				'media' => $media,
				'priority' => $priority ? intval($priority) : 1
			);
		}
		wp_reset_query();
		// MEDIA DECKS OVERRIDE
		if (class_exists( 'Futusign_Override' )) {
			$args = array(
				'post_type' => 'futusign_media_deck',
				'posts_per_page' => -1,
				'tax_query' => array(
					array(
						'taxonomy' => 'futusign_override',
						'field'    => 'id',
						'terms'    => $subscribed_override_ids,
					),
				),
			);
			$loop = new WP_Query( $args );
			while ( $loop->have_posts() ) {
				$loop->the_post();
				$priority = get_field('priority');
				$media = array();
				$mediaRaw = get_field('media');
				for ($i = 0; $i < sizeof($mediaRaw); $i++) {
					$mediaRawItem = $mediaRaw[$i];
					switch ($mediaRawItem['acf_fc_layout']) {
						case 'image':
							$media[] = array(
								'type' => 'IMAGES',
								'file' => $mediaRawItem['file'],
								'imageDuration' => intval($mediaRawItem['image_duration'])
							);
							break;
						case 'web':
							$media[] = array(
								'type' => 'WEBS',
								'url' => $mediaRawItem['url'],
								'webDuration' => intval($mediaRawItem['web_duration'])
							);
							break;
						case 'youtube':
							$media[] = array(
								'type' => 'YOUTUBE_VIDEOS',
								'url' => $mediaRawItem['url'],
								'suggestedQuality' => $mediaRawItem['suggested_quality']
							);
							break;
						default:
					}
				}
				$media_decks_override[] = array(
					'id' => get_the_ID(),
					'title' => get_the_title(),
					'media' => $media,
					'priority' => $priority ? intval($priority) : 1
				);
			}
		  wp_reset_query();
		}
	}
	// WEBS
	if (class_exists( 'Futusign_Web' )) {
		$args = array(
			'post_type' => 'futusign_web',
			'posts_per_page' => -1,
			'tax_query' => array(
				array(
					'taxonomy' => 'futusign_playlist',
					'field'    => 'id',
					'terms'    => $subscribed_playlist_ids,
				),
			),
		);
		$loop = new WP_Query( $args );
		while ( $loop->have_posts() ) {
			$loop->the_post();
			$priority = get_field('priority');
			$webs[] = array(
				'id' => get_the_ID(),
				'title' => get_the_title(),
				'url' => get_field('url'),
				'webDuration' => intval(get_field('web_duration')),
				'priority' => $priority ? intval($priority) : 1
			);
		}
		wp_reset_query();
		// WEBS OVERRIDE
		if (class_exists( 'Futusign_Override' )) {
			$args = array(
				'post_type' => 'futusign_web',
				'posts_per_page' => -1,
				'tax_query' => array(
					array(
						'taxonomy' => 'futusign_override',
						'field'    => 'id',
						'terms'    => $subscribed_override_ids,
					),
				),
			);
			$loop = new WP_Query( $args );
			while ( $loop->have_posts() ) {
				$loop->the_post();
				$priority = get_field('priority');
				$webs_override[] = array(
					'id' => get_the_ID(),
					'title' => get_the_title(),
					'url' => get_field('url'),
					'webDuration' => intval(get_field('web_duration')),
					'priority' => $priority ? intval($priority) : 1
				);
			}
		  wp_reset_query();
		}
	}
	// YOUTUBE VIDEOS
	if (class_exists( 'Futusign_Youtube' )) {
		$args = array(
			'post_type' => 'futusign_yt_video',
			'posts_per_page' => -1,
			'tax_query' => array(
				array(
					'taxonomy' => 'futusign_playlist',
					'field'    => 'id',
					'terms'    => $subscribed_playlist_ids,
				),
			),
		);
		$loop = new WP_Query( $args );
		while ( $loop->have_posts() ) {
			$loop->the_post();
			$priority = get_field('priority');
			$youtube_videos[] = array(
				'id' => get_the_ID(),
				'title' => get_the_title(),
				'url' => get_field('url'),
				'suggestedQuality' => get_field('suggested_quality'),
				'priority' => $priority ? intval($priority) : 1
			);
		}
		wp_reset_query();
		// YOUTUBE VIDEOS OVERRIDE
		if (class_exists( 'Futusign_Override' )) {
			$args = array(
				'post_type' => 'futusign_yt_video',
				'posts_per_page' => -1,
				'tax_query' => array(
					array(
						'taxonomy' => 'futusign_override',
						'field'    => 'id',
						'terms'    => $subscribed_override_ids,
					),
				),
			);
			$loop = new WP_Query( $args );
			while ( $loop->have_posts() ) {
				$loop->the_post();
				$priority = get_field('priority');
				$youtube_videos_override[] = array(
					'id' => get_the_ID(),
					'title' => get_the_title(),
					'url' => get_field('url'),
					'suggestedQuality' => get_field('suggested_quality'),
					'priority' => $priority ? intval($priority) : 1
				);
			}
		  wp_reset_query();
		}
	}
	// SLIDE DECKS
	$args = array(
		'post_type' => 'futusign_slide_deck',
		'posts_per_page' => -1,
		'tax_query' => array(
			array(
				'taxonomy' => 'futusign_playlist',
				'field'    => 'id',
				'terms'    => $subscribed_playlist_ids,
			),
		),
	);
	$loop = new WP_Query( $args );
	while ( $loop->have_posts() ) {
		$loop->the_post();
		$priority = get_field('priority');
		$slide_decks[] = array(
			'id' => get_the_ID(),
			'title' => get_the_title(),
			'file' => get_field('file'),
			'slideDuration' => intval(get_field('slide_duration')),
			'priority' => $priority ? intval($priority) : 1
		);
	}
	wp_reset_query();
	// SLIDE DECKS OVERRIDE
	if (class_exists( 'Futusign_Override' )) {
		$args = array(
			'post_type' => 'futusign_slide_deck',
			'posts_per_page' => -1,
			'tax_query' => array(
				array(
					'taxonomy' => 'futusign_override',
					'field'    => 'id',
					'terms'    => $subscribed_override_ids,
				),
			),
		);
		$loop = new WP_Query( $args );
		while ( $loop->have_posts() ) {
			$loop->the_post();
			$priority = get_field('priority');
			$slide_decks_override[] = array(
				'id' => get_the_ID(),
				'title' => get_the_title(),
				'file' => get_field('file'),
				'slideDuration' => intval(get_field('slide_duration')),
				'priority' => $priority ? intval($priority) : 1
			);
		}
		wp_reset_query();
	}
	// LAYERS
	if (class_exists( 'Futusign_Layer' )) {
		$args = array(
			'post_type' => 'futusign_layer',
			'posts_per_page' => -1,
			'tax_query' => array(
				array(
					'taxonomy' => 'futusign_playlist',
					'field'    => 'id',
					'terms'    => $subscribed_playlist_ids,
				),
			),
		);
		$loop = new WP_Query( $args );
		while ( $loop->have_posts() ) {
			$loop->the_post();
			$layers[] = array(
				'id' => get_the_ID(),
				'title' => get_the_title(),
				'url' => get_field('url'),
			);
		}
		wp_reset_query();
	}
	// OVERLAY
	$overlayId = $screen['overlay'];
	if ($overlayId !== null) {
		$args = array(
			'post_type' => 'futusign_overlay',
			'posts_per_page' => -1,
			'page_id' => $overlayId
		);
		$loop = new WP_Query( $args );
		while ( $loop->have_posts() ) {
			$loop->the_post();
			$id = get_the_ID();
			$upper_left = get_field('upper_left');
			$upper_middle = get_field('upper_middle');
			$upper_right = get_field('upper_right');
			$middle_left = get_field('middle_left');
			$middle_right = get_field('middle_right');
			$lower_left = get_field('lower_left');
			$lower_middle = get_field('lower_middle');
			$lower_right = get_field('lower_right');
			$overlay = array(
				'id' => $id,
				'upperLeft' => $upper_left ? $upper_left->ID : null,
				'upperMiddle' => $upper_middle ? $upper_middle->ID : null,
				'upperRight' => $upper_right ? $upper_right->ID : null,
				'middleLeft' => $middle_left ? $upper_left->ID : null,
				'middleRight' => $middle_right ? $upper_right->ID : null,
				'lowerLeft' => $lower_left ? $lower_left->ID : null,
				'lowerMiddle' => $lower_left ? $lower_middle->ID : null,
				'lowerRight' => $lower_left ? $lower_right->ID : null,
			);
		}
		wp_reset_query();
	}
	// WIDGETS
	if ($overlayId !== null) {
		$args = array(
			'post_type' => 'futusign_ov_widget',
			'posts_per_page' => -1,
		);
		$loop = new WP_Query( $args );
		while ( $loop->have_posts() ) {
			$loop->the_post();
			$ov_widgets[] = array(
				'id' => get_the_ID(),
				'url' => get_permalink(),
			);
		}
		wp_reset_query();
	}
	//  MONITOR
	if (class_exists( 'Futusign_Monitor' )) {
		$monitor = array();
		$keys = [ 'api_key', 'auth_domain', 'database_url', 'project_id', 'storage_bucket', 'messaging_sender_id', 'email', 'password' ];
		$outputKeys = [ 'apiKey', 'authDomain', 'databaseURL', 'projectId', 'storageBucket', 'messagingSenderId', 'email', 'password' ];
		$options = get_option('futusign_monitor_option_name');
		for ($i = 0; $i < sizeOf($keys); $i++) {
			$key = $keys[$i];
			$outputKey = $outputKeys[$i];
			$monitor[$outputKey] = $options[$key];
		}
	}
	header( 'Content-Type: application/json' );
	header( 'Cache-Control: no-cache, no-store, must-revalidate');
	echo '{';
	echo '"screen": ';
	echo json_encode( $screen );
	echo ', "images": ';
	echo json_encode( $images );
	echo ', "imagesOverride": ';
	echo json_encode( $images_override );
	echo ', "mediaDecks": ';
	echo json_encode( $media_decks );
	echo ', "mediaDecksOverride": ';
	echo json_encode( $media_decks_override );
	echo ', "webs": ';
	echo json_encode( $webs );
	echo ', "websOverride": ';
	echo json_encode( $webs_override );
	echo ', "youtubeVideos": ';
	echo json_encode( $youtube_videos );
	echo ', "youtubeVideosOverride": ';
	echo json_encode( $youtube_videos_override );
	echo ', "slideDecks": ';
	echo json_encode( $slide_decks );
	echo ', "slideDecksOverride": ';
	echo json_encode( $slide_decks_override );
	echo ', "layers": ';
	echo json_encode( $layers );
	echo ', "overlay": ';
	echo json_encode( $overlay );
	echo ', "ovWidgets": ';
	echo json_encode( $ov_widgets );
	echo ', "monitor": ';
	echo json_encode( $monitor );
	echo '}';
}
// DEGUG 4
if ($futusign_debug === '4') {
	echo 4;
	die();
}
