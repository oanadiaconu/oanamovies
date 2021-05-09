<?php

require __DIR__ . '/wp-load.php';
require __DIR__ . '/abstractrequest.php';
require __DIR__ . '/movies-api-settings.php';

const CATEGORY_MAPPING = [
	'28' => 3,
	'12' => 2,
	'16' => 4,
	'35' => 5,
	'80' => 6,
	'99' => 9,
	'18' => 10,
	'10751' => 11,
	'14' => 12,
	'36' => 13,
	'27' => 14,
	'10402' => 15,
	'9648' => 16,
	'10749' => 17,
	'878' => 18,
	'10770' => 19,
	'53' => 20,
	'10752' => 21,
	'37' => 22,
];

$tmdbHeaders = [
	"Content-type: application/json",
	"Authorization: Bearer " . TMDB_BEARER_KEY,
];

$result = makeRequest(TMDB_BASE_URL . '/movie/popular?page=' . rand(1,500), 'GET', $tmdbHeaders); 
$moviesResponse = json_decode($result, true);

foreach ($moviesResponse['results'] as $movie) {
	if (!get_page_by_title($movie['title'], OBJECT, 'post')) {
		$omdbResult = makeRequest(OMDB_BASE_URL . 't=' . urlencode($movie['title']) . '&plot=full', 'GET', ["Content-type: application/json"]); 
		$omdbMovieDetails = json_decode($omdbResult, true);
		$reviewsResult = makeRequest(TMDB_BASE_URL . '/movie/' . $movie['id'] . '/reviews', 'GET', $tmdbHeaders);
		$reviews = json_decode($reviewsResult, true);
		$networksResult = makeRequest(TMDB_BASE_URL . '/movie/' . $movie['id'] . '/watch/providers', 'GET', $tmdbHeaders);
		$networks = getMovieAvailability(json_decode($networksResult, true));

		$post_data = array(
			'post_author' => 1,
		    'post_title' => $movie['title'],
		    'post_content' => constructDescription($movie['original_title'], $movie['overview'], $movie['vote_average'], $omdbMovieDetails, $reviews['results'][0], $networks),
		    'post_type' => 'post',
		    'post_status' => 'publish',
		    'post_excerpt' => $movie['overview'],
		    'post_category' => getCategories($movie['genre_ids']),
		);
		
		$imageInfo = getMovieImage(TMDB_IMAGE_BASE_URL . $movie['backdrop_path']);

		$postId = wp_insert_post( $post_data, true );
		if (!is_wp_error($insert_id)) {
			attachImageToPost($imageInfo, $postId);
		}
	}
}

function getCategories($movieGenres)
{
	$categories = [];
	foreach ($movieGenres as $genre) {
		$categories[] = CATEGORY_MAPPING[$genre];
	}

	return $categories;
}

function constructDescription ($originalTitle, $overview, $rating, $movieDetails, $review, $networks)
{
	$description = '<!-- wp:paragraph -->
		<p><b class="grey-title">Original Title:</b> ' . $originalTitle . '</p>
		<!-- /wp:paragraph -->';

	$description .= '<!-- wp:paragraph -->
		<p><b class="grey-title">Rating:</b> ' . $rating . '</p>
		<!-- /wp:paragraph -->';

	$description .= '<!-- wp:paragraph -->
		<p><b class="grey-title">Runtime:</b> ' . $movieDetails['Runtime'] . '</p>
		<!-- /wp:paragraph -->';

	$description .= '<!-- wp:paragraph -->
		<p><b class="grey-title">Main actors:</b> ' . $movieDetails['Actors'] . '</p>
		<!-- /wp:paragraph -->';

	$description .= '<!-- wp:paragraph -->
		<p><b class="grey-title">Director:</b> ' . $movieDetails['Director'] . '</p>
		<!-- /wp:paragraph -->';

	$description .= '<!-- wp:paragraph -->
		<p><b class="grey-title">Writer:</b> ' . $movieDetails['Writer'] . '</p>
		<!-- /wp:paragraph -->';	

	$description .= '<!-- wp:paragraph -->
		<p>' . $overview . '</p>
		<!-- /wp:paragraph -->';

	$description .= '<!-- wp:paragraph -->
		<p>' . $movieDetails['Plot'] . '</p>
		<!-- /wp:paragraph -->';

	if (isset($review['content'])) {
		$description .= '<!-- wp:paragraph -->
			<p><b class="grey-title">My take on the movie:</b> ' . $review['content'] . '</p>
			<!-- /wp:paragraph -->';
	}

	if (count($networks)) {
		$description .= '<!-- wp:paragraph -->
			<p><b class="grey-title">Available On:</b> </p>
			<!-- /wp:paragraph -->

			<!-- wp:columns -->
			<div class="wp-block-columns">
			<!-- wp:column {"width":"100%"} -->
			<div class="wp-block-column" style="flex-basis:100%"><!-- wp:group -->
			<div class="wp-block-group"><div class="wp-block-group__inner-container">';

			foreach ($networks as $network) {
				$description .= '<!-- wp:image {"width":100,"height":100,"sizeSlug":"large","className":"is-inline"} -->
					<figure class="wp-block-image size-large is-resized is-inline"><img src="' . $network['url'] . '" alt="" width="100" height="100"/><figcaption><strong>' . $network['name'] . '</strong></figcaption></figure>
					<!-- /wp:image -->';
			}

			$description .= '<!-- wp:paragraph -->
				<!-- /wp:paragraph -->
				</div></div>
				<!-- /wp:group -->
				</div>
				<!-- /wp:column -->
				</div>
				<!-- /wp:columns -->';
	}

	return $description;
}


