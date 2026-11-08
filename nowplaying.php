<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

$stream_url = 'https://ais-sa2.cdnstream1.com/1976_128.mp3';
$history_file = __DIR__ . '/song_history.json';

$current_full = "America's Country - Three DECADES of Country";
$current_artist = "America's Country";
$current_title = "Three DECADES of Country";

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

// Extract just string titles for easy checking
$titles = array_map(function($item) {
    return is_array($item) ? ($item['title'] ?? '') : $item;
}, $history);

// If we have a valid live song and it is NOT the current top song in history, prepend it
if ($current_full !== "America's Country - Three DECADES of Country" && ($empty = empty($titles) || $titles[0] !== $current_full)) {
    // Remove if it exists further down in the history to prevent duplicates
    $history = array_values(array_filter($history, function($item) use ($current_full) {
        $t = is_array($item) ? ($item['title'] ?? '') : $item;
        return $t !== $current_full;
    }));

    // Prepend new track
    array_unshift($history, [
        'title' => $current_full,
        'time'  => time()
    ]);

    // Keep only the top 3
    $history = array_slice($history, 0, 3);
    
    // Save back to file
    file_put_contents($history_file, json_encode($history));
}

// Prepare display array
$display_history = array_map(function($item) {
    return is_array($item) ? ($item['title'] ?? '') : $item;
}, array_slice($history, 0, 3));

// Fallback if empty
if (empty($display_history) || empty($display_history[0])) {
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
