<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

$stream_url = 'https://ais-sa2.cdnstream1.com/1976_128.mp3';

// Default fallback
$data = [
    'artist' => "America's Country",
    'title'  => "Three DECADES of Country",
    'full'   => "America's Country - Three DECADES of Country"
];

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
        // Skip audio payload to reach the metadata block
        fread($fp, $metaInterval);

        $lenByte = ord(fread($fp, 1));
        if ($lenByte > 0) {
            $rawMeta = fread($fp, $lenByte * 16);

            if (preg_match("/StreamTitle='(.*?)';/i", $rawMeta, $matches)) {
                $rawTitle = trim($matches[1]);
                
                if (!empty($rawTitle)) {
                    // Filter out commercial ad tags
                    if (strpos($rawTitle, 'ADWTAG_') !== false || strpos($rawTitle, 'THIS STATION WILL CONTINUE') !== false) {
                        $data['artist'] = "America's Country";
                        $data['title']  = "Three DECADES of Country";
                        $data['full']   = "America's Country - Three DECADES of Country";
                    } 
                    // Standard Track Metadata (Artist - Title)
                    elseif (strpos($rawTitle, ' - ') !== false) {
                        list($artist, $title) = explode(' - ', $rawTitle, 2);
                        $data['artist'] = trim($artist);
                        $data['title']  = trim($title);
                        $data['full']   = $data['artist'] . ' - ' . $data['title'];
                    } 
                    // Catch-all string
                    else {
                        $data['full']  = $rawTitle;
                        $data['title'] = $rawTitle;
                    }
                }
            }
        }
    }
    fclose($fp);
}

echo json_encode($data);
exit;
