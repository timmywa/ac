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

    // Find the ICY metadata interval byte offset
    foreach ($wrapperData as $header) {
        if (strncasecmp($header, 'icy-metaint:', 12) === 0) {
            $metaInterval = (int)trim(substr($header, 12));
            break;
        }
    }

    if ($metaInterval > 0) {
        // Skip audio frames to reach the metadata block
        fread($fp, $metaInterval);

        $lenByte = ord(fread($fp, 1));
        if ($lenByte > 0) {
            $rawMeta = fread($fp, $lenByte * 16);

            if (preg_match("/StreamTitle='(.*?)';/i", $rawMeta, $matches)) {
                $rawTitle = trim($matches[1]);
                
                // Ignore ad tags or station ID breaks, use fallback or keep previous if needed
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

// Load history log
$history = [];
if (file_exists($history_file)) {
    $history_data = json_decode(file_get_contents($history_file), true);
    if (is_array($history_data)) {
        $history = $history_data;
    }
}

// If we got a valid song title and it's different from the last logged track, update history
if ($current_full !== "America's Country - Three DECADES of Country" && (empty($history) || $history[0] !== $current_full)) {
    array_unshift($history, $current_full);
    $history = array_slice(array_unique($history), 0, 3);
    file_put_contents($history_file, json_encode($history));
}

// Fallback history if log is empty
if (empty($history)) {
    $history = [
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
    'history' => $history
]);
exit;
