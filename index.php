<?php
// Pre-fetch stream data on server-load for instant rendering (MUST be at the very top)
$status_json = @file_get_contents('https://ais-sa2.cdnstream1.com/status-json.xsl');
$initial_current = "America's Country - Three DECADES of Country";
$initial_history = ["Loading recent tracks..."];

if ($status_json) {
    $parsed = json_decode($status_json, true);
    if (isset($parsed['icestats']['source'])) {
        $sources = isset($parsed['icestats']['source']['mount']) ? [$parsed['icestats']['source']] : $parsed['icestats']['source'];
        foreach ($sources as $s) {
            if (strpos($s['mount'], '1976_128') !== false) {
                if (!empty($s['title']) && strpos($s['title'], 'ADWTAG_') === false) {
                    $initial_current = $s['title'];
                }
                if (!empty($s['history'])) {
                    $initial_history = array_column(array_slice($s['history'], 0, 3), 'title');
                }
                break;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>America's Country - Listen Live | Three Decades of Country</title>
    
    <link rel="preconnect" href="https://onlineradiobox.com" crossorigin>
    <link rel="dns-prefetch" href="https://ecdn.onlineradiobox.com">
    <link rel="dns-prefetch" href="https://cdn.onlineradiobox.com">
    <link rel="preconnect" href="https://ais-sa2.cdnstream1.com" crossorigin>

    <link rel="preload" href="css/main.css?v=1.3" as="style">
    <link rel="preload" href="images/Site%20Banner-option3_1_1.webp" as="image" fetchpriority="high">

    <meta name="description" content="Stream America's Country! Enjoy three decades of country music from the 90s to today. Listen live on any device with no registration required.">
    <meta name="author" content="SuiteSounds Entertainment, LLC">
    <link rel="canonical" href="https://americascountry.us/">
    <link rel="icon" href="images/favicon.png" type="image/x-icon">
    
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://americascountry.us/">
    <meta property="og:title" content="America's Country - Three Decades of Country">
    <meta property="og:description" content="Stream America's Country! Enjoy three decades of country from the 90s to today.">
    <meta property="og:image" content="https://americascountry.us/images/logo-5001.png">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="stylesheet" href="css/main.css?v=1.3">
    <link rel="manifest" href="/site.webmanifest">

    <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "RadioStation",
      "@id": "https://americascountry.us/#station",
      "name": "America's Country",
      "url": "https://americascountry.us/",
      "logo": "https://americascountry.us/images/logo-5001.png",
      "image": "https://americascountry.us/images/Site%20Banner-option3_1_1.webp",
      "description": "Stream America's Country! Enjoy three decades of country music from the 90s to today.",
      "slogan": "Three DECADES of Country!",
      "email": "help@americascountry.us",
      "telephone": "+1-801-788-4916",
      "parentOrganization": {
        "@type": "Organization",
        "name": "SuiteSounds Entertainment, LLC"
      },
      "sameAs": [
        "https://www.facebook.com/americascountry",
        "https://x.com/americascountry",
        "https://instagram.com/americascountry",
        "https://www.threads.net/@americascountry",
        "https://bsky.app/profile/americascountry.us"
      ]
    },
    {
      "@type": "RadioBroadcastService",
      "@id": "https://americascountry.us/#service",
      "name": "America's Country Live Stream",
      "broadcaster": {
        "@id": "https://americascountry.us/#station"
      }
    }
  ]
}
    </script>
</head>

<body>
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
    
    <div class="theme-switcher" id="theme-switcher" onclick="toggleTheme()" role="button" tabindex="0" aria-label="Toggle light or dark theme">
        <span class="theme-icon" id="theme-icon" aria-hidden="true">🌙</span>
        <span class="theme-switcher-text" id="theme-text">Dark</span>
    </div>

    <main class="container">
        <header>
            <a href="https://americascountry.us" aria-label="America's Country Homepage">
                <img src="images/Site%20Banner-option3_1_1.webp"
                    fetchpriority="high"
                    loading="eager"
                    alt="America's Country - Free Online Country Radio Station Banner"
                    width="800"
                    height="400">
            </a>
            <h1><span class="tagline">Three Decades of Country!</span></h1>
        </header>

     <section id="tunein">
            <h2>🎵 Listen Now!</h2>
            <p>Click the <strong>play</strong> button in the player below to start listening to America's Country!</p>
            
        <!-- Live Stream Player -->
        <div class="ac-radio-player-card">
            <div class="ac-player-header">
                <span class="ac-live-badge">LIVE</span>
                <span class="ac-station-slogan">Three DECADES of Country</span>
            </div>

            <div class="ac-now-playing-box">
                <span class="ac-label">NOW PLAYING</span>
                <div id="now-playing-track" class="ac-track-title"><?php echo htmlspecialchars($initial_current); ?></div>
            </div>

            <audio id="ac-audio-stream" controls preload="none" style="width: 100%; margin: 15px 0;">
                <source src="https://ais-sa2.cdnstream1.com/1976_128.mp3" type="audio/mpeg">
                Your browser does not support the audio element.
            </audio>

            <div class="ac-history-section">
                <span class="ac-history-label">Recently Played</span>
                <ul id="ac-history-list" class="ac-history-list">
                    <?php foreach ($initial_history as $track): ?>
                        <li><?php echo htmlspecialchars($track); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <style>
        .ac-radio-player-card {
            background: #1a1512;
            color: #f4eee1;
            border: 1px solid #3d2f24;
            border-radius: 8px;
            padding: 20px;
            max-width: 450px;
            font-family: inherit;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            margin: 20px auto;
        }
        .ac-player-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
            border-bottom: 1px solid #3d2f24;
            padding-bottom: 10px;
        }
        .ac-live-badge {
            background: #b85d1d;
            color: #fff;
            font-size: 11px;
            font-weight: bold;
            padding: 3px 8px;
            border-radius: 4px;
            letter-spacing: 1px;
        }
        .ac-station-slogan {
            font-size: 13px;
            color: #c8b6a6;
            font-style: italic;
        }
        .ac-now-playing-box {
            background: #261f18;
            padding: 12px 15px;
            border-radius: 6px;
            border-left: 4px solid #b85d1d;
        }
        .ac-label {
            font-size: 10px;
            letter-spacing: 1.5px;
            color: #b85d1d;
            font-weight: bold;
            display: block;
            margin-bottom: 4px;
        }
        .ac-track-title {
            font-size: 16px;
            font-weight: 600;
            color: #ffffff;
        }
        .ac-history-section {
            margin-top: 15px;
            border-top: 1px solid #3d2f24;
            padding-top: 12px;
        }
        .ac-history-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #9e8b7c;
            display: block;
            margin-bottom: 8px;
        }
        .ac-history-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .ac-history-list li {
            font-size: 13px;
            color: #c8b6a6;
            padding: 4px 0;
            border-bottom: 1px dashed #2d231b;
        }
        .ac-history-list li:last-child {
            border-bottom: none;
        }
        </style>

        <script>
        function updatePlayerState() {
            fetch('/nowplaying.php?t=' + Date.now())
                .then(res => res.json())
                .then(data => {
                    if (data.current && data.current.full) {
                        document.getElementById('now-playing-track').textContent = data.current.full;
                    }
                    if (data.history && data.history.length > 0) {
                        const historyList = document.getElementById('ac-history-list');
                        historyList.innerHTML = '';
                        data.history.forEach(track => {
                            const li = document.createElement('li');
                            li.textContent = track;
                            historyList.appendChild(li);
                        });
                    }
                })
                .catch(err => console.warn('Player update check failed:', err));
        }

        // Poll every 15 seconds to keep track and history fresh
        setInterval(updatePlayerState, 15000);
        </script>
            
            <ul style="margin-top: 20px; margin-bottom: 20px;">
                <li>
                    <a href="https://ais-sa2.cdnstream1.com/1976_128.mp3" target="_blank" rel="noopener noreferrer">
                        ▶ PLAY 128 kb/s MP3 (desktop)
                    </a>
                </li>
                <li>
                    <a href="https://ais-sa2.cdnstream1.com/1976_64.aac" target="_blank" rel="noopener noreferrer">
                        ▶ PLAY 64 kb/s HE-AAC (mobile)
                    </a>
                </li>
            </ul>
        </section>
        
        <section id="services">
            <h2>🎧 Station Listing Services</h2>
            <div class="expandable-wrapper" id="services-wrapper">
                <ul class="twocolumns">
                    <li><a href="https://www.audacy.com/stations/americascountry" target="_blank" rel="noopener noreferrer">Audacy Radio</a></li>
                    <li><a href="https://www.radio.net/s/americascountry" target="_blank" rel="noopener noreferrer">Radio.net</a></li>
                    <li><a href="https://tunein.com/radio/Americas-Country-s126270/" target="_blank" rel="noopener noreferrer">TuneIn Radio</a></li>
                    <li><a href="https://streema.com/radios/Americas_Country" target="_blank" rel="noopener noreferrer">Streema</a></li>
                    <li><a href="https://openradio.app/station/america-s-country-m2gk4jpk" target="_blank" rel="noopener noreferrer">OpenRadio.app</a></li>
                    <li><a href="https://radio.garden/live/salt-lake-city-ut/america-s-country/" target="_blank" rel="noopener noreferrer">Radio Garden</a></li>
                    <li><a href="https://radoxo.com/usa/americas-country" target="_blank" rel="noopener noreferrer">Radoxo</a></li>
                    <li><a href="https://onlineradiobox.com/us/americascountry/" target="_blank" rel="noopener noreferrer">Online Radio Box</a></li>
                    <li><a href="https://www.radioplug.co.uk/channel/Americas-Country" target="_blank" rel="noopener noreferrer">RadioPlug</a></li>
                    <li><a href="https://www.jawaradio.com/radio/international-radio/america%E2%80%99s-country" target="_blank" rel="noopener noreferrer">JAWA Radio</a></li>
                    <li><a href="https://www.listenonlineradio.com/usa/americas-country" target="_blank" rel="noopener noreferrer">ListenOnline Radio</a></li>
                    <li><a href="https://www.liveradio.ie/stations/americas-country" target="_blank" rel="noopener noreferrer">LiveRadio IE</a></li>
                    <li><a href="https://netchoiceradio.com/station/americas-country" target="_blank" rel="noopener noreferrer">Net Choice Radio</a></li>
                    <li><a href="https://www.liveradio.uk/stations/americas-country" target="_blank" rel="noopener noreferrer">LiveRadio UK</a></li>
                    <li><a href="https://radiostationusa.fm/online/americas-country" target="_blank" rel="noopener noreferrer">RadioStation USA</a></li>
                    <li><a href="https://mytuner-radio.com/radio/americas-country-439609/" target="_blank" rel="noopener noreferrer">myTuner Radio</a></li>
                    <li><a href="https://appradiofm.com/radio/America's-Country-Salt-Lake-City-Utah--UT--USA-9iia2" target="_blank" rel="noopener noreferrer">RadioFM</a></li>
                    <li><a href="https://liveonlineradio.net/americas-country" target="_blank" rel="noopener noreferrer">Live Online Radio</a></li>
                    <li><a href="https://forwardmystream.com/station/americascountry" target="_blank" rel="noopener noreferrer">Forward My Stream</a></li>
                    <li><a href="https://radioonlinelive.com/usa/americas-country/" target="_blank" rel="noopener noreferrer">Radio Online Live</a></li>
                    <li><a href="https://us-radio.com/americas-country-r7148" target="_blank" rel="noopener noreferrer">US-Radio.com</a></li>
                    <li><a href="https://theonestopradio.com/radio/us-america-s-country" target="_blank" rel="noopener noreferrer">OneStop Radio</a></li>
                    <li><a href="https://liveradious.com/13239-americas-country.html" target="_blank" rel="noopener noreferrer">Live Radio US</a></li>
                    <li><a href="https://radiostationslive.com/station/americas-country-american-fork" target="_blank" rel="noopener noreferrer">Radio Stations Live</a></li>
                </ul>
            </div>
            <button onclick="toggleServices()" id="view-more-btn" class="show-more-btn">View All Services</button>
            <div class="note">
                <strong>NOTE:</strong> Some of these links may open a page that will start playing automatically.
            </div>
        </section>

        <section id="mobile">
            <h2>📱 Listen on your Mobile Device</h2>
            <p>Listen on your device with these apps. Search for <em>America's Country</em>.</p>
            <ul class="twocolumns">
                <li><a href="https://play.google.com/store/apps/details?id=com.radiocom&pcampaignid=web_share" target="_blank" rel="noopener noreferrer">Audacy for Android</a></li>
                <li><a href="https://play.google.com/store/apps/details?id=tunein.player&pcampaignid=web_share" target="_blank" rel="noopener noreferrer">TuneIn for Android</a></li>
                <li><a href="https://play.google.com/store/apps/details?id=com.finallevel.radiobox&pcampaignid=web_share" target="_blank" rel="noopener noreferrer">Online Radio Box for Android</a></li>
                <li><a href="https://apple.co/3x4SpXx" target="_blank" rel="noopener noreferrer">Audacy for iOS</a></li>
                <li><a href="https://apple.co/32V2zv7" target="_blank" rel="noopener noreferrer">TuneIn for iOS</a></li>
                <li><a href="https://apple.co/3deG0UG" target="_blank" rel="noopener noreferrer">XiiaLive for iOS</a></li>
                <li><a href="https://apple.co/3iGyogH" target="_blank" rel="noopener noreferrer">nRadio for iOS</a></li>
                <li><a href="https://apple.co/3hFTrOP" target="_blank" rel="noopener noreferrer">StreamS HiFi Radio for iOS</a> ($2.99 USD)</li>
            </ul>
        </section>

        <section id="smart">
            <h2>🗣️ Listen on your Smart Speaker</h2>
            <p>You can also listen to <em>America's Country</em> on your Echo, Nest, or HomePod.</p>
            <ul>
                <li>"Alexa, play America's Country on TuneIn Radio."</li>
                <li>"OK Google, play America's Country."</li>
                <li>"Hey Siri, play America's Country."</li>
            </ul>
        </section>

        <section id="social" itemscope itemtype="https://schema.org/RadioStation">
            <h2>📲 Follow Us on Social Media</h2>
            <p>Stay connected and get the latest updates from <em itemprop="name">America's Country</em>!</p>
            <div class="social-links">
                <a href="https://x.com/americascountry" target="_blank" rel="noopener noreferrer" title="Follow us on X (Twitter)" aria-label="Follow America's Country on X">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                </a>
                <a href="https://www.facebook.com/americascountry" target="_blank" rel="noopener noreferrer" title="Follow us on Facebook" aria-label="Follow America's Country on Facebook">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                </a>
                <a href="https://www.instagram.com/americascountry" target="_blank" rel="noopener noreferrer" title="Follow us on Instagram" aria-label="Follow America's Country on Instagram">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                </a>
                <a href="https://www.threads.net/@americascountry" target="_blank" rel="noopener noreferrer" title="Follow us on Threads" aria-label="Follow America's Country on Threads">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M12.186 24h-.007c-3.581-.024-6.334-1.205-8.184-3.509C2.35 18.44 1.5 15.586 1.472 12.01v-.017c.03-3.579.879-6.43 2.525-8.482C5.845 1.205 8.6.024 12.18 0h.014c2.746.02 5.043.725 6.826 2.098 1.677 1.292 2.858 3.13 3.509 5.467l-2.04.569c-1.104-3.96-3.898-5.984-8.304-6.015-2.91.022-5.11.936-6.54 2.717C4.307 6.504 3.616 8.914 3.589 12c.027 3.086.718 5.496 2.057 7.164 1.43 1.781 3.632 2.695 6.54 2.717 2.623-.02 4.358-.631 5.8-2.045 1.647-1.613 1.618-3.593 1.09-4.798-.31-.71-.873-1.3-1.634-1.75-.192 1.352-.622 2.446-1.284 3.272-.886 1.102-2.14 1.704-3.73 1.790-1.202.065-2.361-.218-3.259-.801-1.063-.689-1.685-1.74-1.752-2.964-.065-1.19.408-2.285 1.33-3.082.88-.76 2.119-1.207 3.583-1.291a13.853 13.853 0 0 1 3.02.142c-.126-.742-.375-1.332-.47-1.364 0 0-.648-.642-1.978-.69-1.118-.04-2.282.164-3.063.9-.843.794-1.222 1.974-1.222 1.974l-1.72-.453s.416-1.545 1.518-2.633c1.122-1.106 2.782-1.429 4.396-1.378 1.878.059 3.025.895 3.025.895.565.565 1.033 1.472 1.248 2.48a9.086 9.086 0 0 1 2.181 1.447c1.04.97 1.7 2.250 1.968 3.805.616 3.573-1.194 6.04-3.542 7.443-1.618.967-3.584 1.35-5.842 1.364zm3.314-14.7c-1.857-.03-3.583.123-4.844.372.494.372 1.375 1.193 1.375 3.908 0 .969-.179 1.827-.533 2.565a7.842 7.842 0 0 0 1.79-2.565c.533-1.07.794-2.59.794-4.51 0-1.193-.18-2.016-.582-2.77z"/>
                    </svg>
                </a>
                <a href="https://bsky.app/profile/americascountry.us" target="_blank" rel="noopener noreferrer" title="Follow us on Bluesky" aria-label="Follow America's Country on Bluesky">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M12 10.8c-1.087-2.114-4.046-6.053-6.798-7.995C2.566.944 1.561 1.266.902 2.105.139 3.107.139 5.498.139 6.789c0 1.24.375 2.533.936 3.714.561 1.181 1.354 2.293 2.209 3.293C5.225 15.849 7.724 17.849 12 18.4c4.276-.551 6.775-2.551 8.716-4.604.855-1 1.648-2.112 2.209-3.293.561-1.181.936-2.474.936-3.714 0-1.291 0-3.682-.763-4.684C22.439 1.266 21.434.944 18.798 2.805 16.046 4.747 13.087 8.686 12 10.8z"/>
                    </svg>
                </a>
            </div>
