<?php

// Define the input URLs for the episodes
$episodes = [
    'Episode 1' => 'https://vod.forja.ma/snrt?url=https://vod.forja.ma//vod/SNRT/98888/playlist.m3u8',
    'Episode 2' => 'https://vod.forja.ma/snrt?url=https://vod.forja.ma//vod/SNRT/98889/playlist.m3u8',
];

// Initialize the output content for the playlist
$playlistContent = "#EXTM3U\n\n";

// Function to extract the verify token from the URL
function extractToken($url) {
    // Use CURL to fetch the content of the URL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($ch);
    curl_close($ch);

    // Search for the 'verify' token in the response body
    if (preg_match('/verify=([a-zA-Z0-9%=_\-&]+)/', $response, $matches)) {
        return $matches[1]; // Return the token without decoding
    }

    return null; // Return null if the token is not found
}

// Process each episode
foreach ($episodes as $episode => $url) {
    // Extract the token using the function
    $token = extractToken($url);

    if ($token !== null) {
        // Extract the base URL from the original URL (the part without the token)
        $parsedUrl = parse_url($url);
        parse_str($parsedUrl['query'], $queryParams);
        $baseUrl = $queryParams['url'];

        // Create the final URL with the token (keeping the token encoded)
        $finalUrl = $baseUrl . '?verify=' . $token;

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
