<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// CDNStream metadata endpoint for America's Country (Mount 1976_128)
$stream_metadata_url = 'https://metadata.cdnstream1.com/metadata?mount=1976_128'; 

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $stream_metadata_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 3); // 3-second timeout protects 1GB droplet from hanging
curl_setopt($ch, CURLOPT_USERAGENT, 'AmericasCountryNowPlaying/1.0');

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Default fallback text if feed drops momentarily
$data = [
    'artist' => 'America\'s Country',
    'title'  => 'Hot Country Hits',
    'full'   => 'America\'s Country - Hot Country Hits'
];

if ($http_code === 200 && !empty($response)) {
    $json = json_decode($response, true);
    
    // Parse CDNStream standard response fields
    if (!empty($json['artist']) && !empty($json['title'])) {
        $data['artist'] = trim($json['artist']);
        $data['title']  = trim($json['title']);
        $data['full']   = $data['artist'] . ' - ' . $data['title'];
    } elseif (!empty($json['song'])) {
        $data['full']   = trim($json['song']);
    }
}

echo json_encode($data);
exit;
