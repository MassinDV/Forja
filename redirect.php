<?php

// Check if the 'id' parameter is provided in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("HTTP/1.1 400 Bad Request");
    echo "Error: 'id' parameter is required.";
    exit;
}

// Get the ID from the query parameter
$id = htmlspecialchars($_GET['id']);

// Construct the token extraction URL
$tokenUrl = "https://vod.forja.ma/snrt?url=https://vod.forja.ma//vod/SNRT/$id/playlist.m3u8";

// Function to extract the 'verify' token
function extractToken($url) {
    // Use CURL to fetch the content of the URL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($ch);
    curl_close($ch);

    // Search for the 'verify' token in the response body
    if (preg_match('/verify=([a-zA-Z0-9%=_\-&]+)/', $response, $matches)) {
        return $matches[1]; // Return the token (encoded form)
    }

    return null; // Return null if the token is not found
}

// Extract the token
$token = extractToken($tokenUrl);

if ($token === null) {
    header("HTTP/1.1 500 Internal Server Error");
    echo "Error: Unable to extract the verify token for ID $id.";
    exit;
}

// Construct the final redirect URL with the token
$finalUrl = "https://vod.forja.ma//vod/SNRT/$id/playlist.m3u8?verify=$token";

// Redirect to the final URL
header("Location: $finalUrl");
exit;

?>
