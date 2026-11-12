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
        <section class="collection" aria-labelledby="cinema-title">
            <h1 id="cinema-title">Find nearby cinemas</h1>
            <p>Search selected Birmingham cinemas within 10 km of your approximate location.
                Distances are measured in a straight line.</p>

            <button id="use-location" type="button"
                    data-search-url="{{ route('cinemas.search', [], false) }}">
                Use my location
            </button>

            <p id="location-status" role="status"></p>
            <ul id="cinema-results" class="film-list" role="list"></ul>

            <p>Venue locations from <a href="https://www.openstreetmap.org/copyright">OpenStreetMap contributors</a>.</p>
            <p><a href="{{ url('/watchlist') }}">Back to my watchlist</a></p>
        </section>
    </main>

    <script>
        const locationButton = document.querySelector('#use-location');
        const locationStatus = document.querySelector('#location-status');
        const cinemaResults = document.querySelector('#cinema-results');

        locationButton.addEventListener('click', () => {
            cinemaResults.replaceChildren();

            if (!navigator.geolocation) {
                locationStatus.textContent = 'Location access is unavailable in this browser.';
                return;
            }

            locationButton.disabled = true;
            locationStatus.textContent = 'Finding your location…';

            navigator.geolocation.getCurrentPosition(
                async ({ coords }) => {
                    locationStatus.textContent = 'Looking for nearby cinemas…';

                    const url = new URL(locationButton.dataset.searchUrl, window.location.origin);
                    url.searchParams.set('latitude', coords.latitude.toFixed(3));
                    url.searchParams.set('longitude', coords.longitude.toFixed(3));

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

                        locationStatus.textContent = results.length === 0
                            ? 'No cinemas in our Birmingham directory are within 10 km of your location.'
                            : `${results.length} ${results.length === 1 ? 'cinema found' : 'cinemas found'}.`;
                    } catch {
                        cinemaResults.replaceChildren();
                        locationStatus.textContent = 'Cinema search is unavailable right now. Please try again.';
                    } finally {
                        locationButton.disabled = false;
                    }
                },
                (error) => {
                    locationStatus.textContent = error.code === 1
                        ? 'Location permission was denied. You can allow it in your browser settings and try again.'
                        : 'Your location could not be detected. Please try again.';
                    locationButton.disabled = false;
                },
                { timeout: 10000 }
            );
        });
    </script>
</body>
</html>