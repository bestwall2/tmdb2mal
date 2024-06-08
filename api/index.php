<?php
$api_key = "5ec279387e9aa9488ef4d00b22acc451"; // Please provide your own TMDb API key.

// Check if the required parameters are set
if (!isset($_GET['id']) || !isset($_GET['s'])) {
    die('Error: Missing required parameters.');
}

$id = $_GET['id'];
$season = $_GET['s'];

// Fetch season data from TMDb
$season_url = "https://api.themoviedb.org/3/tv/$id/season/$season?api_key=$api_key";
$season_response = file_get_contents($season_url);

if ($season_response === FALSE) {
    die('Error: Failed to fetch season data.');
}

$m = json_decode($season_response);
if (!isset($m->air_date)) {
    die('Error: Invalid season data received.');
}

$date = $m->air_date;
$month = explode("-", $date)[1];
$year = explode("-", $date)[0];
$yearAndMonth = $year . '-' . $month . '-01';  // Adjusted to Y-m-01 format

// Fetch TV show data from TMDb
$show_url = "https://api.themoviedb.org/3/tv/$id?api_key=$api_key";
$show_response = file_get_contents($show_url);

if ($show_response === FALSE) {
    die('Error: Failed to fetch show data.');
}

$y = json_decode($show_response);
if (!isset($y->name)) {
    die('Error: Invalid show data received.');
}

$title = $y->name;

// AniList GraphQL query
$query = '
{
  Media(search: "'. $title .'", type: ANIME, startDate_greater: "'. $yearAndMonth .'", startDate_lesser: "'. $yearAndMonth .'") {
    id
    title {
      romaji
    }
    startDate {
      year
      month
      day
    }
  }
}
';

// Make request to AniList API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://graphql.anilist.co");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array('query' => $query)));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

$response = curl_exec($ch);
curl_close($ch);

if ($response === FALSE) {
    die('Error: Failed to fetch anime data from AniList API.');
}

$response_data = json_decode($response, true);

if (!isset($response_data['data']['Media']['id'])) {
    die('Error: No matching anime found or invalid response from AniList API.');
}

$mal_id = $response_data['data']['Media']['id'];

echo $mal_id;
?>