function getMovieImage($imageURL)
{
	$image_url = $imageURL;
	$image = pathinfo($image_url);//Extracting information into array.
	$image_name = $image['basename'];
	$upload_dir = wp_upload_dir();
	$image_data = file_get_contents($image_url);
	$unique_file_name = wp_unique_filename($upload_dir['path'], $image_name);
	$filename = basename($unique_file_name);

	return [
		'image_data' => $image_data,
		'filename' => $filename,
		'image' => $image,
		'upload_dir' => $upload_dir
	];
}

function attachImageToPost($imageInfo, $postId)
{
	if ($imageInfo['image'] != '') {
        // Check folder permission and define file location
        if (wp_mkdir_p($imageInfo['upload_dir']['path'])) {
            $file = $imageInfo['upload_dir']['path'] . '/' . $imageInfo['filename'];
        } else {
            $file = $imageInfo['upload_dir']['basedir'] . '/' . $imageInfo['filename'];
        }
        // Create the image  file on the server
        file_put_contents($file, $imageInfo['image_data']);
        // Check image file type
        $wp_filetype = wp_check_filetype($imageInfo['filename'], null);
        // Set attachment data
        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => sanitize_file_name($imageInfo['filename']),
            'post_content' => '',
            'post_status' => 'inherit',
        );
        // Create the attachment
        $attach_id = wp_insert_attachment($attachment, $file, $postId);
        // Include image.php
        require_once ABSPATH . 'wp-admin/includes/image.php';
        // Define attachment metadata
        $attach_data = wp_generate_attachment_metadata($attach_id, $file);
        // Assign metadata to attachment
        wp_update_attachment_metadata($attach_id, $attach_data);
        // And finally assign featured image to post
        $thumbnail = set_post_thumbnail($postId, $attach_id);
    }
}

function getMovieAvailability($networks)
{
	$returnNetworks = [];
	$countryNetworks = [];
	$availableNetworks = [];
	if (isset($networks['results']['US'])) {
		$countryNetworks = $networks['results']['US'];
	} elseif (isset($networks['results']['GB'])) {
		$countryNetworks = $networks['results']['GB'];
	} elseif (isset($networks['results']['CA'])) {
		$countryNetworks = $networks['results']['CA'];
	}

	if (isset($countryNetworks['flatrate'])) {
		$availableNetworks = $countryNetworks['flatrate'];
	} elseif (isset($countryNetworks['buy'])) {
		$availableNetworks = $countryNetworks['buy'];
	} elseif (isset($countryNetworks['rent'])) {
		$availableNetworks = $countryNetworks['rent'];
	}

	foreach($availableNetworks as $network) {
		$returnNetworks[] = [
			'url' => 'https://image.tmdb.org/t/p/original' . $network['logo_path'],
			'name' => $network['provider_name'],
		];
	}

	return $returnNetworks;
}

?>