<address itemscope itemtype="https://schema.org/Organization">
    If you need help or something isn't working right, please <a href="mailto:help@americascountry.us"><span itemprop="email">help@americascountry.us</span></a>, text, or call us: <a href="tel:+18017884916" itemprop="telephone">+1 (801) 788-4916</a>.
</address>        
        </section>
    </main>

    <footer>
        <p>Thank you for visiting <em>America's Country</em>! All Rights Reserved.<br>
        Copyright © 2011 - <span id="current-year"></span> SuiteSounds Entertainment, LLC.</p>
        <p>
            <a href="https://stationplaylist.com" target="_blank" rel="noopener noreferrer" aria-label="StationPlaylist Automation Software">
                <img src="images/splpowered.png" alt="America's Country is powered by Station Playlist" width="88" height="31" loading="lazy" style="margin-right: 20px;">
            </a>
            <a href="https://angryaudio.com/products/c6s/" target="_blank" rel="noopener noreferrer" aria-label="Angry Audio Chameleon Audio Processing">
                <img src="images/AAlogo88x31.jpg" alt="Angry Audio Chameleon Audio Processing" width="88" height="31" loading="lazy">
            </a>
        </p>
        <div id="donate-button-container">
            <div id="donate-button"></div>
        </div>
    </footer>

    <script src="https://www.paypalobjects.com/donate/sdk/donate-sdk.js" defer></script>
    <script>
        let currentTheme = localStorage.getItem('theme') || 'dark';

        document.addEventListener('DOMContentLoaded', () => {
            const currentYearEl = document.getElementById('current-year');
            if (currentYearEl) {
                currentYearEl.textContent = new Date().getFullYear();
            }
            
            applyTheme(currentTheme);
            updateThemeUI();

            // Lazy-render PayPal button once SDK script loads
            if (window.PayPal && PayPal.Donation) {
                renderPayPal();
            } else {
                window.addEventListener('load', renderPayPal);
            }
        });

        function renderPayPal() {
            if (document.getElementById('donate-button').children.length > 0) return;
            PayPal.Donation.Button({
                env: 'production',
                hosted_button_id: 'AG8ADTWQ3VKRE',
                image: {
                    src: 'https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif',
                    alt: 'Donate with PayPal button',
                    loading: 'lazy'
                }
            }).render('#donate-button');
        }

        function toggleTheme() {
            currentTheme = currentTheme === 'dark' ? 'light' : 'dark';
            localStorage.setItem('theme', currentTheme);
            applyTheme(currentTheme);
            updateThemeUI();
        }

        function applyTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
        }

        function updateThemeUI() {
            const themeIcon = document.getElementById('theme-icon');
            const themeText = document.getElementById('theme-text');
            
            if (!themeIcon || !themeText) return;

            if (currentTheme === 'light') {
                themeIcon.textContent = '☀️';
                themeText.textContent = 'Light';
            } else {
                themeIcon.textContent = '🌙';
                themeText.textContent = 'Dark';
            }
        }

        function toggleServices() {
            const wrapper = document.getElementById('services-wrapper');
            const btn = document.getElementById('view-more-btn');
            
            wrapper.classList.toggle('expanded');
            
            if (wrapper.classList.contains('expanded')) {
                btn.textContent = 'Show Less';
            } else {
                btn.textContent = 'View All Services';
                document.getElementById('services').scrollIntoView({ behavior: 'smooth' });
            }
        }
    </script>
    <script defer src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7092128557292662" crossorigin="anonymous"></script>
    <script defer src="https://www.googletagmanager.com/gtag/js?id=G-7PSR0H57RS"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-7PSR0H57RS');
    </script>
</body>
</html>
