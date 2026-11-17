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

let savingFilm = false;
let savedListNeedsRefresh = false;
const savedFilmIds = new Set();

const saveFeedback = document.createElement('div');
saveFeedback.className = 'toast-container position-fixed bottom-0 end-0 p-3';
saveFeedback.setAttribute('role', 'status');

const saveToast = document.createElement('div');
saveToast.className = 'toast';
saveToast.setAttribute('aria-atomic', 'true');

const saveToastMessage = document.createElement('div');
saveToastMessage.className = 'toast-body';
saveToast.append(saveToastMessage);
saveFeedback.append(saveToast);
document.body.append(saveFeedback);

function showSaveConfirmation(message) {
    if (window.bootstrap?.Toast) {
        window.bootstrap.Toast.getOrCreateInstance(saveToast).show();
        saveToastMessage.textContent = message;
    } else {
        searchStatus.textContent = message;
    }
}

function refreshSavedList() {
    if (savedListNeedsRefresh && window.location.hash === '#yet-to-watch') {
        window.location.reload();
    }
}

window.addEventListener('hashchange', refreshSavedList);

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

    const saveError = document.createElement('p');
    saveError.className = 'error';
    saveError.setAttribute('role', 'alert');
    saveError.style.margin = '0';

    actionButton.addEventListener('click', async () => {
        if (selectedMatchForm) {
            selectedMatchForm.elements.namedItem('tmdb_id').value = String(film.id);
            selectedMatchForm.requestSubmit();
            return;
        }

        if (savingFilm) {
            saveError.textContent = 'Another film is being saved. Try again in a moment.';
            return;
        }

        savingFilm = true;
        saveError.textContent = '';
        actionButton.disabled = true;
        actionButton.textContent = 'Adding…';

        const watchlistForm = watchlistInput.form;
        const data = new FormData(watchlistForm);
        data.set('title', film.title);
        data.set('tmdb_id', String(film.id));

        try {
            const response = await fetch(watchlistForm.action, {
                method: 'POST',
                headers: { Accept: 'application/json' },
                body: data,
            });

            if (response.status !== 201) {
                throw new Error('Watchlist save failed');
            }

            const result = await response.json();
            if (!Number.isInteger(result.unwatched_count)) {
                throw new Error('Invalid watchlist response');
            }

            savedFilmIds.add(film.id);
            savedListNeedsRefresh = true;
            document.querySelector('#unwatched-count').textContent = result.unwatched_count;
            actionButton.textContent = 'Added';
            actionButton.setAttribute('aria-label', `Added to watchlist: ${filmLabel}`);
            showSaveConfirmation(`${filmLabel} added to your watchlist.`);
            refreshSavedList();
        } catch {
            actionButton.disabled = false;
            actionButton.textContent = 'Add to watchlist';
            saveError.textContent = 'Could not confirm the save. Check your watchlist before trying again.';
        } finally {
            savingFilm = false;
        }
    });

    if (!selectedMatchForm && savedFilmIds.has(film.id)) {
        actionButton.disabled = true;
        actionButton.textContent = 'Added';
        actionButton.setAttribute('aria-label', `Added to watchlist: ${filmLabel}`);
    }

    const detailsButton = document.createElement('button');
    detailsButton.type = 'button';
    detailsButton.className = 'secondary';
    detailsButton.textContent = 'Film details';
    detailsButton.setAttribute('aria-label', `Film details: ${filmLabel}`);
    detailsButton.addEventListener('click', () => showFilmDetails(film, detailsButton));

    const actions = document.createElement('div');
    actions.className = 'd-flex flex-wrap gap-2';
    actions.append(actionButton, detailsButton);
    info.append(actions, saveError);

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
const filmDetailsModal = document.querySelector('#film-details-modal');
const filmDetailsTitle = document.querySelector('#film-details-title');
const filmDetailsStatus = document.querySelector('#film-details-status');
const filmDetailsContent = document.querySelector('#film-details-content');
let detailsRequest = null;

filmDetailsModal.addEventListener('hide.bs.modal', () => detailsRequest?.abort());

async function showFilmDetails(film, trigger) {
    if (!window.bootstrap?.Modal) {
        searchStatus.textContent = 'Film details could not open. Refresh the page and try again.';
        return;
    }

    detailsRequest?.abort();
    const request = new AbortController();
    detailsRequest = request;

    filmDetailsTitle.textContent = film.title;
    filmDetailsContent.replaceChildren();
    filmDetailsStatus.textContent = 'Loading film details…';
    filmDetailsModal.addEventListener('hidden.bs.modal', () => trigger.focus(), { once: true });
    window.bootstrap.Modal.getOrCreateInstance(filmDetailsModal).show(trigger);

    try {
        const url = filmDetailsModal.dataset.detailsUrl.replace('MOVIE_ID', encodeURIComponent(film.id));
        const response = await fetch(url, {
            headers: { Accept: 'application/json' },
            signal: request.signal,
        });
        if (!response.ok) {
            throw new Error('Film details request failed');
        }

        const details = await response.json();
        if (request.signal.aborted) {
            return;
        }

        if (typeof film.poster_path === 'string' &&
            /^\/[\w-]+\.(jpe?g|png|webp)$/i.test(film.poster_path)) {
            const poster = document.createElement('img');
            poster.src = `https://image.tmdb.org/t/p/w185${film.poster_path}`;
            poster.alt = '';
            poster.width = 96;
            poster.height = 144;
            poster.className = 'rounded object-fit-cover';
            poster.addEventListener('error', () => poster.remove(), { once: true });
            filmDetailsContent.append(poster);
        }

        const description = document.createElement('div');
        description.style.flex = '1 1 14rem';
        if (details.release_year) {
            const year = document.createElement('p');
            year.className = 'fw-semibold mb-2';
            year.textContent = details.release_year;
            description.append(year);
        }
        const synopsis = document.createElement('p');
        synopsis.textContent = details.overview || 'No synopsis available.';
        description.append(synopsis);
        filmDetailsContent.append(description);
        filmDetailsStatus.textContent = '';
    } catch {
        if (!request.signal.aborted) {
            filmDetailsStatus.textContent = 'Could not load film details. Close this window and try again.';
        }
    }
}
