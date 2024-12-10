<?php

// Define the episodes with their URLs
$episodes = [
    'Episode 1' => 'https://vod.forja.ma/snrt?url=https://vod.forja.ma//vod/SNRT/98888/playlist.m3u8',
    'Episode 2' => 'https://vod.forja.ma/snrt?url=https://vod.forja.ma//vod/SNRT/98889/playlist.m3u8',
    'Episode 3' => 'https://vod.forja.ma/snrt?url=https://vod.forja.ma//vod/SNRT/98890/playlist.m3u8'
];

// Initialize the playlist content
$playlistContent = "";

// Iterate through each episode URL
foreach ($episodes as $episode => $url) {
    // Parse the query string to extract the 'verify' token
    $queryString = parse_url($url, PHP_URL_QUERY);
    parse_str($queryString, $queryParams);

    if (isset($queryParams['url'])) {
        // Extract the embedded URL with the token
        $embeddedUrl = $queryParams['url'];
        $verifyToken = null;

        if (strpos($embeddedUrl, '?') !== false) {
            // Extract the 'verify' token from the embedded URL
            parse_str(parse_url($embeddedUrl, PHP_URL_QUERY), $embeddedParams);
            $verifyToken = $embeddedParams['verify'] ?? null;
        }

        if ($verifyToken) {
            // Construct the final URL with the 'verify' token
            $finalUrl = $embeddedUrl . "?verify=" . $verifyToken;
            // Append to the playlist content
            $playlistContent .= "#EXTINF:-1, $episode\n$finalUrl\n";
        }
    }
}

// Save the content to a .m3u file
file_put_contents('exported_playlists.m3u', $playlistContent);

echo "HLS file 'exported_playlists.m3u' has been created successfully.";

?>
