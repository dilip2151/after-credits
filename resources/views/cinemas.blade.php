<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cinema Finder | After Credits</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        body.cinema-page {
            margin: 0;
            background: #f7f3ed;
            color: #382f3a;
            font-family: system-ui, sans-serif;
            line-height: 1.55;
        }

        .cinema-page .cinema-container {
            width: min(64rem, calc(100% - 2rem));
            margin-inline: auto;
        }

        .cinema-page .cinema-header {
            background: #392d3b;
            color: #fff;
        }

        .cinema-header nav {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem 1rem;
            padding-block: 0.75rem;
        }

        .cinema-header a {
            display: inline-flex;
            align-items: center;
            min-height: 44px;
            color: inherit;
            font-weight: 650;
        }

        .cinema-header .cinema-brand {
            gap: 0.65rem;
            text-decoration: none;
        }

        .cinema-brand-mark {
            display: grid;
            place-items: center;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.65rem;
            background: #67496a;
        }

        .cinema-main {
            padding-block: clamp(1rem, 4vw, 2.5rem);
        }

        .cinema-banner {
            padding: clamp(1.25rem, 5vw, 3rem);
            border-radius: 1.25rem;
            background: linear-gradient(120deg, #342633, #604858);
            color: #fff;
        }

        .cinema-banner h1 {
            margin: 0;
            color: inherit;
            font-size: clamp(1.85rem, 5vw, 3rem);
            line-height: 1.15;
            letter-spacing: -0.03em;
        }

        .cinema-banner p {
            max-width: 58ch;
            margin: 0.75rem 0 0;
            color: #faedf5;
        }

        .cinema-panel {
            margin-top: 1rem;
            padding: clamp(1rem, 4vw, 1.75rem);
            border: 1px solid #ddd2da;
            border-radius: 1rem;
            background: #fff;
            box-shadow: 0 8px 24px #392d3b0b;
        }

        .cinema-panel h2 {
            margin: 0 0 0.85rem;
            color: #44334a;
            font-size: 1.25rem;
        }

        .cinema-actions {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 0.65rem;
        }

        .cinema-page .cinema-actions button {
            min-width: 0;
            min-height: 44px;
            padding: 0.65rem 0.9rem;
            border: 1px solid #5b405d;
            border-radius: 0.65rem;
            background: #5b405d;
            color: #fff;
            font: inherit;
            font-weight: 650;
            white-space: normal;
            overflow-wrap: anywhere;
            cursor: pointer;
        }

        .cinema-page #use-birmingham {
            border-color: #aebfa9;
            background: #e7efdf;
            color: #304332;
        }

        .cinema-page .cinema-actions button:disabled {
            opacity: 0.65;
            cursor: wait;
        }

        .cinema-page :focus-visible {
            outline: 3px solid #916b98;
            outline-offset: 3px;
        }

        .cinema-status {
            margin: 0.85rem 0 0;
            color: #5b4a60;
        }

        .cinema-status:empty,
        .cinema-results:empty {
            margin: 0;
        }

        .cinema-results {
            display: grid;
            gap: 0.65rem;
            margin: 0.75rem 0 0;
            padding: 0;
            list-style: none;
        }

        .cinema-result {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem 1rem;
            padding: 0.85rem;
            border: 1px solid #ddd2da;
            border-radius: 0.75rem;
            background: #faf7f2;
            overflow-wrap: anywhere;
        }

        .cinema-result-info {
            min-width: 0;
        }

        .cinema-distance {
            display: inline-block;
            margin: 0.3rem 0 0;
            padding: 0.15rem 0.5rem;
            border-radius: 0.5rem;
            background: #eee5f3;
            color: #44334a;
            font-size: 0.875rem;
        }

        .cinema-page .cinema-map-link {
            display: inline-flex;
            align-items: center;
            min-height: 44px;
            color: #5b405d;
            font-weight: 650;
        }

        .cinema-credit {
            margin: 1rem 0 0;
            color: #5b4a60;
            font-size: 0.875rem;
        }

        .cinema-page .cinema-credit a {
            color: #5b405d;
        }

        @media (max-width: 34rem) {
            .cinema-actions {
                grid-template-columns: minmax(0, 1fr);
            }
        }
    </style>
