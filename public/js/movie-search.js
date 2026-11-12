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

    summary.textContent = 'Where to watch in the UK';
    summary.setAttribute('aria-label', `Where to watch ${film.title} in the UK`);
    summary.style.minHeight = '44px';
    summary.style.paddingBlock = '0.6rem';
    summary.style.cursor = 'pointer';
    status.setAttribute('role', 'status');

    details.style.flexBasis = '100%';
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
            const url = new URL(`${film.id}/watch-providers`, searchForm.action);
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
            const websites = providers.websites ?? {};

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
                    const website = getHttpsUrl(websites[name]);

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
    row.className = 'film-row';

    const title = document.createElement('strong');
    const year = film.release_date?.slice(0, 4);
    title.textContent = year ? `${film.title} (${year})` : film.title;

    const actionButton = document.createElement('button');
    actionButton.type = 'button';
    actionButton.className = 'secondary';
    actionButton.textContent = selectedMatchForm ? 'Use this match' : 'Add to watchlist';
    actionButton.setAttribute('aria-label', selectedMatchForm
        ? `Match ${selectedMatchForm.dataset.matchFilm} to ${title.textContent}`
        : `Add ${title.textContent} to watchlist`);

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

    const cinemaPanel = createCinemaShowtimes(film, {
        editions: new URL(`${encodeURIComponent(film.id)}/cinema-editions`, searchForm.action),
        showtimes: (editionId) => new URL(
            `../cinemas/showtimes/${encodeURIComponent(editionId)}`,
            searchForm.action,
        ),
    });
    cinemaPanel.style.flexBasis = '100%';

    row.append(title, actionButton, createWatchOptions(film), cinemaPanel);
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