<?php

// Define the input URLs and corresponding episode names
$episodes = [
    'Episode 1' => 'https://vod.forja.ma/snrt?url=https://vod.forja.ma//vod/SNRT/98888/playlist.m3u8',
    'Episode 2' => 'https://vod.forja.ma/snrt?url=https://vod.forja.ma//vod/SNRT/98889/playlist.m3u8',
    'Episode 3' => 'https://vod.forja.ma/snrt?url=https://vod.forja.ma//vod/SNRT/98890/playlist.m3u8',
];

// Initialize the output content for the playlist
$playlistContent = "#EXTM3U\n\n";

// Process each episode
foreach ($episodes as $episode => $url) {
    // Use CURL to fetch the content of the URL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($ch);
    curl_close($ch);

    // Check if the response contains the 'verify' token
    if (preg_match('/verify=([a-zA-Z0-9%]+)/', $response, $matches)) {
        // Decode the 'verify' token
        $verifyToken = urldecode($matches[1]);

        // Extract the base URL from the original URL
        $parsedUrl = parse_url($url);
        parse_str($parsedUrl['query'], $queryParams);
        $baseUrl = $queryParams['url'];

        // Append the unique 'verify' token to the base URL
        $finalUrl = $baseUrl . '?verify=' . $verifyToken;

        // Add the entry to the playlist
        $playlistContent .= "#EXTINF:-1, $episode\n$finalUrl\n\n";
    } else {
        echo "Failed to extract the verify token for $episode\n";
    }
}

// Save the playlist to a file
file_put_contents('playlists.m3u', $playlistContent);

echo "Playlist has been generated and saved as playlists.m3u\n";

?>
