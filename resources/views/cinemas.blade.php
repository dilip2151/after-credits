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
            <p>Share your approximate location to find cinemas near you.</p>

            <button id="use-location" type="button">Use my location</button>
            <p id="location-status" role="status"></p>

            <p><a href="{{ url('/watchlist') }}">Back to my watchlist</a></p>
        </section>
    </main>

    <script>
        const locationButton = document.querySelector('#use-location');
        const locationStatus = document.querySelector('#location-status');

        locationButton.addEventListener('click', () => {
            if (!navigator.geolocation) {
                locationStatus.textContent = 'Location access is unavailable in this browser.';
                return;
            }

            locationButton.disabled = true;
            locationStatus.textContent = 'Finding your location…';

            navigator.geolocation.getCurrentPosition(
                () => {
                    locationStatus.textContent = 'Location detected.';
                    locationButton.disabled = false;
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