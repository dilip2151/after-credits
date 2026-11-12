<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Credits | After Credits</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <header class="site-header">
        <div class="container">
            <a class="brand" href="{{ url('/watchlist') }}">After Credits</a>
        </div>
    </header>

    <main class="container">
        <section class="collection" aria-labelledby="credits-title">
            <h1 id="credits-title">Credits</h1>

            <h2>Movie data</h2>
            <p>
                <a href="https://www.themoviedb.org/">
                    <img src="{{ asset('images/tmdb-logo.svg') }}"
                         alt="The Movie Database"
                         width="120"
                         style="max-width: 100%; height: auto;">
                </a>
            </p>
            <p>This product uses the TMDB API but is not endorsed or certified by TMDB.</p>

            <p><a href="{{ url('/watchlist') }}">Back to my watchlist</a></p>
        </section>
    </main>
</body>
</html>