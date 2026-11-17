<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My watchlist | After Credits</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        .watchlist-page .hero {
            padding-block: clamp(1.25rem, 4vw, 2.5rem);
        }

        .watchlist-page .add-film-controls {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 0.5rem;
        }

        .watchlist-page .add-film-controls input {
            min-width: 0;
            width: 100%;
        }

        .watchlist-page button,
        .watchlist-page summary {
            min-height: 44px;
        }

        .watchlist-page summary {
            box-sizing: border-box;
            padding-block: 0.6rem;
            cursor: pointer;
        }

        .watchlist-page .saved-film {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            align-items: start;
            gap: 0.75rem;
            overflow-wrap: anywhere;
        }

        .saved-film .film-details,
        .saved-film .film-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.5rem;
            min-width: 0;
        }

        .saved-film .film-details h4,
        .saved-film .film-actions form {
            margin: 0;
        }

        .saved-film .film-actions .remove-film-button {
            border-color: transparent;
            text-decoration: underline;
        }

        .saved-film [data-watch-error]:empty {
            display: none;
        }

        .saved-film .film-availability {
            min-width: 0;
            border-top: 1px solid #c4d8ec;
            padding-top: 0.25rem;
        }

        .watchlist-page select {
            max-width: 100%;
            min-height: 44px;
        }

        @media (min-width: 44rem) {
            .watchlist-page .saved-film {
                grid-template-columns: minmax(0, 1fr) minmax(0, auto);
            }

            .saved-film .film-actions {
                justify-content: flex-end;
            }

            .saved-film .film-availability {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 24rem) {
            .watchlist-page .add-film-controls {
                grid-template-columns: minmax(0, 1fr);
            }
        }

        body.after-credits {
            background: #f7f3ed;
            color: #382f3a;
            font-family: system-ui, sans-serif;
            font-size: 1rem;
            line-height: 1.55;
        }

        .after-credits .container {
            width: min(72rem, calc(100% - 2rem));
            max-width: none;
            margin-inline: auto;
        }

        .after-credits .site-header {
            background: #392d3b;
            border-bottom: 0;
        }

        .after-credits .site-header a {
            color: #fff;
            display: inline-flex;
            align-items: center;
            min-height: 44px;
            border-color: #8194aa;
        }

        .watchlist-page .hero {
            margin-block: 1.5rem;
            padding: clamp(1rem, 4vw, 2.5rem);
            border-radius: 1.25rem;
            background: #49364a;
            color: #fff;
        }

        .watchlist-page .hero h1 {
            color: inherit;
            font-size: clamp(2rem, 6vw, 3.5rem);
            line-height: 1.1;
            margin: 0;
        }

        .watchlist-page .hero h1 em,
        .watchlist-page .hero .intro {
            color: #f3dce9;
        }

        .watchlist-page .ticket {
            box-shadow: none;
            background: #e7f1ff;
            color: #382f3a;
            border-radius: 1rem;
        }

        .watchlist-page .ticket h2 {
            font-size: 1rem;
        }

        .watchlist-workspace {
            display: grid;
            gap: 1rem;
            align-items: start;
            padding-bottom: 2rem;
        }

        .watchlist-page .watchlist-workspace > .collection {
            min-width: 0;
            margin: 0;
            padding: clamp(1rem, 3vw, 1.5rem);
            border: 1px solid #ddd2da;
            border-radius: 1rem;
            background: #fff;
        }

        .watchlist-workspace > .collection > h2 {
            margin: 0 0 0.75rem;
            font-size: 1.35rem;
        }

        .watchlist-page summary {
            color: #44334a;
            font-weight: 650;
        }

        .watchlist-page .film-row {
            background: #faf7f2;
            border: 1px solid #ddd2da;
            border-radius: 0.8rem;
            padding: 0.85rem;
            overflow-wrap: anywhere;
            min-width: 0;
        }

        .watchlist-page .film-row + .film-row {
            margin-top: 0.65rem;
        }

        .watchlist-page .saved-film .film-actions {
            max-width: 100%;
        }

        .watchlist-page .film-availability > details {
            padding-inline: 0.65rem;
            border-radius: 0.5rem;
            background: #eaf0e7;
        }

        .watchlist-page .film-availability > details + details {
            margin-top: 0.4rem;
        }

        .watchlist-page #film-search-status:empty,
        .watchlist-page #film-search-results:empty {
            display: none;
        }

        .after-credits :focus-visible {
            outline: 3px solid #765181;
            outline-offset: 3px;
        }


        @media (max-width: 44rem) {
            .watchlist-page .hero {
                display: grid;
                grid-template-columns: minmax(0, 1fr);
                gap: 1rem;
            }

            .watchlist-page .ticket {
                width: auto;
                min-width: 0;
            }
        }
        .watchlist-group + .watchlist-group {
            border-top: 1px solid #ddd2da;
            margin-top: 1.5rem;
            padding-top: 1rem;
        }

        .watchlist-group > h3 {
            margin: 0 0 0.65rem;
            font-size: 1.05rem;
        }

        .watchlist-group [hidden] {
            display: none;
        }

        .saved-film .rewatch-options {
            min-width: 0;
            grid-column: 1 / -1;
        }

        .saved-film .saved-film-poster {
            display: grid;
            place-items: center;
            flex: 0 0 3.5rem;
            width: 3.5rem;
            aspect-ratio: 2 / 3;
            overflow: hidden;
            border-radius: 0.5rem;
            background: #e9e1eb;
            color: #5b4a60;
            font-size: 0.7rem;
            text-align: center;
        }

        .saved-film .saved-film-poster img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .watchlist-shortcuts {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(8rem, 100%), 1fr));
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .watchlist-shortcuts a {
            display: grid;
            place-items: center;
            min-height: 44px;
            padding: 0.5rem;
            border: 1px solid #ddd2da;
            border-radius: 0.65rem;
            background: #fff;
            color: #44334a;
            font-weight: 650;
            text-align: center;
        }

        .watchlist-shortcuts a[aria-current="page"],
        .watchlist-shortcuts a:hover {
            background: #44334a;
            color: #fff;
        }

        .watchlist-page .watchlist-group > h3 {
            color: #44334a;
            font-size: 1.15rem;
            margin-bottom: 0.85rem;
            scroll-margin-top: 1rem;
        }

        .watchlist-page .saved-film .film-details {
            display: grid;
            grid-template-columns: 3.5rem minmax(0, 1fr);
            gap: 0.75rem;
        }

        .watchlist-page .saved-film .film-details h4 {
            font-size: 1.05rem;
            line-height: 1.35;
        }

        .watchlist-page .saved-film .film-details .status {
            grid-column: 2;
            justify-self: start;
        }

        .watchlist-page .film-availability > details[open] {
            padding-bottom: 0.65rem;
        }

        .watchlist-page [data-page-panel][hidden],
        .watchlist-page [data-watchlist-overview][hidden] {
            display: none;
        }

        .watchlist-page .watchlist-workspace {
            grid-template-columns: minmax(0, 1fr);
        }

        .watchlist-page[data-view] .watchlist-shortcuts {
            margin-top: 1rem;
        }

        .watchlist-page[data-view] .watchlist-group {
            border: 0;
            margin: 0;
            padding: 0;
        }
        .watchlist-page .watchlist-count-links {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.5rem;
        }

        .watchlist-count-links a {
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
            min-height: 44px;
            padding: 0.55rem;
            border: 1px solid #cdbace;
            border-radius: 0.6rem;
            background: #fff;
            color: #44334a;
            text-decoration: none;
            overflow-wrap: anywhere;
        }

        .watchlist-count-links a:hover,
        .watchlist-count-links a[aria-current="page"] {
            background: #44334a;
            color: #fff;
        }

        .watchlist-count-links strong {
            font-size: clamp(1.35rem, 4vw, 2rem);
            line-height: 1.1;
        }

        .watchlist-page .hero {
            position: relative;
            isolation: isolate;
            overflow: hidden;
        }

        .watchlist-poster-stack {
            position: absolute;
            z-index: -1;
            inset: 0 38% 0 0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            pointer-events: none;
            opacity: 0.22;
        }

        .watchlist-poster-stack img {
            flex: 0 1 auto;
            width: clamp(5rem, 15vw, 12rem);
            min-width: 0;
            height: auto;
            aspect-ratio: 2 / 3;
            object-fit: cover;
            border-radius: 0.65rem;
            box-shadow: 0 8px 24px #08172c;
            transform: rotate(-9deg);
        }

        .watchlist-poster-stack img + img {
            margin-inline-start: -1.15rem;
        }

        .watchlist-poster-stack img:nth-child(even) {
            transform: rotate(9deg);
        }

        @media (max-width: 44rem) {
            .watchlist-poster-stack {
                inset: 0;
            }
        }

        body.after-credits {
            background: #f7f3ed;
        }

        .watchlist-page .hero {
            align-items: center;
            gap: clamp(1rem, 4vw, 3rem);
            padding: clamp(1.25rem, 5vw, 3.75rem);
            border: 1px solid #715868;
            border-radius: 1.5rem;
            background: linear-gradient(120deg, #342633, #604858);
            box-shadow: 0 16px 35px #392d3b24;
        }

        .watchlist-page .hero h1 {
            font-weight: 750;
            letter-spacing: -0.045em;
        }

        .watchlist-page .hero .intro {
            max-width: 32ch;
            margin-bottom: 0;
        }

        .watchlist-poster-stack {
            inset-inline: 4% 32%;
            justify-content: flex-end;
            opacity: 0.38;
            mask-image: linear-gradient(90deg, transparent, #000 48%);
        }

        .watchlist-poster-stack img {
            width: clamp(5rem, 13vw, 10rem);
            border: 1px solid #bda3b5;
        }

        .watchlist-page .ticket {
            padding: clamp(0.85rem, 3vw, 1.5rem);
            background: #49364a;
            border: 1px solid #aa8fa7;
            color: #fff;
            box-shadow: 0 10px 28px #07182755;
        }

        .watchlist-page .ticket h2 {
            color: #faedf5;
        }

        .watchlist-count-links a {
            padding: clamp(0.55rem, 2vw, 0.9rem);
            background: #eee5f3;
            font-weight: 650;
        }

        .watchlist-page .watchlist-workspace > .collection {
            padding: clamp(1rem, 3vw, 2rem);
            border-radius: 1.15rem;
            box-shadow: 0 8px 24px #392d3b0b;
        }

        .watchlist-page #film-search-title {
            font-size: clamp(1.4rem, 3vw, 1.85rem);
            letter-spacing: -0.025em;
        }

        .watchlist-page .add-film-controls input {
            min-height: 44px;
            border-radius: 0.65rem;
        }

        .watchlist-page .add-film-controls button {
            border-radius: 0.65rem;
        }

        @media (max-width: 44rem) {
            .watchlist-poster-stack {
                inset: 0 0 auto;
                height: 12rem;
                opacity: 0.25;
            }
        }
        .watchlist-page .watchlist-count-links a:first-child:not([aria-current]):not(:hover):not(:focus-visible) {
            background: #e7efdf;
            color: #304332;
        }

        .watchlist-page .watchlist-count-links a:hover,
        .watchlist-page .watchlist-count-links a:focus-visible,
        .watchlist-page .watchlist-count-links a[aria-current="page"] {
            background: #5b405d;
            color: #fff;
        }

        .watchlist-page button:not(.secondary) {
            background: #5b405d;
            color: #fff;
        }

        .watchlist-page .secondary {
            background: #f1e8f2;
            border-color: #cdbace;
            color: #44334a;
        }

        .watchlist-page .status[data-watched="true"] {
            background: #e7efdf;
            color: #304332;
        }

        .watchlist-page .search-film-poster,
        .watchlist-page .search-film-options > details {
            background: #eee5f3;
            color: #44334a;
        }
    </style>
    <script src="{{ asset('js/cinema-showtimes.js') }}" defer></script>
    <script src="{{ asset('js/movie-search.js') }}" defer></script>
</head>
<body class="after-credits">
    @php
        $watchedCount = $items->where('watched', true)->count();
        $unwatchedCount = $items->count() - $watchedCount;
    @endphp

    <header class="site-header">
        <div class="container">
            <nav aria-label="Main navigation"
                 style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem;">
                <a class="brand" href="{{ url('/watchlist') }}" style="margin-inline-end: auto;">
                    <span class="brand-mark" aria-hidden="true">▶</span>
                    After Credits
                </a>
                <a href="{{ route('credits.index') }}">Credits</a>
            </nav>
        </div>
    </header>

    <main class="container watchlist-page">
        <section class="hero" aria-labelledby="page-title">
            <div>
                <h1 id="page-title">My <em>watchlist</em></h1>
                <p class="intro">Keep track of the films you want to see and the ones you’ve watched.</p>
                @php
                    $filmsWithTmdbIds = $items->filter(fn ($item) => $item->tmdb_id);
                    $bannerFilms = $filmsWithTmdbIds->reject(fn ($item) => $item->watched)->take(2)
                        ->concat($filmsWithTmdbIds->filter(fn ($item) => $item->watched)->take(2));
                @endphp

                @if ($bannerFilms->isNotEmpty())
                    <div class="watchlist-poster-stack" aria-hidden="true">
                        @foreach ($bannerFilms as $film)
                            <img src="{{ route('watchlist.poster', ['item' => $film->id]) }}"
                                 alt="" width="64" height="96" decoding="async"
                                 onerror="this.remove()">
                        @endforeach
                    </div>
                @endif
            </div>

            <aside class="ticket" aria-labelledby="ticket-title">
                <h2 id="ticket-title">Watchlist at a glance</h2>
                <div class="ticket-counts watchlist-count-links">
                    <a href="#yet-to-watch" data-view-link="yet-to-watch">
                        <span>Yet to watch</span>
                        <strong id="unwatched-count">{{ $unwatchedCount }}</strong>
                    </a>
                    <a href="#watched" data-view-link="watched">
                        <span>Watched</span>
                        <strong id="watched-count">{{ $watchedCount }}</strong>
                    </a>
                </div>
            </aside>
        </section>

        <nav class="watchlist-shortcuts" aria-label="Watchlist shortcuts">
            <a href="#find-a-film" data-view-link="find-a-film">Find a film</a>
            <a href="{{ route('cinemas.index') }}">Cinema Finder</a>
        </nav>

        <div class="watchlist-workspace">
            <section class="collection" data-page-panel="find-a-film" aria-labelledby="film-search-title">
                <h2 id="film-search-title">Find a film</h2>

                <form id="film-search-form" class="add-film" method="GET"
                      action="{{ route('movies.search') }}">
                    <label for="film-query">Film title to search</label>
                    <div class="add-film-controls">
                        <input id="film-query" name="query" type="search"
                               minlength="2" maxlength="100" required>
                        <button type="submit">Search</button>
                    </div>
                </form>

                <div id="film-match-controls" hidden>
                    <p id="film-match-status" role="status"></p>
                    <button id="cancel-film-match" class="secondary" type="button">
                        Cancel matching
                    </button>
                </div>

                <p id="film-search-status" role="status"></p>
                <ul id="film-search-results" class="film-list" role="list"></ul>

                <form method="POST" action="{{ route('watchlist.store') }}" hidden>
                    @csrf
                    <input id="title" name="title" type="hidden">
                </form>

                @error('title')
                    <p class="error" role="alert">{{ $message }}</p>
                @enderror

                @error('tmdb_id')
                    <p class="error" role="alert">{{ $message }}</p>
                @enderror
            </section>

            <section class="collection" data-page-panel="saved" aria-label="Watchlist films" hidden>

                @foreach ([
                    'unwatched' => ['Yet to watch', $items->reject(fn ($item) => $item->watched), 'Search for a film and select Add to watchlist.'],
                    'watched' => ['Watched', $items->filter(fn ($item) => $item->watched), 'Films you mark as watched will appear here.'],
                ] as $group => [$heading, $groupItems, $emptyMessage])
                    <section class="watchlist-group" data-page-panel="{{ $group === 'unwatched' ? 'yet-to-watch' : 'watched' }}" aria-labelledby="{{ $group }}-heading">
                        <h3 id="{{ $group }}-heading">{{ $heading }}</h3>
                        <p id="{{ $group }}-empty" class="empty-state" @if ($groupItems->isNotEmpty()) hidden @endif>{{ $emptyMessage }}</p>
                        <ul id="{{ $group }}-films" class="film-list" role="list" @if ($groupItems->isEmpty()) hidden @endif>
                            @foreach ($groupItems as $item)
                                <li class="film-row saved-film">
                                    <div class="film-details">
                                        <span class="saved-film-poster" aria-hidden="true">
                                            @if ($item->tmdb_id)
                                                <img src="{{ route('watchlist.poster', ['item' => $item->id]) }}"
                                                     alt="" width="56" height="84" loading="lazy" decoding="async"
                                                     onerror="this.parentElement.textContent = 'No poster'">
                                            @else
                                                No poster
                                            @endif
                                        </span>
                                        <h4>{{ $item->title }}</h4>
                                        <span class="status" data-watch-status tabindex="-1"
                                              @unless ($item->watched) hidden @endunless
                                              data-watched="{{ $item->watched ? 'true' : 'false' }}">
                                            {{ $item->watched ? 'Watched' : 'Unwatched' }}
                                        </span>
                                    </div>

                                    <div class="film-actions">
                                        @unless ($item->watched)
                                            <form method="POST" data-mark-watched
                                                  action="{{ route('watchlist.mark-watched', ['item' => $item->id]) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button class="secondary" type="submit"
                                                        aria-label="Mark as watched: {{ $item->title }}">
                                                    Mark as watched
                                                </button>
                                                <p class="error" role="alert" data-watch-error></p>
                                            </form>
                                        @endunless

                                        <form method="POST" data-remove-film="{{ $item->title }}"
                                              action="{{ route('watchlist.destroy', ['item' => $item->id]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="secondary remove-film-button" type="submit"
                                                    aria-label="Remove {{ $item->title }} from watchlist">
                                                Remove
                                            </button>
                                        </form>

                                        @unless ($item->tmdb_id)
                                            <form method="POST" data-match-film="{{ $item->title }}"
                                                  action="{{ route('watchlist.match', ['item' => $item->id]) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="tmdb_id" value="">
                                                <button class="secondary" type="button" data-start-film-match hidden
                                                        aria-label="Match film: {{ $item->title }}">
                                                    Match film
                                                </button>
                                            </form>
                                        @endunless
                                    </div>

                                    @if ($item->tmdb_id)
                                        <div class="film-availability" data-saved-watch-options
                                             data-tmdb-id="{{ $item->tmdb_id }}"
                                             data-film-title="{{ $item->title }}"
                                             data-editions-url="{{ route('movies.cinema-editions', ['movie' => $item->tmdb_id], false) }}"
                                             data-showtimes-url="{{ route('cinemas.showtimes', ['film' => 'MOVIEGLU_ID'], false) }}"></div>
                                    @else
                                        <div class="film-availability">
                                            <a href="#find-a-film">Find watch options for {{ $item->title }} in film search</a>
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endforeach
            </section>
        </div>
    </main>

    <div class="modal fade" id="film-details-modal" tabindex="-1" aria-labelledby="film-details-title"
         aria-hidden="true" data-details-url="{{ route('movies.details', ['movie' => 'MOVIE_ID'], false) }}">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
            <div class="modal-content" style="background:#f7f3ed;color:#382f3a">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="film-details-title">Film details</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="film-details-status" role="status" class="mb-0"></p>
                    <div id="film-details-content" class="d-flex flex-wrap align-items-start gap-3"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const watchlistPage = document.querySelector('.watchlist-page');
        const viewLinks = [...watchlistPage.querySelectorAll('[data-view-link]')];

        function showWatchlistView() {
            const view = window.location.hash.slice(1);
            const selected = viewLinks.some((link) => link.dataset.viewLink === view);

            if (selected) {
                watchlistPage.dataset.view = view;
            } else {
                delete watchlistPage.dataset.view;
            }

            watchlistPage.querySelectorAll('[data-watchlist-overview]').forEach((element) => {
                element.hidden = selected;
            });

            watchlistPage.querySelectorAll('[data-page-panel]').forEach((panel) => {
                const name = panel.dataset.pagePanel;
                const showingList = view === 'yet-to-watch' || view === 'watched';
                panel.hidden = name === 'saved'
                    ? !showingList
                    : selected && name !== view;
            });

            viewLinks.forEach((link) => {
                if (link.dataset.viewLink === view) {
                    link.setAttribute('aria-current', 'page');
                } else {
                    link.removeAttribute('aria-current');
                }
            });
        }

        window.addEventListener('hashchange', showWatchlistView);
        showWatchlistView();

        watchlistPage.addEventListener('click', (event) => {
            if (event.target.closest('[data-start-film-match]')) {
                window.location.hash = 'find-a-film';
                showWatchlistView();
            }
        }, true);

        function wrapRewatchOptions(card) {
            const availability = card.querySelector('.film-availability');
            const disclosure = document.createElement('details');
            disclosure.className = 'rewatch-options';

            const summary = document.createElement('summary');
            summary.textContent = 'Watch again';
            summary.setAttribute('aria-label', `Watch ${card.querySelector('h4').textContent} again`);

            availability.before(disclosure);
            disclosure.append(summary, availability);

            availability.querySelectorAll('details').forEach((panel) => {
                panel.open = false;
            });
        }

        document.querySelectorAll('#watched-films > .saved-film').forEach(wrapRewatchOptions);

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-saved-watch-options]').forEach((container) => {
                const film = {
                    id: container.dataset.tmdbId,
                    title: container.dataset.filmTitle,
                };

                const onlineOptions = createWatchOptions(film);
                const onlineSummary = onlineOptions.querySelector('summary');
                onlineSummary.textContent = 'Online platform';
                onlineSummary.setAttribute('aria-label', `Online platform options for ${film.title}`);

                const cinemaOptions = createCinemaShowtimes(film, {
                    editions: container.dataset.editionsUrl,
                    showtimes: (editionId) => container.dataset.showtimesUrl.replace(
                        'MOVIEGLU_ID',
                        encodeURIComponent(editionId),
                    ),
                });
                const cinemaSummary = cinemaOptions.querySelector('summary');
                cinemaSummary.textContent = 'Cinemas';
                cinemaSummary.setAttribute('aria-label', `Cinemas showing ${film.title}`);

                container.append(onlineOptions, cinemaOptions);
            });
        });

        const watchedCount = document.querySelector('#watched-count');
        const unwatchedCount = document.querySelector('#unwatched-count');

        document.querySelectorAll('[data-remove-film]').forEach((form) => {
            form.addEventListener('submit', (event) => {
                if (!window.confirm(`Remove "${form.dataset.removeFilm}" from your watchlist?`)) {
                    event.preventDefault();
                }
            });
        });

        document.querySelectorAll('[data-mark-watched]').forEach((form) => {
            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                const button = form.querySelector('button');
                const card = form.closest('.saved-film');
                const status = card.querySelector('[data-watch-status]');
                const errorMessage = form.querySelector('[data-watch-error]');

                if (button.disabled) {
                    return;
                }

                button.disabled = true;
                errorMessage.textContent = '';

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: { Accept: 'application/json' },
                        body: new FormData(form),
                    });

                    if (!response.ok) {
                        throw new Error('Update request failed');
                    }

                    const result = await response.json();

                    if (result.watched !== true) {
                        throw new Error('Unexpected update response');
                    }

                    status.textContent = 'Watched';
                    status.dataset.watched = 'true';
                    status.hidden = false;
                    watchedCount.textContent = Number(watchedCount.textContent) + 1;
                    unwatchedCount.textContent = Number(unwatchedCount.textContent) - 1;
                    form.remove();
                    wrapRewatchOptions(card);

                    const watchedFilms = document.querySelector('#watched-films');
                    const unwatchedFilms = document.querySelector('#unwatched-films');

                    watchedFilms.prepend(card);
                    watchedFilms.hidden = false;
                    document.querySelector('#watched-empty').hidden = true;

                    const hasUnwatchedFilms = unwatchedFilms.childElementCount > 0;
                    unwatchedFilms.hidden = !hasUnwatchedFilms;
                    document.querySelector('#unwatched-empty').hidden = hasUnwatchedFilms;

                    if (watchlistPage.dataset.view === 'yet-to-watch') {
                        const heading = document.querySelector('#unwatched-heading');
                        heading.tabIndex = -1;
                        heading.focus();
                    } else {
                        status.focus();
                    }
                } catch {
                    errorMessage.textContent = 'Could not confirm the update. Please try again.';
                    button.disabled = false;
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>