</head>
<body class="cinema-page">
    <header class="cinema-header">
        <nav class="cinema-container" aria-label="Main navigation">
            <a class="cinema-brand" href="{{ url('/watchlist') }}">
                <span class="cinema-brand-mark" aria-hidden="true">▶</span>
                After Credits
            </a>
            <a href="{{ url('/watchlist') }}">Watchlist</a>
        </nav>
    </header>

    <main class="cinema-container cinema-main">
        <section class="cinema-banner" aria-labelledby="cinema-title">
            <h1 id="cinema-title">Find nearby cinemas</h1>
            <p>Search selected Birmingham cinemas within 10 km of your approximate location
                or Birmingham city centre. Distances are measured in a straight line.</p>
        </section>

        <section class="cinema-panel" aria-labelledby="cinema-search-title"
                 data-search-url="{{ route('cinemas.search', [], false) }}">
            <h2 id="cinema-search-title">Choose a search location</h2>

            <div class="cinema-actions">
                <button id="use-location" type="button">Use my location</button>
                <button id="use-birmingham" type="button">
                    Search from Birmingham city centre
                </button>
            </div>

            <p id="location-status" class="cinema-status" role="status"></p>
            <ul id="cinema-results" class="cinema-results" role="list"></ul>
        </section>

        <p class="cinema-credit">
            Venue locations from
            <a href="https://www.openstreetmap.org/copyright">OpenStreetMap contributors</a>.
        </p>
    </main>

    <script>
        const cinemaSection = document.querySelector('[data-search-url]');
        const locationButton = document.querySelector('#use-location');
        const birminghamButton = document.querySelector('#use-birmingham');
        const locationStatus = document.querySelector('#location-status');
        const cinemaResults = document.querySelector('#cinema-results');

        function setSearchBusy(busy) {
            locationButton.disabled = busy;
            birminghamButton.disabled = busy;
        }

        async function searchCinemas(latitude, longitude, originLabel) {
            setSearchBusy(true);
            cinemaResults.replaceChildren();
            locationStatus.textContent = `Looking for cinemas near ${originLabel}…`;

            const url = new URL(cinemaSection.dataset.searchUrl, window.location.origin);
            url.searchParams.set('latitude', latitude.toFixed(3));
            url.searchParams.set('longitude', longitude.toFixed(3));

            try {
                const response = await fetch(url, {
                    headers: { Accept: 'application/json' },
                });

                if (!response.ok) {
                    throw new Error('Cinema search failed');
                }

                const { results } = await response.json();

                for (const cinema of results) {
                    const row = document.createElement('li');
                    row.className = 'cinema-result';

                    const info = document.createElement('div');
                    info.className = 'cinema-result-info';

                    const name = document.createElement('strong');
                    name.textContent = cinema.name;

                    const distance = document.createElement('p');
                    distance.className = 'cinema-distance';
                    distance.textContent = `${cinema.distance_km} km`;

                    const nameRow = document.createElement('div');
                    nameRow.append(name);
                    info.append(nameRow, distance);

                    const mapLink = document.createElement('a');
                    mapLink.className = 'cinema-map-link';
                    mapLink.href = cinema.map_url;
                    mapLink.textContent = 'View on map';
                    mapLink.setAttribute('aria-label', `View on map: ${cinema.name}`);

                    row.append(info, mapLink);
                    cinemaResults.append(row);
                }

                const count = results.length;
                locationStatus.textContent = count === 0
                    ? `No cinemas in our directory are within 10 km of ${originLabel}.`
                    : `${count} ${count === 1 ? 'cinema' : 'cinemas'} within 10 km of ${originLabel}.`;
            } catch {
                cinemaResults.replaceChildren();
                locationStatus.textContent = 'Cinema search is unavailable right now. Please try again.';
            } finally {
                setSearchBusy(false);
            }
        }

        birminghamButton.addEventListener('click', () => {
            searchCinemas(52.486, -1.890, 'Birmingham city centre');
        });

        locationButton.addEventListener('click', () => {
            cinemaResults.replaceChildren();

            if (!navigator.geolocation) {
                locationStatus.textContent = 'Location access is unavailable. You can search from Birmingham city centre.';
                return;
            }

            setSearchBusy(true);
            locationStatus.textContent = 'Finding your location…';

            navigator.geolocation.getCurrentPosition(
                ({ coords }) => {
                    searchCinemas(coords.latitude, coords.longitude, 'your location');
                },
                (error) => {
                    locationStatus.textContent = error.code === 1
                        ? 'Location permission was denied. You can search from Birmingham city centre.'
                        : 'Your location could not be detected. Try again or search from Birmingham city centre.';
                    setSearchBusy(false);
                },
                { timeout: 10000 }
            );
        });
    </script>
</body>
</html>