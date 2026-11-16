const searchForm = document.querySelector('#film-search-form');
const searchInput = document.querySelector('#film-query');
const searchButton = searchForm.querySelector('button[type="submit"]');
const searchStatus = document.querySelector('#film-search-status');
const searchResults = document.querySelector('#film-search-results');
const watchlistInput = document.querySelector('#title');
const matchControls = document.querySelector('#film-match-controls');
const matchStatus = document.querySelector('#film-match-status');
const cancelMatchButton = document.querySelector('#cancel-film-match');

let matchForm = null;
let activeSearch = null;

function createLink(label, url) {
    const link = document.createElement('a');
    link.textContent = label;
    link.href = url;
    return link;
}

function getHttpsUrl(value) {
    if (typeof value !== 'string') {
        return null;
    }

    try {
        const url = new URL(value);
        return url.protocol === 'https:' ? url.href : null;
    } catch {
        return null;
    }
}

function createWatchOptions(film) {
    const details = document.createElement('details');
    const summary = document.createElement('summary');
    const status = document.createElement('p');

    summary.textContent = 'Online platform';
    summary.setAttribute('aria-label', `Online platform options for ${film.title}`);
    status.setAttribute('role', 'status');
    details.append(summary, status);

    let loading = false;
    let loaded = false;

    details.addEventListener('toggle', async () => {
        if (!details.open || loading || loaded) {
            return;
        }

        loading = true;
        status.textContent = 'Loading UK watch options…';

        try {
            const url = new URL(`${encodeURIComponent(film.id)}/watch-providers`, searchForm.action);
            const response = await fetch(url, {
                headers: { Accept: 'application/json' },
            });

            if (!response.ok) {
                throw new Error('Watch options request failed');
            }

            const providers = await response.json();
            const categories = [
                ['Subscription', providers.subscription],
                ['Rent', providers.rent],
                ['Buy', providers.buy],
            ];
            const options = document.createElement('dl');

            for (const [label, names] of categories) {
                if (!Array.isArray(names) || names.length === 0) {
                    continue;
                }

                const heading = document.createElement('dt');
                heading.textContent = label;

                const group = document.createElement('dd');
                const list = document.createElement('ul');
                list.className = 'film-list';
                list.setAttribute('role', 'list');

                for (const name of names) {
                    const item = document.createElement('li');
                    const website = getHttpsUrl(providers.websites?.[name]);

                    if (website) {
                        item.append(createLink(name, website));
                    } else {
                        item.textContent = name;
                    }

                    list.append(item);
                }

                group.append(list);
                options.append(heading, group);
            }

            if (options.childElementCount === 0) {
                status.textContent = 'No UK subscription, rental, or purchase options listed.';
            } else {
                details.append(options);

                const listingsUrl = getHttpsUrl(providers.link);
                if (listingsUrl) {
                    const listings = document.createElement('p');
                    listings.append(createLink(`View all UK listings for ${film.title}`, listingsUrl));
                    details.append(listings);
                }

                const credit = document.createElement('p');
                credit.append(
                    'Availability data from ',
                    createLink('JustWatch', 'https://www.justwatch.com/uk'),
                    '.',
                );
                details.append(credit);
                status.textContent = 'UK watch options loaded.';
            }

            loaded = true;
        } catch {
            status.textContent = 'Could not load watch options. Close and reopen this section to retry.';
        } finally {
            loading = false;
        }
    });

    return details;
}

