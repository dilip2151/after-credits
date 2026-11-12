<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My watchlist | After Credits</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    @php
        $watchedCount = $items->where('watched', true)->count();
        $unwatchedCount = $items->count() - $watchedCount;
    @endphp

    <header class="site-header">
        <div class="container">
            <a class="brand" href="{{ url('/watchlist') }}">
                <span class="brand-mark" aria-hidden="true">▶</span>
                After Credits
            </a>
        </div>
    </header>

    <main class="container">
        <section class="hero" aria-labelledby="page-title">
            <div>
                <h1 id="page-title">My <em>watchlist.</em></h1>
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

        <section class="collection" aria-labelledby="film-search-title">
            <h2 id="film-search-title">Find a film</h2>

            <form id="film-search-form" class="add-film" method="GET" action="{{ route('movies.search') }}">
                <label for="film-query">Film title to search</label>
                <div class="add-film-controls">
                    <input id="film-query" name="query" type="search"
                           minlength="2" maxlength="100" required>
                    <button type="submit">Search</button>
                </div>
            </form>

            <p id="film-search-status" role="status"></p>
            <ul id="film-search-results" class="film-list" role="list"></ul>
        </section>

        <section class="collection" aria-labelledby="saved-films-title">
            <h2 id="saved-films-title">Saved films</h2>

            <form class="add-film" method="POST" action="{{ route('watchlist.store') }}">
                @csrf
                <label for="title">Film title</label>
                <div class="add-film-controls">
                    <input id="title" name="title" type="text"
                           value="{{ old('title') }}" maxlength="255" required
                           @error('title') aria-invalid="true" aria-describedby="title-error" @enderror>
                    <button type="submit">Add film</button>
                </div>

                @error('title')
                    <p id="title-error" class="error">{{ $message }}</p>
                @enderror
            </form>

            @forelse ($items as $item)
                @if ($loop->first)
                    <ul class="film-list" role="list">
                @endif

                <li class="film-row">
                    <div class="film-details">
                        <h3>{{ $item->title }}</h3>
                        <span class="status" data-watch-status
                              data-watched="{{ $item->watched ? 'true' : 'false' }}"
                              tabindex="-1">
                            {{ $item->watched ? 'Watched' : 'Unwatched' }}
                        </span>
                    </div>

                    @unless ($item->watched)
                        <form method="POST"
                              action="{{ route('watchlist.mark-watched', ['item' => $item->id]) }}"
                              data-mark-watched>
                            @csrf
                            @method('PATCH')
                            <button class="secondary" type="submit"
                                    aria-label="Mark as watched: {{ $item->title }}">
                                Mark as watched
                            </button>
                            <p class="error" role="alert" data-watch-error></p>
                        </form>
                    @endunless
                </li>

                @if ($loop->last)
                    </ul>
                @endif
            @empty
                <p class="empty-state">Your watchlist is empty. Add a film to get started.</p>
            @endforelse
        </section>
    </main>

    <script>
        const searchForm = document.querySelector('#film-search-form');
        const searchInput = document.querySelector('#film-query');
        const searchButton = searchForm.querySelector('button[type="submit"]');
        const searchStatus = document.querySelector('#film-search-status');
        const searchResults = document.querySelector('#film-search-results');

        searchForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            if (searchButton.disabled) {
                return;
            }

            const query = searchInput.value.trim();
            searchResults.replaceChildren();

            if (query.length < 2) {
                searchStatus.textContent = 'Enter at least two characters.';
                return;
            }

            const url = new URL(searchForm.action);
            url.searchParams.set('query', query);

            searchButton.disabled = true;
            searchStatus.textContent = 'Searching for films…';

            try {
                const response = await fetch(url, {
                    headers: { Accept: 'application/json' },
                });

                if (!response.ok) {
                    throw new Error('Film search failed');
                }

                const { results } = await response.json();

                if (!Array.isArray(results)) {
                    throw new Error('Unexpected search response');
                }

                for (const film of results) {
                    const row = document.createElement('li');
                    row.className = 'film-row';

                    const title = document.createElement('strong');
                    const year = film.release_date?.slice(0, 4);
                    title.textContent = year ? `${film.title} (${year})` : film.title;

                    row.append(title);
                    searchResults.append(row);
                }

                searchStatus.textContent = results.length === 0
                    ? 'No films found. Try a different title.'
                    : `${results.length} ${results.length === 1 ? 'match' : 'matches'} shown.`;
            } catch {
                searchResults.replaceChildren();
                searchStatus.textContent = 'Film search is unavailable right now. Please try again.';
            } finally {
                searchButton.disabled = false;
            }
        });

        const watchedCount = document.querySelector('#watched-count');
        const unwatchedCount = document.querySelector('#unwatched-count');

        document.querySelectorAll('[data-mark-watched]').forEach((form) => {
            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                const button = form.querySelector('button');
                const status = form.closest('li').querySelector('[data-watch-status]');
                const errorMessage = form.querySelector('[data-watch-error]');

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
                    watchedCount.textContent = Number(watchedCount.textContent) + 1;
                    unwatchedCount.textContent = Number(unwatchedCount.textContent) - 1;
                    status.focus();
                    form.remove();
                } catch {
                    errorMessage.textContent = 'Could not confirm the update. Please try again.';
                    button.disabled = false;
                }
            });
        });
    </script>
</body>
</html>