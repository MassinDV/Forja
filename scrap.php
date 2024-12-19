<?php

// URL to scrape data from
$url = "https://forja.ma/category/films";

// Function to fetch the webpage content
function fetchWebContent($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $content = curl_exec($ch);
    curl_close($ch);
    return $content;
}

// Function to extract required data from the webpage content
function scrapeFilms($html) {
    $films = [];
    
    // Match each film entry
    preg_match_all('/slug:\s*"([^"]+)"\s*vertical_image:\s*"([^"]+)"\s*this id:\s*\$u002F(\d+)/', $html, $matches, PREG_SET_ORDER);

    foreach ($matches as $match) {
        $slug = $match[1]; // Film slug
        $verticalImage = str_replace(['\u002F'], [''], $match[2]); // Fix image URL
        $id = $match[3]; // Film ID
        $films[] = [
            'slug' => $slug,
            'vertical_image' => $verticalImage,
            'id' => $id,
        ];
    }

    return $films;
}

// Function to generate M3U content
function generateM3U($films) {
    $m3uContent = "#EXTM3U\n";
    foreach ($films as $film) {
        $m3uContent .= "#EXTINF:-1 group-title=\"Morcccan Movies\" tvg-id=\"\" tvg-logo=\"{$film['vertical_image']}\" tvg-type=\"Movies\",{$film['slug']}\n";
        $m3uContent .= "http://forja.freesite.online/redirect.php?id={$film['id']}?.mp4\n";
    }
    return $m3uContent;
}

// Fetch the webpage content
$htmlContent = fetchWebContent($url);

// Scrape film data
$films = scrapeFilms($htmlContent);

// Generate M3U content
$m3uContent = generateM3U($films);

// Save to a file
$file = "films.m3u";
if (file_put_contents($file, $m3uContent)) {
    echo "M3U playlist saved to $file\n";
} else {
    echo "Failed to save M3U playlist.\n";
}
