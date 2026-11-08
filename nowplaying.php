<?php
// Pre-fetch stream data on server-load for instant rendering
$history_file = __DIR__ . '/song_history.json';
$initial_current = "America's Country - Three DECADES of Country";
$initial_history = [
    "George Strait - Troubadour",
    "Alan Jackson - Chattahoochee",
    "Tim McGraw - Live Like You Were Dying"
];

if (file_exists($history_file)) {
    $saved_data = json_decode(file_get_contents($history_file), true);
    if (!empty($saved_data) && is_array($saved_data)) {
        $display_saved = array_map(function($item) {
            return is_array($item) ? ($item['title'] ?? '') : $item;
        }, array_slice($saved_data, 0, 3));
        
        $display_saved = array_filter($display_saved);
        if (!empty($display_saved)) {
            $initial_history = array_values($display_saved);
            $initial_current = $initial_history[0];
        }
    }
}
?>
