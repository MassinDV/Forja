<?php

// Target URL to scrape
$url = "https://forja.ma/category/films";

// Fetch the content of the URL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$response = curl_exec($ch);
curl_close($ch);

// Check if fetching was successful
if (!$response) {
    echo "Error: Unable to fetch content from $url\n";
    exit(1);
}

// Parse the response to extract required data
$pattern = '/slug:\s*"(.+?)".*?vertical_image:\s*"(https:\\\\u002F\\\\u002F.+?)".*?this id:\s*(\\\\u002F\d+)/s';
preg_match_all($pattern, $response, $matches, PREG_SET_ORDER);

// Check if any matches were found
if (empty($matches)) {
    echo "Error: No data found in the target URL.\n";
    exit(1);
}

// Prepare the playlist content
$playlist = "#EXTM3U\n\n";
foreach ($matches as $match) {
    $slug = $match[1];
    $vertical_image = str_replace("\\u002F", "/", $match[2]);
    $id = str_replace("\\u002F", "", $match[3]);

    $playlist .= "#EXTINF:-1 group-title=\"Moroccan Movies\" tvg-id=\"\" tvg-logo=\"$vertical_image\" tvg-type=\"Movies\",$slug\n";
    $playlist .= "http://forja.freesite.online/redirect.php?id=$id?.mp4\n\n";
}

// Save the playlist to a file
$file = "films.m3u";
file_put_contents($file, $playlist);

echo "Playlist generated and saved to $file.\n";
?>