function createFilmRow(film, selectedMatchForm) {
    const row = document.createElement('li');
    row.className = 'film-row search-film-card';

    const poster = document.createElement('div');
    poster.className = 'search-film-poster';
    poster.setAttribute('aria-hidden', 'true');
    poster.textContent = 'No poster';

    if (typeof film.poster_path === 'string' &&
        /^\/[\w-]+\.(jpe?g|png|webp)$/i.test(film.poster_path)) {
        const image = document.createElement('img');
        image.alt = '';
        image.loading = 'lazy';
        image.decoding = 'async';
        image.addEventListener('error', () => {
            poster.textContent = 'No poster';
        }, { once: true });
        image.src = `https://image.tmdb.org/t/p/w185${film.poster_path}`;
        poster.replaceChildren(image);
    }

    const info = document.createElement('div');
    info.className = 'search-film-info';

    const title = document.createElement('h3');
    title.textContent = film.title;
    info.append(title);

    const year = film.release_date?.slice(0, 4);
    if (year) {
        const releaseYear = document.createElement('p');
        releaseYear.className = 'search-film-year';
        releaseYear.textContent = year;
        info.append(releaseYear);
    }

    const filmLabel = year ? `${film.title} (${year})` : film.title;
    const actionButton = document.createElement('button');
    actionButton.type = 'button';
    actionButton.textContent = selectedMatchForm ? 'Use this match' : 'Add to watchlist';
    actionButton.setAttribute('aria-label', selectedMatchForm
        ? `Use this match: ${filmLabel}`
        : `Add to watchlist: ${filmLabel}`);

    actionButton.addEventListener('click', () => {
        if (selectedMatchForm) {
            selectedMatchForm.elements.namedItem('tmdb_id').value = String(film.id);
            selectedMatchForm.requestSubmit();
            return;
        }

        const watchlistForm = watchlistInput.form;
        watchlistInput.value = film.title;

        const movieId = document.createElement('input');
        movieId.type = 'hidden';
        movieId.name = 'tmdb_id';
        movieId.value = String(film.id);
        watchlistForm.append(movieId);

        try {
            watchlistForm.requestSubmit();
        } finally {
            movieId.remove();
        }
    });

    info.append(actionButton);

    const onlinePanel = createWatchOptions(film);
    onlinePanel.querySelector('summary').setAttribute(
        'aria-label', `Online platform options for ${filmLabel}`,
    );

    const cinemaPanel = createCinemaShowtimes(film, {
        editions: new URL(`${encodeURIComponent(film.id)}/cinema-editions`, searchForm.action),
        showtimes: (editionId) => new URL(
            `../cinemas/showtimes/${encodeURIComponent(editionId)}`,
            searchForm.action,
        ),
    });
    const cinemaSummary = cinemaPanel.querySelector('summary');
    cinemaSummary.textContent = 'Cinemas';
    cinemaSummary.setAttribute('aria-label', `Cinemas and showtimes for ${filmLabel}`);

    const options = document.createElement('div');
    options.className = 'search-film-options';
    options.append(onlinePanel, cinemaPanel);

    row.append(poster, info, options);
    return row;
}

function clearSearch() {
    activeSearch?.abort();
    activeSearch = null;
    searchButton.disabled = false;
    searchResults.replaceChildren();
    searchStatus.textContent = '';
}

searchForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    clearSearch();

    const query = searchInput.value.trim();
    if (query.length < 2) {
        searchStatus.textContent = 'Enter at least two characters.';
        return;
    }

    const request = new AbortController();
    const selectedMatchForm = matchForm;
    activeSearch = request;

    const url = new URL(searchForm.action);
    url.searchParams.set('query', query);
    searchButton.disabled = true;
    searchStatus.textContent = 'Searching for films…';

    try {
        const response = await fetch(url, {
            headers: { Accept: 'application/json' },
            signal: request.signal,
        });

        if (!response.ok) {
            throw new Error('Film search failed');
        }

        const { results } = await response.json();
        if (activeSearch !== request) {
            return;
        }

        if (!Array.isArray(results)) {
            throw new Error('Unexpected search response');
        }

        for (const film of results) {
            searchResults.append(createFilmRow(film, selectedMatchForm));
        }

        searchStatus.textContent = results.length === 0
            ? 'No films found. Try a different title.'
            : `${results.length} ${results.length === 1 ? 'match' : 'matches'} shown.`;
    } catch {
        if (activeSearch === request) {
            searchResults.replaceChildren();
            searchStatus.textContent = 'Film search is unavailable right now. Please try again.';
        }
    } finally {
        if (activeSearch === request) {
            activeSearch = null;
            searchButton.disabled = false;
        }
    }
});

document.querySelectorAll('[data-start-film-match]').forEach((button) => {
    button.addEventListener('click', () => {
        clearSearch();

        matchForm = button.form;
        matchControls.hidden = false;
        matchStatus.textContent = `Matching “${matchForm.dataset.matchFilm}”. Choose the correct title and year.`;

        searchInput.value = matchForm.dataset.matchFilm.slice(0, 100);
        searchInput.focus();

        if (searchInput.value.trim().length >= 2) {
            searchForm.requestSubmit();
        }
    });

    button.hidden = false;
});

cancelMatchButton.addEventListener('click', () => {
    const startButton = matchForm?.querySelector('[data-start-film-match]');

    clearSearch();
    matchForm = null;
    matchControls.hidden = true;
    matchStatus.textContent = '';
    startButton?.focus();
});