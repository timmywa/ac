<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

$status_url = 'https://ais-sa2.cdnstream1.com/status-json.xsl';
$target_mount = '1976_128';

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL            => $status_url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 3,
    CURLOPT_CONNECTTIMEOUT => 2,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$data = [
    'current' => [
        'artist' => "America's Country",
        'title'  => "Three DECADES of Country",
        'full'   => "America's Country - Three DECADES of Country"
    ],
    'history' => []
];

if ($http_code === 200 && !empty($response)) {
    $json = json_decode($response, true);
    
    if (isset($json['icestats']['source'])) {
        $sources = $json['icestats']['source'];
        if (isset($sources['mount'])) {
            $sources = [$sources];
        }
        
        foreach ($sources as $source) {
            if (isset($source['mount']) && strpos($source['mount'], $target_mount) !== false) {
                // Current track
                $raw_title = $source['title'] ?? '';
                if (!empty($raw_title)) {
                    if (strpos($raw_title, 'ADWTAG_') === false) {
                        $data['current']['full'] = trim($raw_title);
                        if (strpos($raw_title, ' - ') !== false) {
                            list($artist, $title) = explode(' - ', $raw_title, 2);
                            $data['current']['artist'] = trim($artist);
                            $data['current']['title']  = trim($title);
                        }
                    }
                }
                
                // Recent history (if provided by Icecast server array)
                if (!empty($source['history']) && is_array($source['history'])) {
                    foreach (array_slice($source['history'], 0, 3) as $hist_item) {
                        if (!empty($hist_item['title'])) {
                            $data['history'][] = trim($hist_item['title']);
                        }
                    }
                }
                break;
            }
        }
    }
}

// Fallback history if server doesn't provide history array natively
if (empty($data['history'])) {
    $data['history'] = [
        "Recent Track 3",
        "Recent Track 2",
        "Recent Track 1"
    ];
}

echo json_encode($data);
exit;
