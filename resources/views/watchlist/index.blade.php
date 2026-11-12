<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My watchlist | After Credits</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="{{ asset('js/movie-search.js') }}" defer></script>
</head>
<body>
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

            @if ($items->isEmpty())
                <p class="empty-state">Your watchlist is empty. Add a film to get started.</p>
            @else
                <ul class="film-list" role="list">
                    @foreach ($items as $item)
                        <li class="film-row">
                            <div class="film-details">
                                <h3>{{ $item->title }}</h3>
                                <span class="status" data-watch-status
                                      data-watched="{{ $item->watched ? 'true' : 'false' }}"
                                      tabindex="-1">
                                    {{ $item->watched ? 'Watched' : 'Unwatched' }}
                                </span>
                            </div>

                            <div style="display: grid; gap: 0.5rem;">
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

                                <form method="POST"
                                      action="{{ route('watchlist.destroy', ['item' => $item->id]) }}"
                                      data-remove-film="{{ $item->title }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="secondary" type="submit"
                                            aria-label="Remove {{ $item->title }} from watchlist">
                                        Remove
                                    </button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </main>

    <script>
        const watchedCount = document.querySelector('#watched-count');
        const unwatchedCount = document.querySelector('#unwatched-count');

        document.querySelectorAll('[data-remove-film]').forEach((form) => {
            form.addEventListener('submit', (event) => {
                const title = form.dataset.removeFilm;

                if (!window.confirm(`Remove "${title}" from your watchlist?`)) {
                    event.preventDefault();
                }
            });
        });

        document.querySelectorAll('[data-mark-watched]').forEach((form) => {
            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                const button = form.querySelector('button');
                const status = form.closest('li').querySelector('[data-watch-status]');
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