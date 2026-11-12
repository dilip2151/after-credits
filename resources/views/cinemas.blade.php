<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cinema finder | After Credits</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <header class="site-header">
        <div class="container">
            <a class="brand" href="{{ url('/watchlist') }}">After Credits</a>
        </div>
    </header>

    <main class="container">
        <section class="collection" aria-labelledby="cinema-title"
                 data-search-url="{{ route('cinemas.search', [], false) }}">
            <h1 id="cinema-title">Find nearby cinemas</h1>
            <p>Search selected Birmingham cinemas within 10 km of your approximate location
                or Birmingham city centre. Distances are measured in a straight line.</p>

            <p><button id="use-location" type="button">Use my location</button></p>
            <p><button id="use-birmingham" class="secondary" type="button">
                Search from Birmingham city centre
            </button></p>

            <p id="location-status" role="status"></p>
            <ul id="cinema-results" class="film-list" role="list"></ul>

            <p>Venue locations from <a href="https://www.openstreetmap.org/copyright">OpenStreetMap contributors</a>.</p>
            <p><a href="{{ url('/watchlist') }}">Back to my watchlist</a></p>
        </section>
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
                    row.className = 'film-row';

                    const name = document.createElement('strong');
                    name.textContent = `${cinema.name} (${cinema.distance_km} km)`;

                    const mapLink = document.createElement('a');
                    mapLink.href = cinema.map_url;
                    mapLink.textContent = 'View on map';
                    mapLink.setAttribute('aria-label', `View on map: ${cinema.name}`);

                    row.append(name, mapLink);
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