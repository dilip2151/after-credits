<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My watchlist | After Credits</title>
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
            background: #edf3fa;
            color: #142942;
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
            background: #10253e;
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
            background: #142e4b;
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
            color: #cbe1fa;
        }

        .watchlist-page .ticket {
            box-shadow: none;
            background: #e7f1ff;
            color: #142942;
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
            border: 1px solid #cad9e9;
            border-radius: 1rem;
            background: #fff;
        }

        .watchlist-workspace > .collection > h2 {
            margin: 0 0 0.75rem;
            font-size: 1.35rem;
        }

        .watchlist-page summary {
            color: #174779;
            font-weight: 650;
        }

        .watchlist-page .film-row {
            background: #f7faff;
            border: 1px solid #cad9e9;
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
            background: #eef4fc;
        }

        .watchlist-page .film-availability > details + details {
            margin-top: 0.4rem;
        }

        .watchlist-page #film-search-status:empty,
        .watchlist-page #film-search-results:empty {
            display: none;
        }

        .after-credits :focus-visible {
            outline: 3px solid #337ab7;
            outline-offset: 3px;
        }

        @media (min-width: 58rem) {
            .watchlist-workspace {
                grid-template-columns: minmax(18rem, 2fr) minmax(0, 3fr);
            }
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
            border-top: 1px solid #cad9e9;
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
                <a href="{{ route('cinemas.index') }}">Cinema Finder</a>
                <a href="{{ route('credits.index') }}">Credits</a>
            </nav>
        </div>
    </header>

    <main class="container watchlist-page">
        <section class="hero" aria-labelledby="page-title">
            <div>
                <h1 id="page-title">My <em>watchlist</em></h1>
                <p class="intro">Keep track of the films you want to see and the ones you’ve watched.</p>
            </div>

            <aside class="ticket" aria-labelledby="ticket-title">
                <h2 id="ticket-title">Watchlist at a glance</h2>
                <dl class="ticket-counts">
                    <div>
                        <dt>To watch</dt>
                        <dd id="unwatched-count">{{ $unwatchedCount }}</dd>
                    </div>
                    <div>
                        <dt>Watched</dt>
                        <dd id="watched-count">{{ $watchedCount }}</dd>
                    </div>
                </dl>
            </aside>
        </section>

        <div class="watchlist-workspace">
            <section class="collection" aria-labelledby="film-search-title">
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

            <section class="collection" aria-labelledby="saved-films-title">
                <h2 id="saved-films-title">Saved films</h2>

                @foreach ([
                    'unwatched' => ['Yet to watch', $items->reject(fn ($item) => $item->watched), 'Search for a film and select Add to watchlist.'],
                    'watched' => ['Watched', $items->filter(fn ($item) => $item->watched), 'Films you mark as watched will appear here.'],
                ] as $group => [$heading, $groupItems, $emptyMessage])
                    <section class="watchlist-group" aria-labelledby="{{ $group }}-heading">
                        <h3 id="{{ $group }}-heading">{{ $heading }}</h3>
                        <p id="{{ $group }}-empty" class="empty-state" @if ($groupItems->isNotEmpty()) hidden @endif>{{ $emptyMessage }}</p>
                        <ul id="{{ $group }}-films" class="film-list" role="list" @if ($groupItems->isEmpty()) hidden @endif>
                            @foreach ($groupItems as $item)
                                <li class="film-row saved-film">
                                    <div class="film-details">
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
                                            <a href="#film-query">Find watch options for {{ $item->title }} in film search</a>
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

    <script>
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

                    status.focus();
                } catch {
                    errorMessage.textContent = 'Could not confirm the update. Please try again.';
                    button.disabled = false;
                }
            });
        });
    </script>
</body>
</html>