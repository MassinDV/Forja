<?php
// List of input URLs for episodes
$episodes = [
    "https://vod.forja.ma/snrt?url=https://vod.forja.ma//vod/SNRT/98888/playlist.m3u8",
    "https://vod.forja.ma/snrt?url=https://vod.forja.ma//vod/SNRT/98889/playlist.m3u8",
    "https://vod.forja.ma/snrt?url=https://vod.forja.ma//vod/SNRT/98890/playlist.m3u8"
];

// File to save the playlist
$outputFile = "exported_playlists.m3u";
file_put_contents($outputFile, ""); // Clear file contents if exists

// Function to extract the token and generate the updated URL
function updateUrl($inputUrl) {
    $queryString = parse_url($inputUrl, PHP_URL_QUERY);
    parse_str($queryString, $queryParams);

    if (isset($queryParams['url'])) {
        $embeddedUrl = $queryParams['url'];
        $verifyToken = null;

        if (strpos($embeddedUrl, '?') !== false) {
            parse_str(parse_url($embeddedUrl, PHP_URL_QUERY), $embeddedParams);
            $verifyToken = $embeddedParams['verify'] ?? null;
        }

        if ($verifyToken) {
            return $embeddedUrl . "?verify=" . $verifyToken;
        } else {
            return "No 'verify' token found in: " . $embeddedUrl;
        }
    } else {
        return "Invalid input URL or missing 'url' parameter.";
    }
}

// Iterate over episodes and save updated URLs to the file
foreach ($episodes as $episode) {
    $updatedUrl = updateUrl($episode);
    file_put_contents($outputFile, $updatedUrl . PHP_EOL, FILE_APPEND);
}
