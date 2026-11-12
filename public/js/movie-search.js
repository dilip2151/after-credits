const searchForm = document.querySelector('#film-search-form');
const searchInput = document.querySelector('#film-query');
const searchButton = searchForm.querySelector('button[type="submit"]');
const searchStatus = document.querySelector('#film-search-status');
const searchResults = document.querySelector('#film-search-results');
const watchlistInput = document.querySelector('#title');

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

            const addButton = document.createElement('button');
            addButton.type = 'button';
            addButton.className = 'secondary';
            addButton.textContent = 'Add to watchlist';
            addButton.setAttribute('aria-label', `Add ${title.textContent} to watchlist`);

            addButton.addEventListener('click', () => {
                watchlistInput.value = film.title;
                watchlistInput.form.requestSubmit();
            });

            row.append(title, addButton);
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