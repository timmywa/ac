<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

$stream_url = 'https://ais-sa2.cdnstream1.com/1976_128.mp3';
$history_file = __DIR__ . '/song_history.json';

$current_full = "America's Country - Three DECADES of Country";
$current_artist = "America's Country";
$current_title = "Three DECADES of Country";

// Connect to the stream with ICY metadata requested
$headers = [
    "Icy-MetaData: 1",
    "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)"
];

$opts = [
    'http' => [
        'method'  => 'GET',
        'header'  => implode("\r\n", $headers),
        'timeout' => 3
    ]
];

$context = stream_context_create($opts);
$fp = @fopen($stream_url, 'r', false, $context);

if ($fp) {
    $metaInterval = 0;
    $metaDataHeaders = stream_get_meta_data($fp);
    $wrapperData = $metaDataHeaders['wrapper_data'] ?? [];

    foreach ($wrapperData as $header) {
        if (strncasecmp($header, 'icy-metaint:', 12) === 0) {
            $metaInterval = (int)trim(substr($header, 12));
            break;
        }
    }

    if ($metaInterval > 0) {
        fread($fp, $metaInterval);
        $lenByte = ord(fread($fp, 1));
        if ($lenByte > 0) {
            $rawMeta = fread($fp, $lenByte * 16);

            if (preg_match("/StreamTitle='(.*?)';/i", $rawMeta, $matches)) {
                $rawTitle = trim($matches[1]);
                
                if (!empty($rawTitle) && strpos($rawTitle, 'ADWTAG_') === false && strpos($rawTitle, 'THIS STATION WILL CONTINUE') === false) {
                    $current_full = $rawTitle;
                    if (strpos($rawTitle, ' - ') !== false) {
                        list($art, $tit) = explode(' - ', $rawTitle, 2);
                        $current_artist = trim($art);
                        $current_title = trim($tit);
                    } else {
                        $current_title = $rawTitle;
                    }
                }
            }
        }
    }
    fclose($fp);
}

// Load existing history
$history = [];
if (file_exists($history_file)) {
    $saved_data = json_decode(file_get_contents($history_file), true);
    if (is_array($saved_data)) {
        $history = $saved_data;
    }
}

// Clean out entries older than 12 hours
$twelve_hours_ago = time() - 43200;
$history = array_filter($history, function($item) use ($twelve_hours_ago) {
    // Support both raw string format or object format gracefully
    if (is_string($item)) return true;
    return isset($item['time']) && $item['time'] > $twelve_hours_ago;
});
$history = array_values($history);

// Normalize history items to strings for easy comparison
$history_titles = array_map(function($item) {
    return is_array($item) ? $item['title'] : $item;
}, $history);

// If we have a valid song and it's different from the current top of history
$top_song = !empty($history_titles) ? $history_titles[0] : '';
if ($current_full !== "America's Country - Three DECADES of Country" && $top_song !== $current_full) {
    // Add new entry with timestamp to the front
    array_unshift($history, [
        'title' => $current_full,
        'time'  => time()
    ]);
    
    // Keep only unique entries and slice to max 3
    $seen = [];
    $unique_history = [];
    foreach ($history as $h) {
        $t = is_array($h) ? $h['title'] : $h;
        if (!isset($seen[$t])) {
            $seen[$t] = true;
            $unique_history[] = is_array($h) ? $h : ['title' => $h, 'time' => time()];
        }
    }
    
    $history = array_slice($unique_history, 0, 3);
    file_put_contents($history_file, json_encode($history));
}

// Prepare display array (last 3 titles)
$display_history = array_map(function($item) {
    return is_array($item) ? $item['title'] : $item;
}, array_slice($history, 0, 3));

// Fallback if empty
if (empty($display_history)) {
    $display_history = [
        "George Strait - Troubadour",
        "Alan Jackson - Chattahoochee",
        "Tim McGraw - Live Like You Were Dying"
    ];
}

echo json_encode([
    'current' => [
        'artist' => $current_artist,
        'title'  => $current_title,
        'full'   => $current_full
    ],
    'history' => $display_history
]);
exit;
