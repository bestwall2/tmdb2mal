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
  $yearAndMonth = $year . '-' . $month;

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

  // Fetch anime data from Jikan
  $jikan_url = "https://api.jikan.moe/v4/anime?q=" . urlencode($title) . "&start_date=$yearAndMonth&order_by=start_date";
  $jikan_response = file_get_contents($jikan_url);

  if ($jikan_response === FALSE) {
    die('Error: Failed to fetch anime data.');
  }

  $x = json_decode($jikan_response);
  if (!isset($x->data[0]->mal_id)) {
    die('Error: No matching anime found.');
  }

  echo $x->data[0]->mal_id;
?>
